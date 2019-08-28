<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;

/**
 * Class Asset
 * @package Collected\Common
 */
class Asset extends \Collected\Base\BitrixInstances
{
    /**
     * Set instance of current class
     *
     * @override
     *
     * @return void
     *
     * @access public
     */
    public static function setInstance()
    {
        static::$instance = \Bitrix\Main\Page\Asset::getInstance();
    }

    /**
     * Returns instance of \Bitrix\Main\Page\Asset
     *
     * @return \Bitrix\Main\Page\Asset
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }
}