<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;
	CModule::IncludeModule("forum");

$i=0;
$arFilter = array();

$arFilter["ACTIVE"] = "Y";
$arFilter["NAME"] = "Отзывы";
$arOrder = array("SORT"=>"ASC", "NAME"=>"ASC");
$db_Forum = CForumNew::GetList($arOrder, $arFilter);
$forumID=false;
while ($ar_Forum = $db_Forum->Fetch())
{
  $forumID=$ar_Forum['ID'];
}

if ($forumID===false):

	$arFields = Array(
	   "NAME" => "Отзывы", 
	   "DESCRIPTION" => "Форум содержит отзывы о товарах",
	   "FORUM_GROUP_ID" => 0,
	   "GROUP_ID" => array(1 => "Y", 2 => "I"), 
	   "SITES" => array(), // заполняется ниже
	   "ACTIVE" => "Y", 
	   "MODERATION" => "N",
	   "INDEXATION" => "Y",
	   "SORT" => 150,
	   "ASK_GUEST_EMAIL" => "N",
	   "USE_CAPTCHA" => "N",
	   "ALLOW_HTML" => "N",
	   "ALLOW_ANCHOR" => "Y",
	   "ALLOW_BIU" => "Y",
	   "ALLOW_IMG" => "Y",
	   "ALLOW_VIDEO" => "Y",
	   "ALLOW_LIST" => "Y",
	   "ALLOW_QUOTE" => "Y",
	   "ALLOW_CODE" => "Y",
	   "ALLOW_FONT" => "Y",
	   "ALLOW_SMILES" => "Y",
	   "ALLOW_UPLOAD" => "Y", 
	   "ALLOW_UPLOAD_EXT" => "",
	   "ALLOW_TOPIC_TITLED" => "N", 
	   "EVENT1" => "forum");
	   
	$db_res = CSite::GetList($lby="sort", $lorder="asc");
	while ($res = $db_res->Fetch()):
	   $arFields["SITES"][$res["LID"]] = "/".$res["LID"]."/forum/#FORUM_ID#/#TOPIC_ID#/";
	endwhile;

	$res = CForumNew::Add($arFields);
	if (intVal($res) > 0):
		$forumID=$res;
	endif;

endif;

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/washing/index.php", Array("FORUM_ID" => $forumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/stoves/index.php", Array("FORUM_ID" => $forumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/refrigerators/index.php", Array("FORUM_ID" => $forumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/appliance/index.php", Array("FORUM_ID" => $forumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/builtin/index.php", Array("FORUM_ID" => $forumID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."catalog/home/index.php", Array("FORUM_ID" => $forumID));

$wizard =& $this->GetWizard();
COption::SetOptionString("forum", "GROUP_DEFAULT_RIGHT", "W", $wizard->GetVar("siteID"));

?>