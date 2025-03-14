<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
		"DETAIL_PAGER_SETTINGS" => array(
			"NAME" => GetMessage("CN_P_DETAIL_PAGER_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"TIP"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HELPER_RUTUBELIST_PARAM_TIP_NAME"),
			"TYPE" => "LIST",
			"VALUES" => array(
   						"CHANNEL" => GetMessage("HELPER_RUTUBELIST_PARAM_CHANNEL_NAME"),
						"PLST" => GetMessage("HELPER_RUTUBELIST_PARAM_PLST_NAME"),
							),
		),
		"PLAYLISTS_CODE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HELPER_RUTUBELIST_PARAM_PLAYLISTS_CODE_NAME"),
			"TYPE" => "STRING",
		),
		"WIN1251"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HELPER_RUTUBELIST_PARAM_WIN1251_NAME"),
			"TYPE" => "CHECKBOX",
		),
		"RUTUBE_VIDEO_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HELPER_RUTUBELIST_PARAM_RUTUBE_VIDEO_COUNT_NAME"),
			"TYPE" => "STRING",
			'DEFAULT' => GetMessage("HELPER_RUTUBELIST_PARAM_RUTUBE_VIDEO_COUNT_DEFAULT"),
		),

		"CACHE_TIME_OUT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HELPER_RUTUBELIST_PARAM_CACHE_TIME_OUT_NAME"),
			"TYPE" => "STRING",
			'DEFAULT' => GetMessage("HELPER_RUTUBELIST_PARAM_CACHE_TIME_OUT_DEFAULT"),
		),

"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BN_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("HELPER_RUTUBELIST_PAGER_TITLE_PAGE") , false, true);
?>

