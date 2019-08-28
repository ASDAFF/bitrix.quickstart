<?php

namespace Lm\Components;

/**
 * Class Breadcrumbs
 * @package Lm\Components
 */
class Breadcrumbs extends \Lm\Base\Component
{
    protected static $componentName = 'bitrix:breadcrumb';
    protected static $params = array(
        'START_FROM' => '0',
        'PATH' => '',
        'SITE_ID' => SITE_ID,
    );
}