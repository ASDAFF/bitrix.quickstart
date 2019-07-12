<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["BRAND_CODE"] = trim($arParams["BRAND_CODE"]);
	

if(!CModule::IncludeModule("iblock"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

// get enum id & props
$arResult['TITLE'] = $arResult['KEYWORDS'] = $arResult['DESCRIPTION'] = $arResult['H1'] = $arResult['DETAIL_TEXT'] = $realname = '';
$arResult['PICTURE'] = false;

$arEnSel = Array(
	"ID",
	"NAME",
	"IBLOCK_ID",
	"DETAIL_PICTURE",
	"DETAIL_TEXT",
	"PROPERTY_xmlcode",
	"PROPERTY_title",
	"PROPERTY_keywords",
	"PROPERTY_h1",
	"PROPERTY_description"
);
$res = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$arParams["BRANDS_IBLOCK_ID"], "ACTIVE"=>"Y", "CODE"=>$arParams["BRAND_CODE"]), false, false, $arEnSel);
if($arElement = $res->GetNext()){	
	$arResult['TITLE'] = $arElement['PROPERTY_TITLE_VALUE'];
	$arResult['KEYWORDS'] = $arElement['PROPERTY_KEYWORDS_VALUE'];
	$arResult['DESCRIPTION'] = $arElement['PROPERTY_DESCRIPTION_VALUE'];
	$arResult['H1'] = $arElement['PROPERTY_H1_VALUE'];
	$arResult['DETAIL_TEXT'] = $arElement['DETAIL_TEXT'];
	
	if(intval($arElement['DETAIL_PICTURE'])>0)
		$arResult['PICTURE'] = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], array('width'=>300, 'height'=>400), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	
	$rsProduct = CIBlockPropertyEnum::GetList(array(), array("XML_ID" => $arElement["PROPERTY_XMLCODE_VALUE"]));
	if($product = $rsProduct->GetNext()){
		$arrFilter["PROPERTY_fil_models_brand"] = $product['ID'];
		
		if(strlen(trim($arResult['TITLE']))==0)
			$arResult['TITLE'] = $product['VALUE'];
			
		if(strlen(trim($arResult['H1']))==0) 
			$arResult['H1'] = $product['VALUE'];
		
		$arResult['REALNAME'] = $product['VALUE'];
	}
}elseif($product = CIBlockPropertyEnum::GetByID($arParams["BRAND_CODE"])){
	$arrFilter["PROPERTY_fil_models_brand"] = $arParams["BRAND_CODE"];
	
	$arResult['TITLE'] = $arResult['H1'] = $arResult['REALNAME'] = $product['VALUE'];
}else{
	ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}

$arFilter = Array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'GLOBAL_ACTIVE'=>'Y', 'PROPERTY'=>Array('fil_models_brand'=>${$arParams["FILTER_NAME"]}["PROPERTY_fil_models_brand"]));
$db_list = CIBlockSection::GetList(Array("left_margin"=>"asc"), $arFilter, true);
$db_list->SetUrlTemplates("", "/brands/".$arParams["BRAND_CODE"]."/#SECTION_CODE#/");

$arResult['SECTIONS'] = array();
$arResult['SEC_FL'] = 0;
while($ar_result = $db_list->GetNext()){
	$arResult['SECTIONS'][] = $ar_result;
	if($ar_result['DEPTH_LEVEL']==1)
		$arResult['SEC_FL']++;
}

$this->IncludeComponentTemplate();

$APPLICATION->AddChainItem($arResult['REALNAME'], $APPLICATION->GetCurUri());

$APPLICATION->SetTitle($arResult['H1']);
if(strlen($arResult['TITLE'])>0)
	$APPLICATION->SetPageProperty("title", $arResult['TITLE']);
	
if(strlen($arResult['KEYWORDS'])>0)
	$APPLICATION->SetPageProperty("keywords", $arResult['KEYWORDS']);
	
if(strlen($arResult['DESCRIPTION'])>0)
	$APPLICATION->SetPageProperty("description", $arResult['DESCRIPTION']);
?>
