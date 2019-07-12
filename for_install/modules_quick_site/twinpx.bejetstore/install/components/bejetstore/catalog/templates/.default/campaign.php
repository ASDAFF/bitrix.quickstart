<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
if(strpos($arParams["ELEMENT_SORT_FIELD"], "arSortParams") !== false){
	global $arSortParams;
	$arParams["ELEMENT_SORT_FIELD"] = $arSortParams["ELEMENT_SORT_FIELD"];
	$arParams["ELEMENT_SORT_ORDER"] = $arSortParams["ELEMENT_SORT_ORDER"];
}
if(strpos($arParams["ELEMENT_SORT_FIELD2"], "arSortParams") !== false){
	global $arSortParams;
	$arParams["ELEMENT_SORT_FIELD2"] = $arSortParams["ELEMENT_SORT_FIELD2"];
	$arParams["ELEMENT_SORT_ORDER2"] = $arSortParams["ELEMENT_SORT_ORDER2"];
}
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y",
	"SITE_ID" => SITE_ID
);

if (0 < intval($arResult["VARIABLES"]["ELEMENT_ID"]))
{
	$arFilter["PROPERTY_SPECIALOFFER"] = $arResult["VARIABLES"]["ELEMENT_ID"];
	$arAddCacheParams = array();
}elseif('' != $arResult["VARIABLES"]["ELEMENT_CODE"]){
	$arAddCacheParams = array(
		"CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"]
	);
}
?>
<?$obCache = new CPHPCache();
if ($obCache->InitCache(36000, serialize(array_merge($arFilter,$arAddCacheParams)), "/iblock/campaign"))
{
	$arSecitionIDList = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
	$arSecitionIDList = array();
	if (\Bitrix\Main\Loader::includeModule("iblock"))
	{
		if('' != $arResult["VARIABLES"]["ELEMENT_CODE"]){
			$rsCampaign = CIBlockElement::GetList(array(), array("=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"], "ACTIVE" => "Y", "SITE_ID" => SITE_ID));
			if($arCampaign = $rsCampaign->Fetch()){
				$arFilter["PROPERTY_SPECIALOFFER"] = $arCampaign["ID"];
			}
		}

		$dbRes = CIBlockElement::GetList(array(), $arFilter, false, false, false, array('ID','SECTION_ID','IBLOCK_ID',));

		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/iblock/campaign");

			//while ($arGoup = $dbRes->Fetch())
			while ($arGoup = $dbRes->GetNextElement())
			{
				$arFields1 = $arGoup->GetFields();
				//$arSecitionIDList[] = $arGoup["IBLOCK_SECTION_ID"];
				//print_R($arFields1);
				$arSecitionIDList[] = $arFields1["ID"];
			}
			$CACHE_MANAGER->RegisterTag("campaign_id_".$arFilter["PROPERTY_SPECIALOFFER"]);
			$CACHE_MANAGER->EndTagCache();
		}
		else
		{
			while($arGoup = $dbRes->Fetch())
				$arSecitionIDList[] = $arGoup["IBLOCK_SECTION_ID"];
		}
	}
	$obCache->EndDataCache($arSecitionIDList);
}?>
<?if(!empty($arSecitionIDList)):?>
<div class="row">
<div class="col-sm-3">
<?
global $arrFILTER;
$arrFILTER = array(
	//"SECTION_ID" => $arSecitionIDList
	"ID" => $arSecitionIDList
);
//print_R($arrFILTER);
?>
<?$bShowFilter = $APPLICATION->IncludeComponent(
	"bejetstore:catalog.smart.filter",
	"bejetstore",
	Array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arSecitionIDList,
		"FILTER_NAME" => "arrFILTER",
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SAVE_IN_SESSION" => "N",
		"XML_EXPORT" => "Y",
		"SECTION_TITLE" => "NAME",
		"SECTION_DESCRIPTION" => "DESCRIPTION",
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
		"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"]
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);?>
</div>
<?if($bShowFilter):
$arParams["LINE_ELEMENT_COUNT"] = 3;?>
<hr class="visible-xs i-size-L">
<div class="col-sm-9 bj-catalogue-group">
<?else:
$arParams["LINE_ELEMENT_COUNT"] = 4;?>
<div class="bj-catalogue-group">
<?endif;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"bejetstore", 
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => "arrFILTER",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => "N",
		"SHOW_ALL_WO_SECTION" => "Y"
	),
	$component
);?></div>
</div>
<?endif;?>