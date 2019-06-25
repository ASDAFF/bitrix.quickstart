<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arComponentDescription = array(
        "NAME" => GetMessage("SB_COMPONENT_NAME"),
        "DESCRIPTION" => GetMessage("SB_COMPONENT_DESCRIPTION"),
        "COMPLEX" => "Y",
        "SORT" => 1,
        "PATH" => array(
            "ID" => "startshop",
            "NAME" => GetMessage("SB_MODULE_NAME"),
            "SORT" => 1
        )
    );
?>