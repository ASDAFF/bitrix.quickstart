<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();?>

<?
$arComponentDescription = array(
	"NAME" => GetMessage("rksoft_REGISTER_PLUS_DESC_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("rksoft_REGISTER_DESC_PLUS_DESC"),
	"ICON" => "/images/icon.gif",
	"PATH" => array(
			"ID" => "rk_soft",
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_DESC_SECTION_NAME"),
			"CHILD" => array(
				"ID" => "register_plus",
				"NAME" => GetMessage("rksoft_REGISTER_PLUS_DESC_NAME")
			),
		),
);
?>