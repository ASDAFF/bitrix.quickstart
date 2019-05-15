<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arUserFieldNames = array('ID','LOGIN','NAME','SECOND_NAME','LAST_NAME','EMAIL','DATE_REGISTER','PERSONAL_PROFESSION','PERSONAL_WWW','PERSONAL_BIRTHDAY','PERSONAL_ICQ','PERSONAL_GENDER','PERSONAL_PHOTO','PERSONAL_PHONE','PERSONAL_FAX','PERSONAL_MOBILE','PERSONAL_PAGER','PERSONAL_STREET','PERSONAL_MAILBOX','PERSONAL_CITY','PERSONAL_STATE','PERSONAL_ZIP','PERSONAL_COUNTRY','PERSONAL_NOTES','ADMIN_NOTES','WORK_POSITION', 'XML_ID');

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
		$userProp[$val["FIELD_NAME"]] = (strLen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
	}
}

$arTemplateParameters = array(
	'PM_URL' => array(
		'TYPE' => 'STRING',
		'DEFAULT' => '/messages/form/#USER_ID#/',
		'NAME' => GetMessage('INTR_ISL_TPL_PARAM_PM_URL'),
	),
	
	"USER_PROPERTY"=>array(
		"NAME" => GetMessage('INTR_ISL_TPL_PARAM_USER_PROPERTY'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('ID', 'LOGIN', 'FULL_NAME'),
	),
); 


?>