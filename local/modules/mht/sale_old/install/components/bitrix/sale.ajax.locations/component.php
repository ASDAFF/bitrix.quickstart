<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Sale\SalesZone;

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
CUtil::InitJSCore(array('core', 'ajax'));

$arParams["AJAX_CALL"] = $arParams["AJAX_CALL"] == "Y" ? "Y" : "N";
$arParams["COUNTRY"] = intval($arParams["COUNTRY"]);
$arParams["REGION"] = intval($arParams["REGION"]);
$arParams["LOCATION_VALUE"] = intval($arParams["LOCATION_VALUE"]);
$arParams["ALLOW_EMPTY_CITY"] = $arParams["ALLOW_EMPTY_CITY"] == "N" ? "N" : "Y";
$arParams["ZIPCODE"] = IntVal($arParams["ZIPCODE"]);
$arParams["SHOW_QUICK_CHOOSE"] = $arParams["SHOW_QUICK_CHOOSE"] == "N" ? "N" : "Y";

if (strlen($arParams["SITE_ID"]) <= 0)
	$arParams["SITE_ID"] = SITE_ID;


if ($arParams["ZIPCODE"] > 0)
{
	$arZip = CSaleLocation::GetByZIP($arParams["ZIPCODE"]);
	if (is_array($arZip) && count($arZip) > 1)
	{
		$arParams["LOCATION_VALUE"] = IntVal($arZip["ID"]);
	}
}


// take into account sales zone
$arResult["SINGLE_CITY"] = "N";
$citiesIds = SalesZone::getCitiesIds($arParams["SITE_ID"]);

if(count($citiesIds) == 1 && strlen($citiesIds[0]) > 0)
{
	$rsLocationsList = CSaleLocation::GetList(
		array(),
		array("CITY_ID" => $citiesIds[0]),
		false,
		false,
		array("ID")
	);

	if ($arLoc = $rsLocationsList->GetNext())
	{
		$arParams["LOCATION_VALUE"] = $arLoc["ID"];
		$arResult["SINGLE_CITY"] = "Y";
	}
}

if(!SalesZone::checkLocationId($arParams["LOCATION_VALUE"], $arParams["SITE_ID"]))
	$arParams["LOCATION_VALUE"] = 0;

if ($arParams["LOCATION_VALUE"] > 0)
{
	if ($arLocation = CSaleLocation::GetByID($arParams["LOCATION_VALUE"]))
	{
		$arParams["COUNTRY"] = $arLocation["COUNTRY_ID"];
		$arParams["REGION"] = $arLocation["REGION_ID"];
		$arParams["CITY"] = $arLocation["CITY_ID"];
	}
}

//check in location city
$arResult["EMPTY_CITY"] = "N";
$arCityFilter = array("!CITY_ID" => "NULL", ">CITY_ID" => "0");
if ($arParams["COUNTRY"] > 0)
	$arCityFilter["COUNTRY_ID"] = $arParams["COUNTRY"];
$rsLocCount = CSaleLocation::GetList(array(), $arCityFilter, false, false, array("ID"));
if (!$rsLocCount->Fetch())
	$arResult["EMPTY_CITY"] = "Y";

//check in location region
$arResult["EMPTY_REGION"] = "N";
$arRegionFilter = array("!REGION_ID" => "NULL", ">REGION_ID" => "0");
if ($arParams["COUNTRY"] > 0 && $arParams["REGION"] > 0)
	$arRegionFilter["COUNTRY_ID"] = $arParams["COUNTRY"];
if ($arParams["REGION"] > 0)
	$arRegionFilter["REGION_ID"] = $arParams["REGION"];
$rsLocCount = CSaleLocation::GetList(array(), $arRegionFilter, false, false, array("ID"));
if (!$rsLocCount->Fetch())
	$arResult["EMPTY_REGION"] = "Y";

//check if exist another city
if ($arResult["EMPTY_CITY"] == "Y" && $arResult["EMPTY_REGION"] == "Y")
{
	$arCityFilter = array("!CITY_ID" => "NULL", ">CITY_ID" => "0");
	$rsLocCount = CSaleLocation::GetList(array(), $arCityFilter, false, false, array("ID"));
	if ($rsLocCount->Fetch())
		$arResult["EMPTY_CITY"] = "N";
}

//location default
$arParams["LOC_DEFAULT"] = array();
$dbLocDefault = CSaleLocation::GetList(
		array(
			"SORT" => "ASC",
			"COUNTRY_NAME_LANG" => "ASC",
			"CITY_NAME_LANG" => "ASC"
		),
		array("LOC_DEFAULT" => "Y", "LID" => LANGUAGE_ID),
		false,
		false,
		array("*")
);
while ($arLocDefault = $dbLocDefault->Fetch())
{
	if ($arLocDefault["LOC_DEFAULT"] == "Y"
		&& SalesZone::checkCountryId($arLocDefault["COUNTRY_ID"], $arParams["SITE_ID"])
		&& SalesZone::checkRegionId($arLocDefault["REGION_ID"], $arParams["SITE_ID"])
		&& SalesZone::checkCityId($arLocDefault["CITY_ID"], $arParams["SITE_ID"])
	)
	{
		$nameDefault = "";
		$nameDefault .= ((strlen($arLocDefault["COUNTRY_NAME"])<=0) ? "" : $arLocDefault["COUNTRY_NAME"]);
		if (strlen($arLocDefault["COUNTRY_NAME"])>0 && strlen($arLocDefault["REGION_NAME"])>0)
			$nameDefault .= " - ".$arLocDefault["REGION_NAME"];
		elseif (strlen($arLocDefault["REGION_NAME"])>0)
			$nameDefault .= $arLocDefault["REGION_NAME"];

		if ((strlen($arLocDefault["COUNTRY_NAME"])>0 || strlen($arLocDefault["REGION_NAME"])>0) && strlen($arLocDefault["CITY_NAME"])>0)
			$nameDefault .= " - ".$arLocDefault["CITY_NAME"];
		elseif (strlen($arLocDefault["CITY_NAME"])>0)
			$nameDefault .= $arLocDefault["CITY_NAME"];

		$arLocDefault["LOC_DEFAULT_NAME"] = $nameDefault;
		$arParams["LOC_DEFAULT"][] = $arLocDefault;
	}
}


//location value
if ($arParams["LOCATION_VALUE"] > 0 )
{
	if ($arLocation = CSaleLocation::GetByID($arParams["LOCATION_VALUE"]))
	{
		if ($arResult["EMPTY_REGION"] == "Y" && $arResult["EMPTY_CITY"] == "Y")
			$arParams["COUNTRY"] = $arParams["LOCATION_VALUE"];
		else
			$arParams["COUNTRY"] = $arLocation["COUNTRY_ID"];

		if ($arResult["EMPTY_CITY"] == "Y")
			$arParams["REGION"] = $arLocation["ID"];
		else
			$arParams["REGION"] = $arLocation["REGION_ID"];

		$arParams["CITY"] = $arParams["CITY_OUT_LOCATION"] == "Y" ? $arParams["LOCATION_VALUE"] : $arLocation["CITY_ID"];
	}
}

$locationString = "";

//select country
$arResult["COUNTRY_LIST"] = array();

if ($arResult["EMPTY_REGION"] == "Y" && $arResult["EMPTY_CITY"] == "Y")
	$rsCountryList = CSaleLocation::GetList(array("SORT" => "ASC", "NAME_LANG" => "ASC"), array("LID" => LANGUAGE_ID), false, false, array("ID", "COUNTRY_ID", "COUNTRY_NAME_LANG"));
else
	$rsCountryList = CSaleLocation::GetCountryList(array("SORT" => "ASC", "NAME_LANG" => "ASC"));

while ($arCountry = $rsCountryList->GetNext())
{
	if(!SalesZone::checkCountryId($arCountry["ID"], $arParams["SITE_ID"]))
		continue;

	if ($arResult["EMPTY_REGION"] == "Y" && $arResult["EMPTY_CITY"] == "Y")
		$arCountry["NAME_LANG"] = $arCountry["COUNTRY_NAME_LANG"];

	$arResult["COUNTRY_LIST"][] = $arCountry;
	if ($arCountry["ID"] == $arParams["COUNTRY"] && strlen($arCountry["NAME_LANG"]) > 0)
		$locationString .= $arCountry["NAME_LANG"];
}

if (count($arResult["COUNTRY_LIST"]) <= 0)
	$arResult["COUNTRY_LIST"] = array();
elseif (count($arResult["COUNTRY_LIST"]) == 1)
	$arParams["COUNTRY"] = $arResult["COUNTRY_LIST"][0]["ID"];

//select region
$arResult["REGION_LIST"] = array();
if (($arParams["COUNTRY"] > 0 || count($arResult["COUNTRY_LIST"]) <= 0) && (strlen($arParams["REGION_INPUT_NAME"]) > 0 || $arParams["ZIPCODE"] > 0))
{
	$arRegionFilter = array("LID" => LANGUAGE_ID, "!REGION_ID" => "NULL", "!REGION_ID" => "0");
	if ($arParams["COUNTRY"] > 0)
		$arRegionFilter["COUNTRY_ID"] = IntVal($arParams["COUNTRY"]);

	if ($arResult["EMPTY_CITY"] == "Y")
		$rsRegionList = CSaleLocation::GetList(array("SORT" => "ASC", "NAME_LANG" => "ASC"), $arRegionFilter, false, false, array("ID", "REGION_ID", "REGION_NAME_LANG"));
	else
		$rsRegionList = CSaleLocation::GetRegionList(array("SORT" => "ASC", "NAME_LANG" => "ASC"), $arRegionFilter);

	while ($arRegion = $rsRegionList->GetNext())
	{
		if(!SalesZone::checkRegionId($arRegion["ID"], $arParams["SITE_ID"]))
			continue;

		if ($arResult["EMPTY_CITY"] == "Y")
			$arRegion["NAME_LANG"] = $arRegion["REGION_NAME_LANG"];

		$arResult["REGION_LIST"][] = $arRegion;
		if ($arRegion["ID"] == $arParams["REGION"] && strlen($arRegion["NAME_LANG"]) > 0)
			$locationString = $arRegion["NAME_LANG"].", ".$locationString;
	}
}
if (count($arResult["REGION_LIST"]) <= 0)
	$arResult["REGION_LIST"] = array();
elseif (count($arResult["REGION_LIST"]) == 1)
	$arParams["REGION"] = $arResult["REGION_LIST"][0]["ID"];

//select city
$arResult["CITY_LIST"] = array();
if (
		$arResult["EMPTY_CITY"] == "N"
		&& ((count($arResult["COUNTRY_LIST"]) > 0 && count($arResult["REGION_LIST"]) > 0 && $arParams["COUNTRY"] > 0 && $arParams["REGION"] > 0)
		|| (count($arResult["COUNTRY_LIST"]) <= 0 && count($arResult["REGION_LIST"]) > 0 && $arParams["REGION"] > 0)
		|| (count($arResult["COUNTRY_LIST"]) > 0 && count($arResult["REGION_LIST"]) <= 0 && $arParams["COUNTRY"] > 0)
		|| (count($arResult["COUNTRY_LIST"]) <= 0 && count($arResult["REGION_LIST"]) <= 0))
	)
{
	$arCityFilter = array("LID" => LANGUAGE_ID);
	if ($arParams["COUNTRY"] > 0)
		$arCityFilter["COUNTRY_ID"] = $arParams["COUNTRY"];
	if ($arParams["REGION"] > 0)
		$arCityFilter["REGION_ID"] = $arParams["REGION"];

	if ($arParams['ALLOW_EMPTY_CITY'] == 'Y')
	{
		$rsLocationsList = CSaleLocation::GetList(
			array(
				"SORT" => "ASC",
				"COUNTRY_NAME_LANG" => "ASC",
				"CITY_NAME_LANG" => "ASC"
			),
			$arCityFilter,
			false,
			false,
			array(
				"ID", "CITY_ID", "CITY_NAME"
			)
		);

		while ($arCity = $rsLocationsList->GetNext())
		{
			if(!SalesZone::checkCityId($arCity["CITY_ID"], $arParams["SITE_ID"]))
				continue;

			$arResult["CITY_LIST"][] = array(
				"ID" => $arCity[$arParams["CITY_OUT_LOCATION"] == "Y" ? "ID" : "CITY_ID"],
				"CITY_ID" => $arCity['CITY_ID'],
				"CITY_NAME" => $arCity["CITY_NAME"],
			);
			if ($arCity["ID"] == $arParams["CITY"])
			{
				$locationString = (strlen($arCity["CITY_NAME"]) > 0 ? $arCity["CITY_NAME"].", " : "").$locationString;
				if(IntVal($arParams["LOCATION_VALUE"]) <= 0)
					$arParams["LOCATION_VALUE"] = $arCity["ID"];
			}
		}//end while
	}//end if
}

if ($arResult["EMPTY_CITY"] == "Y")
	$arParams["REGION_INPUT_NAME"] = "";

if ($arResult["EMPTY_REGION"] == "Y" && $arResult["EMPTY_CITY"] == "Y")
	$arParams["COUNTRY_INPUT_NAME"] = "";

$arResult["LOCATION_STRING"] = $locationString;
$arParams["JS_CITY_INPUT_NAME"] = CUtil::JSEscape($arParams["CITY_INPUT_NAME"]);

$arTmpParams = array(
	"COUNTRY_INPUT_NAME" => $arParams["COUNTRY_INPUT_NAME"],
	"REGION_INPUT_NAME" => $arParams["REGION_INPUT_NAME"],
	"CITY_INPUT_NAME" => $arParams["CITY_INPUT_NAME"],
	"CITY_OUT_LOCATION" => $arParams["CITY_OUT_LOCATION"],
	"ALLOW_EMPTY_CITY" => $arParams["ALLOW_EMPTY_CITY"],
	"ONCITYCHANGE" => $arParams["ONCITYCHANGE"],
);

$arResult["JS_PARAMS"] = CUtil::PhpToJsObject($arTmpParams);

$serverName = COption::GetOptionString("main", "server_name", "");
if (strlen($serverName) > 0)
	$arParams["SERVER_NAME"] = "http://".$serverName;

$arResult["ADDITIONAL_VALUES"] = "siteId:".$arParams["SITE_ID"];

$this->IncludeComponentTemplate();

if ($arParams["AJAX_CALL"] != "Y")
{
	IncludeAJAX();
	$template =& $this->GetTemplate();
	$APPLICATION->AddHeadScript($template->GetFolder().'/proceed.js');
}
?>