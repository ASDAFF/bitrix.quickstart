<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Components;

/**
 * Class Breadcrumbs
 * @package Collected\Components
 */
class Breadcrumbs extends \Collected\Base\Component
{
    protected static $componentName = 'bitrix:breadcrumb';
    protected static $params = array(
        'START_FROM' => '0',
        'PATH' => '',
        'SITE_ID' => SITE_ID,
    );
}