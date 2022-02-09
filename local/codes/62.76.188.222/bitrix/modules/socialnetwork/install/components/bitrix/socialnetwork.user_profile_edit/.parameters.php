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
	"TIME_ZONE" => GetMessage("SONET_UP1_TIME_ZONE"),
	"LAST_LOGIN" => GetMessage("SONET_UP1_LAST_LOGIN"),
	"DATE_REGISTER" => GetMessage("SONET_UP1_DATE_REGISTER"),
	"LID" => GetMessage("SONET_UP1_LID"),

	"PERSONAL_BIRTHDAY" => GetMessage("SONET_UP1_PERSONAL_BIRTHDAY"),
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

if (IsModuleInstalled("forum"))
{
	$userProp1 = array_merge($userProp1, array(
		'FORUM_SHOW_NAME' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_SHOW_NAME'),
		'FORUM_DESCRIPTION' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_DESCRIPTION'),
		'FORUM_INTERESTS' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_INTERESTS'),
		'FORUM_SIGNATURE' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_SIGNATURE'),
		'FORUM_AVATAR' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_AVATAR'),
		'FORUM_HIDE_FROM_ONLINE' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_HIDE_FROM_ONLINE'),
		'FORUM_SUBSC_GET_MY_MESSAGE' => GetMessage('SONET_UP1_FORUM_PREFIX').GetMessage('SONET_UP1_FORUM_SUBSC_GET_MY_MESSAGE'),
	));
}

if (IsModuleInstalled("blog"))
{
	$userProp1 = array_merge($userProp1, array(
		'BLOG_ALIAS' => GetMessage('SONET_UP1_BLOG_PREFIX').GetMessage('SONET_UP1_BLOG_ALIAS'),
		'BLOG_DESCRIPTION' => GetMessage('SONET_UP1_BLOG_PREFIX').GetMessage('SONET_UP1_BLOG_DESCRIPTION'),
		'BLOG_INTERESTS' => GetMessage('SONET_UP1_BLOG_PREFIX').GetMessage('SONET_UP1_BLOG_INTERESTS'),
		'BLOG_AVATAR' => GetMessage('SONET_UP1_BLOG_PREFIX').GetMessage('SONET_UP1_BLOG_AVATAR'),
	));
}

$arComponentParameters = Array(
	"GROUPS" => array(
		"VARIABLE_ALIASES" => array(
			"NAME" => GetMessage("SONET_VARIABLE_ALIASES"),
		),
	),
	"PARAMETERS" => Array(
		"PATH_TO_USER" => Array(
			"NAME" => GetMessage("SONET_PATH_TO_USER"),
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
		
		"EDITABLE_FIELDS"=>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SONET_EDITABLE_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => array_merge($userProp, $userProp1),
			"MULTIPLE" => "Y",
			"DEFAULT" => array('LOGIN', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_BIRTHDAY', 'PERSONAL_CITY', 'PERSONAL_COUNTRY', 'PERSONAL_FAX', 'PERSONAL_GENDER', 'PERSONAL_ICQ', 'PERSONAL_MAILBOX', 'PERSONAL_MOBILE', 'PERSONAL_PAGER', 'PERSONAL_PHONE', 'PERSONAL_PHOTO', 'PERSONAL_STATE', 'PERSONAL_STREET', 'PERSONAL_WWW', 'PERSONAL_ZIP'),
		),
		"DATE_TIME_FORMAT" => CComponentUtil::GetDateTimeFormatField(GetMessage("SONET_DATE_TIME_FORMAT"), "VISUAL"),
		"NAME_TEMPLATE" => array(
			"TYPE" => "LIST",
			"NAME" => GetMessage("SONET_NAME_TEMPLATE"),
			"VALUES" => CComponentUtil::GetDefaultNameTemplates(),
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SHOW_LOGIN" => Array(
			"NAME" => GetMessage("SONET_SHOW_LOGIN"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"Y",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),			
	)
);
?>