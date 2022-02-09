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
	),
	$component
);
?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.blog.post.edit",
	"",
	Array(
		"ID"						=> $arResult["VARIABLES"]["post_id"],
		"PATH_TO_BLOG"				=> $arResult["PATH_TO_USER_BLOG"],
		"PATH_TO_POST"				=> $arResult["PATH_TO_USER_BLOG_POST"],
		"PATH_TO_POST_EDIT"			=> $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
		"PATH_TO_USER"				=> $arResult["PATH_TO_USER"],
		"PATH_TO_DRAFT"				=> $arResult["PATH_TO_USER_BLOG_DRAFT"], 
		"PATH_TO_SMILE" 			=> $arParams["PATH_TO_BLOG_SMILE"], 
		"SET_TITLE"					=> $arResult["SET_TITLE"],
		"GROUP_ID"					=>$arParams["BLOG_GROUP_ID"],
		"POST_PROPERTY" 			=> $arParams["POST_PROPERTY"],
		"DATE_TIME_FORMAT" 			=> $arResult["DATE_TIME_FORMAT"],
		"USER_ID"					=> $arResult["VARIABLES"]["user_id"],
		"USER_VAR" 					=> $arResult["ALIASES"]["user_id"],
		"PAGE_VAR" 					=> $arResult["ALIASES"]["blog_page"],
		"POST_VAR" 					=> $arResult["ALIASES"]["post_id"],
		"SET_NAV_CHAIN" 			=> "N", 
		"USE_SOCNET" 				=> "Y",
		"ALLOW_POST_MOVE" 			=> $arParams["ALLOW_POST_MOVE"],
		"PATH_TO_BLOG_POST" 		=> $arParams["PATH_TO_BLOG_POST"],
		"PATH_TO_BLOG_POST_EDIT" 	=> $arParams["PATH_TO_BLOG_POST_EDIT"],
		"PATH_TO_BLOG_DRAFT" 		=> $arParams["PATH_TO_BLOG_DRAFT"],
		"PATH_TO_BLOG_BLOG" 		=> $arParams["PATH_TO_BLOG_BLOG"],
		"PATH_TO_USER_POST" 		=> $arResult["PATH_TO_USER_BLOG_POST"],
		"PATH_TO_USER_POST_EDIT" 	=> $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
		"PATH_TO_USER_DRAFT" 		=> $arResult["PATH_TO_USER_BLOG_DRAFT"],
		"PATH_TO_USER_BLOG" 		=> $arResult["PATH_TO_USER_BLOG"],
		"PATH_TO_GROUP_POST" 		=> $arResult["PATH_TO_GROUP_BLOG_POST"],
		"PATH_TO_GROUP_POST_EDIT" 	=> $arResult["PATH_TO_GROUP_BLOG_POST_EDIT"],
		"PATH_TO_GROUP_DRAFT"		=> $arResult["PATH_TO_GROUP_BLOG_DRAFT"],
		"PATH_TO_GROUP_BLOG" 		=> $arResult["PATH_TO_GROUP_BLOG"],
		"NAME_TEMPLATE" 			=> $arParams["NAME_TEMPLATE"],
		"SHOW_LOGIN" 				=> $arParams["SHOW_LOGIN"],
		"IMAGE_MAX_WIDTH" => $arParams["BLOG_IMAGE_MAX_WIDTH"],
		"IMAGE_MAX_HEIGHT" => $arParams["BLOG_IMAGE_MAX_HEIGHT"],
		"ALLOW_POST_CODE" => $arParams["BLOG_ALLOW_POST_CODE"],
		"USE_GOOGLE_CODE" => $arParams["BLOG_USE_GOOGLE_CODE"],	
		"USE_CUT" => $arParams["BLOG_USE_CUT"],	
	),
	$component
);
?>
