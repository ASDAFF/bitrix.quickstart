<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ALFA_EMAIL_TO" => array(
			"NAME" => GetMessage("ALFA_MSG_EMAIL_TO"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => COption::GetOptionString("main", "email_from", ""),
		),
		"ALFA_USE_CAPTCHA" => array(
			"NAME" => GetMessage("ALFA_MSG_USE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"PARENT" => "BASE",
			"VALUE" => "Y",
		),
		"ALFA_MESSAGE_AGREE" => array(
			"NAME" => GetMessage("ALFA_MSG_MESSAGE_AGREE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => GetMessage("ALFA_MSG_MESSAGE_AGREE_DEFAULT"),
		),
		"AJAX_MODE" => array(),
	)
);

?>