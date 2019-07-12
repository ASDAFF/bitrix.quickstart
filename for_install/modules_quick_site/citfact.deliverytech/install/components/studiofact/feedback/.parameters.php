<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = Array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$event_type = Array();
$db_get = CEventType::GetList(Array("LID" => "ru"));
while ($ar_get = $db_get->Fetch()) {
	$event_type[$ar_get["EVENT_NAME"]] = $ar_get["NAME"]." [".$ar_get["EVENT_NAME"]."]";
}

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"PARENT_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_PARENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "feedback_form",
		),
		"EVENT_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_EVENT_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $event_type
		),
		"HEAD" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_HEAD"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"PATH" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_PATH"),
			"TYPE" => "STRING",
			"DEFAULT" => SITE_DIR,
		),
		"CHECK_EMAIL" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_CHECK_EMAIL"),
			"TYPE" => "LIST",
			"VALUES" => Array("Y" => GetMessage("PVKD_FEEDBACK_YES"), "N" => GetMessage("PVKD_FEEDBACK_NO"))
		),
		"CHECK_PHONE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_CHECK_PHONE"),
			"TYPE" => "LIST",
			"VALUES" => Array("Y" => GetMessage("PVKD_FEEDBACK_YES"), "N" => GetMessage("PVKD_FEEDBACK_NO"))
		),
		"CHECK_PHONE_EXP" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PVKD_FEEDBACK_CHECK_PHONE_EXP"),
			"TYPE" => "STRING",
			"DEFAULT" => "^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{6,10}$",
		),
	),
);

$props = Array();
if (intVal($arCurrentValues["IBLOCK_ID"]) > 1) {
	$db_get = CIBlockProperty::GetList(Array("SORT" => "ASC", "NAME" => "ASC"), Array("ACTIVE" => "Y", "IBLOCK_ID" => intVal($arCurrentValues["IBLOCK_ID"])));
	while ($ar_get = $db_get->Fetch()) {
		$props[$ar_get["CODE"]] = $ar_get["NAME"];
	}
	$arComponentParameters["PARAMETERS"]["VISIBLE"] = Array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("PVKD_FEEDBACK_VISIBLE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $props
	);
} ?>