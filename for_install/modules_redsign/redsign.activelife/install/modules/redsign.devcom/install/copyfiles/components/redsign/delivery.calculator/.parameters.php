<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Sale\Delivery;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arDeliveries = array();

if (!Loader::includeModule('sale') || !Loader::includeModule('iblock')) {
    return;
}

$arDeliveryObjects = Delivery\Services\Manager::getActiveList();
$arDeliveries = array();
foreach ($arDeliveryObjects as $deliveryId => $arDelivery) {
    $arDeliveries[$deliveryId] = $arDelivery['NAME'];
}

//\Bitrix\Main\Diag\Debug::dump($arDeliveries);

$arComponentParameters = array(
    'PARAMETERS' => array(
        'ELEMENT_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_ELEMENT_ID'),
            'TYPE' => 'STRING',
        ),
        'LOCATION_FROM' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_LOCATION_FROM'),
            'TYPE' => 'STRING',
        ),
        'LOCATION_TO' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_LOCATION_TO'),
            'TYPE' => 'STRING',
        ),
        'LOCATION_ZIP' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_LOCATION_ZIP'),
            'TYPE' => 'STRING',
        ),
        'QUANTITY' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_QUANTITY'),
            'TYPE' => 'STRING',
        ),
        'CURRENCY' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_CURRENCY'),
            'TYPE' => 'STRING'
        ),
        'DELIVERY' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('RSDC_PARAMETER_DELIVERY'),
            'TYPE' => 'LIST',
            'VALUES' => $arDeliveries,
            'MULTIPLE' => 'Y'
        )
    ),
);

//die();
