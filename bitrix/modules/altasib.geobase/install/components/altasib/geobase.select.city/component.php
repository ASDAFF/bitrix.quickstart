<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0' || $incMod == '3')
	return false;

$arParams["CACHE_TIME"] = 0;

$arResult["POPUP_BACK"] = COption::GetOptionString("altasib.geobase", 'popup_back', 'Y');
// Cookies
$arDataC = (array)json_decode($APPLICATION->get_cookie("ALTASIB_GEOBASE_CODE"));
// selected cities codes
$arSelCodes = array();

$arResult['AUTODETECT_ENABLE'] = COption::GetOptionString("altasib.geobase", "autodetect_enable", "Y");

if(!empty($_SESSION["ALTASIB_GEOBASE_CODE"]) || !empty($arDataC)){
	$arResult['USER_CHOICE'] = CAltasibGeoBase::GetDataKladr();

	$arSelCodes[] = $arResult['USER_CHOICE']['CODE'];
} else{
	if($arResult['AUTODETECT_ENABLE'] == "Y")
	{
		// On-line auto detection
		$arDataO = CAltasibGeoBase::GetCodeByAddr();

		if($arDataO["CITY"]["NAME"] != GetMessage('ALTASIB_GEOBASE_KLADR_CITY_NAME')){
			$arResult['AUTODETECT'] = $arDataO;
			$arSelCodes[] = $arDataO["CODE"];
		}
	}
}

$arCitySel = array();

$arCITY = CAltasibGeoBaseSelected::GetMoreCacheCities();

foreach($arCITY as $arCities){
	if(empty($arCities["R_FNAME"])) {
		$arRG = CAltasibGeoBase::GetRegionLang($arCities["CTR_CODE"], $arCities['R_ID']);
		if(!empty($arRG['region_name'])){
			if (LANG_CHARSET == 'windows-1251')
				$arRG['region_name'] = iconv("UTF-8", "windows-1251", $arRG['region_name']);

			$arCities["R_FNAME"] = $arRG['region_name'];
		}
	}

	if($arResult['USER_CHOICE']["CODE"] != $arCities['C_CODE']
		&& $arResult['AUTODETECT']["CODE"] != $arCities['C_CODE']
		&& $arResult['USER_CHOICE']["C_CODE"] != $arCities['C_CODE']
		&& $arResult['USER_CHOICE']["CITY_RU"] != $arCities['C_NAME']){
			$arCitySel[] = $arCities;
	}
	$arSelCodes[] = $arCities['C_CODE'];
}

$arResult['SELECTED'] = $arCitySel;

$arResult['SEL_CODES'] = $arSelCodes;

if($arResult['AUTODETECT_ENABLE'] == "Y")
	$arResult["auto"] = CAltasibGeoBase::GetAddres();

$arResult['ONLY_SELECT'] = COption::GetOptionString("altasib.geobase", "only_select_cities", "N");

$arCurr = CAltasibGeoBase::GetCurrency($use_func=true, $reload=false);

if(CAltasibGeoBase::CheckCountry($arCurr["country"]))
	$arResult['RU_ENABLE'] = "Y";
else
	$arResult['RU_ENABLE'] = "N";

/////Mobile Detect//////

$checkType = '';
$checkType = CAltasibGeoBase::DeviceIdentification();

/**
 * @var $this CBitrixComponent
 */

if ($checkType == 'mobile' || $checkType == 'pda') {
	$this->IncludeComponentTemplate("mobile");
}
else
	$this->IncludeComponentTemplate();

////////////////////////
?>