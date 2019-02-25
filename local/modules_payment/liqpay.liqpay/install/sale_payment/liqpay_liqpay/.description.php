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

include(GetLangFileName(dirname(__FILE__).'/', '/.description.php'));

$psTitle = GetMessage('LP_MODULE_NAME');
$psDescription = GetMessage('LP_MODULE_DESC');

$arPSCorrespondence = array(
	'PUBLIC_KEY' => array(
		'NAME'  => GetMessage('LP_PUBLIC_KEY'),
		'DESCR' => GetMessage('LP_PUBLIC_KEY_DESC'),
		'VALUE' => '',
		'TYPE'  => ''
	),
	'PRIVATE_KEY' => array(
		'NAME'  => GetMessage('LP_PRIVATE_KEY'),
		'DESCR' => GetMessage('LP_PRIVATE_KEY_DESC'),
		'VALUE' => '',
		'TYPE'  => ''
	),
	'AMOUNT' => array(
		'NAME'  => GetMessage('LP_AMOUNT'),
		'DESCR' => '',
		'VALUE' => 'SHOULD_PAY',
		'TYPE'  => 'ORDER'
	),
	'CURRENCY' => array(
		'NAME'  => GetMessage('LP_CURRENCY'),
		'DESCR' => '',
		'VALUE' => 'CURRENCY',
		'TYPE'  => 'ORDER'
	),
	'ORDER_ID' => array(
		'NAME'  => GetMessage('LP_ORDER_ID'),
		'DESCR' => '',
		'VALUE' => 'ID',
		'TYPE'  => 'ORDER'
	),
	'RESULT_URL' => array(
		'NAME'  => GetMessage('LP_RESULT_URL'),
		'DESCR' => GetMessage('LP_RESULT_URL_DESC'),
		'VALUE' => 'http://'.$_SERVER['HTTP_HOST'].'/personal/order/',
		'TYPE'  => ''
	),
	'SERVER_URL' => array(
		'NAME'  => GetMessage('LP_SERVER_URL'),
		'DESCR' => GetMessage('LP_SERVER_URL_DESC'),
		'VALUE' => 'http://'.$_SERVER['HTTP_HOST'].'/personal/ps_result.php',
		'TYPE'  => ''
	),
	'ACTION' => array(
		'NAME'  => GetMessage('LP_ACTION'),
		'DESCR' => GetMessage('LP_ACTION_DESC'),
		'VALUE' => 'https://www.liqpay.com/api/pay',
		'TYPE'  => ''
	),

);