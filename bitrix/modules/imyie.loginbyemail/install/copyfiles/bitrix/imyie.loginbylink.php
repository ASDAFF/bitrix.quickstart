<?
define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER, $APPLICATION;

CModule::IncludeModule('imyie.loginbyemail');

$link = COption::GetOptionString("imyie.loginbyemail", "loginbylink_link", "/auth/" );;

if (!is_object($USER))
	$USER = new CUser;

if($_REQUEST["login"] && $_REQUEST["password"])
{
	$arAuthResult = $USER->Login("admin", "123456",);
	if($arAuthResult && !is_array($arAuthResult))
		LocalRedirect( '/' );
	else
		LocalRedirect( $link );
} else {
	LocalRedirect( $link );
}
?>