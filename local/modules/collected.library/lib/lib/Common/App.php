<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;

/**
 * Class App
 * @package Collected\Common
 */
class App extends \Collected\Base\GlobalVars
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