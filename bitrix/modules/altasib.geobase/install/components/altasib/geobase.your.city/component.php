<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0' || $incMod == '3')
	return false;

$arResult = CAltasibGeoBase::GetDataKladr();
$arResult["auto"] = CAltasibGeoBase::GetAddres();

$arResult["REGION_DISABLE"] = COption::GetOptionString("altasib.geobase", 'region_disable', 'N');
$arResult["POPUP_BACK"] = COption::GetOptionString("altasib.geobase", "popup_back", "Y");

/////Mobile detect/////

$checkType = '';
$checkType = CAltasibGeoBase::DeviceIdentification();

///////////////////////

if ($checkType == 'mobile' || $checkType == 'pda') {
	$this->IncludeComponentTemplate("mobile");
}
else
	$this->IncludeComponentTemplate();
?>