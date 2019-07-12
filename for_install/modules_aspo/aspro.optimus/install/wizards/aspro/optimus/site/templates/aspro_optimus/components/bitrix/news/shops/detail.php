<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?$APPLICATION->ShowViewContent('map_content');?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"shops",
	Array(
		"IBLOCK_CATALOG_TYPE" => $arParams["IBLOCK_CATALOG_TYPE"],
		"IBLOCK_CATALOG_ID" => $arParams["IBLOCK_CATALOG_ID"],
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_TITLE" => 'N',
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ADD_ELEMENT_CHAIN" => 'N',
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => 'A', // for map!
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" 			=> $arParams["USE_SHARE"],
		"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"CATALOG_FILTER_NAME" => $arParams["CATALOG_FILTER_NAME"],
		"IBLOCK_CATALOG_TYPE" => $arParams["IBLOCK_CATALOG_TYPE"],
		"CATALOG_IBLOCK_ID1" => $arParams["CATALOG_IBLOCK_ID1"],
		"CATALOG_IBLOCK_ID2" => $arParams["CATALOG_IBLOCK_ID2"],
		"CATALOG_IBLOCK_ID3"  => $arParams["CATALOG_IBLOCK_ID3"],
		"SHOW_BACK_LINK"  => $arParams["SHOW_BACK_LINK"],
		"GALLERY_PROPERTY" => $arParams["GALLERY_PROPERTY"],
		"SHOW_GALLERY"  => $arParams["SHOW_GALLERY"],
		"LINKED_PRODUCTS_PROPERTY"  => $arParams["LINKED_PRODUCTS_PROPERTY"],
		"SHOW_LINKED_PRODUCTS" => $arParams["SHOW_LINKED_PRODUCTS"],
		"GOOGLE_API_KEY" => $arParams["GOOGLE_API_KEY"],
	),
	$component
);?>

<?
if ($arParams['SET_TITLE'] == 'Y') {
	$APPLICATION->SetTitle($_SESSION['SHOP_TITLE']);
	$APPLICATION->SetPageProperty("title", $_SESSION['SHOP_TITLE']);
}
if ($arParams['ADD_ELEMENT_CHAIN'] == 'Y') {
	$APPLICATION->AddChainItem($_SESSION['SHOP_TITLE'], "");
}
?>