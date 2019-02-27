<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_platika';
$unit_id = 226272;
$account_id = 15662295;
$invoice = true;

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
