<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/.description.php"));

$psTitle = GetMessage("ONPAY.SALE_PAYMENT_ONPAY__TITLE");
$psDescription = GetMessage("ONPAY.SALE_PAYMENT_ONPAY__DESCRIPTION");

$arPSCorrespondence = array(
		"EMAIL" => array(
				"NAME" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__EMAIL"),
				"DESCR" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__EMAIL_DESCR"),
				"VALUE" => "EMAIL",
				"TYPE" => "USER"
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__SHOULD_PAY"),
				"DESCR" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__SHOULD_PAY_DESCR"),
				"VALUE" => "SHOULD_PAY",
				"TYPE" => "ORDER"
			),
		"CURRENCY" => array(
				"NAME" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__CURRENCY"),
				"DESCR" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__CURRENCY_DESCR"),
				"VALUE" => "CURRENCY",
				"TYPE" => "ORDER"
			),
		"ORDER_ID" => array(
				"NAME" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__ORDER_ID"),
				"DESCR" => GetMessage("ONPAY.SALE_PAYMENT_ONPAY__ORDER_ID_DESCR"),
				"VALUE" => "ID",
				"TYPE" => "ORDER"
			),
	);
?>