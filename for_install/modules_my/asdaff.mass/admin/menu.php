<?
$ModuleID = 'asdaff.mass';
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight($ModuleID)>='R') {
	$aMenu = array(
		'parent_menu' => 'global_menu_content',
		'section' => 'asdaff_mass',
		'sort' => 990,
		'text' => GetMessage('WDA_MENUITEM'),
		'url' => '/bitrix/admin/wda.php?lang='.LANGUAGE_ID,
		'more_url' => array('/bitrix/admin/wda_profiles.php?lang='.LANGUAGE_ID),
		'icon' => 'wda_icon_main',
	);
	return $aMenu;
}

?>
