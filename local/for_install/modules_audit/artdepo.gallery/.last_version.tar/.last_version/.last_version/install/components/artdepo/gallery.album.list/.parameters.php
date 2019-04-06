<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("artdepo.gallery") || !CModule::IncludeModule("iblock"))
	return;

// Gallery top sections
$gSection = new CArtDepoGallerySection();
$rsData = $gSection->GetList(array("ID"=>"DESC"), array("ACTIVE" => "Y"));
while($arRes = $rsData->Fetch())
	$arSections[$arRes["ID"]] = $arRes["NAME"];

// Sort by and order
$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"UPDATE_DATE"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

// Site languages
$arLang = array();
foreach(CArtDepoGalleryUtils::GetSiteLangs() as $lan)
    $arLang[$lan["LANGUAGE_ID"]] = $lan["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
	    "DATA_SOURCE" => array("SORT" => "1"),
	),
	
	"PARAMETERS" => array(
		"SECTION_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_SECTION_ID_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arSections,
		),
		"LANGUAGE_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_LANGUAGE_ID_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arLang,
			"DEFAULT" => LANGUAGE_ID,
		),
		"NEWS_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_NEWS_COUNT_NAME"), 
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"SORT_BY1" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_SORT_BY1_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arSortFields,
			"DEFAULT" => "ID",
		),
		"SORT_ORDER1" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_SORT_ORDER1_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arSorts,
			"DEFAULT" => "DESC",
		),
        "DISPLAY_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_DISPLAY_NAME_NAME"), 
			"TYPE" => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
        ),
        "DISPLAY_DATE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_DISPLAY_DATE_NAME"), 
			"TYPE" => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
        ),
        "DISPLAY_COUNT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_DISPLAY_COUNT_NAME"), 
			"TYPE" => "CHECKBOX",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
        ),
		"DETAIL_URL" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_DETAIL_URL_NAME"), 
			"TYPE" => "STRING",
			"DEFAULT" => "album/?ID=#ID#",
		),
		"ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_DESC_ACTIVE_DATE_FORMAT_NAME"), "ADDITIONAL_SETTINGS"),
		"NAME_TRUNCATE_LEN" => array(
		    "PARENT" => "ADDITIONAL_SETTINGS",
		    "NAME" => GetMessage("COMP_ARTDEPO_GALLERY_ALBUM_LIST_NAME_TRUNCATE_LEN_NAME"),
		    "TYPE" => "STRING",
		),
		"SET_TITLE" => array(
		    "PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_ADGL_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),

		"CACHE_TIME" => array("DEFAULT" => "3600"),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, false);
$arComponentParameters["PARAMETERS"]["PAGER_SHOW_ALWAYS"]["DEFAULT"] = "N";
?>
