<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arShowFieldsList = array(
	"NONE" => GetMessage("RS.MONOPOLY.NONE"),
	"RS_NAME" => GetMessage("RS.MONOPOLY.NAME"),
	"RS_PHONE" => GetMessage("RS.MONOPOLY.PHONE"),
	"RS_PERSONAL_SITE" => GetMessage("RS.MONOPOLY.PERSONAL_SITE"),
	"RS_ORGANISATION_NAME" => GetMessage("RS.MONOPOLY.ORGANISATION_NAME"),
	"RS_EMAIL" => GetMessage("RS.MONOPOLY.EMAIL"),
	"RS_TEXTAREA" => GetMessage("RS.MONOPOLY.TEXTAREA"),
);

$arRequiresFieldsList = array(
	"NONE" => GetMessage("RS.MONOPOLY.NONE"),
	"RS_NAME" => GetMessage("RS.MONOPOLY.NAME"),
	"RS_PHONE" => GetMessage("RS.MONOPOLY.PHONE"),
	"RS_PERSONAL_SITE" => GetMessage("RS.MONOPOLY.PERSONAL_SITE"),
	"RS_ORGANISATION_NAME" => GetMessage("RS.MONOPOLY.ORGANISATION_NAME"),
	"RS_EMAIL" => GetMessage("RS.MONOPOLY.EMAIL"),
	"RS_TEXTAREA" => GetMessage("RS.MONOPOLY.TEXTAREA"),
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"EVENT_TYPE" => array(
			"NAME" => GetMessage("RS.MONOPOLY.EVENT_TYPE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"FORM_TITLE" => array(
			"NAME" => GetMessage("RS.MONOPOLY.FORM_TITLE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"FORM_DESCRIPTION" => array(
			"NAME" => GetMessage("RS.MONOPOLY.FORM_DESCRIPTION"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		),
		"EMAIL_TO" => array(
			"NAME" => GetMessage("RS.MONOPOLY.EMAIL_TO"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => COption::GetOptionString("main", "email_from", ""),
		),
		"SHOW_FIELDS" => Array(
			"NAME" => GetMessage("RS.MONOPOLY.SHOW_FIELDS"), 
			"TYPE" => "LIST", 
			"MULTIPLE" => "Y", 
			"VALUES" => $arShowFieldsList,
			"PARENT" => "BASE",
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("RS.MONOPOLY.REQUIRED_FIELDS"), 
			"TYPE" => "LIST", 
			"MULTIPLE" => "Y", 
			"VALUES" => $arShowFieldsList, 
			"PARENT" => "BASE",
		),
		"USE_CAPTCHA" => array(
			"NAME" => GetMessage("RS.MONOPOLY.USE_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"PARENT" => "BASE",
			"VALUE" => "Y",
		),
		"MESSAGE_AGREE" => array(
			"NAME" => GetMessage("RS.MONOPOLY.MESSAGE_AGREE"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => GetMessage("RS.MONOPOLY.MESSAGE_AGREE_DEFAULT"),
		),
		"RS_MONOPOLY_EXT_FIELDS_COUNT" => array(
			"NAME" => GetMessage("RS.MONOPOLY.EXT_FIELDS_COUNT"),
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

$count = IntVal( $arCurrentValues['RS_MONOPOLY_EXT_FIELDS_COUNT'] );
if( $count>0 ) {
	for($i=0; $i<$count; $i++) {
		$arComponentParameters['PARAMETERS']['RS_MONOPOLY_FIELD_'.$i.'_NAME'] = array(
			"NAME" => GetMessage("RS.MONOPOLY.EXT_FIELDS_NAME").($i+1),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "",
		);
	}
}