<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Common;

/**
 * Class User
 * @package Collected\Common
 */
class User extends \Collected\Base\GlobalVars
{
    /**
     * set global variable name
     *
     * @access public
     */
    public static function setVarName()
    {
        static::$varName = 'USER';
    }

    /**
     * Returns instance of \CUser
     *
     * @return \CUser
     *
     * @access public
     */
    public static function get()
    {
        return parent::get();
    }

    /**
     * @return bool
     *
     * @access public
     */
    public static function isGuest()
    {
        return !static::isAuthed();
    }

    /**
     * @return bool
     *
     * @access public
     */
    public static function isAuthed()
    {
        return static::get()->IsAuthorized();
    }

    /**
     * @return bool
     *
     * @access public
     */
    public static function isAdmin()
    {
        return static::get()->IsAuthorized() && static::get()->IsAdmin();
    }
}