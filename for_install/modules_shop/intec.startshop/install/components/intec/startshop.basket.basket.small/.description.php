<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arComponentDescription = array(
        "NAME" => GetMessage("SBBS_COMPONENT_NAME"),
        "DESCRIPTION" => GetMessage("SBBS_COMPONENT_DESCRIPTION"),
        "COMPLEX" => "N",
        "SORT" => 1,
        "PATH" => array(
            "ID" => "startshop",
            "NAME" => GetMessage("SBBS_MODULE_NAME"),
            "SORT" => 1
        )
    );
?>