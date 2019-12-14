<?php

namespace Lema\Seo;

/**
 * Class OpenGraph
 * @package Lema\Seo
 */
class OpenGraph extends \Lema\Base\Markup
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