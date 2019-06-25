<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", $arParams['AUTHORIZE_TEMPLATE'], array(
        "AUTH_FORGOT_PASSWORD_URL" => "",
        "AUTH_RESULT" => $arParams['AUTH_RESULT'],
        "USE_ADAPTABILITY" => $arParams['USE_ADAPTABILITY']
), $component, array("HIDE_ICONS" => "Y"))?>