<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$event = array();
$filters = array("TYPE_ID" => "LW_FEEDBACK_STATIC_FORM", "ACTIVE" => "Y");
$current_event_templates = CEventMessage::GetList($by="ID", $order="DESC", $filters);
while($current_event_template = $current_event_templates->GetNext()){
	$event[$current_event_template["ID"]] = "[".$current_event_template["ID"]."] ".$current_event_template["SUBJECT"];
}

$arFields = Array(
	"NAME" => GetMessage("MFP_NAME"),
	"PHONE" => GetMessage("MFP_PHONE"),
	"EMAIL" => GetMessage("MFP_EMAIL"),
	"MESSAGE" => GetMessage("MFP_MESSAGE")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"ABOUT_FORM" => array(
			"NAME" => GetMessage("MFP_ABOUT_FORM"),
			"SORT"	=> "100"
		),
		"SMS_RU" => array(
			"NAME" => GetMessage("MFP_SMS_RU"),
			"SORT" => "200"
		),
	),
	"PARAMETERS" => array(
		"FORM_NAME" => Array(
			"NAME" => GetMessage("MFP_FORM_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_DEFAULT_FORM_NAME"),
			"PARENT" => "ABOUT_FORM",
		),
		"FORM_DESCRIPTION" => Array(
			"NAME" => GetMessage("MFP_FORM_DESCRIPTION"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_DEFAULT_FORM_DESCRIPTION"),
			"PARENT" => "ABOUT_FORM",
		),
		"FORM_ID" => Array(
			"NAME" => GetMessage("MFP_FORM_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => time(),
			"PARENT" => "ABOUT_FORM",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("MFP_OK_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"),
			"PARENT" => "ABOUT_FORM",
		),
		"ERROR_TEXT" => Array(
			"NAME" => GetMessage("MFP_ERROR_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_ERROR_TEXT"),
			"PARENT" => "ABOUT_FORM",
		),
		"BUTTON_NAME" => Array(
			"NAME" => GetMessage("MFP_BUTTON_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_DEFAULT_BUTTON_NAME"),
			"PARENT" => "ABOUT_FORM",
		),
		"EMAIL_TO" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TO"),
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")),
			"PARENT" => "ABOUT_FORM",
		),
		"USED_FIELDS" => Array(
			"NAME" => GetMessage("MFP_USED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => $arFields,
			"DEFAULT"=>"",
			"COLS"=>25,
			"PARENT" => "ABOUT_FORM",
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("MFP_REQUIRED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => $arFields,
			"DEFAULT"=>"",
			"COLS"=>25,
			"PARENT" => "ABOUT_FORM",
		),
		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TEMPLATES"),
			"TYPE"=>"LIST",
			"VALUES" => $event,
			"DEFAULT"=>"",
			"MULTIPLE"=>"Y",
			"COLS"=>25,
			"PARENT" => "ABOUT_FORM",
		),
		//Настройка api для SMS.RU
		"SMS_RU_STATE" => Array(
			"NAME" => GetMessage("MFP_SMS_RU_STATE"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"N",
			"VALUES" => array(
				"ACTIVE" => GetMessage("MFP_SMS_RU_STATE_ACTIVE"),
				"DISABLED" => GetMessage("MFP_SMS_RU_STATE_DISABLED"),
				"TESTING" => GetMessage("MFP_SMS_RU_STATE_TESTING")
			),
			"DEFAULT"=>"DISABLED",
			"REFRESH" => "Y",
			"PARENT" => "SMS_RU",
			"SORT"=>"10",
		),

	)
);
if ($arCurrentValues["SMS_RU_STATE"] == 'ACTIVE' or $arCurrentValues["SMS_RU_STATE"] == 'TESTING') {

	$arComponentParameters["PARAMETERS"]["SMS_RU_API_KEY"]=array(
		"NAME" => GetMessage("MFP_SMS_RU_API_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => "SMS_RU",
		"SORT"=>"20",
	);
	$arComponentParameters["PARAMETERS"]["SMS_RU_FROM"]=array(
		"NAME" => GetMessage("MFP_SMS_RU_FROM"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => "SMS_RU",
		"SORT"=>"30",
	);
	$arComponentParameters["PARAMETERS"]["SMS_RU_ADMIN_NUMBER"]=array(
		"NAME" => GetMessage("MFP_SMS_RU_ADMIN_NUMBER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => "SMS_RU",
		"SORT"=>"40",
	);
	$arComponentParameters["PARAMETERS"]["SMS_RU_TEMPLATE"]=array(
		"NAME" => GetMessage("MFP_SMS_RU_TEMPLATE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage('MFP_SMS_RU_TEMPLATE_DEFAULT'),
		"PARENT" => "SMS_RU",
		"SORT"=>"50",
	);
}


?>