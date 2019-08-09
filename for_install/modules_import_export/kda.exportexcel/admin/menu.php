<?
if (!CModule::IncludeModule("iblock"))
	return false;

IncludeModuleLangFile(__FILE__);
$moduleId = 'kda.exportexcel';
$moduleIdUl = 'kda_exportexcel';
$moduleFilePrefix = 'kda_export_excel';

$aMenu = array();

global $USER;
$bUserIsAdmin = $USER->IsAdmin();

$bHasWRight = false;
$rsIBlocks = CIBlock::GetList(array("SORT"=>"asc", "NAME"=>"ASC"), array("MIN_PERMISSION" => "R"));
if($arIBlock = $rsIBlocks->Fetch())
{
	$bHasWRight = true;
}
if($APPLICATION->GetGroupRight($moduleId) < "W")
{
	$bHasWRight = false;
}

if($bUserIsAdmin || $bHasWRight)
{
	$aSubMenu[] = array(
		"text" => GetMessage("KDA_MENU_EXPORT_TITLE"),
		"url" => $moduleFilePrefix.".php?lang=".LANGUAGE_ID,
		"more_url" => array($moduleFilePrefix."_profile_list.php"),
		"title" => GetMessage("KDA_MENU_EXPORT_TITLE"),
		"module_id" => $moduleId,
		"items_id" => "menu_".$moduleIdUl,
		"sort" => 100,
		"section" => $moduleIdUl,
	);
	
	if(CModule::IncludeModule('highloadblock'))
	{
		$aSubMenu[] = array(
			"text" => GetMessage("KDA_MENU_EXPORT_TITLE_HIGHLOAD"),
			"url" => $moduleFilePrefix."_highload.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("KDA_MENU_EXPORT_TITLE_HIGHLOAD"),
			"module_id" => $moduleId,
			"items_id" => "menu_".$moduleIdUl."_highload",
			"sort" => 200,
			"section" => $moduleIdUl,
		);			
	}
	
	$aMenu[] = array(
		"parent_menu" => "global_menu_content",
		"section" => $moduleIdUl,
		"sort" => 1401,
		"text" => GetMessage("KDA_MENU_EXPORT_TITLE_PARENT"),
		"title" => GetMessage("KDA_MENU_EXPORT_TITLE_PARENT"),
		"icon" => $moduleIdUl."_menu_export_icon",
		"items_id" => "menu_".$moduleIdUl."_parent",
		"module_id" => $moduleId,
		"items" => $aSubMenu,
	);
}

return $aMenu;
?>