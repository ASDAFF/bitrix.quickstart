<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$MAP_KEY = '';
$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

$strDomain = $_SERVER['HTTP_HOST'];
$wwwPos = strpos($strDomian, 'www.');
if ($wwwPos === 0)
	$strDomain = substr($strDomain, 4);

if ($strMapKeys)
{
	$arMapKeys = unserialize($strMapKeys);
	
	if (array_key_exists($strDomain, $arMapKeys))
		$MAP_KEY = $arMapKeys[$strDomain];
}

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "E")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arAscDesc = array(
	"asc" => GetMessage("BN_P_SORT_ASC"),
	"desc" => GetMessage("BN_P_SORT_DESC"),
);

$arUGroupsEx = Array();
$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
while($arUGroups = $dbUGroups -> Fetch())
{
	$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"RSS_SETTINGS" => array(
			"SORT" => 110,
			"NAME" => GetMessage("T_IBLOCK_DESC_RSS_SETTINGS"),
		),
		"RATING_SETTINGS" => array(
			"SORT" => 120,
			"NAME" => GetMessage("T_IBLOCK_DESC_RATING_SETTINGS"),
		),
		"CATEGORY_SETTINGS" => array(
			"SORT" => 130,
			"NAME" => GetMessage("T_IBLOCK_DESC_CATEGORY_SETTINGS"),
		),
		"REVIEW_SETTINGS" => array(
			"SORT" => 140,
			"NAME" => GetMessage("T_IBLOCK_DESC_REVIEW_SETTINGS"),
		),
		"FILTER_SETTINGS" => array(
			"SORT" => 150,
			"NAME" => GetMessage("T_IBLOCK_DESC_FILTER_SETTINGS"),
		),
		"LIST_SETTINGS" => array(
			"NAME" => GetMessage("CN_P_LIST_SETTINGS"),
		),
		"DETAIL_SETTINGS" => array(
			"NAME" => GetMessage("CN_P_DETAIL_SETTINGS"),
		),
		"DETAIL_PAGER_SETTINGS" => array(
			"NAME" => GetMessage("CN_P_DETAIL_PAGER_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		'KEY' => array(
			'NAME' => GetMessage('ITHIVE_OFFICES_PARAM_KEY'),
			'TYPE' => 'STRING',
			'PARENT' => 'BASE',
			'DEFAULT' => $MAP_KEY,
		),
		'ICON_FILE' => array(
			'NAME' => GetMessage('ITHIVE_OFFICES_PARAM_ICON_FILE'),
			'TYPE' => 'STRING',
			'PARENT' => 'BASE',
			'DEFAULT' => '/bitrix/components/ithive/offices.list/templates/.default/images/map-icon.png',
		),
		'ICON_SIZE' => array(
			'NAME' => GetMessage('ITHIVE_OFFICES_PARAM_ICON_SIZE'),
			'TYPE' => 'STRING',
			'PARENT' => 'BASE',
			'DEFAULT' => '46,31',
		),
		'ICON_OFFSET' => array(
			'NAME' => GetMessage('ITHIVE_OFFICES_PARAM_ICON_OFFSET'),
			'TYPE' => 'STRING',
			'PARENT' => 'BASE',
			'DEFAULT' => '-15,-30',
		),
		"INCLUDE_JQUERY" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ITHIVE_OFFICES_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"VARIABLE_ALIASES" => Array(
			"SECTION_ID" => Array("NAME" => GetMessage("BN_P_SECTION_ID_DESC")),
			"ELEMENT_ID" => Array("NAME" => GetMessage("NEWS_ELEMENT_ID_DESC")),
		),
		"SEF_MODE" => Array(
			"news" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"section" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS_SECTION"),
				"DEFAULT" => "",
				"VARIABLES" => array("SECTION_ID"),
			),
			"detail" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS_DETAIL"),
				"DEFAULT" => "#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID", "SECTION_ID"),
			),
			"search" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_SEARCH"),
				"DEFAULT" => "search/",
				"VARIABLES" => array(),
			),
			"rss" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_RSS"),
				"DEFAULT" => "rss/",
				"VARIABLES" => array(),
			),
			"rss_section" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_RSS_SECTION"),
				"DEFAULT" => "#SECTION_ID#/rss/",
				"VARIABLES" => array("SECTION_ID"),
			),
		),
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		"NEWS_COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"USE_SEARCH" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_SEARCH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"USE_RSS" => Array(
			"PARENT" => "RSS_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_RSS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"USE_RATING" => Array(
			"PARENT" => "RATING_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_RATING"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"USE_CATEGORIES" => Array(
			"PARENT" => "CATEGORY_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_CATEGORIES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"USE_REVIEW" => Array(
			"PARENT" => "REVIEW_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_REVIEW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"USE_FILTER" => Array(
			"PARENT" => "FILTER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"SORT_BY1" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
		),
		"SORT_BY2" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER2" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
		),
		"CHECK_DATES" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("T_IBLOCK_DESC_CHECK_DATES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PREVIEW_TRUNCATE_LEN" => Array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_PREVIEW_TRUNCATE_LEN"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"LIST_ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "LIST_SETTINGS"),
		"LIST_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "LIST_SETTINGS"),
		"LIST_PROPERTY_CODE" => array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => Array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_HIDE_LINK_WHEN_NO_DETAIL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"DISPLAY_NAME" => Array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"META_KEYWORDS" =>array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_KEYWORDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "-",
			"VALUES" => array_merge(Array("-"=>" "),$arProperty_LNS),
		),
		"META_DESCRIPTION" =>array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_DESCRIPTION"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "-",
			"VALUES" => array_merge(Array("-"=>" "),$arProperty_LNS),
		),
		"BROWSER_TITLE" => array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("CP_BN_BROWSER_TITLE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "-",
			"VALUES" => array_merge(Array("-"=>" ", "NAME" => GetMessage("IBLOCK_FIELD_NAME")), $arProperty_LNS),
		),
		"DETAIL_ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "DETAIL_SETTINGS"),
		"DETAIL_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "DETAIL_SETTINGS"),
		"DETAIL_PROPERTY_CODE" => array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => array(
			"PARENT" => "DETAIL_PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_TOP_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"DETAIL_DISPLAY_BOTTOM_PAGER" => array(
			"PARENT" => "DETAIL_PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_BOTTOM_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"DETAIL_PAGER_TITLE" => array(
			"PARENT" => "DETAIL_PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_PAGER_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("T_IBLOCK_DESC_PAGER_TITLE_PAGE"),
		),
		"DETAIL_PAGER_TEMPLATE" => array(
			"PARENT" => "DETAIL_PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_PAGER_TEMPLATE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"DETAIL_PAGER_SHOW_ALL" => array(
			"PARENT" => "DETAIL_PAGER_SETTINGS",
			"NAME" => GetMessage("CP_BN_DETAIL_PAGER_SHOW_ALL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"DISPLAY_PANEL" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("BN_P_DISPLAY_PANEL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SET_TITLE" => Array(),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BN_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"INCLUDE_IBLOCK_INTO_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_INCLUDE_IBLOCK_INTO_CHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ADD_SECTIONS_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_ADD_SECTIONS_CHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"USE_PERMISSIONS" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_USE_PERMISSIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"GROUP_PERMISSIONS" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_GROUP_PERMISSIONS"),
			"TYPE" => "LIST",
			"VALUES" => $arUGroupsEx,
			"DEFAULT" => Array(1),
			"MULTIPLE" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("BN_P_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BN_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, true);

if($arCurrentValues["USE_FILTER"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["FILTER_NAME"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_FILTER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["FILTER_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "FILTER_SETTINGS");
	$arComponentParameters["PARAMETERS"]["FILTER_PROPERTY_CODE"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
}
if($arCurrentValues["USE_PERMISSIONS"]!="Y")
	unset($arComponentParameters["PARAMETERS"]["GROUP_PERMISSIONS"]);
if($arCurrentValues["USE_RSS"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["NUM_NEWS"] = array(
		"PARENT" => "RSS_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_RSS_NUM_NEWS1"),
		"TYPE" => "STRING",
		"DEFAULT"=>'20',
	);
	$arComponentParameters["PARAMETERS"]["NUM_DAYS"] = array(
		"PARENT" => "RSS_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_RSS_NUM_DAYS"),
		"TYPE" => "STRING",
		"DEFAULT"=>'30',
	);
	$arComponentParameters["PARAMETERS"]["YANDEX"] = array(
		"PARENT" => "RSS_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_RSS_YANDEX"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"N",
	);
}
if($arCurrentValues["USE_RATING"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["MAX_VOTE"] = array(
		"PARENT" => "RATING_SETTINGS",
		"NAME" => GetMessage("IBLOCK_MAX_VOTE"),
		"TYPE" => "STRING",
		"DEFAULT" => "5",
	);
	$arComponentParameters["PARAMETERS"]["VOTE_NAMES"] = array(
		"PARENT" => "RATING_SETTINGS",
		"NAME" => GetMessage("IBLOCK_VOTE_NAMES"),
		"TYPE" => "STRING",
		"VALUES" => array(),
		"MULTIPLE" => "Y",
		"DEFAULT" => array("1","2","3","4","5"),
		"ADDITIONAL_VALUES" => "Y",
	);
}
if($arCurrentValues["USE_CATEGORIES"]=="Y")
{
	$arIBlockEx=array();
	$rsIBlockEx = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
	while($arr=$rsIBlockEx->Fetch())
	{
		$arIBlockEx[$arr["ID"]] = $arr["NAME"]."[".$arr["ID"]."] ";
	}
	$arComponentParameters["PARAMETERS"]["CATEGORY_IBLOCK"] = array(
		"PARENT" => "CATEGORY_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CATEGORY_IBLOCK"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockEx,
		"MULTIPLE" => "Y",
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["CATEGORY_CODE"] = array(
		"PARENT" => "CATEGORY_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CATEGORY_CODE"),
		"TYPE" => "STRING",
		"DEFAULT" => "CATEGORY",
	);
	$arComponentParameters["PARAMETERS"]["CATEGORY_ITEMS_COUNT"] = array(
		"PARENT" => "CATEGORY_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CATEGORY_ITEMS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "5",
	);
	if(is_array($arCurrentValues["CATEGORY_IBLOCK"]))
		foreach($arCurrentValues["CATEGORY_IBLOCK"] as $iblock_id)
			if(intval($iblock_id)>0)
			{
				$arComponentParameters["PARAMETERS"]["CATEGORY_THEME_".intval($iblock_id)] = array(
					"PARENT" => "CATEGORY_SETTINGS",
					"NAME" => GetMessage("IBLOCK_CATEGORY_THEME_")." ".$arIBlockEx[$iblock_id],
					"TYPE" => "LIST",
					"VALUES" => array(
						"list" => GetMessage("IBLOCK_CATEGORY_THEME_LIST"),
						"photo" => GetMessage("IBLOCK_CATEGORY_THEME_PHOTO"),
					),
					"DEFAULT" => "list",
				);
			}
}
if(!IsModuleInstalled("forum"))
{
	unset($arComponentParameters["GROUPS"]["REVIEW_SETTINGS"]);
	unset($arComponentParameters["PARAMETERS"]["USE_REVIEW"]);
}
elseif($arCurrentValues["USE_REVIEW"]=="Y")
{
	$arForumList = array();
	if(CModule::IncludeModule("forum"))
	{
		$rsForum = CForumNew::GetList();
		while($arForum=$rsForum->Fetch())
			$arForumList[$arForum["ID"]]=$arForum["NAME"];
	}
	$arComponentParameters["PARAMETERS"]["MESSAGES_PER_PAGE"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_MESSAGES_PER_PAGE"),
		"TYPE" => "STRING",
		"DEFAULT" => intVal(COption::GetOptionString("forum", "MESSAGES_PER_PAGE", "10"))
	);
	$arComponentParameters["PARAMETERS"]["USE_CAPTCHA"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_USE_CAPTCHA"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	);
	$arComponentParameters["PARAMETERS"]["PATH_TO_SMILE"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_PATH_TO_SMILE"),
		"TYPE" => "STRING",
		"DEFAULT" => "/bitrix/images/forum/smile/",
	);
	$arComponentParameters["PARAMETERS"]["FORUM_ID"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_FORUM_ID"),
		"TYPE" => "LIST",
		"VALUES" => $arForumList,
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["URL_TEMPLATES_READ"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_READ_TEMPLATE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["SHOW_LINK_TO_FORUM"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_SHOW_LINK_TO_FORUM"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["POST_FIRST_MESSAGE"] = Array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("F_POST_FIRST_MESSAGE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	);
}
?>
