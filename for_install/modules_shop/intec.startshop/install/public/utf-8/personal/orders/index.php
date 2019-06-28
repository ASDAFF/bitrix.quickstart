<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Личный кабинет");
?>
<?if ($USER->IsAuthorized()):?>
	<?$APPLICATION->IncludeComponent(
			"bitrix:menu",
			"startshop.top.1",
			array(
					"ROOT_MENU_TYPE" => "personal",
					"MENU_CACHE_TYPE" => "N",
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "1",
					"CHILD_MENU_TYPE" => "personal",
					"USE_EXT" => "N",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
			),
			false
	);?>
<?endif;?>
<?$APPLICATION->IncludeComponent(
		"intec:startshop.orders",
		".default",
		array(
				"CURRENCY" => "",
				"COMPONENT_TEMPLATE" => ".default",
				"USE_ADAPTABILITY" => "Y",
				"REQUEST_VARIABLE_ORDER_ID" => "ORDER_ID",
				"404_SET_STATUS" => "Y",
				"404_REDIRECT" => "Y",
				"404_PAGE" => "/404.php",
				"TITLE_ORDERS_LIST" => "Заказы",
				"TITLE_ORDERS_DETAIL" => "Заказ",
				"URL_AUTHORIZE" => "#PERSONAL_PATH#"
		),
		false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
