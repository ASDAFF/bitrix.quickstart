<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!IsModuleInstalled("redsign.location"))
{
	ShowError(GetMessage("REDSIGN_LOCATION_MODULE_NOT_INSTALLED"));
	return;
}

if($arParams["RSLOCATION_INCLUDE_JQUERY"]=="Y")
	CJSCore::Init(array("jquery"));

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;
if($arParams["CACHE_TYPE"] == "N" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"))
	$arParams["CACHE_TIME"] = 0;

	if(!CModule::IncludeModule("redsign.location"))
	{
		$obCache->AbortDataCache();
		ShowError(GetMessage("REDSIGN_LOCATION_MODULE_NOT_INSTALLED"));
		return;
	}
	
	$arResult["CITY"] = CRS_Location::GetCityName();

$this->IncludeComponentTemplate();
?>