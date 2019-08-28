<?php

namespace Lm\Seo;

/**
 * Class OpenGraph
 * @package Lm\Seo
 */
class OpenGraph extends \Lm\Base\Markup
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