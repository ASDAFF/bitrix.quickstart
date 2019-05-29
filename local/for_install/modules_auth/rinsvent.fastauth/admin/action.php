<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule("rinsvent.fastauth"))
	die();

CUtil::JSPostUnescape();

$action = htmlspecialcharsBx($_REQUEST['action']);
$request = $_REQUEST['request'];

$arResult = array('status'=>'');

echo CUtil::PhpToJSObject($arResult);
?>