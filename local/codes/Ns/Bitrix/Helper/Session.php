<?php
namespace Ns\Bitrix\Helper;


/**
 *
 */
class Session
{
    public static $instance;

    public static function useVariant($type)
    {
        if ($type == "message")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new Session\Message();
            }
        }
        else
        {
            throw new \Exception("Unexpected type of Session Helper " . __CLASS__, 1);
        }
        return self::$instance[$type];
    }
}
