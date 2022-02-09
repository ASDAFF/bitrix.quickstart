<?
namespace Ns\Bitrix;

/**
*
*/
class Helper
{
	public static $instance;

	public static function Create($type)
	{
		if ($type == "iblock")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new Helper\IBlock();
			}
		}
		elseif ($type == "files")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new Helper\Files();
			}
		}
		elseif ($type == "grafic")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new Helper\Grafic();
			}
		}
        elseif ($type == "subscribe")
        {
            if (!self::$instance[$type])
            {
                \CModule::IncludeModule("subscribe");
                self::$instance[$type] = new Helper\Subscribe();
            }
        }
        elseif ($type == "user")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new Helper\User();
            }
        }
        elseif ($type == "session")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new Helper\Session();
            }
        }
		else
		{
			throw new \Exception("Unexpected type of Helper: " . $type . ". " . __CLASS__ , 1);
		}
		return self::$instance[$type];
	}
}


?>