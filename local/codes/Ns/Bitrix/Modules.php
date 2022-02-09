<?
namespace Ns\Bitrix;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/**
*
*/
class Modules
{

	public static function IncludeModule($type)
	{
		if (!\CModule::IncludeModule($type))
		{
			throw new Exception("Module " . $type . "is not evailable", 1);
		}
		else
		{
			return true;
		}
	}
}


?>