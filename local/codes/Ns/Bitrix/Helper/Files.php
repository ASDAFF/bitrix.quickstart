<?
namespace Ns\Bitrix\Helper;


/**
*
*/
class Files
{
	public static $instance;

	public static function useVariant($type)
	{
		if ($type == "checker")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new Files\Checker();
			}
		}
		else
		{
			throw new \Exception("Unexpected type of IBlock Helper", 1);
		}
		return self::$instance[$type];
	}
}

?>