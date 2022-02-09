<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$pageId = "user_tasks";
include("util_menu.php");
include("util_profile.php");
?>
<?
if (CSocNetFeatures::IsActiveFeature(SONET_ENTITY_USER, $arResult["VARIABLES"]["user_id"], "tasks"))
{
	?>
	<?
	if (COption::GetOptionString("intranet", "use_tasks_2_0", "N") == "Y")
	{
		$APPLICATION->IncludeComponent(
			"bitrix:tasks.list",
			".default",
			Array(
				"USER_ID" => $arResult["VARIABLES"]["user_id"],
				"ITEMS_COUNT" => $arParams["ITEM_DETAIL_COUNT"],
				"PAGE_VAR" => $arResult["ALIASES"]["page"],
				"USER_VAR" => $arResult["ALIASES"]["user_id"],
				"VIEW_VAR" => $arResult["ALIASES"]["view_id"],
				"TASK_VAR" => $arResult["ALIASES"]["task_id"],
				"ACTION_VAR" => $arResult["ALIASES"]["action"],
				"PATH_TO_USER_PROFILE" => $arResult["PATH_TO_USER"],
				"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
				"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"],
				"PATH_TO_VIDEO_CALL" => $arResult["PATH_TO_VIDEO_CALL"],
				"PATH_TO_USER_TASKS" => $arResult["PATH_TO_USER_TASKS"],
				"PATH_TO_USER_TASKS_TASK" => $arResult["PATH_TO_USER_TASKS_TASK"],
				"PATH_TO_USER_TASKS_VIEW" => $arResult["PATH_TO_USER_TASKS_VIEW"],
				"PATH_TO_USER_TASKS_REPORT" => $arResult["PATH_TO_USER_TASKS_REPORT"],
				"PATH_TO_USER_TASKS_TEMPLATES" => $arResult["PATH_TO_USER_TASKS_TEMPLATES"],
				"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
				"PATH_TO_GROUP_TASKS_TASK" => $arResult["PATH_TO_GROUP_TASKS_TASK"],
				"PATH_TO_GROUP_TASKS_VIEW" => $arResult["PATH_TO_GROUP_TASKS_VIEW"],
				"PATH_TO_GROUP_TASKS_REPORT" => $arResult["PATH_TO_GROUP_TASKS_REPORT"],
				"TASKS_FIELDS_SHOW" => $arParams["TASKS_FIELDS_SHOW"],
				"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
				"SET_TITLE" => $arResult["SET_TITLE"],
				"FORUM_ID" => $arParams["TASK_FORUM_ID"],
				"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
				"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
				"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
				"SHOW_YEAR" => $arParams["SHOW_YEAR"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"USE_THUMBNAIL_LIST" => "N",
				"INLINE" => "Y",
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
	else
	{
		$APPLICATION->IncludeComponent("bitrix:intranet.tasks.menu", ".default", Array(
				"IBLOCK_ID" => $arParams["TASK_IBLOCK_ID"],
				"OWNER_ID" => $arResult["VARIABLES"]["user_id"],
				"TASK_TYPE" => 'user',
				"PAGE_VAR" => $arResult["ALIASES"]["page"],
				"USER_VAR" => $arResult["ALIASES"]["user_id"],
				"VIEW_VAR" => $arResult["ALIASES"]["view_id"],
				"TASK_VAR" => $arResult["ALIASES"]["task_id"],
				"ACTION_VAR" => $arResult["ALIASES"]["action"],
				"PATH_TO_USER_TASKS" => $arResult["PATH_TO_USER_TASKS"],
				"PATH_TO_USER_TASKS_TASK" => $arResult["PATH_TO_USER_TASKS_TASK"],
				"PATH_TO_USER_TASKS_VIEW" => $arResult["PATH_TO_USER_TASKS_VIEW"],
				"PAGE_ID" => "user_tasks",
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
		$APPLICATION->IncludeComponent(
			"bitrix:intranet.tasks",
			".default",
			Array(
				"IBLOCK_ID" => $arParams["TASK_IBLOCK_ID"],
				"OWNER_ID" => $arResult["VARIABLES"]["user_id"],
				"TASK_TYPE" => 'user',
				"ITEMS_COUNT" => $arParams["ITEM_DETAIL_COUNT"],
				"PAGE_VAR" => $arResult["ALIASES"]["page"],
				"USER_VAR" => $arResult["ALIASES"]["user_id"],
				"VIEW_VAR" => $arResult["ALIASES"]["view_id"],
				"TASK_VAR" => $arResult["ALIASES"]["task_id"],
				"ACTION_VAR" => $arResult["ALIASES"]["action"],
				"PATH_TO_USER" => $arResult["PATH_TO_USER"],
				"PATH_TO_MESSAGES_CHAT" => $arResult["PATH_TO_MESSAGES_CHAT"],
				"PATH_TO_CONPANY_DEPARTMENT" => $arParams["PATH_TO_CONPANY_DEPARTMENT"],
				"PATH_TO_VIDEO_CALL" => $arResult["PATH_TO_VIDEO_CALL"],
				"PATH_TO_USER_TASKS" => $arResult["PATH_TO_USER_TASKS"],
				"PATH_TO_USER_TASKS_TASK" => $arResult["PATH_TO_USER_TASKS_TASK"],
				"PATH_TO_USER_TASKS_VIEW" => $arResult["PATH_TO_USER_TASKS_VIEW"],
				"PATH_TO_GROUP_TASKS" => $arResult["PATH_TO_GROUP_TASKS"],
				"PATH_TO_GROUP_TASKS_TASK" => $arResult["PATH_TO_GROUP_TASKS_TASK"],
				"PATH_TO_GROUP_TASKS_VIEW" => $arResult["PATH_TO_GROUP_TASKS_VIEW"],
				"TASKS_FIELDS_SHOW" => $arParams["TASKS_FIELDS_SHOW"],
				"SET_NAV_CHAIN" => $arResult["SET_NAV_CHAIN"],
				"SET_TITLE" => $arResult["SET_TITLE"],
				"FORUM_ID" => $arParams["TASK_FORUM_ID"],
				"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
				"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
				"DATE_TIME_FORMAT" => $arResult["DATE_TIME_FORMAT"],
				"SHOW_YEAR" => $arParams["SHOW_YEAR"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"USE_THUMBNAIL_LIST" => "N",
				"INLINE" => "Y",
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
}
?>