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

if($APPLICATION->GetGroupRight('api.message') == 'D')
	return false;

$aMenu = array(
	'parent_menu' => 'global_menu_services',
	'section'     => 'api_message',
	'sort'        => 100,
	'text'        => Loc::getMessage('ASM_MENU_TEXT'),
	'icon'        => 'forum_menu_icon',
	'page_icon'   => '',
	'items_id'    => 'menu_systemmessage',
	'items'       => array(
		array(
			'text'      => Loc::getMessage('ASM_MENU_ITEM_CONFIG'),
			'title'     => Loc::getMessage('ASM_MENU_ITEM_CONFIG'),
			'url'       => 'api_message_config.php?lang=' . LANGUAGE_ID,
			'more_url'  => array(),
			'icon'      => 'sys_menu_icon',
			'page_icon' => '',
		),
		array(
			'text'      => Loc::getMessage('ASM_MENU_ITEM_MESSAGE'),
			'title'     => Loc::getMessage('ASM_MENU_ITEM_MESSAGE'),
			'url'       => 'api_message_list.php?lang=' . LANGUAGE_ID,
			'more_url'  => array('api_message_edit.php'),
			'icon'      => 'fileman_sticker_icon',
			'page_icon' => '',
		),
	),
);

return $aMenu;
?>