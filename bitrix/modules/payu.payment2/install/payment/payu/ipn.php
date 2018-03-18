<?php
global $error;
$error = 'No Error';

if (!$_POST) {
    $error .= 'Invalid IPN request';
}

// Подключение Api Битрикса и модулй sale и компонента payment
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/payu/PayU_Bitrix.cls.php");

if (!CModule::IncludeModule("sale")) {
    die('Cant load module "sale"');
}

$APPLICATION->IncludeComponent("bitrix:sale.order.payment", "", array());

/* Обработка запроса */
$order = explode('_',$_POST['REFNOEXT']);
$error .= 'Invalid Order ID' . print_r($order, true);
$order = CSaleOrder::getById($order['1']);

/* Подключение обработчика PayU */
$PaySystem = CSalePaySystem::GetByID($order['PAY_SYSTEM_ID'], $order['PERSON_TYPE_ID'] );
$payUParams = unserialize($PaySystem['PSA_PARAMS']);

$PayUOptions = array(
    'merchant'    => $payUParams["MERCHANT"]['VALUE'],
    'secretkey'   => $payUParams["SECURE_KEY"]['VALUE'],
    'encoding'    => 'UTF-8',
);

/* @var $payU PayU_bitrix */

// Проверка запроса системы PayU на валидность.
$payU = PayU_Bitrix::getInst()->setOptions($PayUOptions)->IPN();
if (!$payU->checkHash) {
    $error .= 'Invalid Signature';
}

$paymentStatusSuccess = array(
    'PAYMENT_AUTHORIZED',
    'COMPLETE',
    'TEST',
);

$paymentStatusFiled = array(
    'REVERSED',
    'REFUND',
);

$arField = array();

$arField['PS_STATUS_CODE'] = $_POST['ORDERSTATUS'];
$arField['PS_STATUS_DESCRIPTION'] = $_POST['ORDERSTATUS']. " " . $_POST['PAYMETHOD'];
$arField['PS_STATUS_MESSAGE'] = "REFNO: {$_POST['REFNO']} , REFNOEXT: {$_POST['REFNOEXT']} ";
$arField['PS_SUM'] = $_POST['IPN_TOTALGENERAL'];
$arField['PS_CURRENCY'] = $_POST['CURRENCY'];
$arField['PS_RESPONSE_DATE'] = date( "d.m.Y H:i:s" );
$arField['PAY_VOUCHER_NUM'] = $_POST['REFNO'];
$arField['PAY_VOUCHER_DATE'] = $_POST['IPN_DATE'];

if (in_array($_POST['ORDERSTATUS'], $paymentStatusSuccess)) {
    $arField['PS_STATUS'] = 'Y';
    $result = CSaleOrder::PayOrder($order['ID'], 'Y', false, null, 0, $arField);
} elseif (in_array($_POST['ORDERSTATUS'], $paymentStatusFiled)) {
  $arField['PS_STATUS'] = 'N';
    $result = CSaleOrder::PayOrder($order['ID'], 'N', false, null, 0, $arField);
} else {
    $error .= 'Invalid Order Status';
}

CSaleOrder::update($order['ID'],$arField, true);
/* Ответ системе PayU IPN */

if ($payU->checkHash) {
    echo $payU->getIPNAnswer();
} else {
    $error .= 'Signature Invalid';
}

// Bitrix Admin Api (pilog After)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>