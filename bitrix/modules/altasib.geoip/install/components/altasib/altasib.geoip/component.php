<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!IsModuleInstalled("altasib.geoip"))
{
	ShowError(GetMessage("ALTASIB_GEOIP_MODULE_NOT_INSTALLED"));
	return;
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;
if($arParams["CACHE_TYPE"] == "N" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"))
	$arParams["CACHE_TIME"] = 0;

	if(!CModule::IncludeModule("altasib.geoip"))
	{
		$obCache->AbortDataCache();
		ShowError(GetMessage("ALTASIB_GEOIP_MODULE_NOT_INSTALLED"));
		return;
	}

    $arResult = ALX_GeoIP::GetAddr();

$this->IncludeComponentTemplate();
?>