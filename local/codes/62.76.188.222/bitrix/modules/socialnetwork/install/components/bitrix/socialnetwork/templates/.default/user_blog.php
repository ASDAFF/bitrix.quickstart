<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="feed-blog-post-list">
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
		"PATH_TO_MODERATION" => $arResult["PATH_TO_USER_BLOG_MODERATION"],
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
		"SET_TITLE" => "Y",
		"PAGE_ID" => $pageId,
		"CURRENT_PAGE" => "blog",
	),
	$component
);

if(COption::GetOptionString("blog", "socNetNewPerms", "N") == "N" && $USER->IsAdmin() && !file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bitrix24"))
{
	?>
	<div class="feed-add-error">
		<span class="feed-add-info-icon"></span><span class="feed-add-info-text"><?=GetMessage("BLG_SOCNET_REINDEX", Array("#url#" => $arResult["PATH_TO_REINDEX"]))?></span>
	</div>
	<?
}

$bType = "user";
if(IntVal($arResult["VARIABLES"]["user_id"]) > 0 && $USER->GetID() == $arResult["VARIABLES"]["user_id"])
{
	if($_REQUEST["forme"] == "Y")
		$bType = "forme";
	elseif($_REQUEST["mine"] == "Y")
		$bType = "mine";
	else
		$bType = "all";
}
$arLogParams = Array(
			"ENTITY_TYPE" => "",
			"USER_VAR" => $arParams["VARIABLE_ALIASES"]["user_id"],
			"GROUP_VAR" => $arParams["VARIABLE_ALIASES"]["group_id"],
			"PATH_TO_USER" => $arResult["PATH_TO_USER"],
			"PATH_TO_GROUP" => $arResult["PATH_TO_GROUP"],
			"SET_TITLE" => "N",
			"AUTH" => "Y",
			"SET_NAV_CHAIN" => "N",
			"PATH_TO_MESSAGES_CHAT" => $arParams["PM_URL"],
			"PATH_TO_VIDEO_CALL" => $arParams["PATH_TO_VIDEO_CALL"],
			"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"],
			"PATH_TO_GROUP_PHOTO_SECTION" => $arParams["PARENT_COMPONENT_RESULT"]["PATH_TO_GROUP_PHOTO_SECTION"],
			"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
			"SHOW_YEAR" => $arParams["SHOW_YEAR"],
			"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
			"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
			"SUBSCRIBE_ONLY" => "N",
			"SHOW_EVENT_ID_FILTER" => "Y",
			"USE_COMMENTS" => "Y",
			"PHOTO_THUMBNAIL_SIZE" => "48",
			"PAGE_ISDESC" => "N",
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_SHADOW" => "N",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"CONTAINER_ID" => "log_external_container",
			"PAGE_SIZE" => 10,
			//"LOG_DATE_DAYS" => 365,
			"SHOW_RATING" => $arParams["SHOW_RATING"],
			"RATING_TYPE" => $arParams["RATING_TYPE"],
			"PAGETITLE_TARGET" => "pagetitle_log",
			"SHOW_SETTINGS_LINK" => "Y",
			"AVATAR_SIZE" => $arParams["LOG_THUMBNAIL_SIZE"],
			"AVATAR_SIZE_COMMENT" => $arParams["LOG_COMMENT_THUMBNAIL_SIZE"],
			"NEW_TEMPLATE" => $arParams["LOG_NEW_TEMPLATE"],
			"USER_ID" => 0,
			"GROUP_ID" => 0,
			"EXACT_EVENT_ID" => "blog_post",
			"SET_LOG_CACHE" => "Y",
			"BLOG_COMMENT_ALLOW_VIDEO"  => $arParams["BLOG_COMMENT_ALLOW_VIDEO"],
			"BLOG_COMMENT_ALLOW_IMAGE_UPLOAD" => $arParams["BLOG_COMMENT_ALLOW_IMAGE_UPLOAD"],
			"CURRENT_USER_ID" => $arResult["VARIABLES"]["user_id"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
		);
if($bType == "all")
{
	$arLogParams["DISPLAY"] = "my";
}
elseif($bType == "mine")
{
	$arLogParams["DISPLAY"] = "mine";
}
elseif($bType == "forme")
{
	$arLogParams["DISPLAY"] = "forme";
}
else
{
	$arLogParams["DISPLAY"] = $arResult["VARIABLES"]["user_id"];
}
?><div id="log_external_container"></div><?
$APPLICATION->IncludeComponent(
	"bitrix:socialnetwork.log",
	"",
	$arLogParams,
	$component,
	array("HIDE_ICONS"=>"Y")
);
?>
</div>