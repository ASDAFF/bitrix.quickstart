<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?
$arFormFields = array(
	"NAME" => 1,
	"SECOND_NAME" => 1,
	"LAST_NAME" => 1,
	"AUTO_TIME_ZONE" => 1,
	"PERSONAL_PROFESSION" => 1,
	"PERSONAL_WWW" => 1,
	"PERSONAL_ICQ" => 1,
	"PERSONAL_GENDER" => 1,
	"PERSONAL_BIRTHDAY" => 1,
	"PERSONAL_PHOTO" => 1,
	"PERSONAL_PHONE" => 1,
	"PERSONAL_FAX" => 1,
	"PERSONAL_MOBILE" => 1,
	"PERSONAL_PAGER" => 1,
	"PERSONAL_STREET" => 1,
	"PERSONAL_MAILBOX" => 1,
	"PERSONAL_CITY" => 1,
	"PERSONAL_STATE" => 1,
	"PERSONAL_ZIP" => 1,
	"PERSONAL_COUNTRY" => 1,
	"PERSONAL_NOTES" => 1,
	"WORK_COMPANY" => 1,
	"WORK_DEPARTMENT" => 1,
	"WORK_POSITION" => 1,
	"WORK_WWW" => 1,
	"WORK_PHONE" => 1,
	"WORK_FAX" => 1,
	"WORK_PAGER" => 1,
	"WORK_STREET" => 1,
	"WORK_MAILBOX" => 1,
	"WORK_CITY" => 1,
	"WORK_STATE" => 1,
	"WORK_ZIP" => 1,
	"WORK_COUNTRY" => 1,
	"WORK_PROFILE" => 1,
	"WORK_LOGO" => 1,
	"WORK_NOTES" => 1,
);

if(!CTimeZone::Enabled())
	unset($arFormFields["AUTO_TIME_ZONE"]);

$arUserFields = array();
foreach ($arFormFields as $value => $dummy)
{
	$arUserFields[$value] = "[".$value."] ".GetMessage("rksoft_REGISTER_PLUS_PARAMS_FIELD_".$value);
}
$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
$userProp = array();
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
		$userProp[$val["FIELD_NAME"]] = (strLen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
}

// list users group
$userGroups = Array();
$userGroupsList = CGroup::GetList($by = "c_sort", $order = "asc");
while($arUGroups = $userGroupsList -> Fetch())
{
	$userGroups[$arUGroups["ID"]] = $arUGroups["NAME"];
}

$arComponentParameters = array(
	"PARAMETERS" => array(

		"SHOW_FIELDS" => array(
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_SHOW_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserFields,
			"PARENT" => "BASE",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_SHOW_FIELDS_TIP"),
		),

		"REQUIRED_FIELDS" => array(
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_REQUIRED_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserFields,
			"PARENT" => "BASE",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_REQUIRED_FIELDS_TIP"),
		),

		"AUTH" => array(
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_AUTOMATED_AUTH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "ADDITIONAL_SETTINGS",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_AUTOMATED_AUTH_TIP"),
		),

		"USE_BACKURL" => array(
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_BACKURL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "ADDITIONAL_SETTINGS",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_BACKURL_TIP"),
		),

		"SUCCESS_PAGE" => array(
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_SUCCESS_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "ADDITIONAL_SETTINGS",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_SUCCESS_PAGE_TIP"),
		),

		"SET_TITLE" => array(
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_SET_TITLE_TIP"),
		),

		"USER_PROPERTY"=>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USER_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USER_PROPERTY_TIP"),
		),
		
		"USE_EMAIL_TO_LOGIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_EMAIL_TO_LOGIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_EMAIL_TO_LOGIN_TIP"),
		),
		
		"USE_GROUP" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_GROUP"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"HELP" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USE_GROUP_TIP"),
		),
		
		"USER_GROUP" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("rksoft_REGISTER_PLUS_PARAMS_USER_GROUP"),
			"TYPE" => "LIST",
			"VALUES" => $userGroups,
			"DEFAULT" => Array(1),
		),
	),

);

if($arCurrentValues["USE_GROUP"] != "Y") unset($arComponentParameters["PARAMETERS"]["USER_GROUP"]);
?>