<?php

namespace Lema\Basket;

/**
 * Class Settings
 * @package Lema\Basket
 */
class Settings
{
    /**
     * @var const - ID of position hlblock
     */
    const POSITIONS_HLBLOCK_ID = 1;
    /**
     * @var const - ID of basket hlblock
     */
    const BASKET_HLBLOCK_ID = 2;
    /**
     * @var const - ID of products iblock
     */
    const PRODUCTS_IBLOCK_ID = 13;

    /**
     * @var const - lifetime for cookies
     */
    const COOKIE_TIME = 86400; //1 day


    /**
     * Returns new generated userID
     *
     * @return string
     *
     * @access public
     */
    public static function generateUserId()
    {
        return mt_rand(100,99999) . uniqid(mt_rand(100,99999), true) . mt_rand(100,99999);
    }

    /**
     * Returns exists or generated userID
     *
     * @return string
     *
     * @access public
     */
    public static function getUserId()
    {
        if(isset($_COOKIE['userId']))
            return $_COOKIE['userId'];
        $userId = static::generateUserId();
        setcookie('userId', $userId, time() + static::COOKIE_TIME, '/', $_SERVER['SERVER_NAME']);
        return $userId;
    }

    /**
     * Set userID
     *
     * @param $userId
     *
     * @return bool
     *
     * @access public
     */
    public static function setUserId($userId)
    {
        return setcookie('userId', $userId, time() + static::COOKIE_TIME, '/', 'http://xn--d1aibgbficebcx2gtc.xn--p1ai/');
    }
}