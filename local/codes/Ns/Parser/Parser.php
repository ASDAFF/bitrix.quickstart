<?
namespace Ns\Parser;

/**
*
*/
class Parser
{

	public static function ParseIt(ParserCore $parseObject)
	{
		try
		{
			$parseObject->go();
		}
		catch (\Exception $e)
		{
			prentException($e->getMessage());
		}
	}
}



?>