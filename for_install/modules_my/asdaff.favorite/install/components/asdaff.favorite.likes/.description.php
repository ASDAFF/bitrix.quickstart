<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ASDAFF_CMP_NAME"),
	"DESCRIPTION" => GetMessage("ASDAFF_CMP_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "asdaff.favorites",
			"NAME" => GetMessage("ASDAFF_CMP_DIR")
		)

	),
);

?>