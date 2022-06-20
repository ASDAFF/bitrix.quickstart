 <?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MLIFE_VKG_DESC_NAME"),
	"DESCRIPTION" => GetMessage("MLIFE_VKG_DESC_DESCRIPTION"),
	"ICON" => "/images/component.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => "media",
            "NAME" => "Мультимедия",
            "SORT" => 30
        ),
	),
);
?>