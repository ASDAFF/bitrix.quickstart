<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
    'MAP_DATA' => array(
        'NAME' => GetMessage('MYMS_PARAM_DATA'),
        'TYPE' => 'CUSTOM',
        'JS_FILE' => '/bitrix/components/aqw/video/settings2/settings.js',
        'JS_EVENT' => 'OnAqwVideoListSettingsEdit2',
        'JS_DATA' => LANGUAGE_ID.'||'.GetMessage('MYMS_PARAM_DATA_SET'),
        'PARENT' => 'BASE',
    ),
);

