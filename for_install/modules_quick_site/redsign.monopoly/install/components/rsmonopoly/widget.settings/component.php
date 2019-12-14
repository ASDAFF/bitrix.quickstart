<?
if(!defined("B_PROLOG_INCLUDED") && isset($_REQUEST["AJAX_CALL"]) && $_REQUEST["AJAX_CALL"]=="Y")
{
	define('PUBLIC_AJAX_MODE', true);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	global $APPLICATION, $USER;

	if(!$USER->IsAdmin()) {
		die();
	}

	if(CModule::IncludeModule('redsign.monopoly')) {
		RSMonopoly::saveSettings();
	}

	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION, $USER;

if(!$USER->IsAdmin()) {
	return;
}

$gencolor = COption::GetOptionString('redsign.monopoly', 'gencolor', '0084c9');
list($rr,$gg,$bb) = sscanf($gencolor, '%2x%2x%2x');
$arResult['SETTINGS']['GEN_COLOR']['HEX'] = $gencolor;
$arResult['SETTINGS']['GEN_COLOR']['RGB']['R'] = $rr;
$arResult['SETTINGS']['GEN_COLOR']['RGB']['G'] = $gg;
$arResult['SETTINGS']['GEN_COLOR']['RGB']['B'] = $bb;
$textColorMenu = COption::GetOptionString('redsign.monopoly', 'textColorMenu', 'ffffff');
list($rr,$gg,$bb) = sscanf($textColorMenu, '%2x%2x%2x');
$arResult['SETTINGS']['TEXT_MENU_COLOR']['HEX'] = $textColorMenu;
$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['R'] = $rr;
$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['G'] = $gg;
$arResult['SETTINGS']['TEXT_MENU_COLOR']['RGB']['B'] = $bb;
$arResult['SETTINGS']['headType'] = COption::GetOptionString('redsign.monopoly', 'headType', 'type1');
$arResult['SETTINGS']['headStyle'] = COption::GetOptionString('redsign.monopoly', 'headStyle', 'style1');
$arResult['SETTINGS']['blackMode'] = COption::GetOptionString('redsign.monopoly', 'blackMode', 'N');

$arResult['MAIN_SETTINGS']['MSFichi'] = COption::GetOptionString('redsign.monopoly', 'MSFichi', 'Y');
$arResult['MAIN_SETTINGS']['MSCatalog'] = COption::GetOptionString('redsign.monopoly', 'MSCatalog', 'Y');
$arResult['MAIN_SETTINGS']['MSService'] = COption::GetOptionString('redsign.monopoly', 'MSService', 'Y');
$arResult['MAIN_SETTINGS']['MSAboutAndReviews'] = COption::GetOptionString('redsign.monopoly', 'MSAboutAndReviews', 'Y');
$arResult['MAIN_SETTINGS']['MSNews'] = COption::GetOptionString('redsign.monopoly', 'MSNews', 'Y');
$arResult['MAIN_SETTINGS']['MSPartners'] = COption::GetOptionString('redsign.monopoly', 'MSPartners', 'Y');
$arResult['MAIN_SETTINGS']['MSGallery'] = COption::GetOptionString('redsign.monopoly', 'MSGallery', 'Y');
$arResult['MAIN_SETTINGS']['MSSmallBanners'] = COption::GetOptionString('redsign.monopoly', 'MSSmallBanners', 'N');

$arResult['SETTINGS']['filterType'] = COption::GetOptionString('redsign.monopoly', 'filterType', 'ftype1');
$arResult['SETTINGS']['sidebarPos'] = COption::GetOptionString('redsign.monopoly', 'sidebarPos', 'pos1');

$this->IncludeComponentTemplate();