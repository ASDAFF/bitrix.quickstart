<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();?>

<?
$arComponentDescription = array(
	"NAME" => GetMessage("REGISTER_PLUS_DESC_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("REGISTER_DESC_PLUS_DESC"),
	"ICON" => "/images/icon.gif",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
			"CHILD" => array(
				"ID" => "user",
				"NAME" => GetMessage("REGISTER_PLUS_DESC_NAME")
			),
		),
);
?>
