<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arShowFieldsList = array(
	"NONE" => GetMessage("RS.FLYAWAY.NONE"),
	"RS_NAME" => GetMessage("RS.FLYAWAY.NAME"),
	"RS_PHONE" => GetMessage("RS.FLYAWAY.PHONE"),
	"RS_PERSONAL_SITE" => GetMessage("RS.FLYAWAY.PERSONAL_SITE"),
	"RS_ORGANISATION_NAME" => GetMessage("RS.FLYAWAY.ORGANISATION_NAME"),
	"RS_EMAIL" => GetMessage("RS.FLYAWAY.EMAIL"),
	"RS_TEXTAREA" => GetMessage("RS.FLYAWAY.TEXTAREA"),
);

$arRequiresFieldsList = array(
	"NONE" => GetMessage("RS.FLYAWAY.NONE"),
	"RS_NAME" => GetMessage("RS.FLYAWAY.NAME"),
	"RS_PHONE" => GetMessage("RS.FLYAWAY.PHONE"),
	"RS_PERSONAL_SITE" => GetMessage("RS.FLYAWAY.PERSONAL_SITE"),
	"RS_ORGANISATION_NAME" => GetMessage("RS.FLYAWAY.ORGANISATION_NAME"),
	"RS_EMAIL" => GetMessage("RS.FLYAWAY.EMAIL"),
	"RS_TEXTAREA" => GetMessage("RS.FLYAWAY.TEXTAREA"),
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"EVENT_TYPE" => array(
			"NAME" => GetMessage("RS.FLYAWAY.EVENT_TYPE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"FORM_TITLE" => array(
			"NAME" => GetMessage("RS.FLYAWAY.FORM_TITLE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"FORM_DESCRIPTION" => array(
			"NAME" => GetMessage("RS.FLYAWAY.FORM_DESCRIPTION"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"EMAIL_TO" => array(
			"NAME" => GetMessage("RS.FLYAWAY.EMAIL_TO"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => COption::GetOptionString("main", "email_from", ""),
		),
		"SHOW_FIELDS" => Array(
			"NAME" => GetMessage("RS.FLYAWAY.SHOW_FIELDS"), 
			"TYPE" => "LIST", 
			"MULTIPLE" => "Y", 
			"VALUES" => $arShowFieldsList,
			"PARENT" => "BASE",
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("RS.FLYAWAY.REQUIRED_FIELDS"), 
			"TYPE" => "LIST", 
			"MULTIPLE" => "Y", 
			"VALUES" => $arShowFieldsList, 
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => array(
			"NAME" => GetMessage("RS.FLYAWAY.USE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"PARENT" => "BASE",
			"VALUE" => "Y",
		),
		"MESSAGE_AGREE" => array(
			"NAME" => GetMessage("RS.FLYAWAY.MESSAGE_AGREE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => GetMessage("RS.FLYAWAY.MESSAGE_AGREE_DEFAULT"),
		),
		"RS_FLYAWAY_EXT_FIELDS_COUNT" => array(
			"NAME" => GetMessage("RS.FLYAWAY.EXT_FIELDS_COUNT"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "0",
			"REFRESH" => "Y",
		),
		"CACHE_TIME"  => array(
			"PARENT" => "CACHE_SETTINGS",
			"DEFAULT" => 3600
		),
		"AJAX_MODE" => array(),
	)
);

$count = IntVal( $arCurrentValues['RS_FLYAWAY_EXT_FIELDS_COUNT'] );
if( $count>0 ) {
	for($i=0; $i<$count; $i++) {
		$arComponentParameters['PARAMETERS']['RS_FLYAWAY_FIELD_'.$i.'_NAME'] = array(
			"NAME" => GetMessage("RS.FLYAWAY.EXT_FIELDS_NAME").($i+1),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		);
	}
}