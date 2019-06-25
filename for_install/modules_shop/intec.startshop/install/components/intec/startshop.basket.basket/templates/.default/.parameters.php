<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arTemplateParameters = array();

    $arTemplateParameters['USE_ADAPTABILITY'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SBB_DEFAULT_USE_ADAPTABILITY'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_ITEMS_PICTURES'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SBB_DEFAULT_USE_ITEMS_PICTURES'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    );

    $arTemplateParameters['USE_BUTTON_CLEAR'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SBB_DEFAULT_USE_BUTTON_CLEAR'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_BUTTON_ORDER'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SBB_DEFAULT_USE_BUTTON_ORDER'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y"
    );

    if ($arCurrentValues["USE_BUTTON_ORDER"] == "Y")
        $arTemplateParameters['URL_ORDER'] = array(
            "PARENT" => "URL",
            "NAME" => GetMessage('SBB_DEFAULT_URL_ORDER'),
            "TYPE" => "STRING"
        );

    $arTemplateParameters['USE_SUM_FIELD'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SBB_DEFAULT_USE_SUM_FIELD'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );
?>