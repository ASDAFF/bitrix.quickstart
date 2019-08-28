<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;


/**
 * Class Request
 * @package Collected\Common
 */
class Request extends \Collected\Base\BitrixInstances
{
    /**
     * @return void
     *
     * @access public
     */
    public static function setInstance()
    {
        static::$instance = Context::get()->getRequest();
    }

    /**
     * Returns instance of \Bitrix\Main\HttpRequest
     *
     * @return \Bitrix\Main\HttpRequest
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }
}