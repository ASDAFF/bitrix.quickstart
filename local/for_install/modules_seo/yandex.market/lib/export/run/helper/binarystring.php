<?php

namespace Yandex\Market\Export\Run\Helper;

class BinaryString 
{
	public static function getLength($str)
	{
		return \function_exists('mb_strlen') ? mb_strlen($str, 'latin1') : strlen($str);
	}
	
	public static function getPosition($haystack, $needle, $offset = 0)
	{
		if (\defined('BX_UTF'))
		{
			if (\function_exists('mb_orig_strpos'))
			{
				return mb_orig_strpos($haystack, $needle, $offset);
			}

			return mb_strpos($haystack, $needle, $offset, 'latin1');
		}

		return strpos($haystack, $needle, $offset);
	}
}