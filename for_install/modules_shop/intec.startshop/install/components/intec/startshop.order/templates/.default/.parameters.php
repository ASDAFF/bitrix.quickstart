<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arTemplateParameters = array();

    $arTemplateParameters['USE_ADAPTABILITY'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SO_DEFAULT_USE_ADAPTABILITY'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_ITEMS_PICTURES'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SO_DEFAULT_USE_ITEMS_PICTURES'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    );

    $arTemplateParameters['USE_BUTTON_BASKET'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SO_DEFAULT_USE_BUTTON_BASKET'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y"
    );

    if ($arCurrentValues["USE_BUTTON_BASKET"] == "Y")
        $arTemplateParameters['URL_BASKET'] = array(
            "PARENT" => "URL",
            "NAME" => GetMessage('SO_DEFAULT_URL_BASKET'),
            "TYPE" => "STRING"
        );
?>