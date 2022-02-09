<?
namespace Ns\Bitrix\Helper;


/**
 *
 */
class Subscribe
{
    public static $instance;

    public static function useVariant($type)
    {
        if ($type == "users")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new Subscribe\Users();
            }
        }
        elseif ($type == "crud")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new Subscribe\CRUD();
            }
        }
        else
        {
            throw new \Exception("Unexpected type of Subscribe Helper " . __CLASS__, 1);
        }
        return self::$instance[$type];
    }
}

?>