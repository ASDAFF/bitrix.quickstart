<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = array(
    'NAME' => GetMessage('Текущая дата'),
    'DESCRIPTION' => GetMessage('Выводим текущую дату'),
    'PATH' => array(
        'ID' => 'wm_components',
        'CHILD' => array(
            'ID' => 'curdate',
            'NAME' => 'Текущая дата'
        )
    ),
    'ICON' => '/images/icon.gif',
);

/**
 * example of use:
 *
<? $APPLICATION->IncludeComponent('wm:form.ajax', '.default', array(
    'COMPONENT_TEMPLATE' => '.default',
    'FORM_CLASS' => 'ajax-form',
    'FORM_ACTION' => '/ajax/feedback.php',
    'FORM_BTN_TITLE' => 'Отправить',
    'IBLOCK_FIELDS' => array(
        0 => 'name',
        1 => 'surname',
        2 => 'email',
        3 => 'phone',
    ),
    'IBLOCK_FIELDS_TITLE' => array(
        0 => 'имя',
        1 => 'фамилия',
        2 => 'мыло',
        3 => 'тэха',
    ),
    'IBLOCK_REQUIRED_FIELDS' => array(
        0 => 'name',
        1 => 'email',
        2 => 'phone',
    ),
    'NEED_SAVE_TO_IBLOCK' => 'Y',
    'IBLOCK_TYPE' => 'services',
    'IBLOCK_ID' => '10',
    'NEED_SEND_EMAIL' => 'Y',
    'EVENT_TYPE' => '2',
    'SEND_EMAIL' => array(
        0 => 'name',
        1 => 'surname',
        2 => 'email',
        3 => 'phone',
    ),
    'CACHE_TYPE' => 'A',
    'CACHE_TIME' => '3600',
));?>
 */