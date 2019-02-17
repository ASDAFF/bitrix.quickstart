<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CAltasibFeedbackThemes extends CBitrixComponent
{
	protected function getColorValues($color_)
	{
		$arColors = array();
		$rgbColor = "";
		for($i = 0 ; $i < 3 ; $i++)
		{
			$arr[$i] = substr($color_, $i * 2 + 1, 2);
		}
		$rgbColor .= hexdec($arr[0]) . hexdec($arr[1]) . hexdec($arr[2]);
		for ($i = 0; $i < 3; $i++)
		{
			if ($rgbColor % 1000 > 255)
			{
				$arColors[] = $rgbColor % 100;
				$rgbColor = intval($rgbColor / 100);
			}
			else
			{
				$arColors[] = $rgbColor % 1000;
				$rgbColor = intval($rgbColor / 1000);
			}
		}
		return array_reverse($arColors);
	}

	protected function lighterColor($color_)
	{
		$cols = self::getColorValues($color_);
		$arRgb = array();
		for ($i = 0; $i < count($cols); $i++) {
			$arRgb[] = $cols[$i] + 91 > 255 ? 255 : $cols[$i] + 91;
		}
		if ($arRgb[0] < 130) $arRgb[0] = $arRgb[0] + 80 > 255 ? 255 : $arRgb[0] + 80;
		if ($arRgb[0] > 165 && $arRgb[0] < 170 &&
			$arRgb[3] > 170 && $arRgb[3] < 190) {
			$arRgb[0] += 30;
			$arRgb[3] += 10;
		}
		if ($arRgb[1] > 150 && $arRgb[1] < 170) $arRgb[1]+= 20;
		return sprintf('#%02X%02X%02X', $arRgb[0],$arRgb[1],$arRgb[2]);
	}

	protected function darkerColor($color_)
	{
		$cols = self::getColorValues($color_);
		$arRgb = array();
		for ($i = 0; $i < count($cols); $i++) {
			$arRgb[] = $cols[$i] - 53 < 0 ? 0 : $cols[$i] - 53;
		}
		return sprintf('#%02X%02X%02X', $arRgb[0],$arRgb[1],$arRgb[2]);
	}

	protected function textColor($color_)
	{
		$cols = self::getColorValues($color_);
		$arRgb = array();
		for ($i = 0; $i < count($cols); $i++) {
			$arRgb[] = $cols[$i] - 53 < 0 ? 0 : $cols[$i] - 53;
		}
		return ($arRgb[0] * 0.229 + $arRgb[1] *0.587 + $arRgb[2] * 0.114);
	}

	public function Generate($color = false, $theme = false, $scheme = false, $ALX, $tmplPath, $strStyle)
	{
		$filename = $_SERVER['DOCUMENT_ROOT'].$tmplPath.'/themes/theme_'.md5($theme.'_'.$color.'_'.$scheme.'_'.$ALX).'.css';
		if(file_exists($filename))
			return;

		$tColor = '#fff';
		if($theme)
		{
			switch($theme)
			{
				case 'c1':
					$brighter = '#b2dfdb';
					$normal = '#009688';
					$darker = '#006257';
					break;
				case 'c2':
					$brighter = '#bbdefb';
					$normal = '#2196f3';
					$darker = '#0B5E9E';
					break;
				case 'c3':
					$brighter = '#ffcdd2';
					$normal = '#f44336';
					$darker = '#9F1C12';
					break;
				case 'c4':
					$brighter = '#c8e6c9';
					$normal = '#4caf50';
					$darker = '#19721F';
					break;
				case 'c5':
					$brighter = '#d1c4e9';
					$normal = '#673ab7';
					$darker = '#371377';
					break;
				case 'c6':
					$brighter = '#ffccbc';
					$normal = '#ff5722';
					$darker = '#A6300B';
					break;
				case 'c7':
					$brighter = '#f5f5f5';
					$normal = '#9e9e9e';
					$darker = '#545454';
					$tColor = '#000';
					break;
				case 'c8':
					$brighter = '#cfd8dc';
					$normal = '#607d8b';
					$darker = '#1F475A';
					break;
				default:
					$brighter = '#b2dfdb';
					$normal = '#009688';
					$darker = '#006257';
				break;
			}
		}
		else
		{
			$normal = $color;
			$brighter = self::lighterColor($color);
			$darker = self::darkerColor($color);

			$light = self::textColor($color);
			if($light>127)
				$tColor = '#000';
		}
		if($scheme == "PALE" && $theme)
		{
			$darker = $normal;
			$normal = $brighter;
		}

		if(empty($strStyle))
			$strStyle = GetMessage("AFBF_STYLE_GENERATE");

		$style = $strStyle;
		$aReplace = array(
			"#ID#" => $ALX,
			"#NORMAL#" => $normal,
			"#DARKER#" => $darker,
			"#BRIGHT#" => $brighter,
			"#TCOLOR#" => $tColor,
		);

		foreach($aReplace as $search=>$replace)
			$style = str_replace($search, $replace, $style);

		file_put_contents($filename, $style);
	}
}