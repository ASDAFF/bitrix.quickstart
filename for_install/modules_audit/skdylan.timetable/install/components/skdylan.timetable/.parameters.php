<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */


if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => "timetable"));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

if(isset($arIBlocks))
	array_unshift($arIBlocks, array(0=>"(".GetMessage("SKDYLAN_TIMETABLE_VSE")));

$arFields = array("FullName" => GetMessage("SKDYLAN_TIMETABLE_FIO"), "Phone" => GetMessage("SKDYLAN_TIMETABLE_TELEFON"), "Email" => "Email", "Comment" => GetMessage("SKDYLAN_TIMETABLE_KOMMENTARIY"));
$defaultFields = array(0 => "FullName", 1 => "Phone", 2 => "Email");


$arComponentParameters = array(
	"GROUPS" => array(
        "SETTINGS" => array(
            "NAME" => GetMessage("SETTINGS_FORM")
        ),
        "SETTINGS_FOR_LIST" => array(
            "NAME" => GetMessage("SETTINGS_FOR_LIST")
        ),
	),
	"PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "REFRESH" => "Y",
        ),
        "ONLY_ACTIVE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_ONLY_ACTIVE"),
            "DEFAULT" => "Y",
            "TYPE" => "CHECKBOX",
        ),
        "ONLY_NOLIMIT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ONLY_NOLIMIT"),
            "DEFAULT" => "Y",
            "TYPE" => "CHECKBOX",
        ),
        "IBLOCK_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_COUNT"),
            "DEFAULT" => "100",
        ),

        "COLOR_TABLE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_PAGINATION"),
            "DEFAULT" => "#f1f1f1",
        ),
        "COLOR_TABLE_TEXT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("COLOR_TABLE_TEXT"),
            "DEFAULT" => "#669",
        ),
            "COLOR_TABLE_H3" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("COLOR_TABLE_H3"),
            "DEFAULT" => "#669",
        ),

        "COLOR_TABLE_TEXT_H" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("COLOR_TABLE_TEXT"),
            "DEFAULT" => "#6699ff",
        ),

        "FIELDS" => array(
            "PARENT" => "SETTINGS",
            "NAME" => GetMessage("T_FIELDS"),
            "TYPE" => "LIST",
            "VALUES" => $arFields,
            "DEFAULT" => $defaultFields,
            "ADDITIONAL_VALUES" => "N",
            "MULTIPLE" => "Y",
            "REFRESH" => "Y",
        ),
        "COLOR_B" => array(
            "PARENT" => "SETTINGS",
            "NAME" => GetMessage("T_COLOR_B"),
            "VALUES" => "",
            "DEFAULT" => '#4691A4',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "COLOR_I" => array(
            "PARENT" => "SETTINGS",
            "NAME" => GetMessage("T_COLOR_I"),
            "VALUES" => "",
            "DEFAULT" => '#88D5E9',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
//        "CACHE_TIME" => Array("DEFAULT"=>360000),
	),
);

CIBlockParameters::AddPagerSettings(
    $arComponentParameters,
    GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), //$pager_title
    true, //$bDescNumbering
    true, //$bShowAllParam
    true, //$bBaseLink
    $arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);
?>
