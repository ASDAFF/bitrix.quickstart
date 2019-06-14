<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 * @var           $DBType
 * @var           $adminMenu
 * @var           $adminPage
 *
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if($APPLICATION->GetGroupRight('api.export') < 'W')
	return false;

if(!method_exists($USER, "CanDoOperation"))
	return false;


$aMenu = array();

if($USER->CanDoOperation('edit_other_settings')) {
	$aMenu[] = array(
		 "parent_menu" => "global_menu_services",
		 "section"     => "api.export",
		 "sort"        => 1,
		 "text"        => Loc::getMessage('API_YAMARKET_MENU_TEXT'),
		 "icon"        => "form_menu_icon",
		 "page_icon"   => "form_page_icon",
		 "items_id"    => "menu_yamarket",
		 "items"       => array(
				array(
					 "text"     => Loc::getMessage('API_YAMARKET_MENU_ITEMS_1_TEXT'),
					 "title"    => Loc::getMessage('API_YAMARKET_MENU_ITEMS_1_TEXT'),
					 "url"      => "api_export_list.php?lang=" . LANGUAGE_ID,
					 "more_url" => array("api_export_edit.php"),
				),
		 ),
	);
}

return $aMenu;