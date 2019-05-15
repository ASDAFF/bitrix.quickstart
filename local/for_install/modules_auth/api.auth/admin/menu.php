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

if($APPLICATION->GetGroupRight('api.auth') < 'W')
	return false;

$aMenu = array(
	 'parent_menu' => 'global_menu_services',
	 'section'     => 'api_auth',
	 'sort'        => 100,
	 'text'        => Loc::getMessage('API_AUTH_MENU_TEXT'),
	 'icon'        => 'sonet_menu_icon',
	 'page_icon'   => '',
	 'items_id'    => 'menu_auth',
	 'items'       => array(
			array(
				 'text'      => Loc::getMessage('API_AUTH_MENU_SETTINGS'),
				 'title'     => Loc::getMessage('API_AUTH_MENU_SETTINGS'),
				 'url'       => 'api_auth_settings.php?lang=' . LANGUAGE_ID,
				 'more_url'  => array(),
				 'icon'      => 'sys_menu_icon',
				 'page_icon' => '',
			),
	 ),
);

return $aMenu;
?>