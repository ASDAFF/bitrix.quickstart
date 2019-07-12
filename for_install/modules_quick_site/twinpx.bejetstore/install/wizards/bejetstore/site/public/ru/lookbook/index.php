<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("LookBook");
?>
<?$APPLICATION->IncludeComponent(
	"bejetstore:looks.section.list", 
	".default", 
	array(
		"VIEW_MODE" => "TEXT",
		"SHOW_PARENT_NAME" => "Y",
		"IBLOCK_TYPE" => "looks",
		"IBLOCK_ID" => BEJET_SELLER_LOOKS,//#LOOCS_BLOCK#
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"PAGE_ELEMENTS_COUNT" => "2",
		"LOOKS_COUNT" => "5",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRODUCTS_BLOCK" => 4,
		"CURRENCY_ID" => "RUB"
	),
	false
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.bigdata.products", 
	"bejet.v2", 
	array(
		"RCM_TYPE" => "personal",
		"ID" => "",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"HIDE_NOT_AVAILABLE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"PAGE_ELEMENT_COUNT" => "4",
		"LINE_ELEMENT_COUNT" => "3",
		"TEMPLATE_THEME" => "blue",
		"DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "Y",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"USE_PRODUCT_QUANTITY" => "Y",
		"SHOW_PRODUCTS_2" => "Y",
		"CURRENCY_ID" => "RUB",
		"PROPERTY_CODE_2" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_2" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO",
		"LABEL_PROP_2" => "NEWPRODUCT",
		"PROPERTY_CODE_3" => array(
			0 => "SIZES_CLOTHES",
			1 => "MORE_PHOTO",
			2 => "",
		),
		"CART_PROPERTIES_3" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "",
		),
		"ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_3" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
		),
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"COMPONENT_TEMPLATE" => "bejet.v2",
		"SHOW_FROM_SECTION" => "N",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_ID" => "",
		"SECTION_ELEMENT_CODE" => "",
		"DEPTH" => "",
		"SHOW_PRODUCTS_#BEJET_SELLER_CLOTHES#" => "Y",
		"PROPERTY_CODE_#BEJET_SELLER_CLOTHES#" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_#BEJET_SELLER_CLOTHES#" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_#BEJET_SELLER_CLOTHES#" => "MORE_PHOTO",
		"LABEL_PROP_#BEJET_SELLER_CLOTHES#" => "-",
		"PROPERTY_CODE_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
			0 => "CML2_LINK",
			1 => "ARTNUMBER",
			2 => "COLOR_REF",
			3 => "SIZES_SHOES",
			4 => "SIZES_CLOTHES",
			5 => "MORE_PHOTO",
			6 => "",
		),
		"CART_PROPERTIES_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "",
		),
		"ADDITIONAL_PICT_PROP_#BEJET_SELLER_OFFERS_CLOTHES#" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>