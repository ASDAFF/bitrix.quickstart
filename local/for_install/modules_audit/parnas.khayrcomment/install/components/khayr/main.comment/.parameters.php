<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var Array $arCurrentValues */

if (!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = Array(
	"GROUPS" => Array(
		"VIS" => Array(
			"NAME" => GetMessage("KHAYR_MAIN_COMMENT_VISUAL_PARAMS"),
			"SORT" => 310
		),
		"ACCESS" => Array(
			"NAME" => GetMessage("KHAYR_MAIN_COMMENT_ACCESS_FIELDS_GROUP_NAME"),
			"SORT" => 320
		),
	),
	"PARAMETERS" => Array(
		'OBJECT_ID' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_OBJECT_ID"),
			'TYPE' => 'INT',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'BASE',
		),
		"COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KHAYR_MAIN_COMMENT_COUNT"),
			"TYPE" => "INT",
			"DEFAULT" => "10",
		),
		'MAX_DEPTH' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_MAX_DEPTH"),
			'TYPE' => 'INT',
			'PARENT' => 'BASE',
			'ADDITIONAL_VALUES' => 'N',
			"DEFAULT" => '5',
		),
		'JQUERY' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_JQUERY"),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'BASE',
			'DEFAULT' => 'Y'
		),
		'MODERATE' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_PREMODERATION"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			'ADDITIONAL_VALUES' => 'N',
			"DEFAULT" => 'N',
		),
		'LEGAL' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LEGAL"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			'ADDITIONAL_VALUES' => 'N',
			"DEFAULT" => 'N',
		),
		'LEGAL_TEXT' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LEGAL_TEXT"),
			'TYPE' => 'STRING',
			'PARENT' => 'ACCESS',
			'ADDITIONAL_VALUES' => 'N',
			"DEFAULT" => GetMessage("KHAYR_MAIN_COMMENT_LEGAL_TEXT_DEFAULT"),
		),
		'CAN_MODIFY' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_CAN_MODIFY"),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'ACCESS',
			'DEFAULT' => 'N'
		),
		'NON_AUTHORIZED_USER_CAN_COMMENT' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_NONAUTHORIZED_CAN_COMMENT"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		'REQUIRE_EMAIL' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_REQUIRE_EMAIL"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			"DEFAULT" => 'Y',
			'ADDITIONAL_VALUES' => 'N',
		),
		'USE_CAPTCHA' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_USE_CAPTCHA"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			"DEFAULT" => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		'AUTH_PATH' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_AUTH_PATH"),
			'TYPE' => 'STRING',
			'PARENT' => 'ACCESS',
			'ADDITIONAL_VALUES' => 'N',
			"DEFAULT" => '/auth/',
		),
		"ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("KHAYR_MAIN_COMMENT_ACTIVE_DATE_FORMAT"), "VIS"),
		'LOAD_AVATAR' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LOAD_AVATAR"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'ACCESS',
			"DEFAULT" => 'Y',
			'ADDITIONAL_VALUES' => 'N',
		),
		'LOAD_MARK' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LOAD_MARK"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'VIS',
			"DEFAULT" => 'Y',
			'ADDITIONAL_VALUES' => 'N',
		),
		'LOAD_DIGNITY' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LOAD_DIGNITY"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'VIS',
			"DEFAULT" => 'Y',
			'ADDITIONAL_VALUES' => 'N',
		),
		'LOAD_FAULT' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_LOAD_FAULT"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'VIS',
			"DEFAULT" => 'Y',
			'ADDITIONAL_VALUES' => 'N',
		),
		"ADDITIONAL" => Array(
			"PARENT" => "VIS",
			"NAME" => GetMessage("KHAYR_MAIN_COMMENT_ADDITIONAL"),
			"TYPE" => "STRING",
			'MULTIPLE' => 'Y',
			"DEFAULT" => "",
		),
		'ALLOW_RATING' => Array(
			'NAME' => GetMessage("KHAYR_MAIN_COMMENT_ALLOW_RATING"),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'VIS',
			'DEFAULT' => 'Y'
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("KHAYR_COMMENT"), true, true);
?>