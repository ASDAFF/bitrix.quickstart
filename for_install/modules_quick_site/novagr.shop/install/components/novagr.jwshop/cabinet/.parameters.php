<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "VARIABLE_ALIASES" => Array(),
        "SEF_MODE" => Array(
            "cart" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_CART"),
                "DEFAULT" => "",
                "VARIABLES" => array(),
            ),
            "orders" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_ORDERS"),
                "DEFAULT" => "orders/",
                "VARIABLES" => array(),
            ),
            "cancel" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_CANCEL"),
                "DEFAULT" => "cancel/#ID#/",
                "VARIABLES" => array("ID"),
            ),
            "copy" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_COPY"),
                "DEFAULT" => "copy/#ID#/",
                "VARIABLES" => array("ID"),
            ),
            "detail" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_DETAIL"),
                "DEFAULT" => "detail/#ID#/",
                "VARIABLES" => array("ID"),
            ),
            "subscr" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_SUBSCRIBE"),
                "DEFAULT" => "subscr/",
                "VARIABLES" => array(),
            ),
            "userinfo" => array(
                "NAME" => GetMessage("T_IBLOCK_SEF_PAGE_USERINFO"),
                "DEFAULT" => "userinfo/",
                "VARIABLES" => array(),
            ),
        ),
        "AJAX_MODE" => array(),
    ),
);
?>
