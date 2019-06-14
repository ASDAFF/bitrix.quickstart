<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("mailtrig.events"))
	die("Module not installed");

$API = new CMailTrigAPI();

$API->getRequest();

$API->showResult();
?>