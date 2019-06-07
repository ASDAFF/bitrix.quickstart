<?
/**
 * Created by Tuning-Soft
 * http://tuning-soft.ru/
 *
 * NOTE: Requires PHP version 5.3 or later
 *
 * @package   CAPISearch
 * @author    Anton Kuchkovsky <support@tuning-soft.ru>
 * @copyright © 1984-2015 Tuning-Soft
 * @date      27.11.2015
 */

IncludeModuleLangFile(__FILE__);
$MODULE_ID = basename(dirname(__FILE__));

define('API_SEARCH_CHAR_LENGTH', 2);

Class CApiSearch
{
	protected static $searchMode = '';
	protected static $arStopWords = array();

	function __construct()
	{
		self::$arStopWords = GetMessage('STOP_WORDS');
	}

	public static function setMode($mode = 'JOIN'){

		$mode = ($mode == 'EXACT' ? 'EXACT' : 'JOIN');

		self::$searchMode = $mode;
	}

	public static function getWords($query, $word_length = 3)
	{
		if(!$word_length)
			$word_length = 3;

		if(strlen($query) < API_SEARCH_CHAR_LENGTH)
			return '';

		$q = ToLower($query);
		if(self::$searchMode == 'EXACT')
			return $q;

		$q = strtr($q, GetMessage('EE'));
		//$q = preg_replace('/&([a-zA-Z0-9]+);/', ' ', $q);
		$q = preg_replace('/&([a-zA-Z0-9]+);|([[\"\']+)/', ' ', $q);
		$q = trim(preg_replace('/ +/', ' ', $q));


		$where = '';
		$step = 0;
		$words = explode(' ', $q);
		foreach($words as $word)
		{
			$step++;

			if(in_array($word, self::$arStopWords))
				continue;

			$stem_word = self::stem($word);

			if(strlen($word) <= $word_length)
				$stem_word = $word;

			$where .= '%' . $stem_word . '%';

			if($word !== end($words))
				$where .= ' && ';
		}

		return trim($where);
	}

	protected static function stem($word)
	{
		$a = self::rv($word);

		return self::step4(self::step3(self::step2(self::step1($a[0]))));
	}

	protected static function rv($word)
	{
		$vowels = GetMessage('RV_VOWELS');

		$flag   = 0;
		$rv     = $start = '';

		for($i = 0; $i < strlen($word); $i += API_SEARCH_CHAR_LENGTH)
		{
			if($flag == 1)
				$rv .= substr($word, $i, API_SEARCH_CHAR_LENGTH);
			else
				$start .= substr($word, $i, API_SEARCH_CHAR_LENGTH);

			if(array_search(substr($word, $i, API_SEARCH_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
		}
		return array($start, $rv);
	}

	protected static function step1($word)
	{
		$perfective1 = GetMessage('STEP1_PERFECTIVE1');
		foreach($perfective1 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_YA')))
				return substr($word, 0, strlen($word) - strlen($suffix));
		$perfective2 = GetMessage('STEP1_PERFECTIVE2');
		foreach($perfective2 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix)
				return substr($word, 0, strlen($word) - strlen($suffix));
		$reflexive = GetMessage('STEP1_REFLEXIVE');
		foreach($reflexive as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix)
				$word = substr($word, 0, strlen($word) - strlen($suffix));
		$adjective   = GetMessage('STEP1_ADJECTIVE');
		$participle2 = GetMessage('STEP1_PARTICIPLE2');
		$participle1 = GetMessage('STEP1_PARTICIPLE1');
		foreach($adjective as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix)
			{
				$word = substr($word, 0, strlen($word) - strlen($suffix));
				foreach($participle1 as $suffix)
					if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_YA')))
						$word = substr($word, 0, strlen($word) - strlen($suffix));
				foreach($participle2 as $suffix)
					if(substr($word, -(strlen($suffix))) == $suffix)
						$word = substr($word, 0, strlen($word) - strlen($suffix));
				return $word;
			}
		$verb1 = GetMessage('STEP1_VERB1');
		foreach($verb1 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP1_YA')))
				return substr($word, 0, strlen($word) - strlen($suffix));
		$verb2 = GetMessage('STEP1_VERB2');
		foreach($verb2 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix)
				return substr($word, 0, strlen($word) - strlen($suffix));
		$noun = GetMessage('STEP1_NOUN');
		foreach($noun as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix)
				return substr($word, 0, strlen($word) - strlen($suffix));
		return $word;
	}

	protected static function step2($word)
	{
		return substr($word, -API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP2_I') ? substr($word, 0, strlen($word) - API_SEARCH_CHAR_LENGTH) : $word;
	}

	protected static function step3($word)
	{
		$vowels = GetMessage('STEP3_VOWELS');
		$flag   = 0;
		$r1     = $r2 = '';
		for($i = 0; $i < strlen($word); $i += API_SEARCH_CHAR_LENGTH)
		{
			if($flag == 2)
				$r1 .= substr($word, $i, API_SEARCH_CHAR_LENGTH);
			if(array_search(substr($word, $i, API_SEARCH_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
			if($flag = 1 && array_search(substr($word, $i, API_SEARCH_CHAR_LENGTH), $vowels) === false)
				$flag = 2;
		}
		$flag = 0;
		for($i = 0; $i < strlen($r1); $i += API_SEARCH_CHAR_LENGTH)
		{
			if($flag == 2)
				$r2 .= substr($r1, $i, API_SEARCH_CHAR_LENGTH);
			if(array_search(substr($r1, $i, API_SEARCH_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
			if($flag = 1 && array_search(substr($r1, $i, API_SEARCH_CHAR_LENGTH), $vowels) === false)
				$flag = 2;
		}
		$derivational = GetMessage('STEP3_DERIVATIONAL');
		foreach($derivational as $suffix)
			if(substr($r2, -(strlen($suffix))) == $suffix)
				$word = substr($word, 0, strlen($r2) - strlen($suffix));

		return $word;
	}

	protected static function step4($word)
	{
		if(substr($word, -API_SEARCH_CHAR_LENGTH * 2) == GetMessage('STEP4_NN'))
			$word = substr($word, 0, strlen($word) - API_SEARCH_CHAR_LENGTH);
		else
		{
			$superlative = GetMessage('STEP4_SUPERLATIVE');
			foreach($superlative as $suffix)
				if(substr($word, -(strlen($suffix))) == $suffix)
					$word = substr($word, 0, strlen($word) - strlen($suffix));
			if(substr($word, -API_SEARCH_CHAR_LENGTH * 2) == GetMessage('STEP4_NN'))
				$word = substr($word, 0, strlen($word) - API_SEARCH_CHAR_LENGTH);
		}
		if(substr($word, -API_SEARCH_CHAR_LENGTH, API_SEARCH_CHAR_LENGTH) == GetMessage('STEP4_SOFT_SIGN'))
			$word = substr($word, 0, strlen($word) - API_SEARCH_CHAR_LENGTH);
		return $word;
	}


	public static function ResizeImageGet(
		$file,
		$width,
		$height,
		$resizeType = BX_RESIZE_IMAGE_PROPORTIONAL,
		$bInitSizes = true,
		$arFilters = false,
		$bImmediate = false,
		$jpgQuality = false
	){
		if(!$file || !$width || !$height)
			return false;

		//if(!$arFilters)
			//$arFilters = array('name' => 'sharpen', 'precision' => 95);

		$arFileTmp = CFile::ResizeImageGet(
			$file,
			array(
				'width'  => $width,
				'height' => $height,
			),
			$resizeType,
			$bInitSizes,
			$arFilters,
			$bImmediate,
			$jpgQuality
		);

		return array(
			'SRC'    => CUtil::GetAdditionalFileURL($arFileTmp['src']),
			'WIDTH'  => $arFileTmp['width'],
			'HEIGHT' => $arFileTmp['height'],
		);
	}

	public static function replaceName($name, $query, $resultQuery = '')
	{
		if(!$name || !$query)
			return $name;

		$name          = htmlspecialcharsback($name);
		$arWords       = explode(' ', preg_quote($query, '#'));

		$resultQuery    = str_replace(array('&& ','%'),'',$resultQuery);
		$resultQuery    = preg_replace('/\s+/', ' ', trim($resultQuery));
		$arResultWords  = explode(' ', preg_quote($resultQuery, '#'));

		$arReplaceWords = array_unique(array_merge($arWords, $arResultWords));

		//Для UTF-8 регистронезависимая замена работает только при iu, а в WIN при i + setlocale() если на сервере не настроена локаль
		if(defined('BX_UTF'))
		{
			$new_name = preg_replace('#' . implode('|', $arReplaceWords) . '#imu', '<span class="api-tag">\0</span>', $name); //imu
		}
		else
		{
			setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
			$new_name = preg_replace('#' . implode('|', $arReplaceWords) . '#im', '<span class="api-tag">\0</span>', $name); //imu
		}

		$return = (is_array($name) && $new_name) ? implode(', ', $new_name) : $new_name;

		return $return;
	}

	public static function getDeclination($intNumber, $arMess = array())
	{
		$ch1 = $arMess[0];
		$ch2 = $arMess[1];
		$ch3 = $arMess[2];

		$f = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

		if(substr($intNumber, -2, 1) == 1 and strlen($intNumber) > 1)
			$r = array($ch3, $ch3, $ch3, $ch3, $ch3, $ch3, $ch3, $ch3, $ch3, $ch3);
		else
			$r = array($ch3, $ch1, $ch2, $ch2, $ch2, $ch3, $ch3, $ch3, $ch3, $ch3);

		return str_replace($f, $r, substr($intNumber, -1, 1));
	}
	
	public static function incComponentLang($cp_obj){

		$templateFile = '/bitrix/components/api/search.title/templates/.default';
		if($cp_obj->InitComponentTemplate())
		{
			$template = &$cp_obj->GetTemplate();
			$templateFile = $template->GetFolder();
		}

		return CComponentUtil::__IncludeLang($templateFile, 'template.php');
	}
}

?>