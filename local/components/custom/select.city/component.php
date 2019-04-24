<?
/**
 * @var array $arParams
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!cmodule::includeModule('ipol.sdek'))
    return false;

$arResult = [];
$allCities = false;
if (!is_array($arParams['CITIES']))
    $arParams['CITIES'] = array();
if (count($arParams['CITIES']) == 0)
    $allCities = true;

if ($_SESSION['IPOLSDEK_city'] && !count($arParams['CITIES']) == 1)
    $arResult['city'] = $_SESSION['IPOLSDEK_city'];
elseif (count($arParams['CITIES']) == 1)
    $arResult['city'] = $arParams['CITIES'][0];
 elseif (!$arParams['COUNTRIES'] || in_array('rus', $arParams['COUNTRIES']))
    $arResult['city'] = "Москва";

$countries = CDeliverySDEK::getActiveCountries();
$arExistedCities = array();
$cities = CDeliverySDEK::getCountryCities($countries);
foreach ($cities as $city) {
    if (!array_key_exists($city['NAME'], $arExistedCities))
        $arExistedCities[$city['NAME']] = $city['COUNTRY_NAME'];
    elseif (is_array($arExistedCities[$city['NAME']]))
        $arExistedCities[$city['NAME']][] = $city['COUNTRY_NAME'];
    elseif ($arExistedCities[$city['NAME']] != $city['COUNTRY_NAME'])
        $arExistedCities[$city['NAME']] = array($arExistedCities[$city['NAME']], $city['COUNTRY_NAME']);
    if (!array_key_exists('city', $arResult) || !$arResult['city'])
        $arResult['city'] = $city['NAME'];
}

switch ($arResult['city']) {
    case "Москва" :
        $arResult["phoneBegin"] = "+7(495)";
        $arResult["phoneEnd"] = "967-12-32";
        break;
    case "Санкт-Петербург" :
        $arResult["phoneBegin"] = "+7(812)";
        $arResult["phoneEnd"] = "925-37-39";
        break;
    default :
        $arResult["phoneBegin"] = "8(800)";
        $arResult["phoneEnd"] = "700-04-30";
}
$arList = [];
if (!(count($arParams['CITIES']) == 1)) {
    $arList = CDeliverySDEK::getListFile();
    $arList['PVZ'] = CDeliverySDEK::weightPVZ((CDeliverySDEK::$orderWeight) ? false : COption::GetOptionString(CDeliverySDEK::$MODULE_ID, 'weightD', 1000), $arList['PVZ']);
}
if (count($arList)) {
    foreach ($arList as $mode => $arCities)
        foreach ($arCities as $city => $arPVZ) {
            if (array_key_exists($city, $arExistedCities) && (!$arParams['COUNTRIES'] || is_array($countrySwitcher[$arExistedCities[$city]]) || in_array($countrySwitcher[$arExistedCities[$city]], $arParams['COUNTRIES']))) {
                if ($allCities || in_array($city, $arParams['CITIES'])) {
                    $arResult[$mode][$city] = $arPVZ;
                    if (!in_array($city, $arResult['Regions'])) {
                        $country = (!is_array($arExistedCities[$city])) ? $arExistedCities[$city] : ((in_array(GetMessage('IPOLSDEK_SYNCTY_rus'), $arExistedCities[$city])) ? GetMessage('IPOLSDEK_SYNCTY_rus') : $arExistedCities[$city][0]);
                        $arResult['Regions'][] = $city;
                        $arResult['Subjects'][$city] = $country;
                    }
                }
            }
        }
}

usort($arResult['Regions'], function ($a, $b) {
    if ($a == $b) return 0;
    return $a > $b ? 1 : -1;
});
$this->IncludeComponentTemplate();
