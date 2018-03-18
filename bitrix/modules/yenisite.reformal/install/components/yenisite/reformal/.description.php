<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CD_RO_NAME"),
	"DESCRIPTION" => GetMessage("CD_RO_DESCRIPTION"),
	"ICON" => "/images/reformal.png",
	"CACHE_PATH" => "Y",
	"SORT" => 70,
	"PATH" => array(	
		"ID" => "romza",
		"NAME" => GetMessage("YENISITE_COMPONENTS"),
	),
);
?>