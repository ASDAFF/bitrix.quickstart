<?
/************************************
*
* Universal Extension for component"s result_modifier.php
* last update 21.07.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSDevFuncResultModifier
{
	protected static $highLoadInclude = null;
	
	function SaleBasketBasketSmall($arResult)
	{
		$arNewResult = $arResult;
		if(CModule::IncludeModule("catalog") && is_array($arNewResult["ITEMS"]) && count($arNewResult["ITEMS"])>0)
		{
			$arNewResult["FULL_PRICE"] = 0;
			$arNewResult["NUM_PRODUCTS"] = 0;
			foreach($arNewResult["ITEMS"] as $arItem)
			{
				$arNewResult["FULL_PRICE"] += $arItem["PRICE"] * $arItem["QUANTITY"];
				$arNewResult["NUM_PRODUCTS"]++;
			}
			$arNewResult["PRINT_FULL_PRICE"] = FormatCurrency($arNewResult["FULL_PRICE"], $arNewResult["ITEMS"][0]["CURRENCY"]);
		}
		return $arNewResult;
	}
	
	function SearchTitle($arResult)
	{
		$arNewResult = $arResult;
		if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog'))
		{
			$arIBlocks = array();
			$arOthers = array();
			if(!empty($arNewResult['CATEGORIES']))
			{
				foreach($arNewResult['CATEGORIES'] as $category_id => $arCategory)
				{
					foreach($arNewResult['ITEMS'] as $i => $arItem)
					{
						if($arItem['MODULE_ID']=='iblock')
						{
							if(empty($arIBlocks[$arItem['PARAM2']]))
							{
								$res = CIBlock::GetByID( $arItem['PARAM2'] );
								if($arRes = $res->GetNext())
								{
									$arIBlocks['IBLOCKS'][$arItem['PARAM2']] = $arRes;
								}
							}
							$arIBlocks['ITEMS'][$arItem['PARAM2']][] = $arItem;
						} else {
							$arOthers['ITEMS'][] = $arItem;
						}
					}
				}
			}
			$arNewResult['EXT_SEARCH'] = array(
				'IBLOCK' => $arIBlocks,
				'OTHER' => $arOthers,
			);
		}
		return $arNewResult;
	}
	
	function SearchPage($arResult)
	{
		$arNewResult = $arResult;
		if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog'))
		{
			$arIBlocks = array();
			$arOthers = array();
			if(!empty($arNewResult['SEARCH']))
			{
					foreach($arNewResult['SEARCH'] as $i => $arItem)
					{
						if($arItem['MODULE_ID']=='iblock')
						{
							if(empty($arIBlocks[$arItem['PARAM2']]))
							{
								$res = CIBlock::GetByID( $arItem['PARAM2'] );
								if($arRes = $res->GetNext())
								{
									$arIBlocks['IBLOCKS'][$arItem['PARAM2']] = $arRes;
								}
							}
							$arIBlocks['ITEMS'][$arItem['PARAM2']][] = $arItem;
						} else {
							$arOthers['ITEMS'][] = $arItem;
						}
					}
			}
			$arNewResult['EXT_SEARCH'] = array(
				'IBLOCK' => $arIBlocks,
				'OTHER' => $arOthers,
			);
		}
		return $arNewResult;
	}
	
	function CatalogSmartFilter($arResult)
	{
		$arNewResult = $arResult;
		
		// prices in first place
		if( isset($arNewResult['PRICES']) )
		{
			end($arNewResult['PRICES']);
			while(current($arNewResult['PRICES']))
			{
				$priceKey = key($arNewResult['PRICES']);
				$arPrice = $arResult['ITEMS'][$priceKey];
				unset($arNewResult['ITEMS'][$priceKey]);
				array_unshift($arNewResult['ITEMS'], $arPrice);
				prev($arNewResult['PRICES']);
			}
		}
		
		// add picture for highloadblock
		foreach($arNewResult['ITEMS'] as $key => $arItem)
		{
			if($arItem['PROPERTY_TYPE']=='S' || $arItem['USER_TYPE']=='directory')
			{
				$arPropData = $arItem;
				if (!isset($arItem['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arItem['USER_TYPE_SETTINGS']['TABLE_NAME']))
					continue;
				if (null === self::$highLoadInclude)
					self::$highLoadInclude = \Bitrix\Main\Loader::includeModule('highloadblock');
				if (!self::$highLoadInclude)
					continue;
				$highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arItem['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
				if (!isset($highBlock['ID']))
					continue;
				$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
				$entityDataClass = $entity->getDataClass();
				$fieldsList = $entityDataClass::getMap();
				if(empty($fieldsList))
				{
					continue;
				} else {
					if(count($fieldsList)===1 && isset($fieldsList['ID']))
					{
						$fieldsList = $entityDataClass::getEntity()->getFields();
					}
				}
				$arPropData['USER_TYPE_SETTINGS']['FIELDS_MAP'] = $fieldsList;
				$arPropData['USER_TYPE_SETTINGS']['ENTITY'] = $entity;
				$VALUES_EX = RSDevFuncOffersExtension::GetSortedPropertiesValues($arPropData);
				foreach($VALUES_EX as $arVal)
				{
					if( isset( $arNewResult['ITEMS'][$key]['VALUES'][ $arVal['XML_ID'] ] ) && is_array( $arNewResult['ITEMS'][$key]['VALUES'][ $arVal['XML_ID'] ] ) )
					{
						$arNewResult['ITEMS'][$key]['VALUES'][ $arVal['XML_ID'] ]['PICT'] = $arVal['PICT'];
					}
				}
			}
		}
		
		return $arNewResult;
	}
}