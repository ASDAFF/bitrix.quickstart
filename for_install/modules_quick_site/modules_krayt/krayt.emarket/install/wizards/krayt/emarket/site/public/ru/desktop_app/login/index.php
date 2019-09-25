<?
if ($_SERVER["REQUEST_METHOD"]=="OPTIONS")
{
	//header('Access-Control-Allow-Headers: *');
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	header('Access-Control-Max-Age: 60');
	header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');
	die('');	
}

define("ADMIN_SECTION",false);
require($_SERVER["DOCUMENT_ROOT"]."/desktop_app/headers.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!IsModuleInstalled('bitrix24'))
	header('Access-Control-Allow-Origin: *');

if ($_POST['action'] != 'login')
{
	CHTTP::SetStatus("403 Forbidden");
	die();
}

$result = $USER->Login($_POST['login'], $_POST['password']);
if (!is_bool($result) || $result === false || !$USER->IsAuthorized())
{
	if (IsModuleInstalled('bitrix24'))
		header('Access-Control-Allow-Origin: *');

	if ($APPLICATION->NeedCAPTHAForLogin($_POST['login']))
	{
		$CAPTCHA_CODE = $APPLICATION->CaptchaGetCode();
		echo "{success: false, captchaCode: '".$CAPTCHA_CODE."'}";
	}
	else
	{
		echo "{success: false}";
	}

	CHTTP::SetStatus("401 Unauthorized");
	die();
}

echo "{success: true, sessionId: '".CUtil::JSEscape(session_id())."'}";
die();
?>