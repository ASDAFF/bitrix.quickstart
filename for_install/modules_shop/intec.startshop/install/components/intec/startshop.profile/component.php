<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    global $USER, $APPLICATION;

    if (CModule::IncludeModule('intec.startshop')) {
        CStartShopTheme::ApplyTheme(SITE_ID);
    }

    $arDefaultParams = array(
        'AUTHORIZE_TEMPLATE' => 'startshop',
        'PROFILE_TEMPLATE' => 'startshop.personal',
        'REGISTER_TEMPLATE' => 'startshop',
        'REGISTER_CONFIRM_TEMPLATE' => 'startshop',
        'PASSWORD_CHANGE_TEMPLATE' => 'startshop',
        'PASSWORD_FORGOT_TEMPLATE' => 'startshop',
        'AUTH_RESULT' => $APPLICATION->arAuthResult
    );

    $arParams = array_merge($arDefaultParams, $arParams);
    $sPage = 'authorize';

    $arPages = array(
        'LOGIN' => $_GET['login'] == 'yes',
        'REGISTER' => $_GET['register'] == 'yes',
        'REGISTER_CONFIRM' => $_GET['confirm_registration'] == 'yes',
        'PASSWORD_FORGOT' => $_GET['forgot_password'] == 'yes',
        'PASSWORD_CHANGE' => $_GET['change_password'] == 'yes',
    );

    if ($USER->IsAuthorized())
    {
        $sPage = 'profile';
    }
    else
    {
        if ($arPages['REGISTER']) $sPage = 'register';
        if ($arPages['REGISTER_CONFIRM']) $sPage = 'register_confirm';
        if ($arPages['PASSWORD_FORGOT']) $sPage = 'password_forgot';
        if ($arPages['PASSWORD_CHANGE']) $sPage = 'password_change';
    }

    $this->IncludeComponentTemplate($sPage);
?>