<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
?><h1><?= $APPLICATION->ShowTitle(false); ?></h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:system.auth.forgotpasswd", 
	".default", 
	array(
		"PRMEDIA_FORGOT_PASS_PATH_TO_AUTH" => SITE_DIR . "/auth/",
		"PRMEDIA_FORGOT_PASS_REDIRECT_URL" => ""
	),
	false
); ?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>