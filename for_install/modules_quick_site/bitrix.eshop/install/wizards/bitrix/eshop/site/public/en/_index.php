<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("\"Best Supplies LLC\" e-Store");
?>
<?
$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "6",
	"PROPERTY_CODE" => array(
		0 => "MINIMUM_PRICE",
		1 => "MAXIMUM_PRICE",
	),
	"FLAG_PROPERTY_CODE" => "SALELEADER",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_top1",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => COption::GetOptionString("eshop", "catalogCompare", "Y", SITE_ID) == "Y" ? "Y" : "N",
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "COLOR",
		1 => "WIDTH",
		2 => "",
	),
	"OFFERS_CART_PROPERTIES" => array(
		0 => "COLOR",
		1 => "WIDTH",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"DISPLAY_IMG_WIDTH" => "130",
	"DISPLAY_IMG_HEIGHT" => "130",
	"SHARPEN" => "30",
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);
?>
<?
$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"PROPERTY_CODE" => array(
		0 => "MINIMUM_PRICE",
		1 => "MAXIMUM_PRICE",
	),
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "9",
	"FLAG_PROPERTY_CODE" => "NEWPRODUCT",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id_top2",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => COption::GetOptionString("eshop", "catalogCompare", "Y", SITE_ID) == "Y" ? "Y" : "N",
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "",
	),
	"OFFERS_PROPERTY_CODE" => array(
		0 => "COLOR",
		1 => "WIDTH",
		2 => "",
	),
	"OFFERS_CART_PROPERTIES" => array(
		0 => "COLOR",
		1 => "WIDTH",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"DISPLAY_IMG_WIDTH" => "130",
	"DISPLAY_IMG_HEIGHT" => "130",
	"SHARPEN" => "30",
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>