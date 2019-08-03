<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MailUtil
{
	public static function convertCharset($str, $from, $to)
	{
		$from = trim(strtolower($from));
		$to   = trim(strtolower($to));

		if (in_array($from, array('utf-8', 'utf8')))
		{
			$regex = '/
				([\x00-\x7F]
					|[\xC2-\xDF][\x80-\xBF]
					|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF])
				|(\xE0[\xA0-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]|\xED[\x80-\x9F]
					|\xF0[\x90-\xBF][\x80-\xBF]{0,2}|[\xF1-\xF3][\x80-\xBF]{1,3}|\xF4[\x80-\x8F][\x80-\xBF]{0,2}
					|[\x80-\xFF])
			/x';

			$str = preg_replace_callback($regex, function ($matches)
			{
				return isset($matches[2])
					? str_repeat('?', \CUtil::binStrlen($matches[2]))
					: $matches[1];
			}, $str);
		}

		if ($result = \Bitrix\Main\Text\Encoding::convertEncoding($str, $from, $to, $error))
			$str = $result;
		else
			addMessage2Log(sprintf('Failed to convert email part. (%s -> %s : %s)', $from, $to, $error));

		return $str;
	}

	public static function uue_decode($str)
	{
		preg_match("/begin [0-7]{3} .+?\r?\n(.+)?\r?\nend/i", $str, $reg);

		$str = $reg[1];
		$res = '';
		$str = preg_split("/\r?\n/", trim($str));
		$strlen = count($str);

		for ($i = 0; $i < $strlen; $i++)
		{
			$pos = 1;
			$d = 0;
			$len= (int)(((ord(substr($str[$i],0,1)) -32) - ' ') & 077);

			while (($d + 3 <= $len) AND ($pos + 4 <= strlen($str[$i])))
			{
				$c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
				$c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
				$c2 = (ord(substr($str[$i],$pos+2,1)) ^ 0x20);
				$c3 = (ord(substr($str[$i],$pos+3,1)) ^ 0x20);
				$res .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4)).
						chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2)).
						chr(((($c2 - ' ') & 077) << 6) |  (($c3 - ' ') & 077));

				$pos += 4;
				$d += 3;
			}

			if (($d + 2 <= $len) && ($pos + 3 <= strlen($str[$i])))
			{
				$c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
				$c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
				$c2 = (ord(substr($str[$i],$pos+2,1)) ^ 0x20);
				$res .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4)).
						chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));

				$pos += 3;
				$d += 2;
			}

			if (($d + 1 <= $len) && ($pos + 2 <= strlen($str[$i])))
			{
				$c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
				$c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
				$res .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
			}
		}

		return $res;
	}

	function ByteXOR($a,$b,$l)
	{
		$c="";
		for($i=0; $i<$l; $i++)
			$c .= $a{$i}^$b{$i};
		return($c);
	}

	function BinMD5($val)
	{
		return(pack("H*",md5($val)));
	}
}