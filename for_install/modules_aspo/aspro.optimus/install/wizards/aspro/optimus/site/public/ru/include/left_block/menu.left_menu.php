<?if(!COptimus::IsMainPage()):?>
	<?if(COptimus::IsCatalogPage()):?>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "left_front_catalog", array(
			"ROOT_MENU_TYPE" => "left",
			"MENU_CACHE_TYPE" => "A",
			"MENU_CACHE_TIME" => "3600000",
			"MENU_CACHE_USE_GROUPS" => "N",
			"MENU_CACHE_GET_VARS" => "",
			"MAX_LEVEL" => \Bitrix\Main\Config\Option::get("aspro.optimus", "MAX_DEPTH_MENU", 2),
			"CHILD_MENU_TYPE" => "left",
			"USE_EXT" => "Y",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N" ),
			false, array( "ACTIVE_COMPONENT" => "Y" )
		);?>
	<?endif;?>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
		"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => "",
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N" ),
		false, array( "ACTIVE_COMPONENT" => "Y" )
	);?>
<?else:?>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "left_front_catalog", array(
		"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => "",
		"MAX_LEVEL" => \Bitrix\Main\Config\Option::get("aspro.optimus", "MAX_DEPTH_MENU", 2),
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N" ),
		false, array( "ACTIVE_COMPONENT" => "Y" )
	);?>								
<?endif;?>