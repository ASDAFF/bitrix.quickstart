<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("mlife.asz");
CModule::IncludeModule("iblock");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$errorAr = array();

$arSites = array();
$obSite = CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	if(!$FilterSiteId || (in_array($arResult['ID'],$FilterSiteId)))
		$arSites[$arResult['ID']] = '['.$arResult['ID'].'] - '.$arResult['NAME'];
}

$errorAr = array();

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
	
	$res = \Mlife\Asz\OrderTable::getList(array("select"=>array("*"),"filter"=>array("ID"=>$ID)));
	$dataAr = $res->Fetch();
	
	$dataUpdate = array();
	$dataUpdate["STATUS"] = $_REQUEST['status'];
	$dataUpdate["PAY_ID"] = $_REQUEST['payment'];
	$dataUpdate["DELIVERY_ID"] = $_REQUEST['delivery'];
	
	$dataUpdate["DISCOUNT"] = doubleval($_REQUEST["ORDER"]["DISCOUNT"]);
	$dataUpdate["TAX"] = doubleval($_REQUEST["ORDER"]["TAX"]);
	$dataUpdate["DELIVERY_PRICE"] = doubleval($_REQUEST["ORDER"]["DELIVERY_PRICE"]);
	$dataUpdate["PAYMENT_PRICE"] = doubleval($_REQUEST["ORDER"]["PAYMENT_PRICE"]);
	
	$res = \Mlife\Asz\OrderTable::update($ID,$dataUpdate);
	
	//пользовательские свойства
	$resOrderProps = \Mlife\Asz\OrderpropsValuesTable::getList(
		array(
			"select" => array("*"),
			"filter" => array("UID"=>$dataAr["USERID"]),
		)
	);
	$updatesProp = array();
	while($orderPropsAr = $resOrderProps->Fetch()){
		$updatesProp[] = $orderPropsAr["PROPID"];
		if(in_array($orderPropsAr["PROPID"],array_keys($_REQUEST["USERPROPS"]))){
			if($_REQUEST["USERPROPS"][$orderPropsAr["PROPID"]]!=$orderPropsAr["VALUE"]){
				\Mlife\Asz\OrderpropsValuesTable::update($orderPropsAr["ID"],array("VALUE"=>$_REQUEST["USERPROPS"][$orderPropsAr["PROPID"]]));
			}
		}
	}
	if(is_array($_REQUEST["USERPROPS"])){
		foreach($_REQUEST["USERPROPS"] as $key=>$val){
			if(!in_array($key,$updatesProp)) {
				\Mlife\Asz\OrderpropsValuesTable::add(array(
					"UID" => $dataAr["USERID"],
					"PROPID" => $key,
					"VALUE" => $val,
				));
			}
		}
	}
	
	\Mlife\Asz\BasketTable::$discountHandler = false; //отменяем расчет скидки
	//обновление товаров
	if($_REQUEST["refresh_basket"]==1){
		$priceZakaz = 0;
		if(is_array($_REQUEST["ORDERITEM"])){
			$priceZakaz = 0;
			foreach($_REQUEST["ORDERITEM"] as $entId=>$tovar){
				if($tovar["DELETE"]==1){
					\Mlife\Asz\BasketTable::delete(array("ID"=>$entId));
				}else{
					\Mlife\Asz\BasketTable::update($entId, array(
						"PROD_NAME" => $tovar["PROD_NAME"],
						"PROD_DESC" => $tovar["PROD_DESC"],
						"PRICE_VAL" => $tovar["PRICE_VAL"],
						"QUANT" => $tovar["QUANT"],
						"DISCOUNT_VAL" => $tovar["DISCOUNT_VAL"],
					));
					$priceZakaz = $priceZakaz + (($tovar["PRICE_VAL"]-doubleval($tovar["DISCOUNT_VAL"]))*$tovar["QUANT"]);
					$priceZakaz = doubleval($priceZakaz);
				}
			}
			$allPrice = $priceZakaz + $dataUpdate["DELIVERY_PRICE"] + $dataUpdate["PAYMENT_PRICE"] + $dataUpdate["TAX"] - $dataUpdate["DISCOUNT"];
			//обновление сумм для заказа
			\Mlife\Asz\Handlers::$arFinBasketRefresh = false;
			\Mlife\Asz\OrderTable::update($ID,array(
				"PRICE" => doubleval($allPrice)
			));
			\Mlife\Asz\Handlers::$arFinBasketRefresh = true;
		
		}
		
		//новый товар
		if(is_array($_REQUEST["ORDERITEMNEW"])){
			
			if(intval($_REQUEST["ORDERITEMNEW"]["ID"])>0){
				
				$addItem = array(
					"PROD_ID" => intval($_REQUEST["ORDERITEMNEW"]["ID"]),
					"USERID" => $dataAr["USERID"],
					"PRICE_VAL" => $_REQUEST["ORDERITEMNEW"]["PRICE_VAL"],
					"PRICE_CUR" => $dataAr["CURRENCY"],
					"UPDATE" => time(),
					"QUANT" => $_REQUEST["ORDERITEMNEW"]["QUANT"],
					"DISCOUNT_VAL" => $_REQUEST["ORDERITEMNEW"]["DISCOUNT_VAL"],
					"SITE_ID" => $dataAr["SITEID"],
					"PROD_NAME" => $_REQUEST["ORDERITEMNEW"]["PROD_NAME"],
					"PROD_DESC" => $_REQUEST["ORDERITEMNEW"]["PROD_DESC"],
					"ORDER_ID" => $ID,
				);
				
				$resAr = CIBlockElement::GetById(intval($_REQUEST["ORDERITEMNEW"]["ID"]));
				if($arDataEl = $resAr->GetNext(false,false)){
					$addItem["PROD_LINK"] = $arDataEl["DETAIL_PAGE_URL"];
					\Mlife\Asz\BasketTable::add($addItem);
					$allPrice = $allPrice + doubleval($_REQUEST["ORDERITEMNEW"]["PRICE_VAL"]);
					$allPrice = doubleval($allPrice);
					//обновление сумм для заказа
					\Mlife\Asz\Handlers::$arFinBasketRefresh = false;
					\Mlife\Asz\OrderTable::update($ID,array(
						"PRICE" => doubleval($allPrice)
					));
					\Mlife\Asz\Handlers::$arFinBasketRefresh = true;
				}else{
					$errorAr[] = Loc::getMessage("MLIFE_ASZ_ORDEREL_NO_IB_ELEMENT");
				}
			
			}
			
		}
		
	}
	if(!$_REQUEST['apply']){
		LocalRedirect("mlife_asz_orderlist.php?lang=".LANG);
	}
	
}

if($ID>0)
{
	$filter = array("ID"=>$ID);
	if($FilterSiteId) {
		$filter["SITEID"] = $FilterSiteId;
	}
	$res = \Mlife\Asz\OrderTable::getList(array("select"=>array("*"),"filter"=>$filter));
	
	$dataAr = $res->Fetch();
	
	if(is_array($dataAr)){
		$str_PAY_ID = $dataAr["PAY_ID"];
		$str_STATUS = $dataAr["STATUS"];
		$str_USERID = $dataAr["USERID"];
		$str_SITEID = $dataAr["SITEID"];
		$str_DELIVERY_ID = $dataAr["DELIVERY_ID"];
		$str_PRICE = $dataAr["PRICE"];
		$str_DISCOUNT = $dataAr["DISCOUNT"];
		$str_TAX = $dataAr["TAX"];
		$str_CURRENCY = $dataAr["CURRENCY"];
		$str_DELIVERY_PRICE = $dataAr["DELIVERY_PRICE"];
		$str_PAYMENT_PRICE = $dataAr["PAYMENT_PRICE"];
		$str_DATE = $dataAr["DATE"];
		$str_PASSW = $dataAr["PASSW"];
		$str_PRICE_TOVAR = $str_PRICE - $str_DELIVERY_PRICE - $str_PAYMENT_PRICE;
		$str_USER_LOCATION = "";
		$str_ID = $dataAr["ID"];
		$str_DATE = $dataAr["DATE"];
		$str_PASSW = $dataAr["PASSW"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_EL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = false;
	
}

$siteRight = $str_SITEID;

//статусы
$res = \Mlife\Asz\OrderStatusTable::getList(
	array(
		'select' => array("ID","NAME"),
		'filter' => array("SITEID"=>$siteRight,"ACTIVE"=>"Y"),
	)
);
$arStatus = array();
while($resAr = $res->Fetch()){
	$arStatus[$resAr["ID"]] = $resAr["NAME"];
}

//значения свойств заказа
$res = \Mlife\Asz\OrderpropsTable::getList(
	array(
		'select' => array("*","VAL.VALUE"),
		'filter' => array("SITEID"=>$siteRight,"ACTIVE"=>"Y","VAL.UID"=>$str_USERID),
	)
);
$arProps = array();
$arPropsAr = array();
while($resAr = $res->Fetch()){
	$arProps[] = $resAr;
	if($resAr["TYPE"]=="LOCATION") $str_USER_LOCATION = $resAr["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"];
	$arPropsAr[] = $resAr["ID"];
}
if(!empty($arPropsAr)){
	
	$res = \Mlife\Asz\OrderpropsTable::getList(
		array(
			'select' => array("*"),
			'filter' => array("SITEID"=>$siteRight,"ACTIVE"=>"Y","!ID"=>$arPropsAr),
		)
	);
	while($resAr = $res->Fetch()){
		$resAr["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"] = "";
		$arProps[] = $resAr;
	}
	
}

//способы доставки
$arResult = array();
$arResult["ORDER"] = array(
	"ITEMSUMFIN" => $str_PRICE_TOVAR,
	"LOCATION_ID" => $str_USER_LOCATION,
);

//состав заказа
$arBasketItems = array();
$sostav = \Mlife\Asz\BasketTable::getList(
	array(
		'select' => array("*"),
		'filter' => array("ORDER_ID"=>$ID),
	)
);
$arResult["ORDER"]["ITEMSUMFIN"] = 0;
$arResult["ORDER"]["ITEMDISCOUNTFIN"] = 0;
$arFinBasketIds = array();
while ($arBasketItem = $sostav->Fetch()){
	$arBasketItems[$arBasketItem["ID"]] = $arBasketItem;
	$arResult["ORDER"]["ITEMSUMFIN"] = $arResult["ORDER"]["ITEMSUMFIN"] + (($arBasketItem["PRICE_VAL"])*$arBasketItem["QUANT"]);
	$arResult["ORDER"]["ITEMDISCOUNTFIN"] =  $arResult["ORDER"]["ITEMDISCOUNTFIN"] + (doubleval($arBasketItem["DISCOUNT_VAL"])*$arBasketItem["QUANT"]);
	$arFinBasketIds[] = $arBasketItem["PROD_ID"];
}

//тут собираем ид и типы инфоблока для ссылок на редактирвоание товара
$resB = \Mlife\Asz\ElementTable::getList(array(
	'select' => array("IBLOCK_ID","ID"),
	'filter' => array("ID"=>$arFinBasketIds)
));
$arIbIds = array();
$arIbBsk = array();
while($arDataB = $resB->Fetch()){
	$arIbIds[$arDataB["ID"]] = $arDataB["IBLOCK_ID"];
	if(!isset($arIbBsk[$arDataB["IBLOCK_ID"]])){
		$resIbType = \CIBlock::GetByID($arDataB["IBLOCK_ID"])->GetNext();
		$arIbBsk[$arDataB["IBLOCK_ID"]] = $resIbType["IBLOCK_TYPE_ID"];
	}
}

$arResult["DELIVERY"] = array();
$res = \Mlife\Asz\DeliveryTable::getList(
	array(
		'filter' => array("SITEID"=>$siteRight,"ACTIVE"=>"Y"),
		'select' => array("ID","NAME","ACTIONFILE","DESC")
		)
);
$i = 0;
while($arRes = $res->Fetch()){
	$cl = "\Mlife\\Asz\\Deliver\\".$arRes["ACTIONFILE"];
	if($arRes["ACTIONFILE"] && class_exists($cl)){
		if($cl::getRight($arRes["ID"],$arResult["ORDER"])){
			$i++;
			$arDelivery = $arRes;
			$arDelivery['COST'] = 0;
			
			$arDelivery['COST'] = $cl::getCost($arRes["ID"],$arResult["ORDER"]);
			$arDelivery['PARAMS'] = $cl::getParamsArray($arRes['PARAMS']);
			$arDelivery['COST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arDelivery['COST'],false,$siteRight);
			
			$arResult["DELIVERY"][$arRes['ID']] = $arDelivery;
		}
	}
}

//способы оплаты
$arResult["PAYMENT"] = array();
$res = \Mlife\Asz\PaysystemTable::getList(
	array(
		'filter' => array("SITEID"=>$siteRight,"ACTIVE"=>"Y"),
		'select' => array("ID","NAME","ACTIONFILE","DESC")
		)
);
$i = 0;
while($arRes = $res->Fetch()){
	$cl = "\Mlife\\Asz\\Payment\\".$arRes["ACTIONFILE"];
	if($arRes["ACTIONFILE"] && class_exists($cl)){
		if($cl::getRight($arRes["ID"],$arResult["ORDER"])){
			$i++;
			$arPayment = $arRes;
			$arPayment['COST'] = 0;
			
			$arPayment['COST'] = $cl::getCost($arRes["ID"],$arResult["ORDER"]);
			$arPayment['PARAMS'] = $cl::getParamsArray($arRes['PARAMS']);
			$arPayment['COST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arPayment['COST'],false,$siteRight);
			
			$arResult["PAYMENT"][$arRes['ID']] = $arPayment;
		}
	}
}

//местоположения
$stateAllAr = array();
$state = \Mlife\Asz\StateTable::getList(array(
	'order' => array("CN.NAME"=>"ASC","SORT"=>"ASC","NAME"=>"ASC"),
	'filter' => array("CN.SITEID"=>$siteRight,"ACTIVE"=>"Y"),
	'select' => array("CN.NAME","NAME","ID"),
));
while($arState = $state->Fetch()){
	$stateAllAr[$arState["ID"]] = $arState["MLIFE_ASZ_STATE_CN_NAME"].' - '.$arState["NAME"];
}

//echo'<pre>';print_r($arProps);echo'</pre>';

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_EL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_EL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
/*
$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_EL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_curency_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_EL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);*/
/*
$context = new CAdminContextMenu($aContext);

$context->Show();
*/
if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_EL_SAVED"), "TYPE"=>"OK"));
  
if(count($errorAr)>0){
	CAdminMessage::ShowMessage(implode(', ',$errorAr));
}

?>
<?if($bVarsShowForm){?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE1")?></th>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_NUM")?>:</td>
		<td width="60%">
			<?=$str_ID?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DATE")?>:</td>
		<td width="60%">
			<?=ConvertTimeStamp($str_DATE,"FULL",$siteRight)?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_PASS")?>:</td>
		<td width="60%">
			<?=$str_PASSW?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_STATUS")?>:</td>
		<td width="60%">
			<select name="status" id="status">
			<?foreach($arStatus as $key=>$status){?>
				<option value="<?=$key?>"<?if($key==$str_STATUS){?> selected="selected"<?}?>><?=$status?></option>
			<?}?>
			</select>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE2")?></th>
	</tr>
	<?foreach($arProps as $field){?>
	<?if($field["DELIVERY"]=="N"){?>
	<tr>
		<td width="40%"><?=$field["NAME"]?></td>
		<td width="60%">
			<?if($field["TYPE"]=="TEXT" || $field["TYPE"]=="EMAIL"){?>
				<input type="text" name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>" value="<?=$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]?>"/>
			<?}elseif($field["TYPE"]=="LOCATION"){?>
				<select name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>">
					<?foreach($stateAllAr as $key=>$val){?>
						<option value="<?=$key?>"<?if($key==$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]){?> selected="selected"<?}?>><?=$val?></option>
					<?}?>
				</select>
			<?}elseif($field["TYPE"]=="TEXTAREA"){?>
				<textarea name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>"><?=$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]?></textarea>
			<?}?>
			
		</td>
	</tr>
	<?}?>
	<?}?>
	
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE3")?></th>
	</tr>
	<?foreach($arProps as $field){?>
	<?if($field["DELIVERY"]=="Y"){?>
	<tr>
		<td width="40%"><?=$field["NAME"]?></td>
		<td width="60%">
			<?if($field["TYPE"]=="TEXT" || $field["TYPE"]=="EMAIL"){?>
				<input type="text" name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>" value="<?=$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]?>"/>
			<?}elseif($field["TYPE"]=="LOCATION"){?>
				<select name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>">
					<?foreach($stateAllAr as $key=>$val){?>
						<option value="<?=$key?>"<?if($key==$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]){?> selected="selected"<?}?>><?=$val?></option>
					<?}?>
				</select>
			<?}elseif($field["TYPE"]=="TEXTAREA"){?>
				<textarea name="USERPROPS[<?=$field["ID"]?>]" id="USERPROPS_<?=$field["ID"]?>"><?=$field["MLIFE_ASZ_ORDERPROPS_VAL_VALUE"]?></textarea>
			<?}?>
			
		</td>
	</tr>
	<?}?>
	<?}?>
	
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE4")?></th>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DELIVERY")?></td>
		<td width="60%">
			<select id="delivery" name="delivery">
			<?foreach($arResult["DELIVERY"] as $key=>$arDelivery){?>
				<option value="<?=$key?>"<?if($key==$str_DELIVERY_ID){?> selected="selected"<?}?>><?=$arDelivery["NAME"]?> - <?=$arDelivery["COST_DISPLAY"]?></option>
			<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_PAY")?></td>
		<td width="60%">
			<select id="payment" name="payment">
			<?foreach($arResult["PAYMENT"] as $key=>$arPayment){?>
				<option value="<?=$key?>"<?if($key==$str_PAY_ID){?> selected="selected"<?}?>><?=$arPayment["NAME"]?> - <?=$arPayment["COST_DISPLAY"]?></option>
			<?}?>
			</select>
		</td>
	</tr>
	
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE5")?> <a id="replacetable" href="#"><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE6")?></a></th>
	</tr>
	<tr><td colspan="2">
	<table class="editee" style="width:100%;display:none;">
	<tr>
		<td><b>ID</b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_NAME")?></b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC")?></b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_PRICE")?></b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_KOL")?></b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_SKIDA")?></b></td>
		<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DELETER")?></b></td>
	</tr>
	<?foreach($arBasketItems as $key=>$item){?>
	<tr>
		<td>
		<?if(isset($arIbIds[$item["PROD_ID"]])){?>
		<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?=$arIbIds[$item["PROD_ID"]]?>&type=<?=$arIbBsk[$arIbIds[$item["PROD_ID"]]]?>&ID=<?=$item["PROD_ID"]?>&lang=ru">
		<?}?>
		<?=$item["PROD_ID"]?>
		<?if(isset($arIbIds[$item["PROD_ID"]])){?>
		</a>
		<?}?>
		</td>
		<td><input type="text" value="<?=$item["PROD_NAME"]?>" name="ORDERITEM[<?=$key?>][PROD_NAME]"/></td>
		<td><input type="text" value="<?=$item["PROD_DESC"]?>" name="ORDERITEM[<?=$key?>][PROD_DESC]"/></td>
		<td><input type="text" value="<?=$item["PRICE_VAL"]?>" name="ORDERITEM[<?=$key?>][PRICE_VAL]"/></td>
		<td><input type="text" value="<?=$item["QUANT"]?>" name="ORDERITEM[<?=$key?>][QUANT]"/></td>
		<td><input type="text" value="<?=$item["DISCOUNT_VAL"]?>" name="ORDERITEM[<?=$key?>][DISCOUNT_VAL]"/></td>
		<td><input type="checkbox" value="1" name="ORDERITEM[<?=$key?>][DELETE]"/></td>
	</tr>
	<?}?>
	<tr>
		<td><input type="text" value="" id="newtovar_PROD_NAME" name="ORDERITEMNEW[ID]"/></td>
		<td><input type="text" value="" id="newtovar_PROD_NAME" name="ORDERITEMNEW[PROD_NAME]"/></td>
		<td><input type="text" value="" id="newtovar_PROD_DESC" name="ORDERITEMNEW[PROD_DESC]"/></td>
		<td><input type="text" value="" id="newtovar_PRICE_VAL" name="ORDERITEMNEW[PRICE_VAL]"/></td>
		<td><input type="text" value="" id="newtovar_QUANT" name="ORDERITEMNEW[QUANT]"/></td>
		<td><input type="text" value="" id="newtovar_DISCOUNT_VAL" name="ORDERITEMNEW[DISCOUNT_VAL]"/></td>
		<td></td>
	</tr>
	<tr>
	<td colspan="6">
		<?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ADDTOVAR")?>
	</td>
	</tr>
	</table>
	<br/>
	<table class="editee" style="width:100%;display:none;">
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUMFIN"],false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST2")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMDISCOUNTFIN"],false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST3")?></td>
			<td><input type="text" value="<?=$str_DISCOUNT?>" name="ORDER[DISCOUNT]"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST4")?></td>
			<td><input type="text" value="<?=$str_TAX?>" name="ORDER[TAX]"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST5")?></td>
			<td><input type="text" value="<?=$str_DELIVERY_PRICE?>" name="ORDER[DELIVERY_PRICE]"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST6")?></td>
			<td><input type="text" value="<?=$str_PAYMENT_PRICE?>" name="ORDER[PAYMENT_PRICE]"></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST7")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_PRICE,false,$siteRight);?></td>
		</tr>
	</table>
	<table class="show" style="width:100%;">
		<tr>
			<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_NAME")?></b></td>
			<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC")?></b></td>
			<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_PRICE")?></b></td>
			<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_KOL")?></b></td>
			<td><b><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_SKIDA")?></b></td>
		</tr>
	<?foreach($arBasketItems as $key=>$item){?>
	<tr>
		<td><a href="<?=$item["PROD_LINK"]?>"><?=$item["PROD_NAME"]?></a></td>
		<td><?=$item["PROD_DESC"]?></td>
		<td><?=\Mlife\Asz\CurencyFunc::priceFormat($item["PRICE_VAL"],false,$siteRight);?></td>
		<td><?=$item["QUANT"]?></td>
		<td><?=\Mlife\Asz\CurencyFunc::priceFormat($item["DISCOUNT_VAL"],false,$siteRight);?></td>
	</tr>
	<?}?>
	</table>
	<br/>
	<table class="show" style="width:100%;">
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUMFIN"],false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST2")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMDISCOUNTFIN"],false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST3")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_DISCOUNT,false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST4")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_TAX,false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST5")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_DELIVERY_PRICE,false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST6")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_PAYMENT_PRICE,false,$siteRight);?></td>
		</tr>
		<tr>
			<td><?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_DESC_ST7")?></td>
			<td><?=\Mlife\Asz\CurencyFunc::priceFormat($str_PRICE,false,$siteRight);?></td>
		</tr>
	</table>
	<input type="hidden" name="refresh_basket" id="refresh_basket" value="0"/>
	</td>
	</tr>
	<style>
	table.show td, table.show th {border-right:1px solid #cccccc;border-top:1px solid #cccccc;padding:3px;}
	table.editee td, table.editee th {border-right:1px solid #cccccc;border-top:1px solid #cccccc;padding:3px;}
	table.show, table.editee {background:#ffffff;border-spacing:0;border-left:1px solid #cccccc;border-bottom:1px solid #cccccc;}
	</style>
	<?CUtil::InitJSCore('jquery');?>
	<script>
	$(document).ready(function(){
		$(document).on('click','#replacetable',function(e){
			e.preventDefault();
			if($(this).hasClass("active")) {
				$(this).removeClass("active").html("<?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE6")?>");
				$("table.editee").css({'display':'none'});
				$("table.show").css({'display':'table'});
				$("#refresh_basket").val(0);
			}else{
				$(this).addClass("active").html("<?=Loc::getMessage("MLIFE_ASZ_ORDEREL_PARAM_TITLE6_REPLACE")?>");
				$("table.editee").css({'display':'table'});
				$("table.show").css({'display':'none'});
				$("#refresh_basket").val(1);
			}
		});
	});
	</script>
	
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"orderlist.php?lang=".LANG,
    
  )
);
?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<?//echo BeginNote();?>
<?//=Loc::getMessage("MLIFE_ASZ_ORDEREL_NOTE")?>
<?//echo EndNote();?>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>