<?php
namespace Ns\Bitrix\Helper;


/**
 *
 */
class User
{
    public static $instance;

    public static function useVariant($type)
    {
        if ($type == "crud")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new User\CRUD();
            }
        }
        else
        {
            throw new \Exception("Unexpected type of User Helper " . __CLASS__, 1);
        }
        return self::$instance[$type];
    }
}

?>