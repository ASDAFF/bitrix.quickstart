<?php

namespace Lema\Components;

/**
 * Class NewsDetail
 * @package Lema\Components
 */
class NewsDetail extends \Lema\Base\Component
{
    protected static $componentName = 'bitrix:news.detail';
    protected static $params = array(
        'DISPLAY_DATE' => 'Y',
        'DISPLAY_NAME' => 'Y',
        'DISPLAY_PICTURE' => 'Y',
        'DISPLAY_PREVIEW_TEXT' => 'Y',
        'USE_SHARE' => 'N',
        'SHARE_HIDE' => 'N',
        'SHARE_TEMPLATE' => '',
        'SHARE_HANDLERS' => array('delicious'),
        'SHARE_SHORTEN_URL_LOGIN' => '',
        'SHARE_SHORTEN_URL_KEY' => '',
        'AJAX_MODE' => 'Y',
        'IBLOCK_TYPE' => 'news',
        'IBLOCK_ID' => '3',
        'ELEMENT_ID' => '',
        'ELEMENT_CODE' => '',
        'CHECK_DATES' => 'Y',
        'FIELD_CODE' => array('ID'),
        'PROPERTY_CODE' => array('DESCRIPTION'),
        'IBLOCK_URL' => 'news.php?ID=#IBLOCK_ID#\'',
        'DETAIL_URL' => '',
        'SET_TITLE' => 'Y',
        'SET_CANONICAL_URL' => 'Y',
        'SET_BROWSER_TITLE' => 'Y',
        'BROWSER_TITLE' => '-',
        'SET_META_KEYWORDS' => 'Y',
        'META_KEYWORDS' => '-',
        'SET_META_DESCRIPTION' => 'Y',
        'META_DESCRIPTION' => '-',
        'SET_STATUS_404' => 'Y',
        'SET_LAST_MODIFIED' => 'Y',
        'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
        'ADD_SECTIONS_CHAIN' => 'Y',
        'ADD_ELEMENT_CHAIN' => 'Y',
        'ACTIVE_DATE_FORMAT' => 'd.m.Y',
        'USE_PERMISSIONS' => 'N',
        'GROUP_PERMISSIONS' => array('1'),
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '3600',
        'CACHE_GROUPS' => 'N',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'Y',
        'PAGER_TITLE' => 'Страница',
        'PAGER_TEMPLATE' => '',
        'PAGER_SHOW_ALL' => 'Y',
        'PAGER_BASE_LINK_ENABLE' => 'Y',
        'SHOW_404' => 'Y',
        'MESSAGE_404' => '',
        'STRICT_SECTION_CHECK' => 'Y',
        'PAGER_BASE_LINK' => '',
        'PAGER_PARAMS_NAME' => 'arrPager',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_STYLE' => 'Y',
        'AJAX_OPTION_HISTORY' => 'N',
    );
}
