<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arFilter = array(
    "LID"     => LANGUAGE_ID
);
$rsEventType = CEventType::GetList($arFilter);
$arMailEvent = array("-" => "");
while ($arEvent = $rsEventType->Fetch())
{
	$arMailEvent[$arEvent['EVENT_NAME']] = "[".$arEvent["EVENT_NAME"]."] ".$arEvent["NAME"];
}

include('func.php');

$arComponentParameters = array(
	"PARAMETERS"  =>  array(
		"EVENT_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CSE_EVENT_TYPE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arMailEvent,
			"DEFAULT" => '-',
			//"REFRESH" => "Y",
		),
		"FIELDS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CSE_FIELD_DATA"),
			"TYPE" => "CUSTOM",
			"JS_FILE" => "/bitrix/components/citrus/realty.send_event/settings/settings.js",
			"JS_EVENT" => "OnCitrusSendEventSettingsEdit",
			"JS_DATA" => LANGUAGE_ID.'||'.GetMessage("CSE_FIELD_DATA_SET"),
			"DEFAULT" => serialize(CSE_GetFields($arCurrentValues["EVENT_TYPE"])),
		),
		"SUCCESS_SEND_MESSAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CSE_SUCCESS_SEND_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"AJAX_MODE" => Array(),
	),
);
