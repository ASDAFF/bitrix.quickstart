<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('highloadblock','iblock');

foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(GetMessage("F_NO_MODULE"));
		return 0;
	}
}

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$arResult = array();
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams['ELEMENT_CODE'] = ($arParams["ELEMENT_ID"] > 0 ? '' : trim($arParams['ELEMENT_CODE']));

//Handle case when ELEMENT_CODE used
if($arParams["ELEMENT_ID"] <= 0)
{
	$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
		$arParams["ELEMENT_ID"],
		$arParams["ELEMENT_CODE"],
		false,
		false,
		array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		)
	);
	$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
}
$arResult['ID'] = $arParams["ELEMENT_ID"];

// LOAD COMMENTS
if($arParams['HLBLOCK_PROP_CODE'])
{
	if($arResult['ID'] > 0)
	{
		$rsProps = CIBlockElement::GetProperty(
			$arParams['IBLOCK_ID'],
			$arResult['ID'],
			array("sort"=>"asc"),
			array('CODE' => $arParams['HLBLOCK_PROP_CODE'])
		);
	}
	else
	{
		$rsProps = CIBlockProperty::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array('CODE' => $arParams['HLBLOCK_PROP_CODE'])
		);
	}

	$hlblocks = array();
	$reqParams = array();

	while($arProp = $rsProps->Fetch())
	{
		if(!isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
			continue;

		if(!isset($hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']]))
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array(
					"filter" => array(
						'TABLE_NAME' => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME']
					)
				)
			)->fetch();

			$hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']] = $hlblock;
		}
		else
		{
			$hlblock = $hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']];
		}

		if (isset($hlblock['ID']))
		{
			if(!isset($reqParams[$hlblock['ID']]))
			{
				$reqParams[$hlblock['ID']] = array();
				$reqParams[$hlblock['ID']]['HLB'] = $hlblock;
			}
			$reqParams[$hlblock['ID']]['VALUES'][] = $arProp['VALUE'];
		}
	}
		
	foreach ($reqParams as $params)
	{
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($params['HLB']);
		$entityDataClass = $entity->getDataClass();
		$fieldsList = $entityDataClass::getMap();
		
		if (count($fieldsList) === 1 && isset($fieldsList['ID']))
			$fieldsList = $entityDataClass::getEntity()->getFields();
		
		$directoryOrder = array();
		
		if (isset($fieldsList['UF_SORT']))
			$directoryOrder['UF_SORT'] = 'DESC';
			
		$directoryOrder['ID'] = 'DESC';
		
		$arFilter = array('order' => $directoryOrder);
		
		if($arResult['ID'] > 0)
			$arFilter['filter'] = array('UF_XML_ID' => $params['VALUES']);

		$rsPropEnums = $entityDataClass::getList($arFilter);

		$arResult['RATING'] = 0;
		while ($arEnum = $rsPropEnums->fetch())
		{
			$arResult['COMMENTS'][] = $arEnum;
			
			
			if($arParams['EMARKET_COMMENT_PREMODERATION'] == 'Y')
			{
				if($arEnum['UF_ACTIVE'])
					$arResult['RATING'] += $arEnum['UF_RATING'];
			}
			else
				$arResult['RATING'] += $arEnum['UF_RATING'];
			
		}
		$arResult['RATING'] = $arResult['RATING'] / count($arResult['COMMENTS']);
		
		
		//update rating & count comments
		if($arResult['RATING'] != $arParams['EMARKET_CUR_RATING'])
		{
			CIBlockElement::SetPropertyValuesEx(
				$arParams['ELEMENT_ID'], 
				$arParams['IBLOCK_ID'], 
				array('EMARKET_RATING'=>$arResult['RATING']));
		}
		
		if(count($arResult['COMMENTS']) != $arParams['EMARKET_CUR_COMMENTS_COUNT'])
		{
			CIBlockElement::SetPropertyValuesEx(
				$arParams['ELEMENT_ID'], 
				$arParams['IBLOCK_ID'], 
				array('EMARKET_COMMENTS_COUNT'=>count($arResult['COMMENTS'])));	
		}
	}
}

// LOAD CRITERIA
if($arParams['HLBLOCK_CR_PROP_CODE'])
{
	if($arResult['ID'] > 0)
	{
		$rsProps = CIBlockElement::GetProperty(
			$arParams['IBLOCK_ID'],
			$arResult['ID'],
			array("sort"=>"asc"),
			array('CODE' => $arParams['HLBLOCK_CR_PROP_CODE'])
		);
	}
	else
	{
		$rsProps = CIBlockProperty::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array('CODE' => $arParams['HLBLOCK_CR_PROP_CODE'])
		);
	}

	$hlblocks = array();
	$reqParams = array();
	
	while($arProp = $rsProps->Fetch())
	{
		if(!isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
			continue;

		if(!isset($hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']]))
		{
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
				array(
					"filter" => array(
						'TABLE_NAME' => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME']
					)
				)
			)->fetch();

			$hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']] = $hlblock;
		}
		else
		{
			$hlblock = $hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']];
		}

		if (isset($hlblock['ID']))
		{
			if(!isset($reqParams[$hlblock['ID']]))
			{
				$reqParams[$hlblock['ID']] = array();
				$reqParams[$hlblock['ID']]['HLB'] = $hlblock;
			}
			$reqParams[$hlblock['ID']]['VALUES'][] = $arProp['VALUE'];
		}
	}
	
	foreach ($reqParams as $params)
	{
		$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($params['HLB']);
		$entityDataClass = $entity->getDataClass();
		$fieldsList = $entityDataClass::getMap();
		
		if (count($fieldsList) === 1 && isset($fieldsList['ID']))
			$fieldsList = $entityDataClass::getEntity()->getFields();
		
		$directoryOrder = array();
		
		if (isset($fieldsList['UF_SORT']))
			$directoryOrder['UF_SORT'] = 'ASC';
			
		$directoryOrder['ID'] = 'DESC';
		$arFilter = array('order' => $directoryOrder);
		$rsPropEnums = $entityDataClass::getList($arFilter);

		
		while ($arEnum = $rsPropEnums->fetch())
		{
			$arResult['CRITERIA'][] = $arEnum;
		}
	}
}

$this->IncludeComponentTemplate();