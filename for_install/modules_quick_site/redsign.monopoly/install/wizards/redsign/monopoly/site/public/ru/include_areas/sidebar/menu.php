<?if($arParams['HEAD_TYPE']=='type3'):?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"sidebar_menu", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"CHILD_MENU_TYPE" => "topsub",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "360000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "4",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"CATALOG_PATH" => "#SITE_DIR#catalog/",
		"MAX_ITEM" => "9",
		"IBLOCK_ID" => "",
		"PRICE_CODE" => "",
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CONVERT_CURRENCY" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quan",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
	),
	false
);?>

<?else:?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"sidebar_menu", 
	array(
		"COMPONENT_TEMPLATE" => "sidebar_menu",
		"ROOT_MENU_TYPE" => "topsub",
		"CHILD_MENU_TYPE" => "topsub",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "360000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "4",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"CATALOG_PATH" => "#SITE_DIR#catalog/",
		"MAX_ITEM" => "9",
		"IBLOCK_ID" => "",
		"PRICE_CODE" => "",
		"PRICE_VAT_INCLUDE" => "N",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CONVERT_CURRENCY" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quan",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
	),
	false
);?>

<?endif;?>