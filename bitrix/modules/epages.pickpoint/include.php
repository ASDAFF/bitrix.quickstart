<?
IncludeModuleLangFile(__FILE__);
global $APPLICATION;

if(!function_exists("CheckPickpointLicense"))
{
	function CheckPickpointLicense($sIKN)
	{
		if(preg_match("#[0-9]{10}#", $sIKN))
		{
			return true;
		}
		return false;
	}
}

global $DBType;
global $arOptions;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/constants.php");
define("PP_CSV_URL", "http://www.pickpoint.ru/citys/cities.csv");
define("PP_ZONES_COUNT", 10);

$MODULE_ID = "epages.pickpoint";
if(!CModule::IncludeModule("sale"))
{
	//	trigger_error("Currency is not installed");
	return false;
}
CModule::AddAutoloadClasses(
	$MODULE_ID,
	array(
		"CAllPickpoint" => "mysql/pickpoint.php",
		"CPickpoint" => "general/pickpoint.php",

	)
);

// If module included after OnPageStart
if(!isset($_SESSION["PICKPOINT"]))
{
	CPickpoint::CheckRequest();
}

if(!(COption::GetOptionString($MODULE_ID, "pp_service_types_all", "")))
{
	COption::SetOptionString($MODULE_ID, "pp_service_types_all", serialize($arServiceTypes));
}
if(!(COption::GetOptionString($MODULE_ID, "pp_enclosing_types_all", "")))
{
	COption::SetOptionString($MODULE_ID, "pp_enclosing_types_all", serialize($arEnclosingTypes));
}

$iTimestamp = COption::GetOptionInt($MODULE_ID, "pp_city_download_timestamp", 0);

if(time() > $iTimestamp || !file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/cities.csv"))
{
	CPickpoint::GetCitiesCSV();
}

$arOptions = Array();
$arOptions["OPTIONS"]["pp_add_info"] = COption::GetOptionString($MODULE_ID, "pp_add_info", "1");
$arOptions["OPTIONS"]["pp_ikn_number"] = COption::GetOptionString($MODULE_ID, "pp_ikn_number", "");
$arOptions["OPTIONS"]["pp_enclosure"] = COption::GetOptionString($MODULE_ID, "pp_enclosure", "");
$arOptions["OPTIONS"]["pp_service_types_selected"] = COption::GetOptionString($MODULE_ID, "pp_service_types_selected");
$arOptions["OPTIONS"]["pp_service_types_all"] = COption::GetOptionString($MODULE_ID, "pp_service_types_all");
$arOptions["OPTIONS"]["pp_enclosing_types_selected"] =
	COption::GetOptionString($MODULE_ID, "pp_enclosing_types_selected");
$arOptions["OPTIONS"]["pp_enclosing_types_all"] = COption::GetOptionString($MODULE_ID, "pp_enclosing_types_all");
$arOptions["OPTIONS"]["pp_zone_count"] = COption::GetOptionString($MODULE_ID, "pp_zone_count");
$arOptions["OPTIONS"]["pp_from_city"] = COption::GetOptionString($MODULE_ID, "pp_from_city");
$arOptions["OPTIONS"]["pp_use_coeff"] = COption::GetOptionString($MODULE_ID, "pp_use_coeff");
$arOptions["OPTIONS"]["pp_custom_coeff"] = COption::GetOptionString($MODULE_ID, "pp_custom_coeff");
$arOptions["OPTIONS"]["pp_api_login"] = COption::GetOptionString($MODULE_ID, "pp_api_login");
$arOptions["OPTIONS"]["pp_api_password"] = COption::GetOptionString($MODULE_ID, "pp_api_password");
$arOptions["OPTIONS"]["pp_store_address"] = COption::GetOptionString($MODULE_ID, "pp_store_address");
$arOptions["OPTIONS"]["pp_store_phone"] = COption::GetOptionString($MODULE_ID, "pp_store_phone");
$arOptions["OPTIONS"]["pp_test_mode"] = COption::GetOptionString($MODULE_ID, "pp_test_mode");
$arOptions["OPTIONS"]["pp_free_delivery_price"] = COption::GetOptionString($MODULE_ID, "pp_free_delivery_price");
