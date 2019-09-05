<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arCountries = \GetCountryArray();

foreach ($arResult["USERS"] as &$arUser) {
	// Получаем название страны по коду
	if( !empty($arUser['PERSONAL_COUNTRY']) ){
		$countryRefId = array_search($arUser['PERSONAL_COUNTRY'], $arCountries['reference_id']);
		$arUser['PERSONAL_COUNTRY'] = $arCountries['reference'][$countryRefId];
	}
}
unset($arUser);

if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER']))
{
	$cp =& $this->__component;
	if (strlen($cp->getCachePath()))
	{
		$GLOBALS['CACHE_MANAGER']->RegisterTag('users_list');
	}
}