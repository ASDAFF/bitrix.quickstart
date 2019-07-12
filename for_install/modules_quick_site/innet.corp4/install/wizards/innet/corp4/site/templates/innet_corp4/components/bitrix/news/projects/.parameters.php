<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
$arTemplateParameters = array(
    "INNET_IBLOCK_ID_ORDER" => array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("INNET_IBLOCK_ID_ORDER"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "INNET_IBLOCK_ID_REVIEWS" => array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("INNET_IBLOCK_ID_REVIEWS"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "INNET_IBLOCK_ID_SERVICES" => array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("INNET_IBLOCK_ID_SERVICES"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "INNET_ALLOW_REVIEWS" => array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("INNET_ALLOW_REVIEWS"),
        "TYPE" => "LIST",
        "VALUES" => array("Y" => GetMessage('INNET_ALLOW_REVIEWS_Y'), "N" => GetMessage('INNET_ALLOW_REVIEWS_N')),
        "DEFAULT" => "Y",
    ),
    "INNET_PROJECTS_EMAIL" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("INNET_PROJECTS_EMAIL"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
);
?>