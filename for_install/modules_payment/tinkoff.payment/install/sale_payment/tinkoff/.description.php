<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/tinkoff.php"));

$psTitle = GetMessage("SALE_TINKOFF_TITLE");
$psDescription = GetMessage("SALE_TINKOFF_DESCRIPTION");

$arPSCorrespondence = array(
	"TERMINAL_ID" => array(
		"NAME" => GetMessage("SALE_TINKOFF_TERMINAL_ID_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_TERMINAL_ID_DESCR"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"ORDER_ID" => array(
		"NAME" => GetMessage("SALE_TINKOFF_ORDER_ID_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_ORDER_ID_DESCR"),
		"VALUE" => "ID",
		"TYPE" => "ORDER"
	),
	"SHOP_SECRET_WORD" => array(
		"NAME" => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"TINKOFF_PAYMENT_URL" => array(
		"NAME" => GetMessage("SALE_TINKOFF_PAYMENT_URL_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_PAYMENT_URL_DESCR"),
		"VALUE" => "https://securepay.tinkoff.ru/rest/",
		"TYPE" => ""
	),
	"SHOULD_PAY" => array(
		"NAME" => GetMessage("SALE_TINKOFF_SHOULD_PAY_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_DESC_SHOULD_PAY_DESCR"),
		"VALUE" => "SHOULD_PAY",
		"TYPE" => "ORDER"
	),
	"PAYMENT_DESCRIPTION" => array(
		"NAME" => GetMessage("SALE_TINKOFF_DESCRIPTION_NAME"),
		"DESCR" => GetMessage("SALE_TINKOFF_DESCRIPTION_DESCR"),
		"VALUE" => GetMessage("SALE_TINKOFF_DESCRIPTION_VALUE"),
		"TYPE" => ""
	),
);
?>