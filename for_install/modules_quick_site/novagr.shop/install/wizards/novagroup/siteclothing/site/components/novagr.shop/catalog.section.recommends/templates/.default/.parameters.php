<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
    'MAP_DATA' => array(
        'NAME' => GetMessage('MYMS_PARAM_DATA'),
        'TYPE' => 'CUSTOM',
        'JS_FILE' => '/local/components/novagr.shop/catalog.section.recommends/settings/settings.js',
        'JS_EVENT' => 'OnNovagrShopCSRListSettingsEdit',
        'JS_DATA' => LANGUAGE_ID.'||'.GetMessage('MYMS_PARAM_DATA_SET'),
        'PARENT' => 'BASE',
    ),
);

