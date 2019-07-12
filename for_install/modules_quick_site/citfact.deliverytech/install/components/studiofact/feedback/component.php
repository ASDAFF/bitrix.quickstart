<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = (int)$arParams["IBLOCK_ID"];
$arParams["PARENT_ID"] = trim($arParams["PARENT_ID"]);
if (strlen($arParams["PARENT_ID"]) < 1) {
	$arParams["PARENT_ID"] = "feedback_form";
}
if (strlen($arParams["EVENT_TYPE"]) < 1) {
	$arParams["EVENT_TYPE"] = "PVKD_FEEDBACK_EVENT";
}
if ($arParams["IBLOCK_ID"] < 1) {
	return;
}

global $USER;
$arResult["arUser"] = CUser::GetByID($USER->GetID())->Fetch();
$arResult["PROPS"] = $arResult["SAVE_PROPS"] = Array();
$db_get = CIBlockProperty::GetList(Array("SORT" => "ASC", "NAME" => "ASC"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
while ($ar_get = $db_get->Fetch()) {
	if ($ar_get["PROPERTY_TYPE"] == "S") {
		$arResult["PROPS"][$ar_get["CODE"]] = Array(
			"ID" => $ar_get["ID"],
			"NAME" => $ar_get["NAME"],
			"CODE" => $ar_get["CODE"],
			"DEFAULT_VALUE" => ($ar_get["USER_TYPE"] == "HTML" ? $ar_get["DEFAULT_VALUE"]["TEXT"]: $ar_get["DEFAULT_VALUE"]),
			"PROPERTY_TYPE" => $ar_get["PROPERTY_TYPE"],
			"USER_TYPE" => $ar_get["USER_TYPE"],
			"MULTIPLE" => $ar_get["MULTIPLE"],
			"MULTIPLE_CNT" => $ar_get["MULTIPLE_CNT"],
			"REQUIRED" => $ar_get["IS_REQUIRED"],
			"VISIBLE" => (in_array($ar_get["CODE"], $arParams["VISIBLE"]) ? "N" : "Y"),
			"VALUE" => ((strlen($arResult["arUser"][$ar_get["CODE"]]) > 0) ? trim($arResult["arUser"][$ar_get["CODE"]]) : "")
		);
	}
}

if (SITE_CHARSET != "UTF-8") {
	foreach ($_REQUEST as $code => $value) {
		$_REQUEST[$code] = trim($APPLICATION->ConvertCharset($value, "UTF-8", "windows-1251"));
	}
}
foreach ($arResult["PROPS"] as $code => $value) {
	if ($code == "LINK") {
		if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != "xmlhttprequest") {
			$arResult["PROPS"][$code]["VALUE"] = $_SERVER["SCRIPT_URI"];
		} else {
			$arResult["PROPS"][$code]["VALUE"] = $_REQUEST[$code];
		}
	} else if ($code == "USER_ID") {
		$arResult["PROPS"][$code]["VALUE"] = $arResult["arUser"]["ID"];
	} else {
		if (isset($_REQUEST[$code])) {
			$arResult["PROPS"][$code]["VALUE"] = $_REQUEST[$code];
		}
	}
}

if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
	$arResult["SUCCESS"] = false;
	$arResult["ERROR"] = Array();
	if ($_REQUEST[$arParams["PARENT_ID"]."_submit"] == "Y") {
		$fields = "";
		foreach ($_REQUEST as $code => $value) {
			if (intVal($arResult["PROPS"][$code]["ID"]) > 0) {
				if ($arResult["PROPS"][$code]["REQUIRED"] == "Y" && strlen($value) < 1) {
					$arResult["ERROR"]["FIELD"][$code] = true;
				}
				if (strlen($value) > 0 && $code == "EMAIL" && $arParams["CHECK_EMAIL"] == "Y" && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$arResult["ERROR"]["FIELD"][$code] = true;
				}
				if (strlen($value) > 0 && $code == "PERSONAL_PHONE" && $arParams["CHECK_PHONE"] == "Y" && !preg_match("'".$arParams["CHECK_PHONE_EXP"]."'", $value)) {
					$arResult["ERROR"]["FIELD"][$code] = true;
				}
				if ($arResult["PROPS"][$code]["PROPERTY_TYPE"] == "S" && ($arResult["PROPS"][$code]["USER_TYPE"] == "" || $arResult["PROPS"][$code]["USER_TYPE"] == "UserID")) {
					$arResult["SAVE_PROPS"][$code] = $value;
				} else if ($arResult["PROPS"][$code]["PROPERTY_TYPE"] == "S" && $arResult["PROPS"][$code]["USER_TYPE"] == "HTML") {
					$arResult["SAVE_PROPS"][$code] = Array("VALUE" => Array ("TEXT" => $value, "TYPE" => "html"));
				}
				$fields .= "<b>".$arResult["PROPS"][$code]["NAME"]."</b> ".$value."<br />";
			}
		}
		if (count($arResult["ERROR"]) < 1 && count($arResult["SAVE_PROPS"]) > 0) {
			$new_el = new CIBlockElement;
			$time = date("H:i:s d.m.Y");
			$array = Array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_SECTION_ID" => false,
				"MODIFIED_BY" => $arResult["arUser"]["ID"],
				"NAME" => GetMessage("TIME").$time,
				"ACTIVE" => "N",
				"PROPERTY_VALUES"=> $arResult["SAVE_PROPS"]
			);
			if ($id = $new_el->Add($array)) {
				$fields = "<b>".GetMessage("TIME")."</b>".$time."<br />".$fields;
				CEvent::Send($arParams["EVENT_TYPE"], SITE_ID, Array("FIELDS" => $fields));
				$arResult["SUCCESS"] = true;
			} else {
				$arResult["ERROR"]["BITRIX"][] = $new_el->LAST_ERROR;;
			}
		}
	}
}

if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != "xmlhttprequest") {
	echo '<div id="'.$arParams["PARENT_ID"].'">';
}
$this->IncludeComponentTemplate();
if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) != "xmlhttprequest") {
	echo '</div>';
	include_once ($_SERVER["DOCUMENT_ROOT"].$this->__template->__folder."/script.php");
} ?>