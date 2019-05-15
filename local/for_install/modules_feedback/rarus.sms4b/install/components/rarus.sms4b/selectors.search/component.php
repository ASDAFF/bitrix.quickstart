<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$componentPage = "";

if ($_GET['structure_department'])
{
	$_REQUEST[$arParams['FILTER_NAME'].'_UF_DEPARTMENT'] = $GLOBALS[$arParams['FILTER_NAME'].'_UF_DEPARTMENT'] = $_GET['structure_department'];
	$_REQUEST['set_filter_'.$arParams['FILTER_NAME']] = 'Y';
}

$arParams['LIST_URL'] = $APPLICATION->GetCurPage();
if (!$arParams['DETAIL_URL']) $arParams['DETAIL_URL'] = $arParams['LIST_URL'].'?ID=#USER_ID#';

if (!$arParams['FILTER_NAME'])
	$arParams['FILTER_NAME'] = 'USER_FILTER';

$this->IncludeComponentTemplate();
?>