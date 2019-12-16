<?php

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Context;
use \Bitrix\Sale\Location;
use \Bitrix\Main\Loader;

if (!isset($_POST['siteId']) || !is_string($_POST['siteId'])) {
    die();
}

define('SITE_ID', $_POST['siteId']);
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

if (!Loader::includeModule('sale')) {
    return;
}

$request = Context::getCurrent()->getRequest();
$params = $request->getPost('arParams');
$templateName = $request->getPost('templateName');

$params['ELEMENT_ID'] = (int) $params['ELEMENT_ID'];
$params['AJAX_CALL'] = 'Y';

if (empty($params['LOCATION_FROM'])) {
    $params['LOCATION_FROM'] = Option::get('sale', 'location');
}

if (empty($params['LOCATION_TO']) && Loader::includeModule('redsign.location')) {
    if (isset($_SESSION['RSLOCATION']['LOCATION'])) {
        $locationId = $_SESSION['RSLOCATION']['LOCATION']['ID'];
        if ($location = Location\LocationTable::getRowById($locationId)) {
            $params['LOCATION_TO'] = $location['CODE'];
        }
        
    } else {
        $detectLocation = CRS_Location::GetCityName();
        $cityName = $detectLocation['CITY_NAME'];
        $locationIterator = Location\LocationTable::getList(array(
            'filter' => array(
                '=NAME.NAME' => $cityName
            ),
            'select' => array(
                'ID',
                'CODE'
            )
        ));
        if($locationIterator->fetch()) {
            $params['LOCATION_TO'] = $location['CODE'];
        }
    }
}

if (empty($params['LOCATION_ZIP']) && strlen($params['LOCATION_TO']) > 0) {
    $locationIterator = Location\ExternalTable::getList(array(
        'filter' => array(
            '=SERVICE.CODE' => \CSaleLocation::ZIP_EXT_SERVICE_CODE,
            '=LOCATION.CODE' => $params['LOCATION_TO'],
        ),
        'select' => array('ID', 'XML_ID'),
    ));

    if ($location = $locationIterator->fetch()) {
        $params['LOCATION_ZIP'] = $location['XML_ID'];
    }
}

$APPLICATION->IncludeComponent(
    'redsign:delivery.calculator',
    $templateName,
    $params,
    false
);
