<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arDefaultParams = array(
        'USE_ADAPTABILITY' => 'N'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    if (CModule::IncludeModule('intec.startshop')) {
        CStartShopTheme::ApplyTheme(SITE_ID);
    }
?>