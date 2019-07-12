<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arYesNo = Array(
	"Y" => GetMessage("SBBL_DESC_YES"),
	"N" => GetMessage("SBBL_DESC_NO"),
);


$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SBBL_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/basket.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PERSONAL" => Array(
			"NAME" => GetMessage("SBBL_PATH_TO_PERSONAL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SHOW_PERSONAL_LINK" => Array(
			"NAME" => GetMessage("SBBL_SHOW_PERSONAL_LINK"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "Y",
			"VALUES"=>$arYesNo, 
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);
?>