<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CModule::IncludeModule("iblock");

$arIblockType = Array();
$arIblockIds = Array();

// типы инфоблоков
$dbIBlockType = CIBlockType::GetList(array("sort" => "asc"), array("ACTIVE" => "Y"));
while ($arIBlockType = $dbIBlockType->Fetch()) {
    if ($arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID)) {
        $arIblockType[$arIBlockType["ID"]] = "[" . $arIBlockType["ID"] . "] " . $arIBlockTypeLang["NAME"];
    }
}

// инфоблоки
$arFilter = Array("ACTIVE" => "Y");
if (!empty($arCurrentValues["IBLOCK_TYPE"]))
    $arFilter["TYPE"] = $arCurrentValues["IBLOCK_TYPE"];
$rsIblocks = CIBlock::GetList(Array("SORT" => "ASC"), $arFilter);
while ($arIblock = $rsIblocks->fetch()) {
    $arIblockIds[$arIblock["ID"]] = "[" . $arIblock["ID"] . "] " . $arIblock["NAME"];
}

$SMSC_PHONE = COption::GetOptionString("wl.form", "SMSC_PHONE", "");

$arComponentParameters = Array(
    "GROUPS" => Array(
        "SETTINGS_CRM" => Array(
            "NAME" => GetMessage("SETTINGS_CRM"),
            "SORT" => 750
        ),
        "SETTINGS_FIELDS" => Array(
            "NAME" => GetMessage("SETTINGS_FIELDS"),
            "SORT" => 800
        ),
    ),
    "PARAMETERS" => Array(
        "ID_FORM" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ID_FORM"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "REFRESH" => "N",
        ),
        "ADD_JQUERY" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ADD_JQUERY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "ADD_BOOTSTRAP" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ADD_BOOTSTRAP"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "ADD_MASKEDINPUT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ADD_MASKEDINPUT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "REWRITE_DETAIL_TEXT" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("REWRITE_DETAIL_TEXT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "ADMIN_NOTIFICATION" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ADMIN_NOTIFICATION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "N",
        ),
        "SMS_ENABLE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SMS_ENABLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "SMSC_PHONE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SMSC_PHONE"),
            "TYPE" => "STRING",
            "DEFAULT" => $SMSC_PHONE,
            "REFRESH" => "N",
        ),
        "SMSC_TEMPLATE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SMSC_TEMPLATE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "REFRESH" => "N",
        ),
        "CALLBACK_ENABLE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CALLBACK_ENABLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "N",
        ),
        "CALLBACK_INPUT_ID" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CALLBACK_INPUT_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "1",
            "REFRESH" => "N",
        ),
        "IBLOCK_TYPE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "DEFAULT" => "",
            "REFRESH" => "Y",
            "VALUES" => $arIblockType
        ),
        "IBLOCK_ID" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_ID"),
            "TYPE" => "LIST",
            "DEFAULT" => "",
            "REFRESH" => "Y",
            "VALUES" => $arIblockIds
        ),
        "ENABLE_CRM" => Array(
            "PARENT" => "SETTINGS_CRM",
            "NAME" => GetMessage("ENABLE_CRM"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ),
    )
);

// CRM
if ($arCurrentValues["ENABLE_CRM"] == "Y") {
    $arComponentParameters["PARAMETERS"]["URL_CRM"] = Array(
        "PARENT" => "SETTINGS_CRM",
        "NAME" => GetMessage("URL_CRM"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
        "REFRESH" => "N",
    );
}

// разделы инфоблока
if ($arCurrentValues["IBLOCK_ID"] > 0) {
    $arIblockSections = Array();
    $rsSections = CIBlockSection::GetList(Array("SORT" => "ASC"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]));
    while ($arSection = $rsSections->fetch()) {
        $arIblockSections[$arSection["ID"]] = $arSection["NAME"];
    }
    if (!empty($arIblockSections)) {
        $arIblockSections[0] = GetMessage("IBLOCK_SECTION_EMPTY");
        $arComponentParameters["PARAMETERS"]["IBLOCK_SECTION"] = Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_SECTION"),
            "TYPE" => "LIST",
            "DEFAULT" => 0,
            "REFRESH" => "N",
            "VALUES" => $arIblockSections
        );
    }
}

$CTN_FIELDS = (!empty($arCurrentValues["CTN_FIELDS"])) ? $arCurrentValues["CTN_FIELDS"] : 2;
$arComponentParameters["PARAMETERS"]["CTN_FIELDS"] = Array(
    "PARENT" => "SETTINGS_FIELDS",
    "NAME" => GetMessage("CTN_FIELDS"),
    "TYPE" => "LIST",
    "DEFAULT" => 2,
    "REFRESH" => "Y",
    "ADDITIONAL_VALUES" => "Y",
    "VALUES" => Array(1 => 1, 2 => 2, 3 => 3, 4 => 4)
);

$arFieldTypes = Array(
    "text" => "text",
    "phone" => "phone",
    "email" => "email",
    "hidden" => "hidden"
);

$arFieldSave = Array(
    "DETAIL_TEXT" => GetMessage("FIELED_SAVE_DETAIL_TEXT"),
    "NAME" => GetMessage("FIELED_SAVE_NAME"),
    "PREVIEW_TEXT" => GetMessage("FIELED_SAVE_PREVIEW_TEXT"),
);

// св-ва инфоблока
if ($arCurrentValues["IBLOCK_ID"] > 0) {
    $rsProps = CIBlockProperty::GetList(Array("SORT" => "DESC"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]));
    while ($arProp = $rsProps->fetch())
        $arFieldSave["PROPERTY_" . $arProp["CODE"]] = "[" . $arProp["ID"] . "] " . $arProp["NAME"];
}

for ($i = 1; $i <= $CTN_FIELDS; $i++) {
    $arComponentParameters["GROUPS"]["SETTINGS_FIELD_" . $i] = Array(
        "NAME" => GetMessage("SETTINGS_FIELD") . " " . $i,
        "SORT" => 800 + $i
    );

    $arComponentParameters["PARAMETERS"]["FIELD_REQUIRED_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_REQUIRED"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "",
        "REFRESH" => "N",
    );
    $arComponentParameters["PARAMETERS"]["FIELD_NAME_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_NAME"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
        "REFRESH" => "N",
    );
    $arComponentParameters["PARAMETERS"]["FIELD_CODE_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_CODE"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
        "REFRESH" => "N",
    );
    $arComponentParameters["PARAMETERS"]["FIELD_TYPE_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_TYPE"),
        "TYPE" => "LIST",
        "DEFAULT" => "",
        "REFRESH" => "N",
        "VALUES" => $arFieldTypes
    );
    $arComponentParameters["PARAMETERS"]["FIELD_ERROR_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_ERROR"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
        "REFRESH" => "N",
    );
    $arComponentParameters["PARAMETERS"]["FIELD_SAVE_" . $i] = Array(
        "PARENT" => "SETTINGS_FIELD_" . $i,
        "NAME" => GetMessage("FIELD_SAVE"),
        "TYPE" => "LIST",
        "DEFAULT" => "",
        "REFRESH" => "N",
        "VALUES" => $arFieldSave
    );

    if ($arCurrentValues["ENABLE_CRM"] == "Y") {
        $arComponentParameters["PARAMETERS"]["FIELD_CRM_" . $i] = Array(
            "PARENT" => "SETTINGS_FIELD_" . $i,
            "NAME" => GetMessage("FIELD_CRM"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "REFRESH" => "N",
        );
    }
}
?>