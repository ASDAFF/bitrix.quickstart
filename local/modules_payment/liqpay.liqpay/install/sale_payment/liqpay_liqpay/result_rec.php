<?php
/**
 * Liqpay Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        Liqpay
 * @package         liqpay.liqpay
 * @version         0.0.1
 * @author          Liqpay
 * @copyright       Copyright (c) 2014 Liqpay
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * EXTENSION INFORMATION
 *
 * 1C-Bitrix        14.0
 * LIQPAY API       https://www.liqpay.com/ru/doc
 *
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

$success =
    isset($_POST['amount']) &&
    isset($_POST['currency']) &&
    isset($_POST['public_key']) &&
    isset($_POST['description']) &&
    isset($_POST['order_id']) &&
    isset($_POST['type']) &&
    isset($_POST['status']) &&
    isset($_POST['transaction_id']) &&
    isset($_POST['sender_phone']);

if (!$success) { die(); }

$amount = $_POST['amount'];
$currency = $_POST['currency'];
$public_key = $_POST['public_key'];
$description = $_POST['description'];
$order_id = $_POST['order_id'];
$type = $_POST['type'];
$status = $_POST['status'];
$transaction_id = $_POST['transaction_id'];
$sender_phone = $_POST['sender_phone'];
$insig = $_POST['signature'];

$real_order_id = explode('#', $order_id);
$real_order_id = $real_order_id[0];

if ($real_order_id <= 0) { die(); }
if (!($arOrder = CSaleOrder::GetByID($real_order_id))) { die(); }
if ($arOrder['PAYED'] == 'Y') { die(); }

CSalePaySystemAction::InitParamArrays($arOrder, $arOrder['ID']);
$private_key = CSalePaySystemAction::GetParamValue('PRIVATE_KEY');

$gensig = base64_encode(sha1(join('',compact(
    'private_key',
    'amount',
    'currency',
    'public_key',
    'order_id',
    'type',
    'description',
    'status',
    'transaction_id',
    'sender_phone'
)),1));

if ($insig != $gensig) { die(); }

if ($status == 'success') {
    $sDescription = '';
    $sStatusMessage = '';

    $sDescription .= 'sender phone: '.$sender_phone.'; ';
    $sDescription .= 'amount: '.$amount.'; ';
    $sDescription .= 'currency: '.$currency.'; ';

    $sStatusMessage .= 'status: '.$status.'; ';
    $sStatusMessage .= 'transaction_id: '.$transaction_id.'; ';
    $sStatusMessage .= 'order_id: '.$real_order_id.'; ';

    $arFields = array(
        'PS_STATUS' => 'Y',
        'PS_STATUS_CODE' => $status,
        'PS_STATUS_DESCRIPTION' => $sDescription,
        'PS_STATUS_MESSAGE' => $sStatusMessage,
        'PS_SUM' => $amount,
        'PS_CURRENCY' => $currency,
        'PS_RESPONSE_DATE' => date(CDatabase::DateFormatToPHP(CLang::GetDateFormat('FULL', LANG))),
    );

    CSaleOrder::PayOrder($arOrder['ID'], 'Y');
    CSaleOrder::Update($arOrder['ID'], $arFields);
}
