<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

COption::SetOptionString("catalog", "allow_negative_amount", "Y");
COption::SetOptionString("catalog", "default_can_buy_zero", "Y");
COption::SetOptionString("catalog", "default_quantity_trace", "Y");

if (CModule::IncludeModule("catalog"))
{
	$arStores = array();
	$dbStore= CCatalogStore::GetList(array(), array("XML_ID" => "mebel"), false, false, array("ID"));
	if (!$arStore = $dbStore->Fetch())
	    $arNewStores[] =  array(
			"TITLE" => GetMessage("STORE_NAME_1"),
			"ACTIVE" => "N",
			"ADDRESS" => GetMessage("STORE_ADR_1"),
			"DESCRIPTION" => GetMessage("STORE_DESCR_1"),
			"USER_ID" => $USER->GetID(),
			"GPS_N" => "54.71411",
			"GPS_S" => "20.56675",
			"PHONE" => "+74951234567",
			"SCHEDULE" => "24/7",
			"XML_ID" => "mebel",
		);
	$dbStore= CCatalogStore::GetList(array(), array("XML_ID" => "armada"), false, false, array("ID"));
	if (!$arStore = $dbStore->Fetch())
		$arNewStores[] = array(
			"TITLE" => GetMessage("STORE_NAME_2"),
			"ACTIVE" => "N",
			"ADDRESS" => GetMessage("STORE_ADR_2"),
			"DESCRIPTION" => GetMessage("STORE_DESCR_2"),
			"USER_ID" => $USER->GetID(),
			"GPS_N" => "55.896919",
			"GPS_S" => "37.57983",
			"PHONE" => "+74951234567",
			"SCHEDULE" => "24/7",
			"XML_ID" => "armada",
		);

	if (count($arNewStores) > 0)
	foreach($arNewStores as $arFields)
		CCatalogStore::Add($arFields);
}
?>