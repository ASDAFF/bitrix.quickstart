<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/qiwi.php"));

$psTitle = GetMessage("SALE_TITLE");
$psDescription = GetMessage("SALE_DESCRIPTION", array("#CRON_SCRIPT_PATH#" => str_replace($_SERVER["DOCUMENT_ROOT"], "", dirname(__FILE__)."/status.php")));

$arPSCorrespondence = array(
		"SHOP_ID" => array(
				"NAME" => GetMessage("SALE_SHOP_ID"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"SHOP_PASS" => array(
				"NAME" => GetMessage("SALE_SHOP_PASS"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"CLIENT_PHONE" => array(
				"NAME" => GetMessage("SALE_CLIENT_PHONE"),
				"DESCR" => "",
				"VALUE" => "PHONE",
				"TYPE" => "PROPERTY"
			),
		"ORDER_ID" => array(
				"NAME" => GetMessage("SALE_ORDER_ID"),
				"DESCR" => "",
				"VALUE" => "ID",
				"TYPE" => "ORDER"
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("SALE_SHOULD_PAY"),
				"DESCR" => "",
				"VALUE" => "SHOULD_PAY",
				"TYPE" => "ORDER"
			),
		"BILL_LIFETIME" => array(
				"NAME" => GetMessage("SALE_BILL_LIFETIME"),
				"DESCR" => GetMessage("SALE_BILL_LIFETIME_DESCR"),
				"VALUE" => "240",
				"TYPE" => ""
			),
	);
?>
