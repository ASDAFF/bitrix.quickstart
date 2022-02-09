<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$pageId = "user_calendar";
include("util_menu.php");
include("util_profile.php");
?>
<?
$ownerId = $arResult["VARIABLES"]["user_id"];
if (isset($_GET['bx_enable_calendar']) && $_GET['bx_enable_calendar'] == 'Y')
{
	$rsFeatures = CSocNetFeatures::GetList(
		array(),
		array("ENTITY_ID" => $ownerId, "ENTITY_TYPE" => SONET_ENTITY_USER, "FEATURE" => 'calendar')
	);
	if ($arFeature = $rsFeatures->Fetch())
	{
		CSocNetFeatures::Update($arFeature['ID'], array('ACTIVE' => 'Y'));
		unset($GLOBALS['SONET_FEATURES_CACHE']);
	}
}
if (CSocNetFeatures::IsActiveFeature(SONET_ENTITY_USER, $ownerId, "calendar"))
{
	$calendar2 = (
		(
			!IsModuleInstalled("intranet") 		
			|| COption::GetOptionString("intranet", "calendar_2", "N") == "Y"
		) 
		&& CModule::IncludeModule("calendar")
	);
	if ($calendar2)
	{
		$APPLICATION->IncludeComponent(
			"bitrix:calendar.grid",
			"",
			Array(
				"CALENDAR_TYPE" => 'user',
				"OWNER_ID" => $ownerId,
				"ALLOW_SUPERPOSE" => $arParams['CALENDAR_ALLOW_SUPERPOSE'],
				"ALLOW_RES_MEETING" => $arParams["CALENDAR_ALLOW_RES_MEETING"],
				"SET_TITLE" => 'Y',
				"SET_NAV_CHAIN" => 'Y'
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
	else
	{
		$APPLICATION->IncludeComponent(
			"bitrix:intranet.event_calendar",
			".default",
			Array(
				"IBLOCK_TYPE" => $arParams['CALENDAR_IBLOCK_TYPE'], 
				"IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
				"OWNER_ID" => $ownerId,
				"OWNER_TYPE" => 'USER', // 'USER', 'GROUP' or '' for standart mode
				"INIT_DATE" => "", 
				"WEEK_HOLIDAYS" => $arParams['CALENDAR_WEEK_HOLIDAYS'],
				"YEAR_HOLIDAYS" => $arParams['CALENDAR_YEAR_HOLIDAYS'], 
				"LOAD_MODE" => "ajax", 
				"USE_DIFFERENT_COLORS" => "Y",
				"EVENT_COLORS" => "", 
				"ADVANCED_MODE_SETTINGS" => "Y",
				"SET_TITLE" => 'Y',
				"SET_NAV_CHAIN" => 'Y',
				"WORK_TIME_START" => $arParams['CALENDAR_WORK_TIME_START'],
				"WORK_TIME_END" => $arParams['CALENDAR_WORK_TIME_END'],
				"PATH_TO_USER" => $arResult["PATH_TO_USER"],
				"PATH_TO_USER_CALENDAR" => $arResult["PATH_TO_USER_CALENDAR"],
				"ALLOW_SUPERPOSE" => $arParams['CALENDAR_ALLOW_SUPERPOSE'],
				"SUPERPOSE_GROUPS_CALS" => $arParams['CALENDAR_SUPERPOSE_GROUPS_CALS'],
				"SUPERPOSE_USERS_CALS" => $arParams['CALENDAR_SUPERPOSE_USERS_CALS'],
				"SUPERPOSE_CUR_USER_CALS" => $arParams['CALENDAR_SUPERPOSE_CUR_USER_CALS'],
				"SUPERPOSE_CAL_IDS" => $arParams['CALENDAR_SUPERPOSE_CAL_IDS'],
				"SUPERPOSE_GROUPS_IBLOCK_ID" => $arParams['CALENDAR_SUPERPOSE_GROUPS_IBLOCK_ID'],
				"SUPERPOSE_USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
				"USERS_IBLOCK_ID" => $arParams['CALENDAR_USER_IBLOCK_ID'],
				"ALLOW_RES_MEETING" => $arParams["CALENDAR_ALLOW_RES_MEETING"],
				"RES_MEETING_IBLOCK_ID" => $arParams["CALENDAR_RES_MEETING_IBLOCK_ID"],
				"PATH_TO_RES_MEETING" => $arParams["CALENDAR_PATH_TO_RES_MEETING"],
				"RES_MEETING_USERGROUPS" => $arParams["CALENDAR_RES_MEETING_USERGROUPS"],
				"REINVITE_PARAMS_LIST" => $arParams["CALENDAR_REINVITE_PARAMS_LIST"],
				"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
				"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
				"ALLOW_VIDEO_MEETING" => $arParams["CALENDAR_ALLOW_VIDEO_MEETING"],
				"VIDEO_MEETING_IBLOCK_ID" => $arParams["CALENDAR_VIDEO_MEETING_IBLOCK_ID"],
				"PATH_TO_VIDEO_MEETING" => $arParams["CALENDAR_PATH_TO_VIDEO_MEETING"],
				"PATH_TO_VIDEO_MEETING_DETAIL" => $arParams["CALENDAR_PATH_TO_VIDEO_MEETING_DETAIL"],
				"VIDEO_MEETING_USERGROUPS" => $arParams["CALENDAR_VIDEO_MEETING_USERGROUPS"],
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);
	}
}
elseif(strpos(POST_FORM_ACTION_URI, 'bx_enable_calendar=Y') === false)
{
	$url = POST_FORM_ACTION_URI.(strpos(POST_FORM_ACTION_URI, '?') === false ? '?' : '&').'bx_enable_calendar=Y';
	echo GetMessage('SONET_U_CALENDAR_DIS_MES').' <a href="'.$url.'" title="'.GetMessage('SONET_U_CALENDAR_DIS_TITLE').'">'.GetMessage('SONET_U_CALENDAR_TURN_ON').'</a>';
}
?>