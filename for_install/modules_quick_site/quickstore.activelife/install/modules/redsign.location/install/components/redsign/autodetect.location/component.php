<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("redsign.location")) {
	ShowError(GetMessage("RS_LOCATION_NOT_INSTALLED"));
	return;
}

if (!CModule::IncludeModule('sale')) {
	ShowError(GetMessage("RS_SALE_NOT_INSTALLED"));
	return;
}

$COM_SESS_PREFIX = "RSLOCATION";
$arErrors = array();
$arWarnings = array();
$arrLocations = array();
$arResult["ACTION_URL"] = $APPLICATION->GetCurPage();
$arParams["RSLOC_LOAD_LOCATIONS_CNT"] = IntVal($arParams["RSLOC_LOAD_LOCATIONS_CNT"]);
$arParams["REQUEST_PARAM_NAME"] = "RSLOC_AUTO_DETECT";
$arParams["CITY_NAME"] = "RSLOC_CITY_NAME";
$arParams["CITY_ID"] = "RSLOC_CITY_ID";
$arParams["COUNTRY_NAME"] = "RSLOC_COUNTRY_NAME";
$arParams["COUNTRY_ID"] = "RSLOC_COUNTRY_ID";
$arParams["REGION_NAME"] = "RSLOC_REGION_NAME";
$arParams["REGION_ID"] = "RSLOC_REGION_ID";
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
$arResult["DETECTED"] = CRS_Location::GetCityName();
$arResult["AUTO_DETECT"] = "Y";
//unset($_SESSION[$COM_SESS_PREFIX]);

if ($arParams["RSLOC_INCLUDE_JQUERY"]=="Y")
	CJSCore::Init(array("jquery"));

if ($_REQUEST[$arParams["REQUEST_PARAM_NAME"]] == "Y" && check_bitrix_sessid()) {
	if (empty($_REQUEST[$arParams["CITY_ID"]]) || IntVal($_REQUEST[$arParams["CITY_ID"]]) < 1)
		$arErrors[] = GetMessage("RSLOC_ERROR_NO_CITY_ID");

	if (count($arErrors) < 1) {
		$dbRes = CSaleLocation::GetList(
			array("SORT" => "ASC", "CITY_NAME_LANG" => "ASC"),
			array("LID" => LANGUAGE_ID,"ID" => $_REQUEST[$arParams["CITY_ID"]])
		);
		if ($arFields = $dbRes->Fetch()) {
			$_SESSION[$COM_SESS_PREFIX]["DETECTED"] = $arResult["DETECTED"];
			$_SESSION[$COM_SESS_PREFIX]["LOCATION"] = $arFields;
			$arResult["LOCATION"] = $arFields;
		}
	}
}

if (isset($_SESSION[$COM_SESS_PREFIX]["LOCATION"])) {
	$arResult["LOCATION"] = $_SESSION[$COM_SESS_PREFIX]["LOCATION"];
	$arResult["AUTO_DETECT"] = "N";
}

if (empty($arResult["LOCATION"]) && isset($arResult["DETECTED"]["CITY_NAME"]) && $arResult["DETECTED"]["CITY_NAME"] != "") {
	$dbRes = CSaleLocation::GetList(
		array("SORT" => "ASC", "CITY_NAME_LANG" => "ASC"),
		array("LID" => LANGUAGE_ID,"CITY_NAME" => $arResult["DETECTED"]["CITY_NAME"])
	);
	if ($arFields = $dbRes->Fetch()) {
		$arResult["LOCATION"] = $arFields;
	} else {
		$dbRes = CSaleLocation::GetList(
			array("SORT" => "ASC", "CITY_NAME_LANG" => "ASC"),
			array("LID" => LANGUAGE_ID, "CITY_NAME_ORIG" => $arResult["DETECTED"]["CITY_NAME"])
		);
		if ($arFields = $dbRes->Fetch()) {
			$arResult["LOCATION"] = $arFields;
		}
	}
}

if ($arParams['RSLOC_LOAD_LOCATIONS'] == "Y" && $arParams["RSLOC_LOAD_LOCATIONS_CNT"] > 0) {
	$dbRes = CSaleLocation::GetList(
		array("SORT" => "ASC", "CITY_NAME_LANG" => "ASC"),
		array("LID" => LANGUAGE_ID, ">CITY_ID" => 0),
		false,
		array("nTopCount" => $arParams["RSLOC_LOAD_LOCATIONS_CNT"])
	);
	while ($arFields = $dbRes->Fetch()) {
		$arrLocations[] = $arFields;
	}
}

$arResult["ERROR_MESSAGE"] = $arErrors;
$arResult["WARNING_MESSAGE"] = $arWarnings;
$arResult["LOCATIONS"] = $arrLocations;

$this->IncludeComponentTemplate();
