<?
namespace Ns\Bitrix\Helper;

/**
*
*/
abstract class HelperCore
{
	private $vars;

	public function __call($method, $arguments)
	{
		/**
		 * if we use withVar - set var
		 */
		if (strpos($method, "with") !== false)
		{
			$var = str_replace("with", "", $method);
			$this->vars[strtolower($var)] = reset($arguments);
		}
		/**
		 * if we use getVar - get var
		 */
		elseif (strpos($method, "get") !== false)
		{
			$var = str_replace("get", "", $method);
			return $this->vars[strtolower($var)];
		}
		return $this;
	}
}

?>