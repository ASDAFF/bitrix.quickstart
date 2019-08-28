<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Seo;

/**
 * Class OpenGraph
 * @package Collected\Seo
 */
class OpenGraph extends \Collected\Base\Markup
{
    /**
     * Set prefix for meta tags
     *
     * @return void
     *
     * @access public
     */
    public function setPrefix()
    {
        static::$PREFIX = 'og:';
    }
}