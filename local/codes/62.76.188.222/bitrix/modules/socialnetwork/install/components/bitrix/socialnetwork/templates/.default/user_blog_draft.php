<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$pageId = "user_blog";
include("util_menu.php");
include("util_profile.php");
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.blog.menu",
	"",
	Array(
		"PATH_TO_USER" => $arResult["PATH_TO_USER"],
		"PATH_TO_POST_EDIT" => $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
		"PATH_TO_DRAFT" => $arResult["PATH_TO_USER_BLOG_DRAFT"],
		"USER_ID" => $arResult["VARIABLES"]["user_id"],
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
		"POST_VAR" => $arResult["ALIASES"]["post_id"],
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"],
		"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
		"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
		"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
		"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
		"PATH_TO_MODERATION" => $arResult["PATH_TO_USER_BLOG_MODERATION"],
		"CURRENT_PAGE" => "draft",
	),
	$component
);
if(strpos($arResult["PATH_TO_USER_BLOG"], "?") === false)
	$arResult["PATH_TO_BLOG_CATEGORY"] = $arResult["PATH_TO_USER_BLOG"]."?category=#category_id#";
else
	$arResult["PATH_TO_BLOG_CATEGORY"] = $arResult["PATH_TO_USER_BLOG"]."&category=#category_id#";

$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.blog.draft",
	"",
	Array(
		"MESSAGE_COUNT" => "25", 
		"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"], 
		"PATH_TO_BLOG" => $arResult["PATH_TO_USER_BLOG"], 
		"PATH_TO_BLOG_CATEGORY" => $arResult["PATH_TO_BLOG_CATEGORY"],
		"PATH_TO_POST" => $arResult["PATH_TO_USER_BLOG_POST"], 
		"PATH_TO_POST_EDIT" => $arResult["PATH_TO_USER_BLOG_POST_EDIT"], 
		"PATH_TO_USER" => $arResult["PATH_TO_USER"], 
		"PATH_TO_SMILE" => $arParams["PATH_TO_BLOG_SMILE"], 
		"USER_ID" => $arResult["VARIABLES"]["user_id"], 
		"CACHE_TYPE" => $arResult["CACHE_TYPE"], 
		"CACHE_TIME" => $arResult["CACHE_TIME"], 
		"CACHE_TIME_LONG" => "604800", 
		"SET_NAV_CHAIN" => "N", 
		"SET_TITLE" => $arResult["SET_TITLE"], 
		"NAV_TEMPLATE" => "", 
		"POST_PROPERTY_LIST" => array(),
		"USER_VAR" => $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" => $arResult["ALIASES"]["blog_page"],
		"POST_VAR" => $arResult["ALIASES"]["post_id"],
		"GROUP_ID" => $arParams["BLOG_GROUP_ID"],
		"IMAGE_MAX_WIDTH" => $arParams["BLOG_IMAGE_MAX_WIDTH"],
		"IMAGE_MAX_HEIGHT" => $arParams["BLOG_IMAGE_MAX_HEIGHT"],
		"ALLOW_POST_CODE" => $arParams["BLOG_ALLOW_POST_CODE"],
		"PATH_TO_GROUP" 		=> $arResult["PATH_TO_GROUP"],
	),
	$component
);
?>
