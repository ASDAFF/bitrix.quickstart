<?php

namespace Lm\Common;


/**
 * Class Context
 * @package Lm\Common
 */
class Context extends \Lm\Base\BitrixInstances
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