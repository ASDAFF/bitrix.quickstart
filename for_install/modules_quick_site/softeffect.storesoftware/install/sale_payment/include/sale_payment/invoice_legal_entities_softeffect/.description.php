<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/invoice_legal_entities_softeffect.php"));

$psTitle = GetMessage("SBLP_DTITLE");
$psDescription = GetMessage("SBLP_DDESCR");

$arPSCorrespondence = array(
		"DATE_INSERT" => array(
				"NAME" => GetMessage("SBLP_DATE"),
				"DESCR" => GetMessage("SBLP_DATE_DESC"),
				"VALUE" => "DATE_INSERT",
				"TYPE" => "ORDER"
			),

		"SELLER_NAME" => array(
				"NAME" => GetMessage("SBLP_SUPPLI"),
				"DESCR" => GetMessage("SBLP_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_ADDRESS" => array(
				"NAME" => GetMessage("SBLP_ADRESS_SUPPLI"),
				"DESCR" => GetMessage("SBLP_ADRESS_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_PHONE" => array(
				"NAME" => GetMessage("SBLP_PHONE_SUPPLI"),
				"DESCR" => GetMessage("SBLP_PHONE_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_INN" => array(
				"NAME" => GetMessage("SBLP_INN_SUPPLI"),
				"DESCR" => GetMessage("SBLP_INN_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_KPP" => array(
				"NAME" => GetMessage("SBLP_KPP_SUPPLI"),
				"DESCR" => GetMessage("SBLP_KPP_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_RS" => array(
				"NAME" => GetMessage("SBLP_ORDER_SUPPLI"),
				"DESCR" => GetMessage("SBLP_ORDER_SUPPLI_DESC"),
				"VALUE" => GetMessage("SBLP_ORDER_SUPPLI_VAL"),
				"TYPE" => ""
			),
		"SELLER_KS" => array(
				"NAME" => GetMessage("SBLP_KORORDER_SUPPLI"),
				"DESCR" => GetMessage("SBLP_KORORDER_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SELLER_BIK" => array(
				"NAME" => GetMessage("SBLP_BIK_SUPPLI"),
				"DESCR" => GetMessage("SBLP_BIK_SUPPLI_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"BUYER_NAME" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_DESC"),
				"VALUE" => "COMPANY_NAME",
				"TYPE" => "PROPERTY"
			),
		"BUYER_INN" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER_INN"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_INN_DESC"),
				"VALUE" => "INN",
				"TYPE" => "PROPERTY"
			),
		"BUYER_ADDRESS" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER_ADRES"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_ADRES_DESC"),
				"VALUE" => "ADDRESS",
				"TYPE" => "PROPERTY"
			),
		"BUYER_PHONE" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER_PHONE"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_PHONE_DESC"),
				"VALUE" => "PHONE",
				"TYPE" => "PROPERTY"
			),
		"BUYER_FAX" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER_FAX"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_FAX_DESC"),
				"VALUE" => "FAX",
				"TYPE" => "PROPERTY"
			),
		"BUYER_PAYER_NAME" => array(
				"NAME" => GetMessage("SBLP_CUSTOMER_PERSON"),
				"DESCR" => GetMessage("SBLP_CUSTOMER_PERSON_DESC"),
				"VALUE" => "PAYER_NAME",
				"TYPE" => "PROPERTY"
			),
		"PATH_TO_STAMP" => array(
				"NAME" => GetMessage("SBLP_PRINT"),
				"DESCR" => GetMessage("SBLP_PRINT_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			)
	);
?>