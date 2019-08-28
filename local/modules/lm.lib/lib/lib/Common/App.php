<?php

namespace Lm\Common;

/**
 * Class App
 * @package Lm\Common
 */
class App extends \Lm\Base\GlobalVars
{
    /**
     * set global variable name
     *
     * @return void
     *
     * @access public
     */
    public static function setVarName()
    {
        static::$varName = 'APPLICATION';
    }

    /**
     * Returns instance of \Bitrix\Main\Application
     *
     * @return \Bitrix\Main\Application
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }
}