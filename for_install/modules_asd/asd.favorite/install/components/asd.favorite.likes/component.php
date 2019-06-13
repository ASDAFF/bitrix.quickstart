<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');

if (!$USER->IsAuthorized() && $arParams['USER_ID']<=0)
{
	$APPLICATION->ShowAuthForm(GetMessage('ASD_CMP_NOT_AUTH'));
	return;
}
if (!CModule::IncludeModule('asd.favorite'))
{
	ShowError(GetMessage('ASD_CMP_NOT_INSTALLED'));
	return;
}
if ($arParams['NOT_SHOW_WITH_NOT_FOLDER']=='Y' && $arParams['FOLDER_ID']<=0)
	return;

$arParams['URL_TO_ELEMENT'] = trim($arParams['URL_TO_ELEMENT']);
$arParams['URL_TO_BLOG_POST'] = trim($arParams['URL_TO_BLOG_POST']);
$arParams['URL_TO_FORUM_POST'] = trim($arParams['URL_TO_FORUM_POST']);
$arParams['URL_TO_FORUM_GROUP_POST'] = trim($arParams['URL_TO_FORUM_GROUP_POST']);
$arParams['URL_TO_FORUM_USER_POST'] = trim($arParams['URL_TO_FORUM_USER_POST']);
$arParams['FAV_TYPE'] = trim($arParams['FAV_TYPE']);
$arParams['PREVIEW_WIDTH'] = intval($arParams['PREVIEW_WIDTH']);
$arParams['PREVIEW_HEIGHT'] = intval($arParams['PREVIEW_HEIGHT']);
if (!strlen($arParams['FAV_TYPE']))
	$arParams['FAV_TYPE'] = 'unknown';
if ($arParams['PAGE_COUNT'] <= 0)
	$arParams['PAGE_COUNT'] = 10;
if ($arParams['USER_ID'] <= 0)
	$arParams['USER_ID'] = $USER->GetID();
if ($arParams['PREVIEW_WIDTH'] <= 0)
	$arParams['PREVIEW_WIDTH'] = 50;
if ($arParams['PREVIEW_HEIGHT'] <= 0)
	$arParams['PREVIEW_HEIGHT'] = 50;

if ($_REQUEST['del']>0 && check_bitrix_sessid())
{
	CASDfavorite::UnLike($_REQUEST['del'], $arParams['FAV_TYPE']);
	LocalRedirect($APPLICATION->GetCurPageParam('', array('a', 'sessid', 'del')));
}
if ($_REQUEST['move']>0 && $_REQUEST['moveto']>0 && check_bitrix_sessid())
{
	CASDfavorite::MoveLike($_REQUEST['move'], $arParams['FAV_TYPE'], $_REQUEST['moveto']);
	LocalRedirect($APPLICATION->GetCurPageParam('', array('a', 'sessid', 'move', 'moveto')));
}

$arResult = array(
				'FAVS' => array(),
				'FOLDERS' => array(),
				'CURRENT_FOLDER' => array(),
				'TYPE' => CASDfavorite::GetType($arParams['FAV_TYPE'])->Fetch(),
				'CAN_EDIT' => $USER->GetID()==$arParams['USER_ID'] ? 'Y' : 'N');
$arFilter = array('CODE' => $arParams['FAV_TYPE'], 'USER_ID' => $arParams['USER_ID']);
if ($arParams['FOLDER_ID'] > 0)
	$arFilter['FOLDER_ID'] = $arParams['FOLDER_ID'];
$rsLikes = CASDfavorite::GetLikes($arFilter);
while ($arLikes = $rsLikes->Fetch())
	$arResult['FAVS'][$arLikes['ELEMENT_ID']] = array();

if ($arParams['FOLDER_ID'] > 0)
{
	$arResult['FOLDERS'] = CASDfavorite::GetFolders($arParams['FAV_TYPE'], $arParams['USER_ID']);
	if (isset($arResult['FOLDERS'][$arParams['FOLDER_ID']]))
		$arResult['CURRENT_FOLDER'] = $arResult['FOLDERS'][$arParams['FOLDER_ID']];
}

if (empty($arResult['FOLDERS']) || count($arResult['FOLDERS'])==1)
	$arParams['ALLOW_MOVED'] = 'N';

if ($arParams['ONLY_RESULT'] == 'Y')
{
	$arResult['FAVS'] = array_keys($arResult['FAVS']);
	return $arResult;
}

if ($arResult['TYPE']['MODULE']=='iblock' && !empty($arResult['FAVS']) && CModule::IncludeModule('iblock'))
{
	$rsElements = CIBlockElement::GetList(array(),
										array('ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'Y', 'ID' => array_keys($arResult['FAVS'])),
										false,
										array('nPageSize' => $arParams['PAGE_COUNT']));
	while ($arElements = $rsElements->GetNext())
	{
		if (strlen($arParams['URL_TO_ELEMENT']) > 0)
			$arElements['DETAIL_PAGE_URL'] = str_replace(array('#id#', '#section_id#', '#code#'),
														array($arElements['ID'], $arElements['IBLOCK_SECTION_ID'], $arElements['CODE']),
														$arElements['DETAIL_PAGE_URL']);
		$arResult['FAVS'][$arElements['ID']] = array(
													'ID' => $arElements['ID'],
													'NAME' => $arElements['NAME'],
													'PREVIEW_TEXT' => strlen(trim($arElements['PREVIEW_TEXT']))>0 ?
																	$arElements['PREVIEW_TEXT'] :
																	TruncateText(strip_tags($arElements['DETAIL_TEXT']), 150),
													'DETAIL_PAGE_URL' => $arElements['DETAIL_PAGE_URL'],
													'PREVIEW_PICTURE' => $arImage = CFile::GetFileArray($arElements['PREVIEW_PICTURE']>0 ? $arElements['PREVIEW_PICTURE'] : $arElements['DETAIL_PICTURE']),
													'PREVIEW_PICTURE_RESIZED' => CFile::ResizeImageget($arImage, array('width' => $arParams['PREVIEW_WIDTH'], 'height' => $arParams['PREVIEW_HEIGHT'])),
												);
	}
	$arResult['NAV_STRING'] = $rsElements->GetPageNavStringEx($navComponentObject, '', $arParams['PAGER_TEMPLATE'], false);
}
elseif ($arResult['TYPE']['MODULE']=='blog' && !empty($arResult['FAVS']) && CModule::IncludeModule('blog'))
{
	$p = new blogTextParser(false);
	$arFilter = Array(
			'<=DATE_PUBLISH' => ConvertTimeStamp(false, 'FULL', false),
			'PUBLISH_STATUS' => BLOG_PUBLISH_STATUS_PUBLISH,
			'BLOG_ACTIVE' => 'Y',
			'BLOG_GROUP_SITE_ID' => SITE_ID,
			'>PERMS' => BLOG_PERMS_DENY,
			'ID' => array_keys($arResult['FAVS'])
		);
	$rsPosts = CBlogPost::GetList(
							array('DATE_PUBLISH' => 'DESC'),
							$arFilter,
							false,
							array('nPageSize' => $arParams['PAGE_COUNT']),
							array('ID', 'TITLE', 'AUTHOR_ID', 'CODE', 'BLOG_URL', 'DETAIL_TEXT')
						);
	while ($arPost = $rsPosts->GetNext())
	{
		$arPost['DETAIL_TEXT'] = $arPost['~DETAIL_TEXT'];

		if (strpos($arPost['DETAIL_TEXT'], '[CUT]') !== false)
			list($arPost['DETAIL_TEXT']) = explode('[CUT]', $arPost['DETAIL_TEXT']);

		$arPost['DETAIL_TEXT'] = $p->killAllTags($arPost['DETAIL_TEXT']);
		$arPost['DETAIL_TEXT'] = strip_tags($arPost['DETAIL_TEXT']);
		$arPost['DETAIL_TEXT'] = TruncateText($arPost['DETAIL_TEXT'], 150);

		$arResult['FAVS'][$arPost['ID']] = array(
												'ID' => $arPost['ID'],
												'NAME' => $arPost['TITLE'],
												'PREVIEW_TEXT' => $arPost['DETAIL_TEXT'],
												'DETAIL_PAGE_URL' => str_replace(array('#post_id#', '#post_code#', '#blog_url#', '#author_id#'),
																				array($arPost['ID'], $arPost['CODE'], $arPost['BLOG_URL'], $arPost['AUTHOR_ID']),
																				$arParams['URL_TO_BLOG_POST']),
												'PREVIEW_PICTURE' => array(),
												'PREVIEW_PICTURE_RESIZED' => array(),
											);
	}
	$arResult['NAV_STRING'] = $rsPosts->GetPageNavStringEx($navComponentObject, '', $arParams['PAGER_TEMPLATE'], false);
}
elseif ($arResult['TYPE']['MODULE']=='forum' && !empty($arResult['FAVS']) && CModule::IncludeModule('forum'))
{
	$arTopics = array();
	$rsTopics = CForumTopic::GetList(array('ID' => 'DESC'), array('@ID' => array_keys($arResult['FAVS'])));
	$rsTopics->NavStart($arParams['PAGE_COUNT'], false);
	while ($arTopic = $rsTopics->Fetch())
	{
		if ($arTopic['SOCNET_GROUP_ID'] > 0)
			$path = $arParams['URL_TO_FORUM_GROUP_POST'];
		elseif ($arTopic['OWNER_ID'] > 0)
			$path = $arParams['URL_TO_FORUM_USER_POST'];
		else
			$path = $arParams['URL_TO_FORUM_POST'];

		$arTopics[$arTopic['ID']] = 'Y';
		$arResult['FAVS'][$arTopic['ID']] = array(
												'ID' => $arTopic['ID'],
												'NAME' => $arTopic['TITLE'],
												'PREVIEW_TEXT' => '',
												'DETAIL_PAGE_URL' => str_replace(array('#forum_id#', '#topic_id#', '#user_id#', '#group_id#'),
																				array($arTopic['FORUM_ID'], $arTopic['ID'], $arTopic['OWNER_ID'], $arTopic['SOCNET_GROUP_ID']),
																				$path),
												'PREVIEW_PICTURE' => array(),
												'PREVIEW_PICTURE_RESIZED' => array(),
											);
	}
	$arResult['NAV_STRING'] = $rsTopics->GetPageNavStringEx($navComponentObject, '', $arParams['PAGER_TEMPLATE'], false);

	$p = new textParser(false);
	$rsMess = CForumMessage::GetList(array('ID' => 'ASC'), array('@TOPIC_ID' => array_keys($arTopics)));
	while ($arMess = $rsMess->GetNext())
	{
		if (!isset($arTopics[$arMess['TOPIC_ID']]))
		{
			if (empty($arTopics))
				break;
			else
				continue;
		}
		unset($arTopics[$arMess['TOPIC_ID']]);

		$arMess['POST_MESSAGE'] = $p->killAllTags($arMess['POST_MESSAGE']);
		$arMess['POST_MESSAGE'] = strip_tags($arMess['POST_MESSAGE']);
		$arMess['POST_MESSAGE'] = TruncateText($arMess['POST_MESSAGE'], 150);
		$arResult['FAVS'][$arMess['TOPIC_ID']]['PREVIEW_TEXT'] = $arMess['POST_MESSAGE'];
	}
}

$this->IncludeComponentTemplate();
?>