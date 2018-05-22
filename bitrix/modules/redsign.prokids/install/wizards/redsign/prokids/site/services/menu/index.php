<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule('fileman');

$arMenuTypes['tpanel'] = GetMessage('WIZ_MENU_tpanel');
$arMenuTypes['catalog'] = GetMessage('WIZ_MENU_catalog');
$arMenuTypes['footer'] = GetMessage('WIZ_MENU_footer');
$arMenuTypes['footercatalog'] = GetMessage('WIZ_MENU_footercatalog');

SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);