<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    CJSCore::Init(array('fx', 'popup', 'ajax'));

    if (CModule::IncludeModule('intec.startshop')) {
        CStartShopTheme::ApplyTheme(SITE_ID);
    }
?>