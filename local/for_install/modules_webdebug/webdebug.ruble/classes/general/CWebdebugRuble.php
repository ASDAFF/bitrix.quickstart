<?php
IncludeModuleLangFile(__FILE__);

class CWebdebugRuble {
	/*
	*  Event handler for event "CurrencyFormat" (this event is used in function "CurrencyFormat", "currency" module)
	*/
	function CurrencyFormat($fSum=0, $strCurrency="RUB") {
		if (!isset($fSum) || strlen($fSum)<=0) return false;
		if (!in_array($strCurrency,array("RUB","RUR"))) return false;
		
		// Skip POST
		if (!isset($GLOBALS["webdebug.ruble"]["webdebug_ruble_skip_post"])) {
			$GLOBALS["webdebug.ruble"]["webdebug_ruble_skip_post"] = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_skip_post", "Y");
		}
		if ($GLOBALS["webdebug.ruble"]["webdebug_ruble_skip_post"]=="Y" && isset($_POST) && !empty($_POST)) {
			return false;
		}
		
		// Exclude by REGEX
		if (!isset($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"])) {
			$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"] = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_regex_exclude", "");
			$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"] = explode("\n", $GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"]);
		}
		$bExclude = false;
		if (!is_array($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"])) {
			$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"] = array();
		}
		foreach ($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_exclude"] as $Key => $Row) {
			$Row = trim($Row);
			if ($Row != '' && preg_match($Row, $_SERVER["REQUEST_URI"], $M)) {
				$bExclude = true;
			}
		}
		if ($bExclude) {
			if (!isset($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"])) {
				$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"] = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_regex_include", "");
				$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"] = explode("\n", $GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"]);
			}
			if (!is_array($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"])) {
				$GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"] = array();
			}
			foreach ($GLOBALS["webdebug.ruble"]["webdebug_ruble_regex_include"] as $Key => $Row) {
				$Row = trim($Row);
				if ($Row != '' && preg_match($Row, $_SERVER["REQUEST_URI"], $M)) {
					$bExclude = false;
				}
			}
		}
		if ($bExclude) {
			return false;
		}
		
		// Additional code (eval)
		if (!isset($GLOBALS["webdebug.ruble"]["webdebug_ruble_additional_code"])) {
			$GLOBALS["webdebug.ruble"]["webdebug_ruble_additional_code"] = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_additional_code", "").";";
		}
		if (trim($GLOBALS["webdebug.ruble"]["webdebug_ruble_additional_code"])!="") {
			$Eval = eval($GLOBALS["webdebug.ruble"]["webdebug_ruble_additional_code"]);
			if ($Eval==="" || $Eval===false) return false;
		}
		
		// If in SEO
		$arBacktrace = debug_backtrace(0);
		foreach($arBacktrace as $arFunction) {
			if ($arFunction['function']=='loadFromDatabase' && $arFunction['class']=='Bitrix\Iblock\Template\Entity\ElementPrice') {
				return false;
			}
		}

		$arCurFormat = CCurrencyLang::GetCurrencyFormat($strCurrency);

		if (!isset($arCurFormat["DECIMALS"]))
			$arCurFormat["DECIMALS"] = 2;
		$arCurFormat["DECIMALS"] = IntVal($arCurFormat["DECIMALS"]);
		if ($arCurFormat["HIDE_ZERO"]=='Y' && FloatVal($fSum)==IntVal($fSum)/* && substr($GLOBALS['APPLICATION']->GetCurPage(),0,8)!='/bitrix/'*/) {
			$arCurFormat["DECIMALS"] = 0;
		}
		

		if (!isset($arCurFormat["DEC_POINT"]))
			$arCurFormat["DEC_POINT"] = ".";
		if(!empty($arCurFormat["THOUSANDS_VARIANT"]))
		{
			if($arCurFormat["THOUSANDS_VARIANT"] == "N")
				$arCurFormat["THOUSANDS_SEP"] = "";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "D")
				$arCurFormat["THOUSANDS_SEP"] = ".";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "C")
				$arCurFormat["THOUSANDS_SEP"] = ",";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "S")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "B")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
		}
		
		$Title = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_title", "");
		if ($Title) $Title = ' title=\''.$Title.'\'';
		// Get letter for selected char
		$num = number_format($fSum, $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
		if($arCurFormat["THOUSANDS_VARIANT"] == "B")
			$num = str_replace(" ", "&nbsp;", $num);
		$Price = $num;
		$RubleChar = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_font_char", "a");
		
		$OwnTag = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_own_tag", "N");
		if ($OwnTag=='Y') {
			$RubleChar = '<ruble'.$Title.'>'.$RubleChar.'</ruble>';
		} else {
			$RubleChar = '<span class=\'webdebug-ruble-symbol\''.$Title.'>'.$RubleChar.'</span>';
		}
		
		$Space = COption::GetOptionString("webdebug.ruble", "webdebug_ruble_add_space", "Y")=="Y" ? " " : "";
		if (COption::GetOptionString("webdebug.ruble", "webdebug_ruble_symbol_location", "R")=="R") {
			$Price = $Price.$Space.$RubleChar;
		} else {
			$Price = $RubleChar.$Space.$Price;
		}
		return $Price;
	}
	
	function OnProlog() {
		global $APPLICATION;
		$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/webdebug.ruble.css" />', true);
	}
}

?>