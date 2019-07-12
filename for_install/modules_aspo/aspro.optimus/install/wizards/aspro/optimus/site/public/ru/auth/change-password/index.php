<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Изменение пароля");
	
	if(!$USER->IsAuthorized())
	{
		$APPLICATION->IncludeComponent( "bitrix:system.auth.changepasswd","main",false );
	} 
	else 
	{ 
		LocalRedirect(SITE_DIR.'personal/');
	}
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>