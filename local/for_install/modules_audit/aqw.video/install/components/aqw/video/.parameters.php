<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
        "WIDTH" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_WIDTH"),
            "TYPE" => "STRING",
            "DEFAULT" => "600",
        ),
        "HEIGHT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_HEIGHT"),
            "TYPE" => "STRING",
            "DEFAULT" => "400",
        ),
        "WIDTH_IMAGE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_WIDTH_IMAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => "160",
        ),
        "HEIGHT_IMAGE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_HEIGHT_IMAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => "120",
        ),
        "COUNT_ON_LINE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_COUNT_ON_LINE"),
            "TYPE" => "STRING",
            "DEFAULT" => "3",
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
	),
);
?>
