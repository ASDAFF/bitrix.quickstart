<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentParameters = array(
	"PARAMETERS" => array(
        "ELEMENT_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_ELEMENT_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "N"
        ),
        "SET_TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_SET_ELEMENT_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
        "ADD_ELEMENT_CHAIN" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USERS_DETAIL_ADD_ELEMENT_CHAIN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
		"CACHE_TIME" => Array(
			"DEFAULT" => 3600
		)
	)
);