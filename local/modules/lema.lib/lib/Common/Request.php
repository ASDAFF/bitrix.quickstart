<?php

namespace Lema\Common;


/**
 * Class Request
 * @package Lema\Common
 */
class Request extends \Lema\Base\BitrixInstances
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