<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LNG_NAME_SSTAT"),
	"DESCRIPTION" => GetMessage("LNG_DESCRIPTION_SSTAT"),
	"ICON" => "/images/icon_fb.gif",
	"SORT" => 10,
	"LOGOTYPE" => GetMessage("LNG_IMG_SSTAT"),
	"CACHE_PATH" => "N",
    "PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
              "CHILD" => array(
                      "ID" => "widgets",
                      "NAME" => GetMessage("LNG_CAT_STATUS")
                )
        ),
	"COMPLEX" => "N",
);
?>