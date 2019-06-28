<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arTemplateParameters['USE_ADAPTABILITY'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_ADAPTABILITY'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_ITEMS_PICTURES'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_ITEMS_PICTURES'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    );

    $arTemplateParameters['USE_BUTTON_CLEAR'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_BUTTON_CLEAR'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_BUTTON_BASKET'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_BUTTON_BASKET'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arTemplateParameters['USE_SUM_FIELD'] = array(
        "PARENT" => "VISUAL",
        "NAME" => GetMessage('SB_DEFAULT_USE_SUM_FIELD'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );
?>