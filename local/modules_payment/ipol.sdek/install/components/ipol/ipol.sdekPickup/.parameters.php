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
		/* "MODE" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_MODE'),
			"TYPE"     => "LIST",
			"VALUES"   => array('both' => GetMessage('IPOLSDEK_FRNT_BOTHPROFS'), 'PVZ' => GetMessage('IPOLSDEK_PROF_PICKUP'), 'POSTOMAT' => GetMessage('IPOLSDEK_PROF_POSTOMAT')),
			"DEFAULT" => 'both'
		), */
		"NOMAPS" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_NOMAPS'),
			"TYPE"     => "CHECKBOX",
		),
		"CNT_DELIV" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_CNT_DELIV'),
			"TYPE"     => "CHECKBOX",
		),
		"CNT_BASKET" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_CNT_BASKET'),
			"TYPE"     => "CHECKBOX",
		),
		"FORBIDDEN" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_FORBIDDEN'),
			"TYPE"     => "LIST",
			"VALUES"   => array(0 => '', 'pickup' => GetMessage('IPOLSDEK_PROF_PICKUP'), 'courier' => GetMessage('IPOLSDEK_PROF_COURIER'), 'inpost' => GetMessage('IPOLSDEK_PROF_POSTOMAT')),
			"SIZE"     => 3,
			"MULTIPLE" => "Y",
		),
		"COUNTRIES" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_COUNTRIES'),
			"TYPE"     => "LIST",
			"VALUES"   => $arCountries,
			"SIZE"     => count($arCountries),
			"MULTIPLE" => "Y",
		),
		"CITIES" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage('IPOLSDEK_COMPOPT_CITIES'),
			"TYPE"     => "LIST",
			"VALUES"   => $arCities,
			"SIZE"     => count($arCities),
			"MULTIPLE" => "Y",
		)
	),
);
?>