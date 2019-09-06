<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "VARIABLE_ALIASES" => array(
            "ID" => array(
                "NAME" => GetMessage("WS_SPP_ID_VARIABLE")
            ),
        ),
        "SEF_MODE" => Array(
            "list" => Array(
                "NAME" => GetMessage("WS_SPP_LIST_DESC"),
                "DEFAULT" => "index.php",
                "VARIABLES" => array()
            ),
            "edit" => Array(
                "NAME" => GetMessage("WS_SPP_DETAIL_DESC"),
                "DEFAULT" => "edit/#ID#/",
                "VARIABLES" => array("ID")
            ),
        ),

        "PER_PAGE" => Array(
            "NAME" => GetMessage("WS_SPP_PER_PAGE"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "20",
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),

        'USE_AJAX_LOCATIONS' => array(
            'NAME' => GetMessage("WS_SPP_USE_AJAX_LOCATIONS"),
            'TYPE' => 'CHECKBOX',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'N',
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),

        "SET_TITLE" => Array(),

    )
);
