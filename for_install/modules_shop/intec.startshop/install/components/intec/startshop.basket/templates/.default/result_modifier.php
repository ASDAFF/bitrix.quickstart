<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arDefaultParams = array(
        'USE_ADAPTABILITY' => 'N',
        'USE_ITEMS_PICTURES' => 'Y',
        'USE_BUTTON_CLEAR' => 'N',
        'USE_BUTTON_BASKET' => 'N',
        'USE_SUM_FIELDS' => 'N'
    );

    $arParams = array_merge($arDefaultParams, $arParams);
?>
