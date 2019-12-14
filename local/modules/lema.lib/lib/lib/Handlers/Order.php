<?php

namespace Lema\Handlers;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Order
 * @package Lema\Handlers
 */
class Order
{
    /**
     * @param array $arFields
     */
    public static function beforeAdd(&$arFields)
    {

    }

    /**
     * @param int $id
     * @param array $arFields
     */
    public static function beforeUpdate($id, &$arFields)
    {

    }

    /**
     * @param int $id
     */
    public static function beforeDelete($id)
    {

    }

    /**
     * @param int $id
     * @param array $arFields
     */
    public static function afterAdd($id, $arFields)
    {

    }

    /**
     * @param int $id
     * @param array $arFields
     */
    public static function afterUpdate($id, $arFields)
    {

    }

    /**
     * @param int $id
     */
    public static function afterDelete($id)
    {

    }
}