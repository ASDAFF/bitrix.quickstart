<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SONET_COMPONENT"),
	"DESCRIPTION" => GetMessage("SONET_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "communication",
		"CHILD" => array(
			"ID" => "socialnetwork",
			"NAME" => GetMessage("SONET")
		)
	),
);
?>