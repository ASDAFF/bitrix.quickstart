<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

COption::SetOptionString('socialnetwork', 'allow_tooltip', 'N', false , $site_id);
	
$userGroupID = "";
$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));

if($arGroup = $dbGroup -> Fetch())
{
	$userGroupID = $arGroup["ID"];
}
else
{
	$group = new CGroup;
	$arFields = Array(
	  "ACTIVE"       => "Y",
	  "C_SORT"       => 300,
	  "NAME"         => GetMessage("TASK_WIZARD_CONTENT_EDITOR"),
	  "DESCRIPTION"  => GetMessage("TASK_WIZARD_CONTENT_EDITOR_DESCR"),
	  "USER_ID"      => array(),
	  "STRING_ID"      => "content_editor",
	  );
	$userGroupID = $group->Add($arFields);
	//$DB->Query("INSERT INTO b_sticker_group_task(GROUP_ID, TASK_ID)	SELECT ".intVal($userGroupID).", ID FROM b_task WHERE NAME='stickers_edit' AND MODULE_ID='fileman'", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
if(IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, "/bitrix/admin"), Array($userGroupID => "R"));

	$new_task_id = CTask::Add(array(
	        "NAME" => GetMessage("TASK_WIZARD_CONTENT_EDITOR"),
	        "DESCRIPTION" => GetMessage("TASK_WIZARD_CONTENT_EDITOR_DESC"),
	        "LETTER" => "Q",
	        "BINDING" => "module",
	        "MODULE_ID" => "main",
	));
	if($new_task_id)
	{
	  $arOps = array();
	  $rsOp = COperation::GetList(array(), array("NAME"=>"cache_control|view_own_profile|edit_own_profile"));
	  while($arOp = $rsOp->Fetch())
	    $arOps[] = $arOp["ID"];
	  CTask::SetOperations($new_task_id, $arOps);
	}
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"N", "BINDIG"=>"module","LETTER"=>"Q"));
	if($arTask = $rsTasks->Fetch())
	{
	  CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"fileman", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"F"));
	if($arTask = $rsTasks->Fetch())
	{
	  CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$SiteDir = "";
	if(WIZARD_SITE_ID != "s1"){
		$SiteDir = "/site_" . WIZARD_SITE_ID;
	}
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "index.php"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "about/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "articles/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "contacts/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "faq/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "images/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "include/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "news/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "search/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "services/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR . "vacancy/"), Array($userGroupID => "W"));
}
?>