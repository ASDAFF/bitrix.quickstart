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

if($APPLICATION->GetGroupRight('api.reviews') == 'D')
	return false;

$aMenu = array(
	 'parent_menu' => 'global_menu_services',
	 'section'     => 'api_reviews',
	 'sort'        => 100,
	 'text'        => Loc::getMessage('API_REVIEWS_MENU_TEXT'),
	 'icon'        => 'forum_menu_icon',
	 'page_icon'   => '',
	 'items_id'    => 'menu_reviews',
	 'items'       => array(
			array(
				 'text'      => Loc::getMessage('API_REVIEWS_MENU_ITEM_MESSAGE'),
				 'title'     => Loc::getMessage('API_REVIEWS_MENU_ITEM_MESSAGE'),
				 'url'       => 'api_reviews_list.php?lang=' . LANGUAGE_ID,
				 'more_url'  => array('api_reviews_edit.php'),
				 'icon'      => 'fileman_sticker_icon',
				 'page_icon' => '',
			),
			array(
				 'text'      => Loc::getMessage('API_REVIEWS_MENU_ITEM_SUBSCRIBE'),
				 'title'     => Loc::getMessage('API_REVIEWS_MENU_ITEM_SUBSCRIBE'),
				 'url'       => 'api_reviews_subscribe.php?lang=' . LANGUAGE_ID,
				 'more_url'  => array(),
				 'icon'      => 'learning_icon_groups',
				 'page_icon' => '',
			),
			array(
				 'text'      => Loc::getMessage('API_REVIEWS_MENU_ITEM_AGENT'),
				 'title'     => Loc::getMessage('API_REVIEWS_MENU_ITEM_AGENT'),
				 'url'       => 'api_reviews_agent.php?lang=' . LANGUAGE_ID,
				 'more_url'  => array(),
				 'icon'      => 'sender_trig_menu_icon',
				 'page_icon' => '',
			),
	 ),
);

return $aMenu;
?>