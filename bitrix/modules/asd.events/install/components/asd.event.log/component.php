<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if ($_REQUEST['reset']!='')
	LocalRedirect($APPLICATION->GetCurPageParam('', array('type', 'user', 'reset'), false));

if ($arParams['COUNT'] <= 0)
	$arParams['COUNT'] = 20;
if (!is_array($arParams['TYPES']))
	$arParams['TYPES'] = array();

$arAuditTypes = array(
	'USER_AUTHORIZE' => '[USER_AUTHORIZE] '.GetMessage('MAIN_EVENTLOG_USER_AUTHORIZE'),
	'USER_DELETE' => '[USER_DELETE] '.GetMessage('MAIN_EVENTLOG_USER_DELETE'),
	'USER_INFO' => '[USER_INFO] '.GetMessage('MAIN_EVENTLOG_USER_INFO'),
	'USER_LOGIN' => '[USER_LOGIN] '.GetMessage('MAIN_EVENTLOG_USER_LOGIN'),
	'USER_LOGINBYHASH' => '[USER_LOGINBYHASH] '.GetMessage('MAIN_EVENTLOG_USER_LOGINBYHASH_FAILED'),
	'USER_LOGOUT' => '[USER_LOGOUT] '.GetMessage('MAIN_EVENTLOG_USER_LOGOUT'),
	'USER_PASSWORD_CHANGED' => '[USER_PASSWORD_CHANGED] '.GetMessage('MAIN_EVENTLOG_USER_PASSWORD_CHANGED'),
	'USER_REGISTER' => '[USER_REGISTER] '.GetMessage('MAIN_EVENTLOG_USER_REGISTER'),
	'USER_REGISTER_FAIL' => '[USER_REGISTER_FAIL] '.GetMessage('MAIN_EVENTLOG_USER_REGISTER_FAIL'),
	'USER_GROUP_CHANGED' => '[USER_GROUP_CHANGED] '.GetMessage('MAIN_EVENTLOG_GROUP'),
	'GROUP_POLICY_CHANGED' => '[GROUP_POLICY_CHANGED] '.GetMessage('MAIN_EVENTLOG_GROUP_POLICY'),
	'MODULE_RIGHTS_CHANGED' => '[MODULE_RIGHTS_CHANGED] '.GetMessage('MAIN_EVENTLOG_MODULE'),
	'FILE_PERMISSION_CHANGED' => '[FILE_PERMISSION_CHANGED] '.GetMessage('MAIN_EVENTLOG_FILE'),
	'TASK_CHANGED' => '[TASK_CHANGED] '.GetMessage('MAIN_EVENTLOG_TASK'),
);
$db_events = GetModuleEvents('main', 'OnEventLogGetAuditTypes');
while($arEvent = $db_events->Fetch())
{
	$ar = ExecuteModuleEventEx($arEvent);
	if(is_array($ar))
		$arAuditTypes = array_merge($ar, $arAuditTypes);
}

$arResult = array();
$arResult['LT'] = COption::GetOptionString('main', 'event_log_cleanup_days');
$arResult['TYPES'] = $arAuditTypes;
$arResult['AVAILABLE_TYPES'] = empty($arParams['TYPES']) ? array_keys($arAuditTypes) : $arParams['TYPES'];
$arResult['ITEMS'] = array();
$arResult['USERS'] = array();
$arResult['FILTER_USERS'] = array();
$arResult['GROUPS'] = array();
$arResult['TASKS'] = array();
$arResult['F_MESS'] = array();
$arResult['F_TOPICS'] = array();
$arResult['SECTIONS'] = array();
$arResult['ELEMENTS'] = array();
$arResult['REQUEST_TYPE'] = is_array($_REQUEST['type']) ? $_REQUEST['type'] : array($_REQUEST['type']);
$arResult['REQUEST_USER'] = $_REQUEST['user'];

$arFilter = array('SITE_ID' => !defined('ADMIN_SECTION') ? SITE_ID : '');
if (!empty($arResult['REQUEST_TYPE']) && count(array_diff($arResult['REQUEST_TYPE'], $arResult['AVAILABLE_TYPES']))==0)
	$arFilter['=AUDIT_TYPE_ID'] = $arResult['REQUEST_TYPE'];
elseif (!empty($arParams['TYPES']))
	$arFilter['=AUDIT_TYPE_ID'] = $arParams['TYPES'];
if ($arParams['FILTER_USER'] == 'Y')
{
	$rsData = CEventLog::GetList(array('ID' => 'DESC'), $arFilter);
	while ($arData = $rsData->GetNext())
	{
		if ($arData['USER_ID'] > 0)
		{
			$arResult['USERS'][] = $arData['USER_ID'];
			$arResult['FILTER_USERS'][] = $arData['USER_ID'];
		}
	}
	$arResult['FILTER_USERS'] = array_unique($arResult['FILTER_USERS']);
}

if ($arParams['FILTER_USER']=='Y' && $arResult['REQUEST_USER']>0)
	$arFilter['USER_ID'] = $arResult['REQUEST_USER'];
if (strlen($arParams['SITE_ID'])) {
	$arFilter['SITE_ID'] = $arParams['SITE_ID'];
}
$rsData = CEventLog::GetList(array('ID' => 'DESC'), $arFilter);
$rsData->NavStart($arParams['COUNT']);
while ($arData = $rsData->GetNext())
{
	if ($arParams['NOT_SHOW_USER']!='Y' && $arData['USER_ID']>0)
		$arResult['USERS'][] = $arData['USER_ID'];

	switch ($arData['AUDIT_TYPE_ID'])
	{
		case 'USER_AUTHORIZE':
		case 'USER_LOGOUT':
		case 'USER_REGISTER':
		case 'USER_INFO':
		case 'USER_PASSWORD_CHANGED':
		case 'USER_DELETE':
		case 'USER_GROUP_CHANGED': $arResult['USERS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'USER'; break;

		case 'GROUP_POLICY_CHANGED':
		case 'MODULE_RIGHTS_CHANGED': $arResult['GROUPS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'USER_GROUP'; break;

		case 'TASK_CHANGED': $arResult['TASKS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'TASK'; break;

		case 'FORUM_MESSAGE_APPROVE':
		case 'FORUM_MESSAGE_UNAPPROVE':
		case 'FORUM_MESSAGE_MOVE':
		case 'FORUM_MESSAGE_EDIT': $arResult['F_MESS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'F_MESS'; break;

		case 'FORUM_TOPIC_APPROVE':
		case 'FORUM_TOPIC_UNAPPROVE':
		case 'FORUM_TOPIC_STICK':
		case 'FORUM_TOPIC_UNSTICK':
		case 'FORUM_TOPIC_OPEN':
		case 'FORUM_TOPIC_CLOSE':
		case 'FORUM_TOPIC_MOVE':
		case 'FORUM_TOPIC_EDIT': $arResult['F_TOPICS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'F_TOPIC'; break;

		case 'IBLOCK_SECTION_ADD':
		case 'IBLOCK_SECTION_EDIT': $arResult['SECTIONS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'SECTION'; break;

		case 'IBLOCK_ELEMENT_ADD':
		case 'IBLOCK_ELEMENT_EDIT': $arResult['ELEMENTS'][] = $arData['ITEM_ID']; $arData['ENTITY'] = 'ELEMENT'; break;
	}

	$arData['AUDIT_TYPE_ID'] = preg_replace('/^\[[^]]+\]/is', '', $arAuditTypes[$arData['AUDIT_TYPE_ID']]);
	$arResult['ITEMS'][] = $arData;
}

if (!empty($arResult['USERS']))
{
	$arResult['USERS'] = array_unique($arResult['USERS']);
	$rsUser = CUser::GetList($by='id', $order='desc', array('ID' => implode('|', $arResult['USERS'])));
	$arResult['USERS'] = array();
	$arResult['FILTER_USERS_FULL'] = array();
	while ($arUser = $rsUser->GetNext(true, false))
	{
	 	$arResult['USERS'][$arUser['ID']] = array('ID' => $arUser['ID'], 'LOGIN' => $arUser['LOGIN'],
												'NAME' => trim($arUser['NAME']), 'LAST_NAME' => trim($arUser['LAST_NAME']));
		if (in_array($arUser['ID'], $arResult['FILTER_USERS']))
			$arResult['FILTER_USERS_FULL'][$arUser['ID']] = $arResult['USERS'][$arUser['ID']];
	}

	if ($arParams['FILTER_USER'] == 'Y')
	{
		$arResult['FILTER_USERS'] = $arResult['FILTER_USERS_FULL'];
		if (!function_exists('asd_cmp'))
		{
			function asd_cmp($a, $b)
			{
				if ($a['NAME'] == $b['NAME'])
					return 0;
				return ($a['NAME'] < $b['NAME']) ? -1 : 1;
			}
		}
		usort($arResult['FILTER_USERS'], 'asd_cmp');
		unset($arResult['FILTER_USERS_FULL']);
	}
}

if (!empty($arResult['GROUPS']))
{
	$arResult['GROUPS'] = array();
	$rsGroup = CGroup::GetList($by='sort', $order='asc');
	while ($arGroup = $rsGroup->GetNext(true, false))
		$arResult['GROUPS'][$arGroup['ID']] = array('ID' => $arGroup['ID'], 'NAME' => $arGroup['NAME']);
}

if (!empty($arResult['TASKS']))
{
	$arResult['TASKS'] = array();
	$rsTask = CTask::GetList();
	while ($arTask = $rsTask->GetNext(true, false))
		$arResult['TASKS'][$arTask['ID']] = array('ID' => $arTask['ID'], 'NAME' => $arTask['NAME']);
}

if (!empty($arResult['F_TOPICS']) && CModule::IncludeModule('forum'))
{
	$rsTopics = CForumTopic::GetListEx(array(), array('@ID' => $arResult['F_TOPICS']));
	$arResult['F_TOPICS'] = array();
	while ($arTopics = $rsTopics->GetNext(true, false))
	{
		$arResult['F_TOPICS'][$arTopics['ID']] = array('LAST_MESSAGE_ID' => $arTopics['LAST_MESSAGE_ID']);
		$arResult['F_MESS'][] = $arTopics['LAST_MESSAGE_ID'];
	}
}

if (!empty($arResult['F_MESS']) && CModule::IncludeModule('forum'))
{
	$arForumsPath = array();
	$rsMess = CForumMessage::GetListEx(array(), array('@ID' => $arResult['F_MESS']));
	$arResult['F_MESS'] = array();
	while ($arMess = $rsMess->GetNext(true, false))
	{
		$arMess['MESSAGE_ID'] = $arMess['ID'];
		if (!isset($arForumsPath[$arMess['FORUM_ID']]))
		{
			$arSitesPath = CForumNew::GetSites($arMess['FORUM_ID']);
			$arForumsPath[$arMess['FORUM_ID']] = array_shift($arSitesPath);
		}
		$arResult['F_MESS'][$arMess['ID']] = array('PATH' => CForumNew::PreparePath2Message($arForumsPath[$arMess['FORUM_ID']], $arMess));
	}
}

if (!empty($arResult['SECTIONS']) && CModule::IncludeModule('iblock'))
{
	$rsSect = CIBlockSection::GetList(array(), array('ID' => $arResult['SECTIONS']));
	$arResult['SECTIONS'] = array();
	while ($arSect = $rsSect->GetNext(true, false))
		$arResult['SECTIONS'][$arSect['ID']] = array('ID' => $arSect['ID'], 'NAME' => $arSect['NAME'], 'URL' => $arSect['SECTION_PAGE_URL']);
}

if (!empty($arResult['ELEMENTS']) && CModule::IncludeModule('iblock'))
{
	$rsElement = CIBlockElement::GetList(array(), array('ID' => $arResult['ELEMENTS']));
	$arResult['ELEMENTS'] = array();
	while ($arElement = $rsElement->GetNext(true, false))
		$arResult['ELEMENTS'][$arElement['ID']] = array('ID' => $arElement['ID'], 'NAME' => $arElement['NAME'], 'URL' => $arElement['DETAIL_PAGE_URL']);
}

$arResult['NAV_STRING'] = $rsData->GetPageNavStringEx($navComponentObject, '', $arParams['PAGER_TEMPLATE'], false);

$this->IncludeComponentTemplate();
?>