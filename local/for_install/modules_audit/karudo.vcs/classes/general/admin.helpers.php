<?php

class CVCSAdminHelpers {
	public static function GetFilterOperationsArray() {
		static $arOperations = array(
			'=',
			'>=',
			'<=',
			'>',
			'<',
			//'%',
		);

		return $arOperations;
	}

	public static function GetFiltersOperationsSelect($name, $sel = false, $select_params = '') {
		$arOperations = self::GetFilterOperationsArray();

		$ret = '<select name="'.$name.'" '.$select_params.'>';
		foreach ($arOperations as $op) {
			$hop = htmlspecialchars($op);
			$ret .= '<option value="'.$hop.'"'.(($sel!==false && $sel == $op)?' selected="selected"':'').'>'.$hop.'</option>';
		}
		$ret .= '</select>';

		return $ret;
	}

	public static function FNConvertCharset($string)
	{
		static $systemEncoding, $serverEncoding;

		if (empty($systemEncoding)) {
			$systemEncoding = strtolower(defined("BX_FILE_SYSTEM_ENCODING") ? BX_FILE_SYSTEM_ENCODING : "");
			if (empty($systemEncoding)) {
				if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN")
					$systemEncoding = "windows-1251";
				else
					$systemEncoding = "utf-8";
			}
		}

		if (empty($serverEncoding)) {
			if (defined('BX_UTF'))
				$serverEncoding = "utf-8";
			elseif (defined("SITE_CHARSET") && (strlen(SITE_CHARSET) > 0))
				$serverEncoding = SITE_CHARSET;
			elseif (defined("LANG_CHARSET") && (strlen(LANG_CHARSET) > 0))
				$serverEncoding = LANG_CHARSET;
			else
				$serverEncoding = "windows-1251";

			$serverEncoding = strtolower($serverEncoding);
		}

		if ($serverEncoding == $systemEncoding)
			return $string;

		return CVCSMain::GetAPPLICATION()->ConvertCharset($string, $serverEncoding, $systemEncoding);
	}
}