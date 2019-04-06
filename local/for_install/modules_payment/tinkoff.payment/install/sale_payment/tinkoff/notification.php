<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include(GetLangFileName(dirname(__FILE__)."/", "/tinkoff.php"));

include(dirname(__FILE__)."/sdk/tinkoff_autoload.php");

$orderID = $_REQUEST['OrderId'];
$order = CSaleOrder::GetByID($orderID);

if (!$order) {

    $arFilter = array(
        "ACCOUNT_NUMBER" => $orderID,
    );
    $accountNumberList = CSaleOrder::GetList(array("ACCOUNT_NUMBER" => "ASC"), $arFilter);
    $order = $accountNumberList->arResult[0];
}

if ($order) {
    $orderID = $order['ID'];
} else {
    die('NOTOK'); // ORDER NOT FOUND
}

CSalePaySystemAction::InitParamArrays($orderID, $orderID);

$bankHandler = new Tinkoff(CSalePaySystemAction::GetParamValue("TERMINAL_ID"), CSalePaySystemAction::GetParamValue("SHOP_SECRET_WORD"), CSalePaySystemAction::GetParamValue("TINKOFF_PAYMENT_URL"));

try{
    $bankHandler->checkNotification($_POST);
} catch(TinkoffException $e){
    die($e->getMessage());
}

if ($bankHandler->isOrderFailed()) {
    CSaleOrder::PayOrder($orderID, 'N');
}
elseif ($bankHandler->isOrderPaid()) {
    CSaleOrder::PayOrder($orderID, 'Y');
    CSaleOrder::StatusOrder($orderID, "P");
}
elseif ($bankHandler->isOrderRefunded()) {
    CSaleOrder::PayOrder($orderID, 'N');
    CSaleOrder::StatusOrder($orderID, "N");
    CSaleOrder::CancelOrder($orderID, "Y", "оплата заказа отменена");
}
else {
    die('OK');
}

die('OK');

?>