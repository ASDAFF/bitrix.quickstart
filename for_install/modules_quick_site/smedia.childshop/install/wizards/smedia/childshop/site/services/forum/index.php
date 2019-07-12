<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if (WIZARD_IS_RERUN)
	return;	
if(!CModule::IncludeModule("forum"))
	return;	
$groupsMacros=array();		
$arGroups = Array(
	array("STRING_ID"=>"RATING_VOTE"),
	array("STRING_ID"=>"RATING_VOTE_AUTHORITY"),
	array("STRING_ID"=>"content_editor"),
	array("STRING_ID"=>"sale_administrator"),

);
foreach ($arGroups as $arGroup)
{		
	//Add Group
	$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => $arGroup["STRING_ID"], "STRING_ID_EXACT_MATCH" => "Y"));
	if ($arExistsGroup = $dbResult->Fetch())
		$groupID = $arExistsGroup["ID"];
					
	if ($groupID <= 0)
		continue;
		
	$groupsMacros[$arGroup['STRING_ID']]=$groupID;	
}

$forumGroups =
		Array(
			);


$dbExistsGroup = CForumGroup::GetListEx(array(), array("LID" => LANGUAGE_ID));
while ($arExistsGroup = $dbExistsGroup->Fetch())
{
	foreach ($forumGroups as $key=>$forumGroup)
	{
		if ($arExistsGroup["NAME"] == $forumGroup['LANG'][LANGUAGE_ID]['NAME'])
			$forumGroups[$key]['ID'] = $arExistsGroup["ID"];
	}
}

foreach ($forumGroups as $key=> $group)
{
	if ($group['ID'] > 0)
		continue;
	
	$forumGroups[$key]['ID'] = CForumGroup::Add($arNewGroup);
}
$arForums = Array(
		Array(
			'NAME' => GetMessage('forum_1_NAME'),
			'DESCRIPTION' => '',
			'ACTIVE' => 'Y',
			'MODERATION' => 'N',
			'INDEXATION' => 'Y',
			'ALLOW_MOVE_TOPIC' => 'N',
			'LID' => ''.LANGUAGE_ID.'',
			'ABS_LAST_POSTER_NAME' => '',
			'SORT' => '150',
			'ORDER_BY' => 'P',
			'ORDER_DIRECTION' => 'DESC',
			'ALLOW_HTML' => 'N',
			'ALLOW_ANCHOR' => 'Y',
			'ALLOW_BIU' => 'Y',
			'ALLOW_IMG' => 'Y',
			'ALLOW_VIDEO' => 'Y',
			'ALLOW_TABLE' => 'Y',
			'ALLOW_LIST' => 'Y',
			'ALLOW_QUOTE' => 'Y',
			'ALLOW_CODE' => 'Y',
			'ALLOW_FONT' => 'Y',
			'ALLOW_SMILES' => 'Y',
			'ALLOW_UPLOAD' => 'N',
			'EVENT1' => 'forum',
			'EVENT2' => 'message',
			'EVENT3' => '',
			'ALLOW_NL2BR' => 'N',
			'PATH2FORUM_MESSAGE' => '',
			'ALLOW_UPLOAD_EXT' => '',
			'ALLOW_TOPIC_TITLED' => 'N',
			'FORUM_GROUP_ID' => $forumGroups["tmp_ID_"]["ID"],
			'ASK_GUEST_EMAIL' => 'N',
			'USE_CAPTCHA' => 'Y',
			'XML_ID' => '',
			'SITES' => array(
					''.WIZARD_SITE_ID.'' => ''.WIZARD_SITE_DIR.'forum/index.php?PAGE_NAME=message&FID=#FORUM_ID#&TID=#TOPIC_ID#&MID=#MESSAGE_ID#',
					),
			'GROUP_ID_TMP' => array(
					'2' => 'M',
					'RATING_VOTE' => 'A',
					'RATING_VOTE_AUTHORITY' => 'A',
					'content_editor' => 'U',
					'sale_administrator' => 'Q',
					),
			'TMP_ID' => 'forum_1',
			),

);
$forumMacros=array();
foreach ($arForums as $arForum)
{
	$dbForum = CForumNew::GetList(Array(), Array("SITE_ID" => WIZARD_SITE_ID, "TEXT" => $arForum["NAME"]));
	if ($arDbForum=$dbForum->Fetch())
	{
		if($arDbForum['NAME']==$arForum["NAME"])
		{
			$forumMacros[$arForum["TMP_ID"]]=$arDbForum['ID'];
			continue;
		}
	}
	$arForum['GROUP_ID']=array();
	foreach($arForum['GROUP_ID_TMP'] as $grSTRING_ID=>$perm)
	{		
		if((int)$grSTRING_ID==1 || (int)$grSTRING_ID==2)
		{
			$arForum['GROUP_ID'][(int)$grSTRING_ID]=$perm;
		}
		elseif($groupsMacros[$grSTRING_ID])
		{
			$arForum['GROUP_ID'][$groupsMacros[$grSTRING_ID]]=$perm;
		}		
	}
	$tmpId=$arForum["TMP_ID"];
	unset($arForum['TMP_ID']);
	unset($arForum['GROUP_ID_TMP']);
	
	$forumID = CForumNew::Add($arForum);
	if ($forumID < 1)
		continue;
	$forumMacros[$tmpId]=$forumID;
	
	foreach ($arForum["TOPICS"] as $arTopic)
	{
		$arTopic["FORUM_ID"] = $forumID;
		$topicID = CForumTopic::Add($arTopic);

		if ($topicID < 1 || !isset($arTopic["MESSAGES"]) || !is_array($arTopic["MESSAGES"]) )
			continue;

		foreach ($arTopic["MESSAGES"] as $arMessage)
		{
			$arMessage["FORUM_ID"] = $forumID;
			$arMessage["TOPIC_ID"] = $topicID;

			$messageID = CForumMessage::Add($arMessage, false);
			if ($messageID < 1)
			{
				CForumTopic::Delete($topicID);
				continue 2;
			}
		}
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."catalog/index.php", Array("forum_1" => $forumMacros['forum_1']));
?>