<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Забыли пароль");?>
<?
	if(!$USER->IsAuthorized()){$APPLICATION->IncludeComponent( "bitrix:system.auth.forgotpasswd", "main", false );}
	elseif( !empty( $_REQUEST["backurl"] ) ){ LocalRedirect( $_REQUEST["backurl"] );}
	else{ LocalRedirect(SITE_DIR.'personal/');}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>