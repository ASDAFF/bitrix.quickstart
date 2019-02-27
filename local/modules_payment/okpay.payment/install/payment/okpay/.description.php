<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$psTitle = GetMessage("SPCP_DTITLE");
$psDescription = GetMessage("SPCP_DDESCR");

$arPSCorrespondence = array(
		"WalletID" => array(
				"NAME" => GetMessage("WalletID"),
				"DESCR" => GetMessage("WalletID_DESCR"),
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
