<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
// check parameters
if(!isset($arParams["ID_LOC_DEFAULT"]) or !$arParams["ID_LOC_DEFAULT"]) $arParams["ID_LOC_DEFAULT"] = 1;
$arParams["ID_LOC_DEFAULT"] = intval($arParams["ID_LOC_DEFAULT"]);

if($arParams["USE_LOC_GROUPS"] == "Y")
{
	$arResult["LOC_GRP_DEFAULT"] = $arParams["ID_LOC_GROUP_DEFAULT"];
	if(!isset($arParams["ID_LOC_GROUP_DEFAULT"]) or !$arParams["ID_LOC_GROUP_DEFAULT"]) $arParams["ID_LOC_GROUP_DEFAULT"] = 1;
	$arParams["ID_LOC_GROUP_DEFAULT"] = intval($arParams["ID_LOC_GROUP_DEFAULT"]);
}

if(!isset($arParams["LANG"])) $arParams["LANG"] = "RU";
else $arParams["LANG"] =($arParams["LANG"] == "EN") ? "EN" : "RU";

if(!isset($arParams["CACHE_TIME"])) $arParams["CACHE_TIME"] = 86400;
$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);

// component logic
if ($this->StartResultCache())
{
	if(!CModule::IncludeModule("iblock") or !CModule::IncludeModule("catalog") or !CModule::IncludeModule("sale"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("rksoft.select_city_REQUIRED_MODULES_NOT_INSTALL"));
		return;
	}

	// get result
	$arResult = array();
	$arLocationsInGroup = array();
	$arLocations = array();

	$dbLocationGroup = CSaleLocationGroup::GetLocationList(array("LOCATION_GROUP_ID" => $arParams["ID_LOC_GROUP_DEFAULT"]));
	while ($arLocationInGroup = $dbLocationGroup->Fetch())
	{
		$arLocationsInGroup[] = $arLocationInGroup["LOCATION_ID"];
	}

	$arLocationsInGroup = (count($arLocationsInGroup) > 0) ? $arLocationsInGroup : array();

	$arSelectFields = array("CITY_ID", "CITY_NAME");

	if(is_array($arLocationsInGroup) and count($arLocationsInGroup) > 0)
	{
		$arResult["LOC_GRP"] = $arLocationsInGroup;
		$dbLocations = CSaleLocation::GetList(
			array("SORT" => "ASC"),
			array("LID" => $arParams["LANG"], "ID" => $arLocationsInGroup),
			false,
			false,
			$arSelectFields
		);

		while($location = $dbLocations->Fetch())
		{
			if($arParams["USE_LOC_GROUPS"] == "Y" and $arParams["ID_LOC_GROUP_DEFAULT"] and $location["CITY_ID"] and $location["CITY_NAME"]) $arResult["ITEMS"][] = array("GROUP_ID" => $arParams["ID_LOC_GROUP_DEFAULT"], "CITY_ID" => $location["CITY_ID"], "CITY_NAME" => $location["CITY_NAME"]);
			else
			{
				if($location["CITY_ID"] and $location["CITY_NAME"]) $arResult["ITEMS"][] = array("CITY_ID" => $location["CITY_ID"], "CITY_NAME" => $location["CITY_NAME"]);
			}
		}
	}
	
	else $arResult["ITEMS"] = array();

	$arResult["ITEMS"] = (count($arResult["ITEMS"]) > 0) ? $arResult["ITEMS"] : array();

	$this->IncludeComponentTemplate();
}
?>