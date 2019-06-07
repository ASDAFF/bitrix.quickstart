<?

Class CAPIUncachedArea
{
	public static function OnEpilog()
	{
		global $APPLICATION;

		if(CModule::IncludeModule('api.uncachedarea') && is_object($APPLICATION))
		{
			$APPLICATION->AddBufferContent(array(__CLASS__, 'dummyFunction'));
		}
	}

	public static function OnBeforeEndBufferContent()
	{
		global $APPLICATION;

		if(CModule::IncludeModule('api.uncachedarea') && is_object($APPLICATION) && is_array($APPLICATION->buffer_content))
		{
			$count = count($APPLICATION->buffer_content);

			for($i = 0; $i < $count; $i++)
			{
				if($APPLICATION->buffer_content[ $i ] !== '')
				{
					$APPLICATION->buffer_content[ $i ] = self::replaceUncachedArea($APPLICATION->buffer_content[ $i ]);
				}
			}
		}
	}

	/*public static function OnEndBufferContent(&$content)
	{
		global $APPLICATION;
		if(CModule::IncludeModule('api.uncachedarea') && is_object($APPLICATION))
		{
			$content = self::replaceUncachedArea($content);
		}
	}*/

	private static function replaceUncachedArea($buffer_string)
	{
		if(preg_replace('/<!--api.include[\s]+(.*?)[\s](.*?)-->/s', 0, $buffer_string) !== false)
		{
			$buffer_string = preg_replace_callback(
				'/<!--api.include[\s]+(.*?)[\s](.*?)-->/s',
				array(__CLASS__, 'getIncludeArea'),
				$buffer_string
			);
		}

		return $buffer_string;
	}

	public static function includeFile($rel_path, $arParams = array())
	{

		$inc_area = '<!--api.include ';
		$inc_area .= $rel_path;

		if($arParams)
			$inc_area .= ' ' . serialize($arParams) . ' ';

		$inc_area .= ' -->';

		echo $inc_area;
	}

	private static function getIncludeArea($arPregReplace)
	{

		$content = "";
		ob_start();
		self::includeArea($arPregReplace);
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	private static function includeArea($arPregReplace)
	{
		global $APPLICATION;

		$comment = $arPregReplace[0];
		$rel_path = $arPregReplace[1];
		$arParams = ($arPregReplace[ 2 ] ? unserialize($arPregReplace[ 2 ]) : array());

		if(strlen($rel_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $rel_path))
		{
			$APPLICATION->IncludeFile(
				$rel_path,
				$arParams,
				Array(
					'MODE'     => 'html',
				)
			);
		}
	}

	public static function dummyFunction(){}
}
?>