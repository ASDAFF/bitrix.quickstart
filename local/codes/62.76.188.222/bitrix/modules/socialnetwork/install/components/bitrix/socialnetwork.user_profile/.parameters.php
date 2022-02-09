<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("socialnetwork"))
	return false;

$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
$userProp = array();
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
		$userProp[$val["FIELD_NAME"]] = (strLen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
}

$userProp1 = array(
	"ID" => GetMessage("SONET_UP1_ID"),
	"LOGIN" => GetMessage("SONET_UP1_LOGIN"),
	"NAME" => GetMessage("SONET_UP1_NAME"),
	"SECOND_NAME" => GetMessage("SONET_UP1_SECOND_NAME"),
	"LAST_NAME" => GetMessage("SONET_UP1_LAST_NAME"),
	"EMAIL" => GetMessage("SONET_UP1_EMAIL"),
	"LAST_LOGIN" => GetMessage("SONET_UP1_LAST_LOGIN"),
	"DATE_REGISTER" => GetMessage("SONET_UP1_DATE_REGISTER"),
	"LID" => GetMessage("SONET_UP1_LID"),

	"PERSONAL_BIRTHDAY" => GetMessage("SONET_UP1_PERSONAL_BIRTHDAY"),
	"PERSONAL_BIRTHDAY_YEAR" => GetMessage("SONET_UP1_PERSONAL_BIRTHDAY_YEAR"),
	"PERSONAL_BIRTHDAY_DAY" => GetMessage("SONET_UP1_PERSONAL_BIRTHDAY_DAY"),

	"PERSONAL_PROFESSION" => GetMessage("SONET_UP1_PERSONAL_PROFESSION"),
	"PERSONAL_WWW" => GetMessage("SONET_UP1_PERSONAL_WWW"),
	"PERSONAL_ICQ" => GetMessage("SONET_UP1_PERSONAL_ICQ"),
	"PERSONAL_GENDER" => GetMessage("SONET_UP1_PERSONAL_GENDER"),
	"PERSONAL_PHOTO" => GetMessage("SONET_UP1_PERSONAL_PHOTO"),
	"PERSONAL_NOTES" => GetMessage("SONET_UP1_PERSONAL_NOTES"),

	"PERSONAL_PHONE" => GetMessage("SONET_UP1_PERSONAL_PHONE"),
	"PERSONAL_FAX" => GetMessage("SONET_UP1_PERSONAL_FAX"),
	"PERSONAL_MOBILE" => GetMessage("SONET_UP1_PERSONAL_MOBILE"),
	"PERSONAL_PAGER" => GetMessage("SONET_UP1_PERSONAL_PAGER"),

	"PERSONAL_COUNTRY" => GetMessage("SONET_UP1_PERSONAL_COUNTRY"),
	"PERSONAL_STATE" => GetMessage("SONET_UP1_PERSONAL_STATE"),
	"PERSONAL_CITY" => GetMessage("SONET_UP1_PERSONAL_CITY"),
	"PERSONAL_ZIP" => GetMessage("SONET_UP1_PERSONAL_ZIP"),
	"PERSONAL_STREET" => GetMessage("SONET_UP1_PERSONAL_STREET"),
	"PERSONAL_MAILBOX" => GetMessage("SONET_UP1_PERSONAL_MAILBOX"),

	"WORK_COMPANY" => GetMessage("SONET_UP1_WORK_COMPANY"),
	"WORK_DEPARTMENT" => GetMessage("SONET_UP1_WORK_DEPARTMENT"),
	"WORK_POSITION" => GetMessage("SONET_UP1_WORK_POSITION"),
	"WORK_WWW" => GetMessage("SONET_UP1_WORK_WWW"),
	"WORK_PROFILE" => GetMessage("SONET_UP1_WORK_PROFILE"),
	"WORK_LOGO" => GetMessage("SONET_UP1_WORK_LOGO"),
	"WORK_NOTES" => GetMessage("SONET_UP1_WORK_NOTES"),

	"WORK_PHONE" => GetMessage("SONET_UP1_WORK_PHONE"),
	"WORK_FAX" => GetMessage("SONET_UP1_WORK_FAX"),
	"WORK_PAGER" => GetMessage("SONET_UP1_WORK_PAGER"),

	"WORK_COUNTRY" => GetMessage("SONET_UP1_WORK_COUNTRY"),
	"WORK_STATE" => GetMessage("SONET_UP1_WORK_STATE"),
	"WORK_CITY" => GetMessage("SONET_UP1_WORK_CITY"),
	"WORK_ZIP" => GetMessage("SONET_UP1_WORK_ZIP"),
	"WORK_STREET" => GetMessage("SONET_UP1_WORK_STREET"),
	"WORK_MAILBOX" => GetMessage("SONET_UP1_WORK_MAILBOX"),
);

$arComponentParameters = Array(
	"GROUPS" => array(
		"VARIABLE_ALIASES" => array(
			"NAME" => GetMessage("SONET_VARIABLE_ALIASES"),
		),
	),
	"PARAMETERS" => Array(
		"SET_NAV_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_SET_NAVCHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"SHORT_FORM" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_SHORT_FORM"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PATH_TO_USER" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_FRIENDS_ADD" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_FRIENDS_ADD"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_MESSAGE_FORM" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_MESSAGE_FORM"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_MESSAGES_CHAT" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_MESSAGES_CHAT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_MESSAGES_USERS_MESSAGES" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_MESSAGES_USERS_MESSAGES"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_FRIENDS_DELETE" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_FRIENDS_DELETE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_FRIENDS" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_FRIENDS"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_SEARCH" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_SEARCH"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_GROUP_SEARCH" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_GROUP_SEARCH"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_LOG" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_LOG"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_SEARCH_INNER" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_SEARCH_INNER"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_GROUP" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_GROUP"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_GROUP_EDIT" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_GROUP_EDIT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_GROUP_CREATE" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_GROUP_CREATE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_GROUPS" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_GROUPS"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_FRIENDS_ADD" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_FRIENDS_ADD"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_EDIT" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_EDIT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_FEATURES" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_FEATURES"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_SUBSCRIBE" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_SUBSCRIBE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PATH_TO_USER_SETTINGS_EDIT" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER_SETTINGS_EDIT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "URL_TEMPLATES",
		),
		"PAGE_VAR" => Array(
			"NAME" => GetMessage("SONET_PAGE_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"USER_VAR" => Array(
			"NAME" => GetMessage("SONET_USER_VAR"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25,
			"PARENT" => "VARIABLE_ALIASES",
		),
		"ID" => Array(
			"NAME" => GetMessage("SONET_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "={\$id}",
			"COLS" => 25,
			"PARENT" => "DATA_SOURCE",
		),
		"SET_TITLE" => Array(),
		"USER_FIELDS_MAIN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_FIELDS_MAIN"),
			"TYPE" => "LIST",
			"VALUES" => $userProp1,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"USER_PROPERTY_MAIN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_PROPERTY_MAIN"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"USER_FIELDS_CONTACT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_FIELDS_CONTACT"),
			"TYPE" => "LIST",
			"VALUES" => $userProp1,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"USER_PROPERTY_CONTACT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_PROPERTY_CONTACT"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"USER_FIELDS_PERSONAL" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_FIELDS_PERSONAL"),
			"TYPE" => "LIST",
			"VALUES" => $userProp1,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"USER_PROPERTY_PERSONAL" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_USER_PROPERTY_PERSONAL"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
		"DATE_TIME_FORMAT" => CComponentUtil::GetDateTimeFormatField(GetMessage("SONET_DATE_TIME_FORMAT"), "VISUAL"),
		"AVATAR_SIZE" => Array(
			"NAME" => GetMessage("SONET_USER_AVATAR_SIZE"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "300",
			"COLS" => 3,
			"PARENT" => "VISUAL",
		),		
		"ITEMS_COUNT" => Array(
			"NAME" => GetMessage("SONET_ITEM_COUNT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "6",
			"COLS" => 3,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		'SHOW_YEAR' => array(
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'VALUES' => array(
				'Y' => GetMessage('INTR_ISBN_PARAM_SHOW_YEAR_VALUE_Y'),
				'M' => GetMessage('INTR_ISBN_PARAM_SHOW_YEAR_VALUE_M'),
				'N' => GetMessage('INTR_ISBN_PARAM_SHOW_YEAR_VALUE_N')
			),
			'NAME' => GetMessage('INTR_ISBN_PARAM_SHOW_YEAR'),
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SONET_USER_FIELDS_SEARCHABLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_SONET_USER_FIELDS_SEARCHABLE"),
			"TYPE" => "LIST",
			"VALUES" => $userProp1,
			"MULTIPLE" => "Y",
			"DEFAULT" => array("NAME", "SECOND_NAME", "LAST_NAME", "LOGIN", "PERSONAL_BIRTHDAY", "PERSONAL_PROFESSION", "PERSONAL_GENDER", "PERSONAL_COUNTRY", "PERSONAL_STATE", "PERSONAL_CITY", "PERSONAL_ZIP", "PERSONAL_STREET", "PERSONAL_MAILBOX", "WORK_COMPANY", "WORK_DEPARTMENT", "WORK_POSITION", "WORK_COUNTRY", "WORK_STATE", "WORK_CITY", "WORK_ZIP", "WORK_STREET", "WORK_MAILBOX"),	
		),
		"SONET_USER_PROPERTY_SEARCHABLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_SONET_USER_PROPERTY_SEARCHABLE"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),	
		),
	)
);

if (CModule::IncludeModule("intranet"))
{
	$arComponentParameters["PARAMETERS"]["PATH_TO_CONPANY_DEPARTMENT"] = array(
		"NAME" => GetMessage("SONET_PATH_TO_CONPANY_DEPARTMENT"),
		"DEFAULT" => "/company/structure.php?department=#ID#",
		"PARENT" => "URL_TEMPLATES",
	);
}
?>