<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($_REQUEST['ajax_compare'])
{
	die();
}
else
{
	$compare = ob_get_contents();
	ob_end_clean();
	$APPLICATION->SetPageProperty('CATALOG_COMPARE_LIST', $compare);
}
?>