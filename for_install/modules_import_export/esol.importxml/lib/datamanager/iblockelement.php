<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class IblockElement
{
	protected static $elemListHash = array();
	
	public static function GetList($arFilter, $arKeys, $arOrder=array(), $limit=false)
	{
		$hash = md5(serialize(array_merge(array_keys($arFilter), $arKeys, $arOrder, array($limit))));
		if(!isset(self::$elemListHash[$hash]))
		{
			self::$elemListHash[$hash] = '';
			if(class_exists('\Bitrix\Iblock\ElementTable'))
			{
				$arNeedKeys = array_merge($arKeys, array_keys($arOrder));
				foreach($arFilter as $key=>$val)
				{
					$arNeedKeys[] = preg_replace('/^[^\d\w]*([\d\w]|$)/', '$1', $key);
				}
				$arFields = array_keys(\Bitrix\Iblock\ElementTable::getMap());
				if(count(array_diff($arNeedKeys, $arFields, array('CHECK_PERMISSIONS')))==0)
				{
					self::$elemListHash[$hash] = 'd7';
				}
			}
		}
		if(self::$elemListHash[$hash]=='d7')
		{
			if(isset($arFilter['CHECK_PERMISSIONS'])) unset($arFilter['CHECK_PERMISSIONS']);
			$arParams = array('filter'=>$arFilter, 'select'=>$arKeys);
			if(!empty($arOrder)) $arParams['order'] = $arOrder;
			if($limit!==false) $arParams['limit'] = $limit;
			$dbRes = \Bitrix\Iblock\ElementTable::getList($arParams);
		}
		else
		{
			$dbRes = \CIblockElement::GetList($arOrder, $arFilter, false, ($limit===false ? false : array('nTopCount'=>$limit)), $arKeys);
		}
		return $dbRes;
	}
	
	public static function SelectedRowsCount($dbRes)
	{
		if(is_callable(array($dbRes, 'getSelectedRowsCount'))) return $dbRes->getSelectedRowsCount();
		elseif(is_callable(array($dbRes, 'SelectedRowsCount'))) return $dbRes->SelectedRowsCount();
		else return 0;
	}
}