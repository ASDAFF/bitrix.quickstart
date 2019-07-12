<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arMailEvent = array("-" => "");

$arFilter = array(
    "LID"     => LANGUAGE_ID
);
$rsEventType = CEventType::GetList($arFilter);
while($arEvent = $rsEventType->Fetch())
{
	$arMailEvent[$arEvent['EVENT_NAME']] = "[".$arEvent["EVENT_NAME"]."] ".$arEvent["NAME"];
}

$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

include('func.php');

$arComponentParameters = array(
	"GROUPS" => array(
		"ACCESS_SETTINGS" => array(
			"SORT" => 110,
			"NAME" => GetMessage("CIEE_ACCESS_SETTINGS"),
		),
		"MAIL_SETTINGS" => array(
			"SORT" => 110,
			"NAME" => GetMessage("CIEE_MAIL_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),

		'FIELDS' => array(
			'NAME' => GetMessage('CIEE_FIELD_DATA'),
			'TYPE' => 'CUSTOM',
			'JS_FILE' => '/bitrix/components/citrus/iblock.element.form/settings/settings.js',
			'JS_EVENT' => 'OnCitrusIBlockElementFormSettingsEdit',
			'JS_DATA' => LANGUAGE_ID.'||'.GetMessage('CIEE_FIELD_DATA_SET'),
			'DEFAULT' => serialize(CIEE_GetDefaultFields()),
			'PARENT' => 'BASE',
		),
		"PARENT_SECTION" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CIEE_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"PARENT_SECTION_CODE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CIEE_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"GROUPS" => array(
			"PARENT" => "ACCESS_SETTINGS",
			"NAME" => GetMessage("CIEE_GROUPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arGroups,
		),
		'ACCESS_DENIED_MESSAGE' => array(
			'PARENT' => 'ACCESS_SETTINGS',
			'NAME' => GetMessage("CIEE_ACCESS_DENIED_MESSAGE"),
			'TYPE' => 'STRING',
			'DEFAULT' => "",
		),

		"SEND_MESSAGE" => array(
			"PARENT" => "MAIL_SETTINGS",
			"NAME" => GetMessage("CIEE_SEND_MESSAGE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"MAIL_EVENT" => array(
			"PARENT" => "MAIL_SETTINGS",
			"NAME" => GetMessage("CIEE_MAIL_EVENT"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arMailEvent,
			"DEFAULT" => '-'
		),
		
		"SUCCESS_ADD_MESSAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_SUCCESS_ADD_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),

		"SUBMIT_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_SUBMIT_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("CIEE_SUBMIT_TEXT_DEFAULT"),
		),
		
		"ERROR_LIST_MESSAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_ERROR_TITLE_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		
		"CACHE_TIME" => Array(),
		"AJAX_MODE" => Array(),
	),
);
?>
