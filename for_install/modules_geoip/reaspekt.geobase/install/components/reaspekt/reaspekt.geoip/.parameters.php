<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$statusMod = CModule::IncludeModuleEx("reaspekt.geobase");

if ($statusMod == '0' || $statusMod == '3')
	return false;
?>
<?if ($_REQUEST['bxsender'] != 'fileman_html_editor') {
    if ($statusMod == '0') {
        ShowError(GetMessage("REASPEKT_GEOBASE_MODULE_NOT_FOUND"));
    } elseif ($statusMod == '3') {
        ShowError(GetMessage("REASPEKT_GEOBASE_DEMO_EXPIRED"));
    } elseif ($statusMod == '2') {
        ShowNote(GetMessage("REASPEKT_GEOBASE_DEMO"));
    }
}


$arComponentParameters = array(
    "GROUPS" => array(
	),
	"PARAMETERS" => Array(
        "CHANGE_CITY_MANUAL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("REASPEKT_GEOBASE_CHANGE_CITY_MANUAL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	), 
);