<?

namespace Ns\Bitrix\Helper\IBlock;


/**

*

*/

class Text extends \Ns\Bitrix\Helper\HelperCore

{

	const DEFAULT_SUBSTR_PREVIEW_TEXT = 300;



	public function cut($text, $size = false)

	{

		if (!$size)

		{

			$size = self::DEFAULT_SUBSTR_PREVIEW_TEXT;

		}

		if ($text)

		{

            // $text = \phpQuery::newDocumentHTML($text)->text();

            return "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp" . substr(\HTMLToTxt($text), 0, strripos(substr(HTMLToTxt($text), 0, $size), " ")) . "...";

		}

		else

		{

			throw new \Exception("Unexpected text to cut in " . __CLASS__, 1);

		}

	}



	public function translite($text)

	{

		$arParams = array("replace_space" => "-", "replace_other" => "-");

		return \CUtil::translit(trim($text), "ru", $arParams);

	}



}





?>