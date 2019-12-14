<?php

namespace Lema\Components;

/**
 * Class Breadcrumbs
 * @package Lema\Components
 */
class Breadcrumbs extends \Lema\Base\Component
{
    protected static $componentName = 'bitrix:breadcrumb';
    protected static $params = array(
        'START_FROM' => '0',
        'PATH' => '',
        'SITE_ID' => SITE_ID,
    );
}