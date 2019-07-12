<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Подтверждение регистрации	");
	
	if(!$USER->IsAuthorized())
	{?>
		<?$APPLICATION->IncludeComponent("bitrix:system.auth.confirmation","main",Array(
				"USER_ID" => "confirm_user_id", 
				"CONFIRM_CODE" => "confirm_code", 
				"LOGIN" => "login" 
			)
		);?>
	<?} 
	else 
	{ 
		LocalRedirect(SITE_DIR.'personal/');
	}
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>