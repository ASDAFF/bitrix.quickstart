<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class ProductD7 extends Product
{
	protected $productFields = null;
	
	public function __construct($ie=false)
	{
		$this->productFields = array_keys(\Bitrix\Catalog\ProductTable::getMap());
		parent::__construct($ie);
	}
	
	public function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		$arParams = array();
		if(!empty($arOrder)) $arParams['order'] = $arParams;
		if(!empty($arFilter)) $arParams['filter'] = $arFilter;
		if(is_array($arGroupBy) && !empty($arGroupBy)) $arParams['group'] = $arGroupBy;
		if(is_array($arNavStartParams) && !empty($arNavStartParams))
		{
			if($arNavStartParams['nTopCount']) $arParams['limit'] = $arNavStartParams['nTopCount'];
		}
		if(!empty($arSelectFields)) $arParams['select'] = array_intersect($arSelectFields, $this->productFields);
		return \Bitrix\Catalog\Model\Product::getList($arParams);
	}
	
	public function Add($arFields, $IBLOCK_ID=false, $boolCheck = true)
	{
		foreach($arFields as $k=>$v)
		{
			if(!in_array($k, $this->productFields)) unset($arFields[$k]);
		}
		$arFields = array('fields' => $arFields);
		if($IBLOCK_ID) $arFields['external_fields']['IBLOCK_ID'] = $IBLOCK_ID;
		$result = \Bitrix\Catalog\Model\Product::add($arFields);
		if($result->isSuccess())
		{
			return (int)$result->getId();
		}
		else return false;
	}
	
	public function Update($ID, $IBLOCK_ID=false, $arFields=array())
	{
		foreach($arFields as $k=>$v)
		{
			if(!in_array($k, $this->productFields)) unset($arFields[$k]);
		}
		$arFields = array('fields' => $arFields);
		if($IBLOCK_ID) $arFields['external_fields']['IBLOCK_ID'] = $IBLOCK_ID;
		if($result = \Bitrix\Catalog\Model\Product::update($ID, $arFields))
		{
			return $result->isSuccess();
		}
		else return false;
	}
	
	public function Delete($ID)
	{
		if($result = \Bitrix\Catalog\Model\Product::delete($ID))
		{
			return $result->isSuccess();
		}
		else return false;
	}
}