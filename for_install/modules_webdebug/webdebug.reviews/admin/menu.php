<?
$ModuleID = 'webdebug.reviews';
IncludeModuleLangFile(__FILE__);

$ShowVersion1 = COption::GetOptionString($ModuleID, 'show_version_1');

if(CModule::IncludeModule($ModuleID) && $APPLICATION->GetGroupRight($ModuleID)>='R') {
	
	$arInterfacesMenu = array();
	$resInterfaces = CWD_Reviews2_Interface::GetList(array('SORT'=>'ASC'));
	while ($arInterface = $resInterfaces->GetNext()) {
		$arInterfacesMenu[] = array(
			'text' => $arInterface['NAME'],
			'url' => '/bitrix/admin/wd_reviews2_list.php?interface='.$arInterface['ID'].'&lang='.LANGUAGE_ID,
			'more_url' => array('/bitrix/admin/wd_reviews2_edit.php?interface='.$arInterface['ID']),
		);
	}

	$arInterfacesMenu[] = array(
		'text' => GetMessage('WD_REVIEWS2_INTERFACES'),
		'url' => 'wd_reviews2_interfaces.php?lang='.LANGUAGE_ID,
		'more_url' => array('wd_reviews2_interface.php'),
		'icon' => 'wd_reviews2_icon_interface',
	);

	if ($ShowVersion1!='N') {
		$arInterfacesMenu[] = array(
			'text' => GetMessage("WEBDEBUG_REVIEWS_MENU_NAME"),
			'url' => 'webdebug_reviews.php?lang='.LANGUAGE_ID,
			'icon' => 'webdebug_reviews_icon_17',
		);
	}

	$aMenu = array(
		'parent_menu' => 'global_menu_services',
		'section' => 'webdebug_reviews',
		'sort' => 10,
		'text' => GetMessage('WD_REVIEWS2_GROUP'),
		'icon' => 'wd_reviews2_icon_main',
		'items_id' => 'wd_reviews2_submenu',
		'items' => $arInterfacesMenu,
	);
	return $aMenu;
}


return false;
?>
