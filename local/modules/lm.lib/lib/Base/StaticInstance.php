<?php

namespace Lm\Base;


/**
 * Class StaticInstance
 * @package Lm\Base
 */
abstract class StaticInstance
{
    /**
     * @var static instance of object
     */
    protected static $instance = null;

    /**
     * Returns static instance of current object
     *
     * @return $this
     *
     * @access public
     */
    public static function get()
    {
        if(static::$instance === null)
            static::$instance = new static();
        return static::$instance;
    }
}