<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = array(
    array(
        'parent_menu' => 'global_menu_services',
        'sort' => 1,
        'icon' =>'forum_menu_icon',
        'text' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_TEXT'),
        'title' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_TITLE'),
        'url' => 'soobwa_comments_list.php?lang='.LANGUAGE_ID,
        'items_id' => 'menu_util',
        'items' => array(
            array(
                'icon' => 'fileman_sticker_icon',
                'text' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_ITEMS_1_TEXT'),
                'title' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_ITEMS_1_TITLE'),
                'url' => 'soobwa_comments_list.php?lang='.LANGUAGE_ID,
                'more_url' => array('soobwa_comments_list.php?lang='.LANGUAGE_ID),
            ),
            array(
                'icon' =>'sys_menu_icon',
                'text' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_ITEMS_2_TEXT'),
                'title' => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_MENU_ITEMS_2_TITLE'),
                'url' => '/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=soobwa.comments',
                'more_url' => array('/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=soobwa.comments'),
            ),
        ),
    ),
);

return $aMenu;
