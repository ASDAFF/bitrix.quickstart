<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
$cpt = new CCaptcha();
$cpt->Delete( $_REQUEST['captcha_sid'] );
echo htmlspecialchars($APPLICATION->CaptchaGetCode());?>