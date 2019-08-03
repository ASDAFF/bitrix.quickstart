<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Extrasettings {
	function __construct()
	{

	}
	
	public static function GetMarginTemplates(&$pfile)
	{
		$pdir = dirname(__FILE__).'/../../profiles/';
		CheckDirPath($pdir);
		$pfile = $pdir.'margins.txt';
		if(file_exists($pfile)) $arTemplates = unserialize(file_get_contents($pfile));
		if(!is_array($arTemplates)) $arTemplates = array();
		return $arTemplates;
	}
	
	public static function SaveMarginTemplate($arPost)
	{
		$pfile = '';
		$arTemplates = self::GetMarginTemplates($pfile);
		$PEXTRASETTINGS = array();
		self::HandleParams($PEXTRASETTINGS, $arPost['EXTRASETTINGS']);
		$arMargins = self::GetMargins($PEXTRASETTINGS);
		if(strlen($arPost['template_id']) > 0 && is_numeric($arPost['template_id']))
		{
			$arTemplates[$arPost['template_id']]['MARGINS'] = $arMargins;
		}
		elseif(strlen($arPost['template_name']) > 0)
		{
			$arTemplates[] = array(
				'TITLE' => $arPost['template_name'],
				'MARGINS' => $arMargins
			);
		}
		file_put_contents($pfile, serialize($arTemplates));
		return $arTemplates;
	}
	
	public static function DeleteMarginTemplate($tid)
	{
		$pfile = '';
		$arTemplates = self::GetMarginTemplates($pfile);
		unset($arTemplates[$tid]);
		$arTemplates = array_values($arTemplates);
		file_put_contents($pfile, serialize($arTemplates));
		return $arTemplates;
	}
	
	public static function GetMargins($PEXTRASETTINGS)
	{
		foreach($PEXTRASETTINGS as $k1=>$v1)
		{
			foreach($v1 as $k2=>$v2)
			{
				return $v2['MARGINS'];
			}
		}
	}
	
	public static function HandleParams(&$PEXTRASETTINGS, $arParams)
	{
		global $APPLICATION;
		if(!defined('BX_UTF') || !BX_UTF)
		{
			$arParams = $APPLICATION->ConvertCharsetArray($arParams, "UTF-8", "Windows-1251");
		}

		foreach($arParams as $k1=>$v1)
		{
			$PEXTRASETTINGS[$k1] = array();
			foreach($v1 as $k2=>$v2)
			{
				if($k2=='MARGINS')
				{
					$arMargins = array();
					foreach($v2['PERCENT'] as $k3=>$v3)
					{
						$v3 = str_replace(',', '.', $v3);
						$v2['PRICE_FROM'][$k3] = str_replace(',', '.', $v2['PRICE_FROM'][$k3]);
						$v2['PRICE_TO'][$k3] = str_replace(',', '.', $v2['PRICE_TO'][$k3]);
						if(floatval($v3) > 0)
						{
							$margin = array(
								'TYPE' => $v2['TYPE'][$k3],
								'PERCENT' => floatval($v3),
								'PRICE_FROM' => (strlen(trim($v2['PRICE_FROM'][$k3])) > 0 ? floatval($v2['PRICE_FROM'][$k3]) : false),
								'PRICE_TO' => (strlen(trim($v2['PRICE_TO'][$k3])) > 0 ? floatval($v2['PRICE_TO'][$k3]) : false)
							);
							$arMargins[] = $margin;
						}
					}
					if(!empty($arMargins)) $PEXTRASETTINGS[$k1][$k2] = $arMargins;
					else unset($PEXTRASETTINGS[$k1][$k2]);
					continue;
				}
				
				if($k2=='CONVERSION' || $k2=='EXTRA_CONVERSION')
				{
					$arConversions = array();
					foreach($v2['WHEN'] as $k3=>$v3)
					{
						if(strlen($v2['FROM'][$k3]) > 0 || strlen($v2['TO'][$k3]) > 0 
							|| in_array($v2['CELL'][$k3], array('ELSE'))
							|| in_array($v2['WHEN'][$k3], array('ANY'))
							|| in_array($v2['THEN'][$k3], array('NOT_LOAD', 'MATH_ROUND', 'TRANSLIT', 'STRIP_TAGS', 'CLEAR_TAGS')))
						{
							$arConversion = array(
								'CELL' => $v2['CELL'][$k3],
								'WHEN' => $v3,
								'FROM' => $v2['FROM'][$k3],
								'THEN' => $v2['THEN'][$k3],
								'TO' => $v2['TO'][$k3]
							);
							$arConversions[] = $arConversion;
						}
					}
					if(!empty($arConversions)) $PEXTRASETTINGS[$k1][$k2] = $arConversions;
					else unset($PEXTRASETTINGS[$k1][$k2]);
					continue;
				}
				
				if($k2=='CONDITIONS')
				{
					$arConditions = array();
					foreach($v2['WHEN'] as $k3=>$v3)
					{
						if(strlen($v2['CELL'][$k3]) > 0)
						{
							$arCondition = array(
								'CELL' => $v2['CELL'][$k3],
								'WHEN' => $v3,
								'FROM' => $v2['FROM'][$k3]
							);
							$arConditions[] = $arCondition;
						}
					}
					if(!empty($arConditions)) $PEXTRASETTINGS[$k1][$k2] = $arConditions;
					else unset($PEXTRASETTINGS[$k1][$k2]);
					continue;
				}
				
				if(is_array($v2))
				{
					$v2 = array_map('trim', $v2);
					$v2 = array_diff($v2, array(''));
				}
				
				if($k2=='USE_FILTER_FOR_DEACTIVATE' && $v2!='Y') $v2 = '';
				if($k2=='SET_NEW_ONLY' && $v2!='Y') $v2 = '';
				if($k2=='NOT_TRIM' && $v2!='Y') $v2 = '';
				if($k2=='USE_FOR_SKU_GENERATE' && $v2!='Y') $v2 = '';
				if($k2=='CHANGE_MULTIPLE_SEPARATOR' && $v2!='Y') $v2 = '';
				if($k2=='SECTION_UID_SEPARATED' && $v2!='Y') $v2 = '';
				if($k2=='SECTION_SEARCH_IN_SUBSECTIONS' && $v2!='Y') $v2 = '';
				if($k2=='COPY_CELL_ON_OFFERS' && $v2!='Y') $v2 = '';
				if($k2=='UID_SEARCH_SUBSTRING' && $v2!='Y') $v2 = '';
				if($k2=='SEARCH_SINGLE_OFFERS' && $v2!='Y') $v2 = '';
				if($k2=='MULTIPLE_SAVE_OLD_VALUES' && $v2!='Y') $v2 = '';
				if($k2=='PRICE_USE_EXT' && $v2!='Y') $v2 = '';

				if(!empty($v2))
				{
					$PEXTRASETTINGS[$k1][$k2] = $v2;
				}
				else
				{
					unset($PEXTRASETTINGS[$k1][$k2]);
				}
			}
		}
	}
}