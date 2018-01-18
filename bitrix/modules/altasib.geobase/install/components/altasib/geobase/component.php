<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;
if($arParams["CACHE_TYPE"] == "N" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N"))
	$arParams["CACHE_TIME"] = 0;

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0'){
	ShowError(GetMessage("ALTASIB_GEOBASE_MODULE_NOT_FOUND"));
	return false;
} elseif ($incMod == '3'){
	ShowError(GetMessage("ALTASIB_GEOBASE_DEMO_EXPIRED"));
	return false;
}

if(!empty($arParams["SOURCE"]))
{
	if($arParams["SOURCE"] == 'autodetect')
		$arResult = CAltasibGeoBase::GetAddres();
	elseif($arParams["SOURCE"] == 'kladr_auto')
		$arResult = CAltasibGeoBase::GetCodeByAddr();
	elseif($arParams["SOURCE"] == 'kladr_set')
		$arResult = CAltasibGeoBase::GetDataKladr();
}
else
	$arResult = CAltasibGeoBase::GetAddres();
	
$arResult["REGION_DISABLE"] = COption::GetOptionString("altasib.geobase", 'region_disable', 'N');

$this->IncludeComponentTemplate();?>