<?
namespace Ns\Bitrix\Helper;


/**
*
*/
class Grafic
{
	public static $instance;

	public static function useVariant($type)
	{
		if ($type == "background")
		{
			if (!self::$instance[$type])
			{
				self::$instance[$type] = new Grafic\Background();
			}
		}
		else
		{
			throw new \Exception("Unexpected type of IBlock Helper " . __CLASS__, 1);
		}
		return self::$instance[$type];
	}
}

?>