<?
/**
 * Created by Tuning-Soft
 * http://tuning-soft.ru/
 *
 * NOTE: Requires PHP version 5.3 or later
 *
 * @package   CAPISearchFilter
 * @author    Anton Kuchkovsky <support@tuning-soft.ru>
 * @copyright © 1984-2015 Tuning-Soft
 * @date      05.11.2013
 */

IncludeModuleLangFile(__FILE__);
$MODULE_ID = basename(dirname(__FILE__));

define('API_SF_CHAR_LENGTH', 2);

Class CAPISearchFilter
{
	protected static $arStopWords = array();

	function __construct()
	{
		self::$arStopWords = GetMessage('STOP_WORDS');
	}

	public static function getWords($query, $word_length = 1)
	{
		if(strlen($query) <= 3)
			return '%'.$query.'%';

		$q = ToLower($query);
		$q = strtr($q, GetMessage('EE'));
		$q = preg_replace('/&([a-zA-Z0-9]+);/', ' ', $q);
		$q = trim(preg_replace('/ +/', ' ', $q));

		$where = '';
		$step = 0;
		$words = explode(' ', $q);
		foreach($words as $word)
		{
			$step++;

			if(strlen($word) < $word_length || in_array($word, self::$arStopWords))
				continue;

			if($step > 4)
				break;

			$stem_word = self::stem($word);
			$where .= '%' . $stem_word . '%';

			if($word !== end($words))
				$where .= ' && ';
		}

		return $where;
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

		for($i = 0; $i < strlen($word); $i += API_SF_CHAR_LENGTH)
		{
			if($flag == 1)
				$rv .= substr($word, $i, API_SF_CHAR_LENGTH);
			else
				$start .= substr($word, $i, API_SF_CHAR_LENGTH);

			if(array_search(substr($word, $i, API_SF_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
		}
		return array($start, $rv);
	}

	protected static function step1($word)
	{
		$perfective1 = GetMessage('STEP1_PERFECTIVE1');
		foreach($perfective1 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_YA')))
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
					if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_YA')))
						$word = substr($word, 0, strlen($word) - strlen($suffix));
				foreach($participle2 as $suffix)
					if(substr($word, -(strlen($suffix))) == $suffix)
						$word = substr($word, 0, strlen($word) - strlen($suffix));
				return $word;
			}
		$verb1 = GetMessage('STEP1_VERB1');
		foreach($verb1 as $suffix)
			if(substr($word, -(strlen($suffix))) == $suffix && (substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_A') || substr($word, -strlen($suffix) - API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP1_YA')))
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
		return substr($word, -API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP2_I') ? substr($word, 0, strlen($word) - API_SF_CHAR_LENGTH) : $word;
	}

	protected static function step3($word)
	{
		$vowels = GetMessage('STEP3_VOWELS');
		$flag   = 0;
		$r1     = $r2 = '';
		for($i = 0; $i < strlen($word); $i += API_SF_CHAR_LENGTH)
		{
			if($flag == 2)
				$r1 .= substr($word, $i, API_SF_CHAR_LENGTH);
			if(array_search(substr($word, $i, API_SF_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
			if($flag = 1 && array_search(substr($word, $i, API_SF_CHAR_LENGTH), $vowels) === false)
				$flag = 2;
		}
		$flag = 0;
		for($i = 0; $i < strlen($r1); $i += API_SF_CHAR_LENGTH)
		{
			if($flag == 2)
				$r2 .= substr($r1, $i, API_SF_CHAR_LENGTH);
			if(array_search(substr($r1, $i, API_SF_CHAR_LENGTH), $vowels) !== false)
				$flag = 1;
			if($flag = 1 && array_search(substr($r1, $i, API_SF_CHAR_LENGTH), $vowels) === false)
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
		if(substr($word, -API_SF_CHAR_LENGTH * 2) == GetMessage('STEP4_NN'))
			$word = substr($word, 0, strlen($word) - API_SF_CHAR_LENGTH);
		else
		{
			$superlative = GetMessage('STEP4_SUPERLATIVE');
			foreach($superlative as $suffix)
				if(substr($word, -(strlen($suffix))) == $suffix)
					$word = substr($word, 0, strlen($word) - strlen($suffix));
			if(substr($word, -API_SF_CHAR_LENGTH * 2) == GetMessage('STEP4_NN'))
				$word = substr($word, 0, strlen($word) - API_SF_CHAR_LENGTH);
		}
		if(substr($word, -API_SF_CHAR_LENGTH, API_SF_CHAR_LENGTH) == GetMessage('STEP4_SOFT_SIGN'))
			$word = substr($word, 0, strlen($word) - API_SF_CHAR_LENGTH);
		return $word;
	}

	/* @deprecated */
	function  OnBuildGlobalMenu(){}
}
?>