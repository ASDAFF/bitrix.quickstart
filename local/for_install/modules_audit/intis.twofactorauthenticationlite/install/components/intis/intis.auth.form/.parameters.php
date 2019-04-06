<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arComponentParameters = array(
	"PARAMETERS" => array(
		"REGISTER_URL" => array(
			"NAME" => GetMessage("TWO_FACTOR_AUTH_REGISTER_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"FORGOT_PASSWORD_URL" => array(
			"NAME" => GetMessage("TWO_FACTOR_AUTH_FORGOT_PASSWORD_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"PROFILE_URL" => array(
			"NAME" => GetMessage("TWO_FACTOR_AUTH_PROFILE_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		
		"INTIS_AUTH_URL" => array(
			"NAME" => GetMessage("TWO_FACTOR_AUTH_HANDLER_PATH"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	),
);
?>