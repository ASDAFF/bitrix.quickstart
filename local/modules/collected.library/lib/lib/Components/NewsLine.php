<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Components;

/**
 * Class NewsLine
 * @package Collected\Components
 */
class NewsLine extends \Collected\Base\Component
{
    protected static $componentName = 'bitrix:news.line';
    protected static $params = array(
        'IBLOCK_TYPE' => 'news',
        'IBLOCKS' => Array('3'),
        'NEWS_COUNT' => '20',
        'FIELD_CODE' => Array('ID', 'CODE'),
        'SORT_BY1' => 'ACTIVE_FROM',
        'SORT_ORDER1' => 'DESC',
        'SORT_BY2' => 'SORT',
        'SORT_ORDER2' => 'ASC',
        'DETAIL_URL' => '',
        'ACTIVE_DATE_FORMAT' => 'd.m.Y',
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '300',
        'CACHE_GROUPS' => 'N'
    );
}