<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule("sale"))
    return;

if(!cmodule::includeModule('ipol.sdek'))
    return false;

$arCities = array();
$arList = CDeliverySDEK::getListFile();
foreach($arList as $prof => $cities)
    foreach($cities as $city => $crap)
        if(!array_key_exists($city,$arCities))
            $arCities[$city]=$city;

$optCountries = CDeliverySDEK::getActiveCountries();
$arCountries = array();
foreach($optCountries as $countryCode)
    $arCountries[$countryCode] = GetMessage('IPOLSDEK_SYNCTY_'.$countryCode);

$arComponentParameters = array(
    "PARAMETERS" => array(
        "COUNTRIES" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage('IPOLSDEK_COMPOPT_COUNTRIES'),
            "TYPE" => "LIST",
            "VALUES" => $arCountries,
            "SIZE" => count($arCountries),
            "MULTIPLE" => "Y",
        ),
        "CITIES" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage('IPOLSDEK_COMPOPT_CITIES'),
            "TYPE" => "LIST",
            "VALUES" => $arCities,
            "SIZE" => min(count($arCities), 30),
            "MULTIPLE" => "Y",
        )
    ),
);