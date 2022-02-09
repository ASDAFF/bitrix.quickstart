<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/sberbank_new.php"));

$psTitle = GetMessage("SIBP_DTITLE");
$psDescription = GetMessage("SIBP_TITLE_DESC");

$arPSCorrespondence = array(
		"COMPANY_NAME" => array(
				"NAME" => GetMessage("SIBP_NAME"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"INN" => array(
				"NAME" => GetMessage("SIBP_INN"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"KPP" => array(
				"NAME" => GetMessage("SIBP_KPP"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"SETTLEMENT_ACCOUNT" => array(
				"NAME" => GetMessage("SIBP_NUMBER"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"BANK_NAME" => array(
				"NAME" => GetMessage("SIBP_NAME_BANK"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"BANK_BIC" => array(
				"NAME" => GetMessage("SIBP_REK"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"BANK_COR_ACCOUNT" => array(
				"NAME" => GetMessage("SIBP_CALC"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"ORDER_ID" => array(
				"NAME" => GetMessage("SIBP_ORDER_NUM"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"DATE_INSERT" => array(
				"NAME" => GetMessage("SIBP_DATA"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYER_CONTACT_PERSON" => array(
				"NAME" => GetMessage("SIBP_FIO"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYER_ZIP_CODE" => array(
				"NAME" => GetMessage("SIBP_ZIP"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYER_COUNTRY" => array(
				"NAME" => GetMessage("SIBP_COUNTRY"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYER_CITY" => array(
				"NAME" => GetMessage("SIBP_TOWN"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYER_ADDRESS_FACT" => array(
				"NAME" => GetMessage("SIBP_ADRESS"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("SIBP_SUMM"),
				"DESCR" => "",
				"VALUE" => "",
				"TYPE" => ""
			),
	);
?>