<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;


/**
 * Class Server
 * @package Collected\Common
 */
class Server extends \Collected\Base\BitrixInstances
{
    /**
     * @return void
     *
     * @access public
     */
    public static function setInstance()
    {
        static::$instance = Context::get()->getServer();
    }

    /**
     * Returns instance of \Bitrix\Main\Server
     *
     * @return \Bitrix\Main\Server
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }
}