<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Base;


/**
 * Class BitrixInstances
 * @package Collected\Base
 */
abstract class BitrixInstances
{
    /**
     * @var instance for static singleton call
     */
    protected static $instance = null;

    /**
     * @return mixed
     */
    abstract static function setInstance();

    /**
     * Returns object of current class
     *
     * @return Object
     *
     * @access public
     */
    public static function get()
    {
        static::setInstance();

        return static::$instance;
    }
}