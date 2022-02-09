<?
namespace Ns\Bitrix\Helper;


/**
*
*/
class IBlock
{
	public static $instance;

	public static function useVariant($type)
	{
		if ($type == "checker")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new IBlock\Checker();
			}
		}
		elseif ($type == "text")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new IBlock\Text();
			}
		}
		elseif ($type == "pagination")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new IBlock\Pagination();
			}
		}
        elseif ($type == "validator")
        {
            if (!self::$instance[$type])
            {
                self::$instance[$type] = new IBlock\Validator();
            }
        }
        else
		{
			throw new \Exception("Unexpected type of IBlock Helper: " . $type . ". " . __CLASS__, 1);
		}
		return self::$instance[$type];
	}
}

?>