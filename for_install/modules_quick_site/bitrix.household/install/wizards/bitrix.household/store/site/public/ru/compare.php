<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?echo count($_SESSION["CATALOG_COMPARE_LIST"][$_GET["data"]]["ITEMS"]);?>