<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Mlife\Asz as ASZ;
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!CModule::IncludeModule('mlife.asz')) {
		return;
}

if(!CModule::IncludeModule("iblock"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$arResult = array();
$arResult['ERROR'] = false;
$arResult['ORDER_ID'] = ($_REQUEST['ID']) ? intval($_REQUEST['ID']) : false;
$arResult['ORDER_PASSW'] = ($_REQUEST['PASS']) ? trim($_REQUEST['PASS']) : false;

if(($arResult['ORDER_ID'] && $arResult['ORDER_PASSW']) || ($USER->IsAuthorized() && $arResult['ORDER_ID'])) {
	$arFilter = array(0 =>array('ID'=>$arResult['ORDER_ID'],'SITEID'=>SITE_ID));
	$arFilter[1]['LOGIC'] = "OR";
	if($arResult['ORDER_PASSW']){
		$arFilter[1]['=PASSW'] = $arResult['ORDER_PASSW'];
	}
	if($USER->IsAuthorized()) {
		$arFilter[1]['=USER.BX_UID'] = $USER->GetID();
	}
	$res = Asz\OrderTable::getList(array(
		'select' => array('*','USER.BX_UID'),
		'filter' => $arFilter,
	));
	if($arData = $res->Fetch()) {
		$ASZ_USER = $arData['USERID'];
		$arResult['ORDERDATA'] = $arData;
	}else{
		$arResult['ERROR'] = GetMessage("MLIFE_ASZ_ORDER_ERR1");
	}
}else{
	$arResult['ERROR'] = GetMessage("MLIFE_ASZ_ORDER_ERR1");
}

if(intval($ASZ_USER)>0 && !$arResult['ERROR']) {
	
	$arResult["BASE_CURENCY"] = Asz\CurencyFunc::getBaseCurency(SITE_ID);
	
	$res = ASZ\BasketTable::getList(
		array(
			'select' => array("*"),
			'filter' => array("USERID"=>$ASZ_USER,"ORDER_ID"=>$arResult['ORDER_ID'])
		)
	);
	$arProd = array();
	$arResult["BASKET_ITEMS"] = array();
	$arResult["ORDER"] = array();
	$arResult["ORDER"]["ITEMSUM"] = 0;
	$arResult["ORDER"]["ITEMDISCOUNT"] = 0;
	while($arRes = $res->Fetch()){
		//echo'<pre>';print_r($arRes);echo'</pre>';
		$arProd[$arRes["PROD_ID"]] = $arRes["PROD_ID"];
		if(!$arRes["DISCOUNT_VAL"]) $arRes["DISCOUNT_VAL"] = 0;
		if(!$arRes["DISCOUNT_CUR"]) $arRes["DISCOUNT_CUR"] = $arResult["BASE_CURENCY"];
		$arRes["PRICE_DISPLAY"] = Asz\CurencyFunc::priceFormat($arRes["PRICE_VAL"],$arRes["PRICE_CUR"],SITE_ID);
		$arRes["PRICE_DISPLAY_ALL"] = Asz\CurencyFunc::priceFormat((($arRes["PRICE_VAL"]-$arRes["DISCOUNT_VAL"])*$arRes["QUANT"]),$arRes["PRICE_CUR"],SITE_ID);
		$arRes["DISCOUNT_DISPLAY"] = Asz\CurencyFunc::priceFormat($arRes["DISCOUNT_VAL"],$arRes["DISCOUNT_CUR"],SITE_ID);
		$arResult["ORDER"]["ITEMSUM"] = $arResult["ORDER"]["ITEMSUM"] + (Asz\CurencyFunc::convertBase($arRes["PRICE_VAL"],$arRes["PRICE_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["ORDER"]["ITEMDISCOUNT"] = $arResult["ORDER"]["ITEMDISCOUNT"] + (Asz\CurencyFunc::convertBase($arRes["DISCOUNT_VAL"],$arRes["DISCOUNT_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["BASKET_ITEMS"][] = $arRes;
	}
		
	if(count($arProd)>0){
		$arSelect = array("ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
		$arFilter = array("ID"=>$arProd,"ACTIVE"=>"Y");
		$rs = CIBlockElement::GetList(array(),$arFilter,false,false,$arSelect);
		while($ar = $rs->GetNext(false,false)) {
			$artempFile = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array('width'=>100, 'height'=>100), BX_RESIZE_IMAGE_EXACT, false);
			$ar["IMG_SRC"] = $artempFile['src'];
			$arResult["PROD"][$ar["ID"]] = $ar;
		}
	}
	
	$arResult["ORDER"]["ITEMSUM_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUM"],false,SITE_ID);
	$arResult["ORDER"]["ITEMDISCOUNT_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMDISCOUNT"],false,SITE_ID);
	$arResult["ORDER"]["ITEMSUMFIN"] = $arResult["ORDER"]["ITEMSUM"] - $arResult["ORDER"]["ITEMDISCOUNT"];
	
	//данные клиента
	$res = ASZ\OrderpropsTable::getList(array(
		'select' => array('*','VALUE'=>'VAL.VALUE'),
		'order' => array("SORT"=>"ASC"),
		'filter' => array("SITEID"=>SITE_ID,'VAL.UID'=>$ASZ_USER)
	));
	$arResult["USERPROPS"] = array();
	while($arRes = $res->Fetch()){
		if($arRes["TYPE"]=="LOCATION" && $arRes["VALUE"]) {
			$loc = ASZ\StateTable::getList(array(
				'select' => array("NAME","CN.NAME"),
				'filter' => array("ID"=>$arRes["VALUE"])
			));
			if($arLoc = $loc->Fetch()) {
				$arRes["VALUE"] = $arLoc['MLIFE_ASZ_STATE_CN_NAME'].' - '.$arLoc['NAME'];
			}
		}
		$arResult["USERPROPS"][] = $arRes;
	}
	
	//дополнительные текстовые данные о заказе
	if($arResult['ORDERDATA']['PAY_ID']){
		$res = ASZ\PaysystemTable::getRowById($arResult['ORDERDATA']['PAY_ID']);
		unset($res['PARAMS']);
		$arResult['ORDERDATA']['PAY_DATA'] = $res;
	}
	if($arResult['ORDERDATA']['DELIVERY_ID']){
		$res = ASZ\DeliveryTable::getRowById($arResult['ORDERDATA']['DELIVERY_ID']);
		unset($res['PARAMS']);
		$arResult['ORDERDATA']['DELIVERY_DATA'] = $res;
	}
	if($arResult['ORDERDATA']['DELIVERY_ID']){
		$res = ASZ\OrderstatusTable::getRowById($arResult['ORDERDATA']['STATUS']);
		$arResult['ORDERDATA']['STATUS_DATA'] = $res;
	}
		
	//общая сумма заказа
	$arResult["ORDER"]["DISCOUNT"] = $arResult['ORDERDATA']['DISCOUNT'];
	$arResult["ORDER"]["DISCOUNT_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["DISCOUNT"],$arResult['ORDERDATA']['CURRENCY'],SITE_ID);
	$arResult["ORDER"]['DELIVERYCOST'] = $arResult['ORDERDATA']['DELIVERY_PRICE'];
	$arResult["ORDER"]['DELIVERYCOST_DISPLAY'] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]['DELIVERYCOST'],$arResult['ORDERDATA']['CURRENCY'],SITE_ID);
	$arResult["ORDER"]['PAYMENTCOST'] = $arResult['ORDERDATA']['PAYMENT_PRICE'];
	$arResult["ORDER"]['PAYMENTCOST_DISPLAY'] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]['PAYMENTCOST'],$arResult['ORDERDATA']['CURRENCY'],SITE_ID);
	$arResult["ORDER"]["ORDERSUM"] = $arResult['ORDERDATA']['PRICE'];
	$arResult["ORDER"]["ORDERSUM_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ORDERSUM"],false,SITE_ID);
	$arResult["ORDER"]["ORDERTAX"] = $arResult['ORDERDATA']['TAX'];
	$arResult["ORDER"]["ORDERTAX_DISPLAY"] = Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ORDERTAX"],false,SITE_ID);
		
	//echo '<pre>'; print_r($arResult); echo'</pre>';
}

$this->IncludeComponentTemplate();

?>