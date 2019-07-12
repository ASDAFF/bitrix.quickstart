<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$psTitle = GetMessage("SPCP_DTITLE");
$psDescription = GetMessage("SPCP_DDESCR");

$arPSCorrespondence = array(
		"ShopLogin" => array(
				"NAME" => GetMessage("ShopLogin"),
				"DESCR" => GetMessage("ShopLogin_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"ShopPassword" => array(
				"NAME" => GetMessage("ShopPassword"),
				"DESCR" => GetMessage("ShopPassword_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"ShopPassword2" => array(
				"NAME" => GetMessage("ShopPassword2"),
				"DESCR" => GetMessage("ShopPassword_DESCR2"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"OrderDescr" => array(
				"NAME" => GetMessage("OrderDescr"),
				"DESCR" => GetMessage("OrderDescr_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("SHOULD_PAY"),
				"DESCR" => GetMessage("SHOULD_PAY_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"CURRENCY" => array(
				"NAME" => GetMessage("CURRENCY"),
				"DESCR" => GetMessage("CURRENCY_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"DATE_INSERT" => array(
				"NAME" => GetMessage("DATE_INSERT"),
				"DESCR" => GetMessage("DATE_INSERT_DESCR"),
				"VALUE" => "",
				"TYPE" => ""
			),
	);
?>
