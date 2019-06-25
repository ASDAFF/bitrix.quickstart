<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    global $USER, $APPLICATION;

    if (CModule::IncludeModule('intec.startshop')) {
        CStartShopTheme::ApplyTheme(SITE_ID);
    }

    if ((!$arResult['SHOW_FORM'] && !$USER->IsAuthorized()) || $arResult["MESSAGE_CODE"] == "E01")
    {
        LocalRedirect($APPLICATION->GetCurPage());
        $arResult['SHOW_FORM'] = false;
    }

?>