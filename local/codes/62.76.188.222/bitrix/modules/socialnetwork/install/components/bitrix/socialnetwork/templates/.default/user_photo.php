<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$pageId = "user_photo";
include("util_menu.php");
include("util_profile.php");
?><?
if ($arParams["FATAL_ERROR"] == "Y"):
	if (!empty($arParams["ERROR_MESSAGE"])):
		ShowError($arParams["ERROR_MESSAGE"]);
	else:
		ShowNote($arParams["NOTE_MESSAGE"], "notetext-simple");
	endif;
	return false;
endif;

?>
<?$result = $APPLICATION->IncludeComponent(
	"bitrix:photogallery.user",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"PAGE_NAME" => "INDEX",
		"USER_ALIAS" => $arResult["VARIABLES"]["GALLERY"]["CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],

		"SORT_BY" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_BY"],
		"SORT_ORD" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_ORD"],

		"INDEX_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"GALLERY_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"GALLERIES_URL" => $arResult["~PATH_TO_USER_PHOTO_GALLERIES"],
		"GALLERY_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_GALLERY_EDIT"],
		"SECTION_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT"],
		"SECTION_EDIT_ICON_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT_ICON"],
		"UPLOAD_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_UPLOAD"],

		"ONLY_ONE_GALLERY" => $arParams["PHOTO"]["ALL"]["ONLY_ONE_GALLERY"],
		"GALLERY_GROUPS" => $arParams["PHOTO"]["ALL"]["GALLERY_GROUPS"],
		"GALLERY_SIZE" => $arParams["PHOTO"]["ALL"]["GALLERY_SIZE"],

		"RETURN_ARRAY" => "Y",
		"SET_TITLE" => "N",
		"SET_NAV_CHAIN" => "N",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],

		"GALLERY_AVATAR_SIZE"	=>	$arParams["GALLERY_AVATAR_SIZE"]
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<br />
<?
if ($arParams["PERMISSION"] >= "U")
{
?>
<div id="photo-main-page-right">
	<noindex>
	<div class="photo-controls photo-controls-buttons photo-controls-socnet-gallery">
		<ul class="photo-controls">
			<li class="photo-control photo-control-album-add">
				<a rel="nofollow" href="<?=$result["ALL"]["GALLERY"]["LINK"]["~NEW"]?>" <?
					?>onclick="EditAlbum('<?=CUtil::JSEscape($result["ALL"]["GALLERY"]["LINK"]["~NEW"])?>'); return false;"><span><?=GetMessage("P_ADD_ALBUM")?></span></a>
			</li>
<??>
			<li class="photo-control photo-control-last photo-control-album-upload">
				<a rel="nofollow" href="<?=$result["ALL"]["GALLERY"]["LINK"]["UPLOAD"]?>" target="_self"><span><?=GetMessage("P_UPLOAD")?></span></a>
			</li>
		</ul>
	</div>
	</noindex>
</div>
<?
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:photogallery.section.list",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["PHOTO_USER_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["PHOTO_USER_IBLOCK_ID"],
		"BEHAVIOUR" => "USER",
		"USER_ALIAS" => $arResult["VARIABLES"]["GALLERY"]["CODE"],
		"PERMISSION" => $arResult["VARIABLES"]["PERMISSION"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],

		"SORT_BY" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_BY"],
		"SORT_ORD" => $arParams["PHOTO"]["ALL"]["SECTION_SORT_ORD"],

		"DETAIL_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT"],
		"GALLERIES_URL" => $arResult["~PATH_TO_USER_PHOTO_GALLERIES"],
		"GALLERY_URL" => $arResult["~PATH_TO_USER_PHOTO"],
		"SECTION_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION"],
		"SECTION_EDIT_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT"],
		"SECTION_EDIT_ICON_URL" => $arResult["~PATH_TO_USER_PHOTO_SECTION_EDIT_ICON"],
		"UPLOAD_URL" => $arResult["~PATH_TO_USER_PHOTO_ELEMENT_UPLOAD"],

		"PAGE_ELEMENTS" => $arParams["PHOTO"]["ALL"]["SECTION_PAGE_ELEMENTS"],
		"PAGE_NAVIGATION_TEMPLATE" => $arParams["PHOTO"]["ALL"]["PAGE_NAVIGATION_TEMPLATE"],
 		"DATE_TIME_FORMAT" => $arParams["PHOTO"]["ALL"]["DATE_TIME_FORMAT_SECTION"],
		"ALBUM_PHOTO_THUMBS_SIZE"	=>	$arParams["PHOTO"]["ALL"]["ALBUM_PHOTO_THUMBS_SIZE"],
		"ALBUM_PHOTO_SIZE"	=>	$arParams["PHOTO"]["ALL"]["ALBUM_PHOTO_SIZE"],
		"GALLERY_SIZE" => $arParams["PHOTO"]["ALL"]["GALLERY_SIZE"],

		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"SET_TITLE" => ($arResult["VARIABLES"]["SECTION_ID"] > 0 ? $arParams["SET_TITLE"] : "N"),
		"ADD_CHAIN_ITEM" => "N",
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],

		"SHOW_TAGS" => $arParams["SHOW_TAGS"],

		"USE_COMMENTS" => $arParams["PHOTO"]["ALL"]["USE_COMMENTS"],
		"SHOW_COMMENTS" => $arParams["PHOTO"]["ALL"]["USE_COMMENTS"],
		"COMMENTS_TYPE" => $arParams["PHOTO"]["ALL"]["COMMENTS_TYPE"],
		"MAX_VOTE" => $arParams["PHOTO"]["ALL"]["MAX_VOTE"],
		"VOTE_NAMES" => $arParams["PHOTO"]["ALL"]["VOTE_NAMES"],
		"COMMENTS_COUNT" => $arParams["PHOTO"]["ALL"]["COMMENTS_COUNT"],
		"PATH_TO_SMILE" => $arParams["PHOTO"]["ALL"]["PATH_TO_SMILE"],
		"FORUM_ID" => $arParams["PHOTO"]["ALL"]["FORUM_ID"],
		"USE_CAPTCHA" => $arParams["PHOTO"]["ALL"]["USE_CAPTCHA"],
		"POST_FIRST_MESSAGE" => $arParams["PHOTO"]["ALL"]["POST_FIRST_MESSAGE"],
		"PREORDER" => $arParams["PHOTO"]["ALL"]["PREORDER"],
		"SHOW_LINK_TO_FORUM" => "N",
		"BLOG_URL" => $arParams["PHOTO"]["ALL"]["BLOG_URL"],
		"PATH_TO_BLOG" => $arParams["PHOTO"]["ALL"]["PATH_TO_BLOG"],
		"PATH_TO_USER" => $arParams["PHOTO"]["ALL"]["PATH_TO_USER"],
		"NAME_TEMPLATE" => $arParams["PHOTO"]["ALL"]["NAME_TEMPLATE"],
		"SHOW_LOGIN" => $arParams["PHOTO"]["ALL"]["SHOW_LOGIN"]
	),
	$component,
	array("HIDE_ICONS" => "Y")
);
?>
<?
if ($arParams["PERMISSION"] >= "U")
{
?>
<script>
function __photo_check_right_height()
{
	var res = document.getElementsByTagName('li');
	var result = false;
	for (var ii = 0; ii < res.length; ii++)
	{
		if (res[ii].id.match(/photo\_album\_info\_(\d+)/gi))
		{
			var kk = res[ii].offsetHeight;
			var jj = document.getElementById('photo-main-page-right');
			if (jj && kk > 0) {
				jj.style.height = ((parseInt(jj.offsetHeight / kk) + 1) * kk + 1 + 'px');
				result = true;
				break;
			}
		}
	}
	if (!result)
	{
		setTimeout(__photo_check_right_height, 150);
	}
}
setTimeout(__photo_check_right_height, 150);
</script>
<?
}
?>