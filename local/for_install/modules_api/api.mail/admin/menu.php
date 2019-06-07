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

if($APPLICATION->GetGroupRight('api.mail') < 'W')
	return false;

$aMenu = array(
	 'parent_menu' => 'global_menu_services',
	 'section'     => 'api_mail',
	 'sort'        => 100,
	 'text'        => Loc::getMessage('API_MAIL_MENU_TEXT'),
	 'icon'        => 'form_menu_icon',
	 'page_icon'   => '',
	 'items_id'    => 'api_mail_items',
	 'items'       => array(
			array(
				 'text'      => Loc::getMessage('API_MAIL_MENU_SETTINGS'),
				 'title'     => Loc::getMessage('API_MAIL_MENU_SETTINGS'),
				 'url'       => 'api_mail_settings.php?lang=' . LANGUAGE_ID,
				 'more_url'  => array(),
				 'icon'      => '',
				 'page_icon' => '',
			),
	 ),
);

return $aMenu;
?>