<?php
/**
 * KDAPHPExcel
 *
 * Copyright (c) 2006 - 2013 KDAPHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	KDAPHPExcel
 * @package		KDAPHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.7.9, 2013-06-02
 */


/** KDAPHPExcel root directory */
if (!defined('KDAPHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('KDAPHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(KDAPHPEXCEL_ROOT . 'KDAPHPExcel/Autoloader.php');
}


/**
 * KDAPHPExcel_Calculation_TextData
 *
 * @category	KDAPHPExcel
 * @package		KDAPHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Calculation_TextData {

	private static $_invalidChars = Null;

	private static function _uniord($c) {
		if (ord($c{0}) >=0 && ord($c{0}) <= 127)
			return ord($c{0});
		if (ord($c{0}) >= 192 && ord($c{0}) <= 223)
			return (ord($c{0})-192)*64 + (ord($c{1})-128);
		if (ord($c{0}) >= 224 && ord($c{0}) <= 239)
			return (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
		if (ord($c{0}) >= 240 && ord($c{0}) <= 247)
			return (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
		if (ord($c{0}) >= 248 && ord($c{0}) <= 251)
			return (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
		if (ord($c{0}) >= 252 && ord($c{0}) <= 253)
			return (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
		if (ord($c{0}) >= 254 && ord($c{0}) <= 255) //error
			return KDAPHPExcel_Calculation_Functions::VALUE();
		return 0;
	}	//	function _uniord()

	/**
	 * CHARACTER
	 *
	 * @param	string	$character	Value
	 * @return	int
	 */
	public static function CHARACTER($character) {
		$character	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($character);

		if ((!is_numeric($character)) || ($character < 0)) {
			return KDAPHPExcel_Calculation_Functions::VALUE();
		}

		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding('&#'.intval($character).';', 'UTF-8', 'HTML-ENTITIES');
		} else {
			return chr(intval($character));
		}
	}


	/**
	 * TRIMNONPRINTABLE
	 *
	 * @param	mixed	$stringValue	Value to check
	 * @return	string
	 */
	public static function TRIMNONPRINTABLE($stringValue = '') {
		$stringValue	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($stringValue);

		if (is_bool($stringValue)) {
			return ($stringValue) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if (self::$_invalidChars == Null) {
			self::$_invalidChars = range(chr(0),chr(31));
		}

		if (is_string($stringValue) || is_numeric($stringValue)) {
			return str_replace(self::$_invalidChars,'',trim($stringValue,"\x00..\x1F"));
		}
		return NULL;
	}	//	function TRIMNONPRINTABLE()


	/**
	 * TRIMSPACES
	 *
	 * @param	mixed	$stringValue	Value to check
	 * @return	string
	 */
	public static function TRIMSPACES($stringValue = '') {
		$stringValue	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($stringValue);

		if (is_bool($stringValue)) {
			return ($stringValue) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if (is_string($stringValue) || is_numeric($stringValue)) {
			return trim(preg_replace('/ +/',' ',trim($stringValue,' ')));
		}
		return NULL;
	}	//	function TRIMSPACES()


	/**
	 * ASCIICODE
	 *
	 * @param	string	$characters		Value
	 * @return	int
	 */
	public static function ASCIICODE($characters) {
		if (($characters === NULL) || ($characters === ''))
			return KDAPHPExcel_Calculation_Functions::VALUE();
		$characters	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($characters);
		if (is_bool($characters)) {
			if (KDAPHPExcel_Calculation_Functions::getCompatibilityMode() == KDAPHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
				$characters = (int) $characters;
			} else {
				$characters = ($characters) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
			}
		}

		$character = $characters;
		if ((function_exists('mb_strlen')) && (function_exists('mb_substr'))) {
			if (mb_strlen($characters, 'UTF-8') > 1) { $character = mb_substr($characters, 0, 1, 'UTF-8'); }
			return self::_uniord($character);
		} else {
			if (strlen($characters) > 0) { $character = substr($characters, 0, 1); }
			return ord($character);
		}
	}	//	function ASCIICODE()


	/**
	 * CONCATENATE
	 *
	 * @return	string
	 */
	public static function CONCATENATE() {
		// Return value
		$returnValue = '';

		// Loop through arguments
		$aArgs = KDAPHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			if (is_bool($arg)) {
				if (KDAPHPExcel_Calculation_Functions::getCompatibilityMode() == KDAPHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
					$arg = (int) $arg;
				} else {
					$arg = ($arg) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
				}
			}
			$returnValue .= $arg;
		}

		// Return
		return $returnValue;
	}	//	function CONCATENATE()


	/**
	 * DOLLAR
	 *
	 * This function converts a number to text using currency format, with the decimals rounded to the specified place.
	 * The format used is $#,##0.00_);($#,##0.00)..
	 *
	 * @param	float	$value			The value to format
	 * @param	int		$decimals		The number of digits to display to the right of the decimal point.
	 *									If decimals is negative, number is rounded to the left of the decimal point.
	 *									If you omit decimals, it is assumed to be 2
	 * @return	string
	 */
	public static function DOLLAR($value = 0, $decimals = 2) {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$decimals	= is_null($decimals) ? 0 : KDAPHPExcel_Calculation_Functions::flattenSingleValue($decimals);

		// Validate parameters
		if (!is_numeric($value) || !is_numeric($decimals)) {
			return KDAPHPExcel_Calculation_Functions::NaN();
		}
		$decimals = floor($decimals);

		if ($decimals > 0) {
			return money_format('%.'.$decimals.'n',$value);
		} else {
			$round = pow(10,abs($decimals));
			if ($value < 0) { $round = 0-$round; }
			$value = KDAPHPExcel_Calculation_MathTrig::MROUND($value,$round);
			//	The implementation of money_format used if the standard PHP function is not available can't handle decimal places of 0,
			//		so we display to 1 dp and chop off that character and the decimal separator using substr
			return substr(money_format('%.1n',$value),0,-2);
		}
	}	//	function DOLLAR()


	/**
	 * SEARCHSENSITIVE
	 *
	 * @param	string	$needle		The string to look for
	 * @param	string	$haystack	The string in which to look
	 * @param	int		$offset		Offset within $haystack
	 * @return	string
	 */
	public static function SEARCHSENSITIVE($needle,$haystack,$offset=1) {
		$needle		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($needle);
		$haystack	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($haystack);
		$offset		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($offset);

		if (!is_bool($needle)) {
			if (is_bool($haystack)) {
				$haystack = ($haystack) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
			}

			if (($offset > 0) && (KDAPHPExcel_Shared_String::CountCharacters($haystack) > $offset)) {
				if (KDAPHPExcel_Shared_String::CountCharacters($needle) == 0) {
					return $offset;
				}
				if (function_exists('mb_strpos')) {
					$pos = mb_strpos($haystack, $needle, --$offset, 'UTF-8');
				} else {
					$pos = strpos($haystack, $needle, --$offset);
				}
				if ($pos !== false) {
					return ++$pos;
				}
			}
		}
		return KDAPHPExcel_Calculation_Functions::VALUE();
	}	//	function SEARCHSENSITIVE()


	/**
	 * SEARCHINSENSITIVE
	 *
	 * @param	string	$needle		The string to look for
	 * @param	string	$haystack	The string in which to look
	 * @param	int		$offset		Offset within $haystack
	 * @return	string
	 */
	public static function SEARCHINSENSITIVE($needle,$haystack,$offset=1) {
		$needle		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($needle);
		$haystack	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($haystack);
		$offset		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($offset);

		if (!is_bool($needle)) {
			if (is_bool($haystack)) {
				$haystack = ($haystack) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
			}

			if (($offset > 0) && (KDAPHPExcel_Shared_String::CountCharacters($haystack) > $offset)) {
				if (KDAPHPExcel_Shared_String::CountCharacters($needle) == 0) {
					return $offset;
				}
				if (function_exists('mb_stripos')) {
					$pos = mb_stripos($haystack, $needle, --$offset,'UTF-8');
				} else {
					$pos = stripos($haystack, $needle, --$offset);
				}
				if ($pos !== false) {
					return ++$pos;
				}
			}
		}
		return KDAPHPExcel_Calculation_Functions::VALUE();
	}	//	function SEARCHINSENSITIVE()


	/**
	 * FIXEDFORMAT
	 *
	 * @param	mixed		$value	Value to check
	 * @param	integer		$decimals
	 * @param	boolean		$no_commas
	 * @return	boolean
	 */
	public static function FIXEDFORMAT($value, $decimals = 2, $no_commas = FALSE) {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$decimals	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($decimals);
		$no_commas	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($no_commas);

		// Validate parameters
		if (!is_numeric($value) || !is_numeric($decimals)) {
			return KDAPHPExcel_Calculation_Functions::NaN();
		}
		$decimals = floor($decimals);

		$valueResult = round($value,$decimals);
		if ($decimals < 0) { $decimals = 0; }
		if (!$no_commas) {
			$valueResult = number_format($valueResult,$decimals);
		}

		return (string) $valueResult;
	}	//	function FIXEDFORMAT()


	/**
	 * LEFT
	 *
	 * @param	string	$value	Value
	 * @param	int		$chars	Number of characters
	 * @return	string
	 */
	public static function LEFT($value = '', $chars = 1) {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$chars		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($chars);

		if ($chars < 0) {
			return KDAPHPExcel_Calculation_Functions::VALUE();
		}

		if (is_bool($value)) {
			$value = ($value) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if (function_exists('mb_substr')) {
			return mb_substr($value, 0, $chars, 'UTF-8');
		} else {
			return substr($value, 0, $chars);
		}
	}	//	function LEFT()


	/**
	 * MID
	 *
	 * @param	string	$value	Value
	 * @param	int		$start	Start character
	 * @param	int		$chars	Number of characters
	 * @return	string
	 */
	public static function MID($value = '', $start = 1, $chars = null) {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$start		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($start);
		$chars		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($chars);

		if (($start < 1) || ($chars < 0)) {
			return KDAPHPExcel_Calculation_Functions::VALUE();
		}

		if (is_bool($value)) {
			$value = ($value) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if (function_exists('mb_substr')) {
			return mb_substr($value, --$start, $chars, 'UTF-8');
		} else {
			return substr($value, --$start, $chars);
		}
	}	//	function MID()


	/**
	 * RIGHT
	 *
	 * @param	string	$value	Value
	 * @param	int		$chars	Number of characters
	 * @return	string
	 */
	public static function RIGHT($value = '', $chars = 1) {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$chars		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($chars);

		if ($chars < 0) {
			return KDAPHPExcel_Calculation_Functions::VALUE();
		}

		if (is_bool($value)) {
			$value = ($value) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if ((function_exists('mb_substr')) && (function_exists('mb_strlen'))) {
			return mb_substr($value, mb_strlen($value, 'UTF-8') - $chars, $chars, 'UTF-8');
		} else {
			return substr($value, strlen($value) - $chars);
		}
	}	//	function RIGHT()


	/**
	 * STRINGLENGTH
	 *
	 * @param	string	$value	Value
	 * @return	string
	 */
	public static function STRINGLENGTH($value = '') {
		$value		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);

		if (is_bool($value)) {
			$value = ($value) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		if (function_exists('mb_strlen')) {
			return mb_strlen($value, 'UTF-8');
		} else {
			return strlen($value);
		}
	}	//	function STRINGLENGTH()


	/**
	 * LOWERCASE
	 *
	 * Converts a string value to upper case.
	 *
	 * @param	string		$mixedCaseString
	 * @return	string
	 */
	public static function LOWERCASE($mixedCaseString) {
		$mixedCaseString	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);

		if (is_bool($mixedCaseString)) {
			$mixedCaseString = ($mixedCaseString) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		return KDAPHPExcel_Shared_String::StrToLower($mixedCaseString);
	}	//	function LOWERCASE()


	/**
	 * UPPERCASE
	 *
	 * Converts a string value to upper case.
	 *
	 * @param	string		$mixedCaseString
	 * @return	string
	 */
	public static function UPPERCASE($mixedCaseString) {
		$mixedCaseString	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);

		if (is_bool($mixedCaseString)) {
			$mixedCaseString = ($mixedCaseString) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		return KDAPHPExcel_Shared_String::StrToUpper($mixedCaseString);
	}	//	function UPPERCASE()


	/**
	 * PROPERCASE
	 *
	 * Converts a string value to upper case.
	 *
	 * @param	string		$mixedCaseString
	 * @return	string
	 */
	public static function PROPERCASE($mixedCaseString) {
		$mixedCaseString	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);

		if (is_bool($mixedCaseString)) {
			$mixedCaseString = ($mixedCaseString) ? KDAPHPExcel_Calculation::getTRUE() : KDAPHPExcel_Calculation::getFALSE();
		}

		return KDAPHPExcel_Shared_String::StrToTitle($mixedCaseString);
	}	//	function PROPERCASE()


	/**
	 * REPLACE
	 *
	 * @param	string	$oldText	String to modify
	 * @param	int		$start		Start character
	 * @param	int		$chars		Number of characters
	 * @param	string	$newText	String to replace in defined position 
	 * @return	string
	 */
	public static function REPLACE($oldText = '', $start = 1, $chars = null, $newText) {
		$oldText	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($oldText);
		$start		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($start);
		$chars		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($chars);
		$newText	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($newText);

		$left = self::LEFT($oldText,$start-1);
		$right = self::RIGHT($oldText,self::STRINGLENGTH($oldText)-($start+$chars)+1);

		return $left.$newText.$right;
	}	//	function REPLACE()


	/**
	 * SUBSTITUTE
	 *
	 * @param	string	$text		Value
	 * @param	string	$fromText	From Value
	 * @param	string	$toText		To Value
	 * @param	integer	$instance	Instance Number
	 * @return	string
	 */
	public static function SUBSTITUTE($text = '', $fromText = '', $toText = '', $instance = 0) {
		$text		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($text);
		$fromText	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($fromText);
		$toText		= KDAPHPExcel_Calculation_Functions::flattenSingleValue($toText);
		$instance	= floor(KDAPHPExcel_Calculation_Functions::flattenSingleValue($instance));

		if ($instance == 0) {
			if(function_exists('mb_str_replace')) {
				return mb_str_replace($fromText,$toText,$text);
			} else {
				return str_replace($fromText,$toText,$text);
			}
		} else {
			$pos = -1;
			while($instance > 0) {
				if (function_exists('mb_strpos')) {
					$pos = mb_strpos($text, $fromText, $pos+1, 'UTF-8');
				} else {
					$pos = strpos($text, $fromText, $pos+1);
				}
				if ($pos === false) {
					break;
				}
				--$instance;
			}
			if ($pos !== false) {
				if (function_exists('mb_strlen')) {
					return self::REPLACE($text,++$pos,mb_strlen($fromText, 'UTF-8'),$toText);
				} else {
					return self::REPLACE($text,++$pos,strlen($fromText),$toText);
				}
			}
		}

		return $text;
	}	//	function SUBSTITUTE()


	/**
	 * RETURNSTRING
	 *
	 * @param	mixed	$testValue	Value to check
	 * @return	boolean
	 */
	public static function RETURNSTRING($testValue = '') {
		$testValue	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($testValue);

		if (is_string($testValue)) {
			return $testValue;
		}
		return Null;
	}	//	function RETURNSTRING()


	/**
	 * TEXTFORMAT
	 *
	 * @param	mixed	$value	Value to check
	 * @param	string	$format	Format mask to use
	 * @return	boolean
	 */
	public static function TEXTFORMAT($value,$format) {
		$value	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($value);
		$format	= KDAPHPExcel_Calculation_Functions::flattenSingleValue($format);

		if ((is_string($value)) && (!is_numeric($value)) && KDAPHPExcel_Shared_Date::isDateTimeFormatCode($format)) {
			$value = KDAPHPExcel_Calculation_DateTime::DATEVALUE($value);
		}

		return (string) KDAPHPExcel_Style_NumberFormat::toFormattedString($value,$format);
	}	//	function TEXTFORMAT()

}	//	class KDAPHPExcel_Calculation_TextData
