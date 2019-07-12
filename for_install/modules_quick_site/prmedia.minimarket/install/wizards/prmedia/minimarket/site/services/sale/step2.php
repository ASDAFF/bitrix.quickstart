<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!CModule::IncludeModule('sale'))
{
	return;
}

$moduleId = 'prmedia.minimarket';

$defaultCurrency = 'RUB';
$lang = 'ru';
$rsSite = CSite::GetByID(WIZARD_SITE_ID);
if ($site = $rsSite->Fetch())
{
	$lang = $site['LANGUAGE_ID'];
}
if (empty($lang))
{
	$lang = 'ru';
}

// module 'main' settings
$shopemail = $wizard->GetVar('shopemail');
$sitename = $wizard->GetVar('sitename');
COption::SetOptionString('main', 'email_from', $shopemail);
COption::SetOptionString('main', 'new_user_registration', 'Y');
COption::SetOptionString('main', 'captcha_registration', 'Y');
COption::SetOptionString('main', 'site_name', $sitename);
COption::SetOptionInt('search', 'suggest_save_days', 250);
$captchaPresets = COption::GetOptionString('main', 'CAPTCHA_presets', '');
if (empty($captchaPresets))
{
	COption::SetOptionString('main', 'CAPTCHA_transparentTextPercent', '0');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_numEllipses', '0');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_1', '7F7F7F');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_bLinesOverText', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_numLines', '0');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_textStartX', '40');
	COption::SetOptionString('main', 'CAPTCHA_textFontSize', '26');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_1', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_2', '000000');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_1', '-15');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_2', '15');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_1', '-2');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_2', '-2');
	COption::SetOptionString('main', 'CAPTCHA_bWaveTransformation', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_arBorderColor', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTTFFiles', 'bitrix_captcha.ttf');
	COption::SetOptionString('main', 'CAPTCHA_letters', 'ABCDEFGHJKLMNPQRSTWXYZ23456789');
	COption::SetOptionString('main', 'CAPTCHA_presets', '2');
}
COption::SetOptionString('socialnetwork', 'allow_tooltip', 'N', false, WIZARD_SITE_ID);

//Edit profile task
$editProfileTask = false;
$dbResult = CTask::GetList(Array(), Array('NAME' => 'main_change_profile'));
if ($arTask = $dbResult->Fetch())
	$editProfileTask = $arTask['ID'];
//Registered users group
$dbResult = CGroup::GetList($by, $order, Array('STRING_ID' => 'REGISTERED_USERS'));
if (!$dbResult->Fetch())
{
	$group = new CGroup;
	$arFields = Array(
		'ACTIVE' => 'Y',
		'C_SORT' => 3,
		'NAME' => GetMessage('REGISTERED_USERS'),
		'STRING_ID' => 'REGISTERED_USERS',
	);

	$groupID = $group->Add($arFields);
	if ($groupID > 0)
	{
		COption::SetOptionString('main', 'new_user_registration_def_group', $groupID);
		if ($editProfileTask)
			CGroup::SetTasks($groupID, Array($editProfileTask), true);
	}
}

$rsGroups = CGroup::GetList(($by = 'c_sort'), ($order = 'desc'), array('ACTIVE' => 'Y', 'ADMIN' => 'N', 'ANONYMOUS' => 'N'));
if (!($rsGroups->Fetch()))
{
	$group = new CGroup;
	$arFields = Array(
		'ACTIVE' => 'Y',
		'C_SORT' => 100,
		'NAME' => GetMessage('REGISTERED_USERS'),
		'DESCRIPTION' => '',
	);
	$NEW_GROUP_ID = $group->Add($arFields);
	COption::SetOptionString('main', 'new_user_registration_def_group', $NEW_GROUP_ID);

	$rsTasks = CTask::GetList(array(), array('MODULE_ID' => 'main', 'SYS' => 'Y', 'BINDIG' => 'module', 'LETTER' => 'P'));
	if ($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($NEW_GROUP_ID, $arTask['MODULE_ID'], $arTask['ID']);
	}
}

$userGroupID = '';
$dbGroup = CGroup::GetList($by = '', $order = '', Array('STRING_ID' => 'sale_administrator'));
if ($arGroup = $dbGroup->Fetch())
{
	$userGroupID = $arGroup['ID'];
}
else
{
	$group = new CGroup;
	$arFields = Array(
		'ACTIVE' => 'Y',
		'C_SORT' => 200,
		'NAME' => GetMessage('SALE_WIZARD_ADMIN_SALE'),
		'DESCRIPTION' => GetMessage('SALE_WIZARD_ADMIN_SALE_DESCR'),
		'USER_ID' => array(),
		'STRING_ID' => 'sale_administrator',
	);
	$userGroupID = $group->Add($arFields);
}

if (IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, '/bitrix/admin'), Array($userGroupID => 'R'));
	WizardServices::SetFilePermission(Array($siteID, '/bitrix/admin'), Array($userGroupID => 'R'));

	$new_task_id = CTask::Add(array(
			'NAME' => GetMessage('SALE_WIZARD_ADMIN_SALE'),
			'DESCRIPTION' => GetMessage('SALE_WIZARD_ADMIN_SALE_DESCR'),
			'LETTER' => 'Q',
			'BINDING' => 'module',
			'MODULE_ID' => 'main',
	));
	if ($new_task_id)
	{
		$arOps = array();
		$rsOp = COperation::GetList(array(), array('NAME' => 'cache_control|view_own_profile|edit_own_profile'));
		while ($arOp = $rsOp->Fetch())
			$arOps[] = $arOp['ID'];
		CTask::SetOperations($new_task_id, $arOps);
	}

	$rsTasks = CTask::GetList(array(), array('MODULE_ID' => 'main', 'SYS' => 'N', 'BINDIG' => 'module', 'LETTER' => 'Q'));
	if ($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask['MODULE_ID'], $arTask['ID']);
	}

	CMain::SetGroupRight('sale', $userGroupID, 'U');

	$rsTasks = CTask::GetList(array(), array('MODULE_ID' => 'catalog', 'SYS' => 'Y', 'BINDIG' => 'module', 'LETTER' => 'T'));
	while ($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask['MODULE_ID'], $arTask['ID']);
	}
}

$userGroupID = '';
$dbGroup = CGroup::GetList($by = '', $order = '', Array('STRING_ID' => 'content_editor'));

if ($arGroup = $dbGroup->Fetch())
{
	$userGroupID = $arGroup['ID'];
}
else
{
	$group = new CGroup;
	$arFields = Array(
		'ACTIVE' => 'Y',
		'C_SORT' => 300,
		'NAME' => GetMessage('SALE_WIZARD_CONTENT_EDITOR'),
		'DESCRIPTION' => GetMessage('SALE_WIZARD_CONTENT_EDITOR_DESCR'),
		'USER_ID' => array(),
		'STRING_ID' => 'content_editor',
	);
	$userGroupID = $group->Add($arFields);
	$DB->Query("INSERT INTO b_sticker_group_task(GROUP_ID, TASK_ID)	SELECT " . intVal($userGroupID) . ", ID FROM b_task WHERE NAME='stickers_edit' AND MODULE_ID='fileman'", false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);
}
if (IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, '/bitrix/admin'), Array($userGroupID => 'R'));

	$rsTasks = CTask::GetList(array(), array('MODULE_ID' => 'main', 'SYS' => 'Y', 'BINDIG' => 'module', 'LETTER' => 'P'));
	if ($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask['MODULE_ID'], $arTask['ID']);
	}

	$rsTasks = CTask::GetList(array(), array('MODULE_ID' => 'fileman', 'SYS' => 'Y', 'BINDIG' => 'module', 'LETTER' => 'F'));
	if ($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask['MODULE_ID'], $arTask['ID']);
	}
}