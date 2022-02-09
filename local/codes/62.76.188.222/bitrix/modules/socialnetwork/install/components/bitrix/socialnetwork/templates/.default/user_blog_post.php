<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="feed-blog-post-list feed-blog-post-detail">
<?
$pageId = "user_blog";
include("util_menu.php");
include("util_profile.php");

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
		"SET_TITLE" => "Y",
		"PAGE_ID" => $pageId,
	),
	$component
);

if(strlen($arResult["PATH_TO_USER_BLOG_CATEGORY"]) <= 0)
{
	$catUrl = $arResult["PATH_TO_USER_BLOG"];					
	if(strpos("?", $catUrl) === false)
		$catUrl .= "?";
	else
		$catUrl .= "&";
	$catUrl .= "category=#category_id#";
	$arResult["PATH_TO_USER_BLOG_CATEGORY"] = $catUrl;
}

$APPLICATION->IncludeComponent(
		"bitrix:socialnetwork.blog.post", 
		"", 
		Array(
				"POST_VAR"				=> $arResult["ALIASES"]["post_id"],
				"USER_VAR"				=> $arResult["ALIASES"]["user_id"],
				"PAGE_VAR"				=> $arResult["ALIASES"]["blog_page"],
				"PATH_TO_BLOG"			=> $arResult["PATH_TO_USER_BLOG"],
				"PATH_TO_POST" 			=> $arResult["PATH_TO_USER_BLOG_POST"],
				"PATH_TO_BLOG_CATEGORY"	=> $arResult["PATH_TO_USER_BLOG_CATEGORY"],
				"PATH_TO_POST_EDIT"		=> $arResult["PATH_TO_USER_BLOG_POST_EDIT"],
				"PATH_TO_USER"			=> $arResult["PATH_TO_USER"],
				"PATH_TO_SMILE" => $arParams["PATH_TO_BLOG_SMILE"], 
				"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
				"ID"					=> $arResult["VARIABLES"]["post_id"],
				"CACHE_TYPE"			=> $arResult["CACHE_TYPE"],
				"CACHE_TIME"			=> $arResult["CACHE_TIME"],
				"SET_NAV_CHAIN" 		=> "N", 
				"SET_TITLE"				=> "N",
				"POST_PROPERTY"			=> $arParams["POST_PROPERTY"],
				"DATE_TIME_FORMAT"		=> $arResult["DATE_TIME_FORMAT"],
				"USER_ID" 				=> $arResult["VARIABLES"]["user_id"],
				"GROUP_ID" 				=> $arParams["BLOG_GROUP_ID"],
				"USE_SOCNET" 			=> "Y",
				"NAME_TEMPLATE" 		=> $arParams["NAME_TEMPLATE"],
				"SHOW_LOGIN" 			=> $arParams["SHOW_LOGIN"],
				"DATE_TIME_FORMAT" 		=> $arResult["DATE_TIME_FORMAT"],
				"SHOW_YEAR" 			=> $arParams["SHOW_YEAR"],
				"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"],
				"PATH_TO_VIDEO_CALL" 	=> $arResult["PATH_TO_VIDEO_CALL"],
				"USE_SHARE" 			=> $arParams["USE_SHARE"],
				"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
				"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
				"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
				"SHARE_SHORTEN_URL_LOGIN"		=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
				"SHARE_SHORTEN_URL_KEY" 		=> $arParams["SHARE_SHORTEN_URL_KEY"],
				"SHOW_RATING" => $arParams["SHOW_RATING"],
				"RATING_TYPE" => $arParams["RATING_TYPE"],
				"IMAGE_MAX_WIDTH" => $arParams["BLOG_IMAGE_MAX_WIDTH"],
				"IMAGE_MAX_HEIGHT" => $arParams["BLOG_IMAGE_MAX_HEIGHT"],
				"ALLOW_POST_CODE" => $arParams["BLOG_ALLOW_POST_CODE"],
				"PATH_TO_GROUP" 		=> $arResult["PATH_TO_GROUP"],
				"ALLOW_VIDEO" 					=> $arParams["BLOG_COMMENT_ALLOW_VIDEO"],
				"ALLOW_IMAGE_UPLOAD" 			=> $arParams["BLOG_COMMENT_ALLOW_IMAGE_UPLOAD"],

			),
		$component 
	);
?>
</div>
<?
/*
?>
<div align="right">
	<?
	$APPLICATION->IncludeComponent(
			"bitrix:blog.rss.link",
			"group",
			Array(
					"RSS1"				=> "N",
					"RSS2"				=> "Y",
					"ATOM"				=> "N",
					"BLOG_VAR"			=> $arResult["ALIASES"]["blog"],
					"POST_VAR"			=> $arResult["ALIASES"]["post_id"],
					"GROUP_VAR"			=> $arResult["ALIASES"]["group_id"],
					"PATH_TO_POST_RSS"	=> $arResult["PATH_TO_USER_BLOG_POST_RSS"],
					"PATH_TO_RSS_ALL"	=> $arResult["PATH_TO_RSS_ALL"],
					"BLOG_URL"			=> $arResult["VARIABLES"]["blog"],
					"POST_ID"			=> $arResult["VARIABLES"]["post_id"],
					"MODE"				=> "C",
					"PARAM_GROUP_ID" 	=> $arParams["BLOG_GROUP_ID"],
					"USER_ID" => $arResult["VARIABLES"]["user_id"],
					"COMMENT_ALLOW_VIDEO" => $arParams["BLOG_COMMENT_ALLOW_VIDEO"],
					"NO_URL_IN_COMMENTS" => $arParams["BLOG_NO_URL_IN_COMMENTS"],
					"ALLOW_POST_CODE" => $arParams["BLOG_ALLOW_POST_CODE"],
				),
			$component 
		);
	?>
</div>
<?
*/