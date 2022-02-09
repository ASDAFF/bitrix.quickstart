<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$pageId = "user_forum";
include("util_menu.php");
include("util_profile.php");
?>
<?$APPLICATION->IncludeComponent("bitrix:socialnetwork.forum.topic.list", "", 
	Array(
		"FID"	=>	$arParams["FORUM_ID"],
		"USE_DESC_PAGE" => $arParams["USE_DESC_PAGE"], 
		"SOCNET_GROUP_ID" => 0, 
		"USER_ID" => $arResult["VARIABLES"]["user_id"], 
		"URL_TEMPLATES_TOPIC_LIST"	=>	$arResult["~PATH_TO_USER_FORUM"],
		"URL_TEMPLATES_TOPIC"	=>	$arResult["~PATH_TO_USER_FORUM_TOPIC"],
		"URL_TEMPLATES_MESSAGE" =>  $arResult["~PATH_TO_USER_FORUM_MESSAGE"],
		"URL_TEMPLATES_TOPIC_EDIT"	=>	$arResult["~PATH_TO_USER_FORUM_TOPIC_EDIT"],
		"URL_TEMPLATES_PROFILE_VIEW"	=>	$arResult["~PATH_TO_USER"],
		"PAGEN" =>  $arParams["PAGEN"], 
		"PAGE_NAVIGATION_TEMPLATE" => $arParams["PAGE_NAVIGATION_TEMPLATE"], 
		"PAGE_NAVIGATION_WINDOW" => $arParams["PAGE_NAVIGATION_WINDOW"], 
		"TOPICS_PER_PAGE"	=>	$arParams["TOPICS_PER_PAGE"],
		"MESSAGES_PER_PAGE"	=>	$arParams["MESSAGES_PER_PAGE"],
		"DATE_FORMAT"	=>	$arParams["DATE_FORMAT"],
		"DATE_TIME_FORMAT"	=>	$arParams["DATE_TIME_FORMAT"],
		"WORD_LENGTH"	=>	$arParams["WORD_LENGTH"],
		"SET_TITLE"	=>	"N",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"TMPLT_SHOW_ADDITIONAL_MARKER"	=>	$arParams["~TMPLT_SHOW_ADDITIONAL_MARKER"]
	), 
	$component,
	array("HIDE_ICONS" => "Y") 
);?>