<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arUserFieldNames = array('PERSONAL_PHOTO', 'FULL_NAME', 'ID','LOGIN','NAME','SECOND_NAME','LAST_NAME','EMAIL','DATE_REGISTER','PERSONAL_PROFESSION','PERSONAL_WWW','PERSONAL_BIRTHDAY','PERSONAL_ICQ','PERSONAL_GENDER','PERSONAL_PHONE','PERSONAL_FAX','PERSONAL_MOBILE','PERSONAL_PAGER','PERSONAL_STREET','PERSONAL_MAILBOX','PERSONAL_CITY','PERSONAL_STATE','PERSONAL_ZIP','PERSONAL_COUNTRY','PERSONAL_NOTES', 'WORK_POSITION', 'ADMIN_NOTES','XML_ID');

$userProp = array();

foreach ($arUserFieldNames as $name)
{
	$userProp[$name] = GetMessage('ISL_'.$name);
}

$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
	{
		$userProp[$val["FIELD_NAME"]] = '* '.(strlen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
	}
}

$arTemplateParameters = array(
	"DEFAULT_VIEW" => array(
		"NAME" => GetMessage('ISL_PARAM_DEFAULT_VIEW'),
		"TYPE" => "LIST",
		"VALUES" => array('list' => GetMessage('ISL_PARAM_DEFAULT_VIEW_VALUE_list'), 'table' => GetMessage('ISL_PARAM_DEFAULT_VIEW_VALUE_table')),
		"MULTIPLE" => "N",
		"DEFAULT" => 'list',
	),

	"LIST_VIEW" => array(
		"NAME" => GetMessage('ISL_PARAM_LIST_VIEW'),
		"TYPE" => "LIST",
		"VALUES" => array('list' => GetMessage('ISL_PARAM_LIST_VIEW_VALUE_list'), 'group' => GetMessage('ISL_PARAM_LIST_VIEW_VALUE_group')),
		"MULTIPLE" => "N",
		"DEFAULT" => 'group',
		'REFRESH' => 'Y',
	),
	
	"USER_PROPERTY_TABLE" => array(
		"NAME" => GetMessage('ISL_PARAM_USER_PROPERTY_TABLE'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('FULL_NAME', 'PERSONAL_PHONE', 'EMAIL', 'WORK_POSITION', 'UF_DEPARTMENT'),
	),
	
	"USER_PROPERTY_EXCEL"=>array(
		"NAME" => GetMessage('ISL_PARAM_USER_PROPERTY_TABLE_EXCEL'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('FULL_NAME', 'PERSONAL_PHONE', 'EMAIL', 'WORK_POSITION', 'UF_DEPARTMENT'),
	),

); 

if ($arCurrentValues['LIST_VIEW'] == 'list')
{
	$arTemplateParameters['USER_PROPERTY_LIST'] = array(
		"NAME" => GetMessage('ISL_PARAM_USER_PROPERTY_LIST'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('UF_DEPARTMENT', 'PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_PHONE', 'EMAIL'),
	);
}
else
{
	$arTemplateParameters['USER_PROPERTY_GROUP'] = array(
		"NAME" => GetMessage('ISL_PARAM_USER_PROPERTY_GROUP'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_PHONE', 'EMAIL'),
	);
}
?>