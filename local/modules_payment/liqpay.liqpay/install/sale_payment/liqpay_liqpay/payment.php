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
	include(GetLangFileName(dirname(__FILE__).'/', '/payment.php'));

	$order_id = (strlen(CSalePaySystemAction::GetParamValue('ORDER_ID')) > 0)
		? CSalePaySystemAction::GetParamValue('ORDER_ID')
		: $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID'];

	$amount = (strlen(CSalePaySystemAction::GetParamValue('AMOUNT')) > 0)
		? CSalePaySystemAction::GetParamValue('AMOUNT')
		: $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['SHOULD_PAY'];

	$currency = (strlen(CSalePaySystemAction::GetParamValue('CURRENCY')) > 0)
		? CSalePaySystemAction::GetParamValue('CURRENCY')
		: $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['CURRENCY'];

	$result_url = CSalePaySystemAction::GetParamValue('RESULT_URL');
	$server_url = CSalePaySystemAction::GetParamValue('SERVER_URL');
	$public_key = CSalePaySystemAction::GetParamValue('PUBLIC_KEY');
	$type = 'buy';

    $description = 'Order #'.$order_id;

    $order_id .= '#'.time();

	if ($currency == 'RUR') { $currency = 'RUB'; }

	$private_key = CSalePaySystemAction::GetParamValue('PRIVATE_KEY');
	$signature = '';
	if ($private_key) {
        $signature = base64_encode(sha1(join('',compact(
            'private_key',
            'amount',
            'currency',
            'public_key',
            'order_id',
            'type',
            'description',
            'result_url',
            'server_url'
        )),1));
	}
	$language = LANGUAGE_ID;

    if (!$action = CSalePaySystemAction::GetParamValue('ACTION')) {
        $action = 'https://www.liqpay.com/api/pay';
    }
?>

<?=GetMessage('PAYMENT_DESCRIPTION_PS')?> <b>www.liqpay.com</b>.<br /><br />
<?=GetMessage('PAYMENT_DESCRIPTION_SUM')?>: <b><?=CurrencyFormat($amount, $currency)?></b><br /><br />

<form method="POST" action="<?=$action?>" accept-charset="utf-8">
    <input type="hidden" name="public_key" value="<?=$public_key?>" />
    <input type="hidden" name="amount" value="<?=$amount?>" />
    <input type="hidden" name="currency" value="<?=$currency?>" />
    <input type="hidden" name="description" value="<?=$description?>" />
	<input type="hidden" name="order_id" value="<?=$order_id?>" />
    <input type="hidden" name="result_url" value="<?=$result_url?>" />
	<input type="hidden" name="server_url" value="<?=$server_url?>" />
    <input type="hidden" name="type" value="<?=$type?>" />
    <input type="hidden" name="signature" value="<?=$signature?>" />
    <input type="hidden" name="language" value="<?=$language?>" />
    <input type="image" src="//static.liqpay.com/buttons/p1ru.radius.png" />
</form>