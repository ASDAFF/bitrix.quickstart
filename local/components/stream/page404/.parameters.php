<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
        "REDIRECT" => array(
            "NAME" => GetMessage("404_REDIRECT"),
        ),
	),
    "PARAMETERS" => array(
        "REDIRECT_ONOFF" => array(
            "PARENT" => "REDIRECT",
            "NAME" => GetMessage("404_REDIRECT_ONOFF"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => '',
        ),
        "REDIRECT_URL" => array(
            "PARENT" => "REDIRECT",
            "NAME" => GetMessage("404_REDIRECT_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => '/index.php',
        ),
        "REDIRECT_MSEC" => array(
            "PARENT" => "REDIRECT",
            "NAME" => GetMessage("404_REDIRECT_MSEC"),
            "TYPE" => "STRING",
            "DEFAULT" => '1000',
        ),
        "TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("404_TITLE"),
            "TYPE" => "STRING",
            "DEFAULT" => '404',
        ),
        "CLEAR_PAGE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("404_CLEAR_PAGE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => '',
        ),
        "IMAGE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage('404_IMAGE'),
            "TYPE" => "FILE",
            "FD_TARGET" => "F",
            "FD_EXT" => "png,gif,jpg,jpeg",
            "FD_UPLOAD" => true,
            "DEFAULT" => '',
        ),
    ),
);
?>
