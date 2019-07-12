<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Регистрация");

	if(!$USER->IsAuthorized())
	{?>
	
	<?
		$APPLICATION->IncludeComponent(
			"bitrix:main.register",
			"main",
			Array(
				"USER_PROPERTY_NAME" => "",
				"SHOW_FIELDS" => array("NAME","EMAIL","PERSONAL_PHONE"),
				"REQUIRED_FIELDS" => array("NAME","EMAIL", "PERSONAL_PHONE"),
				"AUTH" => "Y",
				"USE_BACKURL" => "Y",
				"SUCCESS_PAGE" => "",
				"SET_TITLE" => "N",
				"USER_PROPERTY" => array()
			)
		);
		$_REQUEST["REGISTER[LOGIN]"] = $_REQUEST["REGISTER[EMAIL]"];
	} elseif(!empty( $_REQUEST["backurl"] )) {LocalRedirect( $_REQUEST["backurl"] );} else { LocalRedirect(SITE_DIR.'personal/');}

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>