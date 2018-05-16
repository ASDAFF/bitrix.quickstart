<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.05.2018
 * Time: 22:00
 */

namespace Local\Lib\Handlers;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class Basket
{
    public static function OnBeforeBasketDelete($ID)
    {
        //do something
    }

    public static function beforeDelete($ID)
    {
        //do something
    }

}
