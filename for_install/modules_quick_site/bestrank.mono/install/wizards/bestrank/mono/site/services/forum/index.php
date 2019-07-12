<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("forum"))
	return;

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

// Forum group
$arGroupID = Array(
	//"GENERAL" => 0,
	"COMMENTS" => 0,
	//"HIDDEN" => 0,
);

$dbExistsGroup = CForumGroup::GetListEx(array(), array("LID" => LANGUAGE_ID));
while ($arExistsGroup = $dbExistsGroup->Fetch())
{
	foreach ($arGroupID as $xmlID => $ID)
	{
		if ($arExistsGroup["NAME"] == GetMessage($xmlID."_GROUP_NAME") )
			$arGroupID[$xmlID] = $arExistsGroup["ID"];
	}
}

$sort = 1;
foreach ($arGroupID as $xmlID => $groupID)
{
	if ($groupID > 0)
		continue;

	$arNewGroup = Array("SORT" => $sort++, "LANG" => Array());
	foreach($arLanguages as $languageID)
	{
		$arMessages = WizardServices::IncludeServiceLang("index.php", $languageID, $bReturnArray=true);
		$arNewGroup["LANG"][] = Array(
			"LID" => $languageID, 
			"NAME" => (array_key_exists($xmlID."_GROUP_NAME",$arMessages) ? $arMessages[$xmlID."_GROUP_NAME"] : GetMessage($xmlID."_GROUP_NAME")), 
			"DESCRIPTION" => (array_key_exists($xmlID."_GROUP_DESCRIPTION",$arMessages) ? $arMessages[$xmlID."_GROUP_DESCRIPTION"] : GetMessage($xmlID."_GROUP_DESCRIPTION"))
		);
	}

	$arGroupID[$xmlID] = CForumGroup::Add($arNewGroup);
}

$rsUser = CUser::GetByID(1);
if ($arUser = $rsUser->Fetch())
{
	$userName = $arUser["NAME"].(strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0?"":" ").$arUser["LAST_NAME"];
	$authorName = (strlen(trim($userName)) > 0 ? $userName : $arUser["LOGIN"]);
}
else
	$authorName = "admin";

$forumRights=Array(
	"2" => "M",
	WIZARD_PORTAL_ADMINISTRATION_GROUP => "Y",
);
$newUserDefGroup=COption::GetOptionString("main", "new_user_registration_def_group");
if(intval($newUserDefGroup)>0)
	$forumRights[$newUserDefGroup]="M";

$arForums = Array(

	Array(
		"XML_ID" => "BESTRANK_MONO_REVIEWS",
		"NAME" => GetMessage("BESTRANK_MONO_REVIEWS_FORUM_NAME"),
		"DESCRIPTION" => GetMessage("BESTRANK_MONO_REVIEWS_FORUM_DESCRIPTION"),
		"SORT" => 2,
		"ACTIVE" => "Y",
		"ALLOW_HTML" => "N",
		"ALLOW_ANCHOR" => "N",
		"ALLOW_BIU" => "N",
		"ALLOW_IMG" => "N",
		"ALLOW_LIST" => "N",
		"ALLOW_QUOTE" => "Y",
		"ALLOW_CODE" => "N",
		"ALLOW_FONT" => "N",
		"ALLOW_SMILES" => "Y",
		"ALLOW_UPLOAD" => "N",
		"ALLOW_NL2BR" => "N",
		"ALLOW_VIDEO" => "N",
		"ALLOW_TABLE" => "N",
		"ALLOW_ALIGN" => "N",
		"MODERATION" => "N",
		"ALLOW_MOVE_TOPIC" => "Y",
		"ORDER_BY" => "P",
		"ORDER_DIRECTION" => "DESC",
		"LID" => LANGUAGE_ID,
		"PATH2FORUM_MESSAGE" => "",
		"ALLOW_UPLOAD_EXT" => "",
		"FORUM_GROUP_ID" => $arGroupID["COMMENTS"],
		"ASK_GUEST_EMAIL" => "N",
		"USE_CAPTCHA" => "Y",
		"SITES" => Array(
			WIZARD_SITE_ID => WIZARD_SITE_DIR."forum/#FORUM_ID#/#TOPIC_ID#/"
		),
		"EVENT1" => "forum", 
		"EVENT2" => "message",
		"EVENT3" => "",
		"GROUP_ID" => $forumRights,
	),	
);

foreach ($arForums as $arForum)
{
	$dbForum = CForumNew::GetList(Array(), Array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => $arForum["XML_ID"]));
	if ($dbForum->Fetch())
		continue;

	$forumID = CForumNew::Add($arForum);
}

$reviewsForumID = 0;
$dbRes = CForumNew::GetListEx(array(), array("SITE_ID" => WIZARD_SITE_ID, "XML_ID" => "BESTRANK_MONO_REVIEWS"));
if ($arRes = $dbRes->Fetch())
	$reviewsForumID = $arRes["ID"];
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", Array("FORUM_ID" => $reviewsForumID));

	
if (!WIZARD_IS_RERUN)
{
	$APPLICATION->SetGroupRight("forum", WIZARD_PORTAL_ADMINISTRATION_GROUP, "W");
	COption::SetOptionString("forum", "SHOW_VOTES", "N");
	COption::SetOptionString("forum", "file_max_size", 10485760);
}     
?>


