<?if ( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
{
	die();
}

include( GetLangFileName(dirname(__FILE__) . "/", "/.description.php") );


$psTitle = GetMessage('PEYU_PSTITLE');
$psDescription = "<a href=\"http://payu.ru\" target=\"_blank\">http://payu.ru</a>";

$payUPath = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', dirname(__FILE__));
$payUPublicPath = ($_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] .  str_replace('\\', '/', $payUPath);

$IPNDefaultScript = $payUPublicPath . '/ipn.php';
$ResultDefaultScript = $payUPublicPath . '/result_payment.php';

$array = array(
	'payu_merchant',
	'payu_secret_key',
	'payu_lu_url',
	'payu_price_currency',
	'payu_debug_mode',
	'payu_back_ref',
	'payu_language',
	'payu_VAT'
);

$arPSCorrespondence = array(
	"MERCHANT"       => array(
		"NAME"  => GetMessage("PEYU_MERCHANT"),
		"DESCR" => GetMessage("PEYU_MERCHANT"),
		"VALUE" => "PAYUTEST",
		"TYPE"  => ""
	),
	"SECURE_KEY"     => array(
		"NAME"  => GetMessage("PEYU_SECURE_KEY"),
		"DESCR" => GetMessage("PEYU_SECURE_KEY"),
		"VALUE" => "",
		"TYPE"  => ""
	),
	"LU_URL"         => array(
		"NAME"  => GetMessage("PEYU_LU_URL"),
		"DESCR" => GetMessage("PEYU_DESC_LU_URL"),
		"VALUE" => "https://secure.payu.ru/order/lu.php",
		"TYPE"  => ""
	),
    "IPN_LINK" => array(
        "NAME" => GetMessage("PEYU_IPN_LINK"),
        "DESCR" => GetMessage("PEYU_IPN_LINK_DESC"),
        "VALUE" => $IPNDefaultScript,
        "TYPE" => "",
    ),
    "BACK_REF"       => array(
        "NAME"  => GetMessage("PEYU_BACK_REF"),
        "DESCR" => GetMessage("PEYU_DESC_BACK_REF"),
        "VALUE" => $ResultDefaultScript,
        "TYPE"  => ""
    ),
	"PRICE_CURRENCY" => array(
		"NAME"  => GetMessage("PEYU_PRICE_CURRENCY"),
		"DESCR" => GetMessage("PEYU_DESC_PRICE_CURRENCY"),
		"VALUE" => "RUB",
		"TYPE"  => ""
	),
	"DEBUG_MODE"     => array(
		"NAME"  => GetMessage("PEYU_DEBUG_MODE"),
		"DESCR" => GetMessage("PEYU_DESC_DEBUG_MODE"),
		"VALUE" => "1",
		"TYPE"  => ""
	),
	"LANGUAGE"       => array(
		"NAME"  => GetMessage("PEYU_LANGUAGE"),
		"DESCR" => GetMessage("PEYU_DESC_LANGUAGE"),
		"VALUE" => "RU",
		"TYPE"  => ""
	),
	"AUTOMODE"       => array(
		"NAME"  => GetMessage("PEYU_AUTOMODE"),
		"DESCR" => GetMessage("PEYU_DESC_AUTOMODE"),
		"VALUE" => array(
			'Y' =>   array('NAME' => GetMessage("PEYU_AUTOMODE_YES")),
			'N' =>   array('NAME' => GetMessage("PEYU_AUTOMODE_NO")),
		),
		"TYPE" => "SELECT"
	),
    "USE_VAT" => array(
        "NAME" => GetMessage("PEYU_USE_VAT"),
        "DESCR" => GetMessage("PEYU_USE_VAT_DESC"),
        "VALUE" => array(
            "NET" => array('NAME' => GetMessage("PEYU_USE_VAT_TRUE")),
            "GROSS" => array('NAME' => GetMessage("PEYU_USE_VAT_FALSE")),
        ),
        "TYPE" => "SELECT",
    ),
    "VAT_RATE"       => array(
		"NAME"  => GetMessage("PEYU_VAT_RATE"),
		"DESCR" => GetMessage("PEYU_VAT_RATE_DESC"),
		"VALUE" => array(
			'19' =>   array('NAME' => GetMessage("PEYU_VAT_19")),
			'0' =>   array('NAME' => GetMessage("PEYU_VAT_0")),
		),
		"TYPE" => "SELECT"
	),
);
?>
