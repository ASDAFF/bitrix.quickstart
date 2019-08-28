<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;


/**
 * Class Context
 * @package Collected\Common
 */
class Context extends \Collected\Base\BitrixInstances
{
    /**
     * @return void
     *
     * @access public
     */
    public static function setInstance()
    {
        static::$instance = \Bitrix\Main\Application::getInstance()->getContext();
    }

    /**
     * Returns instance of \Bitrix\Main\Context
     *
     * @return \Bitrix\Main\Context
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }
}