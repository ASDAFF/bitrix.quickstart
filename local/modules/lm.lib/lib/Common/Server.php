<?php

namespace Lm\Common;


/**
 * Class Server
 * @package Lm\Common
 */
class Server extends \Lm\Base\BitrixInstances
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