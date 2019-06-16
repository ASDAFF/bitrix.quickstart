<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;
$arSorts = array("ASC" => GetMessage("T_IBLOCK_DESC_ASC"), "DESC" => GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = array(
    "ID" => GetMessage("T_IBLOCK_DESC_FID"),
    "NAME" => GetMessage("T_IBLOCK_DESC_FNAME"),
    "ACTIVE_FROM" => GetMessage("T_IBLOCK_DESC_FACT"),
    "SORT" => GetMessage("T_IBLOCK_DESC_FSORT"),
    "TIMESTAMP_X" => GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])));
while ($arr = $rsProp->Fetch()) {
    $arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S"))) {
        $arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    }
}
CIBlockParameters::AddPagerSettings(
    $arTemplateParameters,
    GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), //$pager_title
    true, //$bDescNumbering
    true, //$bShowAllParam
    true, //$bBaseLink
    $arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);
/*CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);*/

$arTemplateParameters = array(
    "NEWS_COUNT" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
        "TYPE" => "STRING",
        "DEFAULT" => "20",
    ),
    "SORT_BY1" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
        "TYPE" => "LIST",
        "DEFAULT" => "ACTIVE_FROM",
        "VALUES" => $arSortFields,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER1" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
        "TYPE" => "LIST",
        "DEFAULT" => "DESC",
        "VALUES" => $arSorts,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_BY2" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
        "TYPE" => "LIST",
        "DEFAULT" => "SORT",
        "VALUES" => $arSortFields,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER2" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
        "TYPE" => "LIST",
        "DEFAULT" => "ASC",
        "VALUES" => $arSorts,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "FILTER_NAME" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_FILTER"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "DATA_SOURCE"),
    "PROPERTY_CODE" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_PROPERTY"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arProperty_LNS,
        "ADDITIONAL_VALUES" => "Y",
    ),
    "DISPLAY_TOP_PAGER" => array(
        "PARENT" => "DETAIL_PAGER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_TOP_PAGER"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "DISPLAY_BOTTOM_PAGER" => array(
        "PARENT" => "DETAIL_PAGER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_BOTTOM_PAGER"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "PAGER_TITLE" => array(
        "PARENT" => "DETAIL_PAGER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_PAGER_TITLE"),
        "TYPE" => "STRING",
        "DEFAULT" => GetMessage("T_IBLOCK_DESC_PAGER_TITLE_PAGE"),
    ),
    "PAGER_TEMPLATE" => array(
        "PARENT" => "DETAIL_PAGER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_PAGER_TEMPLATE"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "PAGER_SHOW_ALL" => array(
        "PARENT" => "DETAIL_PAGER_SETTINGS",
        "NAME" => GetMessage("CP_BN_DETAIL_PAGER_SHOW_ALL"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "CHECK_DATES" => array(
        "PARENT" => "DATA_SOURCE",
        "NAME" => GetMessage("T_IBLOCK_DESC_CHECK_DATES"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
        "DETAIL",
        "DETAIL_URL",
        GetMessage("T_IBLOCK_DESC_DETAIL_PAGE_URL"),
        "",
        "URL_TEMPLATES"
    ),
    "PREVIEW_TRUNCATE_LEN" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_PREVIEW_TRUNCATE_LEN"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),
    "ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "ADDITIONAL_SETTINGS"),
    "SET_TITLE" => array(),
    "SET_BROWSER_TITLE" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_SET_BROWSER_TITLE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "SET_META_KEYWORDS" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_SET_META_KEYWORDS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "SET_META_DESCRIPTION" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_SET_META_DESCRIPTION"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "SET_LAST_MODIFIED" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_SET_LAST_MODIFIED"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "INCLUDE_IBLOCK_INTO_CHAIN" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_INCLUDE_IBLOCK_INTO_CHAIN"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "ADD_SECTIONS_CHAIN" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_ADD_SECTIONS_CHAIN"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "HIDE_LINK_WHEN_NO_DETAIL" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_DESC_HIDE_LINK_WHEN_NO_DETAIL"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "PARENT_SECTION" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("IBLOCK_SECTION_ID"),
        "TYPE" => "STRING",
        "DEFAULT" => '',
    ),
    "PARENT_SECTION_CODE" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("IBLOCK_SECTION_CODE"),
        "TYPE" => "STRING",
        "DEFAULT" => '',
    ),
    "INCLUDE_SUBSECTIONS" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_INCLUDE_SUBSECTIONS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "STRICT_SECTION_CHECK" => array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("CP_BNL_STRICT_SECTION_CHECK"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "CACHE_TIME" => array("DEFAULT" => 36000000),
    "CACHE_FILTER" => array(
        "PARENT" => "CACHE_SETTINGS",
        "NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "CACHE_GROUPS" => array(
        "PARENT" => "CACHE_SETTINGS",
        "NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "DISPLAY_DATE" => Array(
        "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "DISPLAY_PICTURE" => Array(
        "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "DISPLAY_PREVIEW_TEXT" => Array(
        "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "USE_SHARE" => Array(
        "NAME" => GetMessage("T_IBLOCK_DESC_NEWS_USE_SHARE"),
        "TYPE" => "CHECKBOX",
        "MULTIPLE" => "N",
        "VALUE" => "Y",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
    ),

);

?>