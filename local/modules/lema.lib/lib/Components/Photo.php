<?php

namespace Lema\Components;

/**
 * Class Photo
 * @package Lema\Components
 */
class Photo extends \Lema\Base\Component
{
    protected static $componentName = 'bitrix:photo';
    protected static $params = array(
        'AJAX_MODE' => 'N',
        'AJAX_OPTION_ADDITIONAL' => '',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_STYLE' => 'Y',
        'BROWSER_TITLE' => '-',
        'CACHE_FILTER' => 'N',
        'CACHE_GROUPS' => 'N',
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'A',
        'DETAIL_FIELD_CODE' => array(),
        'DETAIL_PROPERTY_CODE' => array(),
        'DISPLAY_BOTTOM_PAGER' => 'Y',
        'DISPLAY_TOP_PAGER' => 'N',
        'ELEMENT_SORT_FIELD' => 'sort',
        'ELEMENT_SORT_ORDER' => 'asc',
        'IBLOCK_ID' => '1',
        'IBLOCK_TYPE' => 'content',
        'LIST_BROWSER_TITLE' => '-',
        'LIST_FIELD_CODE' => array(),
        'LIST_PROPERTY_CODE' => array(),
        'MESSAGE_404' => '',
        'META_DESCRIPTION' => '-',
        'META_KEYWORDS' => '-',
        'PAGER_BASE_LINK_ENABLE' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
        'PAGER_SHOW_ALL' => 'N',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => '.default',
        'PAGER_TITLE' => 'Фотографии',
        'SECTION_COUNT' => '20',
        'SECTION_LINE_ELEMENT_COUNT' => '3',
        'SECTION_PAGE_ELEMENT_COUNT' => '20',
        'SECTION_SORT_FIELD' => 'sort',
        'SECTION_SORT_ORDER' => 'asc',
        'SET_LAST_MODIFIED' => 'N',
        'SET_STATUS_404' => 'N',
        'SET_TITLE' => 'Y',
        'SHOW_404' => 'N',
        'TOP_ELEMENT_COUNT' => '9',
        'TOP_ELEMENT_SORT_FIELD' => 'sort',
        'TOP_ELEMENT_SORT_ORDER' => 'asc',
        'TOP_FIELD_CODE' => array(),
        'TOP_LINE_ELEMENT_COUNT' => '3',
        'TOP_PROPERTY_CODE' => array(),
        'USE_FILTER' => 'N',
        'USE_PERMISSIONS' => 'N',
        'USE_RATING' => 'N',
        'VARIABLE_ALIASES' => Array(
            'ELEMENT_ID' => 'ELEMENT_ID',
            'SECTION_ID' => 'SECTION_ID'
        ),
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/gallery/',
        'SEF_URL_TEMPLATES' => Array(
            'sections_top' => '',
            'section' => '#SECTION_CODE#/',
            'detail' => '#SECTION_CODE#/#ELEMENT_CODE#/'
        ),
    );
}