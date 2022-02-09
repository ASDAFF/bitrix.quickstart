<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/liqpay.php"));

$psTitle = "LiqPAY.com";
$psDescription = "";

$arPSCorrespondence = array(
		"MERCHANT_ID" => array(
				"NAME" => GetMessage("SALE_MERCHANT_ID"),
				"DESCR" => GetMessage("SALE_MERCHANT_ID_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SIGN" => array(
				"NAME" => GetMessage("SALE_SIGN"),
				"DESCR" => GetMessage("SALE_SIGN_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"PATH_TO_RESULT_URL" => array(
				"NAME" => GetMessage("SALE_PATH_TO_RESULT_URL"),
				"DESCR" => GetMessage("SALE_PATH_TO_RESULT_URL_DESC"),
				"VALUE" => "http://" . $_SERVER["HTTP_HOST"] . "/personal/orders/",
				"TYPE" => ""
			),
		"PATH_TO_SERVER_URL" => array(
				"NAME" => GetMessage("SALE_PATH_TO_SERVER_URL"),
				"DESCR" => GetMessage("SALE_PATH_TO_SERVER_URL_DESC"),
				"VALUE" => "http://" . $_SERVER["HTTP_HOST"] . "/personal/ps_result.php",
				"TYPE" => ""
			),
		"ORDER_ID" => array(
				"NAME" => GetMessage("SALE_ORDER_ID"),
				"DESCR" => "",
				"VALUE" => "ID",
				"TYPE" => "ORDER"
			),
		"CURRENCY" => array(
				"NAME" => GetMessage("SALE_CURRENCY"),
				"DESCR" => "",
				"VALUE" => "CURRENCY",
				"TYPE" => "ORDER"
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("SALE_SHOULD_PAY"),
				"DESCR" => "",
				"VALUE" => "SHOULD_PAY",
				"TYPE" => "ORDER"
			),
		"PHONE" => array(
				"NAME" => GetMessage("SALE_PHONE"),
				"DESCR" => "",
				"VALUE" => "PHONE",
				"TYPE" => "PROPERTY"
			),
		"PAY_METHOD" => array(
				"NAME" => GetMessage("PAYMENT_PM"),
				"DESCR" => GetMessage("PAYMENT_PM_DESCRIPTION"),
				"VALUE" => "",
				"TYPE" => ""
			),
	);
?>
