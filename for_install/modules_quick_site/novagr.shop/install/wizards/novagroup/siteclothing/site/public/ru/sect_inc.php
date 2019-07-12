<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/*$APPLICATION->IncludeComponent("bitrix:eshop.slider", ".default", array(
		"IBLOCK_TYPE_ID" => "catalog",
		"IBLOCK_ID" => array(
			0 => "3",
		),
		"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
		"PROPERTY_CODE" => array(
			0 => "MINIMUM_PRICE",
			1 => "MAXIMUM_PRICE",
		),
		"RAND_COUNT" => "6",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "180",
		"CACHE_GROUPS" => "Y",
		"PARENT_SECTION" => "",
		"PRICE_CODE" => array(
			0 => "BASE",
		)
	),
	false
);    */
$APPLICATION->IncludeComponent("novagr.shop:eshop.catalog.top", "slider", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => "3",
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "6",
	"PROPERTY_CODE" => array(
		0 => "MINIMUM_PRICE",
		1 => "MAXIMUM_PRICE",
	),
	"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "Интернет магазин",
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
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);
?>