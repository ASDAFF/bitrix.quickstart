<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

if (!function_exists('getCurrenHost'))
{
    function getCurrenHost()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'];
    
        return $currentUrl;
    }
}

$psTitle = GetMessage("PAYANYWAY_TITLE");
$psDescription = GetMessage("PAYANYWAY_DESC");

$arPSCorrespondence = array(
		"MNT_PAYMENT_SERVER" => array(
				"NAME" => GetMessage("PAYANYWAY_SERVER"),
				"TYPE" => "SELECT",
				"VALUE" => array(
						"www.payanyway.ru" => array("NAME" => "www.payanyway.ru"),
						"demo.moneta.ru" => array("NAME" => "demo.moneta.ru")
				),
			),
		"MNT_ID" => array(
				"NAME" => GetMessage("PAYANYWAY_ID"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"MNT_AMOUNT" => array(
				"NAME" => GetMessage("PAYANYWAY_AMOUNT"),
				"VALUE" => "SHOULD_PAY",
				"TYPE" => "ORDER"
			),
		"DATA_INTEGRITY_CODE" => array(
				"NAME" => GetMessage("DATA_INTEGRITY_CODE"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"MNT_TEST_MODE" => array(
				"NAME" => GetMessage("PAYANYWAY_TEST_MODE"),
				"TYPE" => "SELECT",
				"VALUE" => array(
						"0" => array("NAME" => GetMessage("PAYANYWAY_TEST_MODE_FALSE")),
						"1" => array("NAME" => GetMessage("PAYANYWAY_TEST_MODE_TRUE"))
				),
			),
		"PAYANYWAY_PAY_URL" => array(
				"NAME" => GetMessage("PAYANYWAY_PAY_URL"),
				"DESCR" => GetMessage("PAYANYWAY_PAY_URL_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYANYWAY_LOGIN" => array(
				"NAME" => GetMessage("PAYANYWAY_LOGIN"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"PAYANYWAY_PASSWORD" => array(
				"NAME" => GetMessage("PAYANYWAY_PASSWORD"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"CHANGE_ORDER_STATUS" => array(
				"NAME" => GetMessage("PAYANYWAY_CHANGE_ORDER_STATUS"),
				"DESCR" => GetMessage("PAYANYWAY_CHANGE_ORDER_STATUS_DESC"),
				"VALUE" => "Y",
				"TYPE" => ""
			),
	);

?>
