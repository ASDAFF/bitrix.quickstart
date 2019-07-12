  <?$APPLICATION->IncludeComponent(
	  "bitrix:menu", 
	  "top_line", 
	  array(
      "ROOT_MENU_TYPE" => "top",
		  "CHILD_MENU_TYPE" => "top",
		  "MENU_CACHE_TYPE" => "A",
		  "MENU_CACHE_TIME" => "3600",
		  "MENU_CACHE_USE_GROUPS" => "Y",
		  "MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
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
		"COMPONENT_TEMPLATE" => "top_line"
	),
	false
  );?>