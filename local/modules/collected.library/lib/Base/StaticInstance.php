<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Base;


/**
 * Class StaticInstance
 * @package Collected\Base
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