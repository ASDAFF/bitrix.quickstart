<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("B1T_CATALOG_SMART_FILTER_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("B1T_CATALOG_SMART_FILTER_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/iblock_filter.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 70,
	"PATH" => array(
		"ID" => "b1team",
                "NAME" => GetMessage("B1T_COMPONENTS"),
		"CHILD" => array(
			"ID" => "b1team_catalog",
			"NAME" => GetMessage("B1T_COMPONENTS_CATALOG")
		)
	),
);
?>