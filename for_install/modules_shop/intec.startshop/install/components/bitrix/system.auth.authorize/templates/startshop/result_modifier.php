<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    if (!empty($arParams['AUTH_URL'])) $arResult['AUTH_URL'] = $arParams['AUTH_URL'];

    if (CModule::IncludeModule('intec.startshop')) {
        CStartShopTheme::ApplyTheme(SITE_ID);
    }
?>