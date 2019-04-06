<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("fileman");
CMedialib::Init();
$arCol = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y')));
foreach($arCol as $collection){
$arColpar[$collection[ID]]=$collection[NAME];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"GAL_SETTINGS" => array(
			"SORT" => 120,
			"NAME" => GetMessage("GAL_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"CHOSEN_COLLECTIONS" => array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("CHOSEN_COLLECTIONS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arColpar,
		),
		"SET_P_TITLE" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("SET_P_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"MAIN_TITLE" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("MAIN_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MAIN_TITLE_VALUE"),
		),
		"SET_JQ" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("SET_JQ"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"SET_FB" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("SET_FB"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"SHOW_B_LINK" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("SHOW_B_LINK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"SHOW_B_LINK_VALUE" => Array(
			"PARENT" => "GAL_SETTINGS",
			"NAME" => GetMessage("SHOW_B_LINK_VALUE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SHOW_B_LINK_DEFAULT_VALUE"),
		),
	),
);
?>