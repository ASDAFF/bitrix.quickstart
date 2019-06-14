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

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!method_exists($USER, 'CanDoOperation'))
	return false;

$aMenu = array();

if($USER->CanDoOperation('edit_other_settings'))
{
	if(!Loader::includeModule('api.orderstatus'))
		return;

	$aMenu = array(
		'parent_menu' => 'global_menu_services',
		'section'     => 'api_orderstatus',
		'sort'        => 100,
		'text'        => Loc::getMessage('AOS_MENU_TEXT'),
		'icon'        => 'form_menu_icon',
		'page_icon'   => 'form_page_icon',
		'items_id'    => 'menu_location',
		'items'       => array(
			array(
				'text'      => Loc::getMessage('AOS_MENU_ITEM_OPTION_TEXT'),
				'title'     => Loc::getMessage('AOS_MENU_ITEM_OPTION_TEXT'),
				'url'       => 'api_orderstatus_options.php?lang=' . LANGUAGE_ID,
				'more_url'  => array(),
				'icon'      => '',
				'page_icon' => '',
			),
			array(
				'text'      => Loc::getMessage('AOS_MENU_ITEM_TEMPLATE_TEXT'),
				'title'     => Loc::getMessage('AOS_MENU_ITEM_TEMPLATE_TEXT'),
				'url'       => 'api_orderstatus_template.php?lang=' . LANGUAGE_ID,
				'more_url'  => array('api_orderstatus_template_edit.php'),
				'icon'      => '',
				'page_icon' => '',
			),
			array(
				'text'      => Loc::getMessage('AOS_MENU_ITEM_MACROS_TEXT'),
				'title'     => Loc::getMessage('AOS_MENU_ITEM_MACROS_TEXT'),
				'url'       => 'api_orderstatus_macros.php?lang=' . LANGUAGE_ID,
				'more_url'  => array('api_orderstatus_macros_edit.php'),
				'icon'      => '',
				'page_icon' => '',
			),
			array(
				'text'      => Loc::getMessage('AOS_MENU_ITEM_SMS_GATEWAY_TEXT'),
				'title'     => Loc::getMessage('AOS_MENU_ITEM_SMS_GATEWAY_TEXT'),
				'url'       => 'api_orderstatus_sms_gateway.php?lang=' . LANGUAGE_ID,
				'more_url'  => array('api_orderstatus_sms_gateway_edit.php'),
				'icon'      => '',
				'page_icon' => '',
			),
			array(
				'text'      => Loc::getMessage('AOS_MENU_ITEM_SMS_STATUS_TEXT'),
				'title'     => Loc::getMessage('AOS_MENU_ITEM_SMS_STATUS_TEXT'),
				'url'       => 'api_orderstatus_sms_status.php?lang=' . LANGUAGE_ID,
				'more_url'  => array('api_orderstatus_sms_status_edit.php'),
				'icon'      => '',
				'page_icon' => '',
			),
		),
	);
}

return $aMenu;
?>