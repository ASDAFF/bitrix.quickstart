<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('CACKLE_COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('CACKLE_COMPONENT_DESC'),
	"ICON" => "/images/icon.png",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "Cackle", // for example "my_project"
        "NAME" => "Cackle",
		"CHILD" => array(
			"ID" => "cackle:comments", // for example "my_project:services"
			"NAME" => GetMessage('CACKLE_MODULE_NAME'),  // for example "Services"
            "CHILD" => array(
                "ID" => "cackle.comments",

            ),
		),
	),
	"COMPLEX" => "N",
);

?>