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

if($APPLICATION->GetGroupRight('api.formdesigner') < 'W')
	return false;

if(!method_exists($USER, 'CanDoOperation'))
	return false;


$aMenu = array();

if($USER->CanDoOperation('edit_other_settings')) {
	$aMenu[] = array(
		 'parent_menu' => 'global_menu_services',
		 'section'     => 'api.formdesigner',
		 'sort'        => 1,
		 'text'        => Loc::getMessage('API_FORMDESIGNER_MENU_TEXT'),
		 'icon'        => 'form_menu_icon',
		 'page_icon'   => 'form_page_icon',
		 'items_id'    => 'menu_api_formdesigner',
		 'items'       => array(
				array(
					 'text'     => Loc::getMessage('API_FORMDESIGNER_MENU_ITEM_CRM'),
					 'title'    => Loc::getMessage('API_FORMDESIGNER_MENU_ITEM_CRM'),
					 'url'      => 'api_formdesigner_crm.php?lang=' . LANGUAGE_ID,
					 'more_url' => array(),
				),
		 ),
	);
}

return $aMenu;