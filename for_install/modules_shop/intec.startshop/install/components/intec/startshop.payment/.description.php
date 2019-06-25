<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arComponentDescription = array(
        "NAME" => GetMessage("SP_COMPONENT_NAME"),
        "DESCRIPTION" => GetMessage("SP_COMPONENT_DESCRIPTION"),
        "COMPLEX" => "N",
        "SORT" => 1,
        "PATH" => array(
            "ID" => "startshop",
            "NAME" => GetMessage("SP_MODULE_NAME"),
            "SORT" => 1
        )
    );
?>