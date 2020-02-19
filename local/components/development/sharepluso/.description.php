<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('SZD_SHAREPLUSO_NAME'), 
	"DESCRIPTION" => GetMessage('SZD_SHAREPLUSO_DESCRIPTION'),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        /*
		"CHILD" => array(
			"ID" => "tools", // for example "my_project:services"
            "NAME" => GetMessage('SZD_SHARE_PLUSO_COMPONENTS_GROUP'),
		),
        */
	),
	"COMPLEX" => "N",
);

?>