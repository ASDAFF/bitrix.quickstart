<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(dirname(__FILE__)."/../include.php");
include(dirname(__FILE__)."/../constants.php");
IncludeModuleLangFile(dirname(__FILE__)."/status.php");
$arRights = $obModule->GetGroupRight();
$arReturn = Array();
if ($arRights > "D") 
{
	$arFilter = Array("ID"=>$_REQUEST["ID"]);
	$obModule->RefreshOrder($arFilter["ID"]);
	$lAdmin = $obModule->GetOrderTable($arFilter);
	$lAdmin->DisplayList();
}
?>