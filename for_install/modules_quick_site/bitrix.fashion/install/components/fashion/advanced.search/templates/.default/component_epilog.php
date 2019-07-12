<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
require_once $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/include_areas/popup.php";
global $APPLICATION;

$APPLICATION->SetTitle('Результаты поиска по запросу &laquo;'.$_REQUEST['q'].'&raquo;');
?>