<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$pageId = "user_log";
include("util_menu.php");
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.log", 
	"", 
	Array(
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"GROUP_VAR" => $arResult["ALIASES"]["group_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["page"],
		"LOG_VAR" => $arResult["ALIASES"]["log_id"],
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
		"PATH_TO_VIDEO_CALL" => $arResult["PATH_TO_VIDEO_CALL"],
		"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
		"PATH_TO_SMILE" => $arResult["PATH_TO_SMILE"],
		"PATH_TO_USER_BLOG_POST" => $arResult["PATH_TO_USER_BLOG_POST"],
		"PATH_TO_GROUP_BLOG_POST" => $arResult["PATH_TO_GROUP_BLOG_POST"],
		"PATH_TO_LOG" => $arResult["PATH_TO_LOG"],
		"LOG_ID" => $arResult["VARIABLES"]["log_id"],
		"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
		"SET_TITLE" => $arResult["SET_TITLE"],
		"ITEMS_COUNT" => $arParams["ITEM_DETAIL_COUNT"],
		"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
		"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
		"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
		"SHOW_YEAR" => $arParams["SHOW_YEAR"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"],
		"SHOW_EVENT_ID_FILTER" => "N",
		"SHOW_SETTINGS_LINK" => "N",
		"SHOW_NAV_STRING" => "N",
		"SET_LOG_CACHE" => "N",
		"USE_COMMENTS" => "Y",
		"BLOG_ALLOW_POST_CODE" => $arParams["BLOG_ALLOW_POST_CODE"],
		"PAGER_DESC_NUMBERING" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"SHOW_RATING" => $arParams["SHOW_RATING"],
		"RATING_TYPE" => $arParams["RATING_TYPE"],
		"AVATAR_SIZE" => $arParams["LOG_THUMBNAIL_SIZE"],
		"AVATAR_SIZE_COMMENT" => $arParams["LOG_COMMENT_THUMBNAIL_SIZE"],
		"NEW_TEMPLATE" => $arParams["LOG_NEW_TEMPLATE"],
		"SUBSCRIBE_ONLY" => "N",
	),
	$component 
);
?>