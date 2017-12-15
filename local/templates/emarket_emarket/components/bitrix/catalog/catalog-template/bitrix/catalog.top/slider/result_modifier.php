<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("catalog"))
	return;

$arParams['ROTATE_TIMER'] = intval($arParams['ROTATE_TIMER']);
if (0 > $arParams['ROTATE_TIMER'])
	$arParams['ROTATE_TIMER'] = 30;
$arParams['ROTATE_TIMER'] *= 1000;

foreach($arResult["ITEMS"] as $cell=>$arElement)
{
	if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) //Product has offers
	{
		$minItemPrice = 0;
		$minItemPriceFormat = "";
		foreach($arElement["OFFERS"] as $arOffer)
		{
			foreach($arOffer["PRICES"] as $code=>$arPrice)
			{
				if($arPrice["CAN_ACCESS"])
				{
					if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
					{
						$minOfferPrice = $arPrice["DISCOUNT_VALUE"];
						$minOfferPriceFormat = $arPrice["PRINT_DISCOUNT_VALUE"];
					}
					else
					{
						$minOfferPrice = $arPrice["VALUE"];
						$minOfferPriceFormat = $arPrice["PRINT_VALUE"];
					}

					if ($minItemPrice > 0 && $minOfferPrice < $minItemPrice)
					{
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
					}
					elseif ($minItemPrice == 0)
					{
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
					}
				}
			}
		}
		if ($minItemPrice > 0)
		{
			$arResult["ITEMS"][$cell]["MIN_OFFER_PRICE"] = $minItemPrice;
			$arResult["ITEMS"][$cell]["PRINT_MIN_OFFER_PRICE"] = $minItemPriceFormat;
		}
	}
}
/*$arDefaultParams = array(
	'PRODUCT_DISPLAY_MODE' => 'N',
	'SKU_BUY_DISPLAY_MODE' => 'N',
	'SKU_PROPS_DISPLAY_MODE' => 'S',
	'ADD_PICT_PROP' => '-',
	'ARTICUL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_ARTICUL_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'MESS_BTN_BUY' => '',
	'MESS_NOT_AVAILABLE' => '',
	'MESS_BTN_DETAIL' => ''
);
$arParams = array_merge($arDefaultParams, $arParams);

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
if ('Y' != $arParams['SKU_BUY_DISPLAY_MODE'])
	$arParams['SKU_BUY_DISPLAY_MODE'] = 'N';
if ('L' != $arParams['SKU_PROPS_DISPLAY_MODE'])
	$arParams['SKU_PROPS_DISPLAY_MODE'] = 'S';

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
	$arParams['ADD_PICT_PROP'] = '';
$arParams['ARTICUL_PROP'] = trim($arParams['ARTICUL_PROP']);
if ('-' == $arParams['ARTICUL_PROP'])
	$arParams['ARTICUL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
$arParams['OFFER_ARTICUL_PROP'] = trim($arParams['OFFER_ARTICUL_PROP']);
if ('-' == $arParams['OFFER_ARTICUL_PROP'])
	$arParams['OFFER_ARTICUL_PROP'] = '';
if (!is_array($arParams['OFFER_TREE_PROPS']))
	$arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
{
	if ('' == $value || '-' == $value)
		unset($arParams['OFFER_TREE_PROPS'][$key]);
}

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
$arParams['MESS_BTN_DETAIL'] = trim($arParams['MESS_BTN_DETAIL']);

if (!empty($arResult['ITEMS']))
{
	$arSKUPropList = array();
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
	if (!empty($arSKU) && is_array($arSKU) && !empty($arParams['OFFER_TREE_PROPS']))
	{
		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			$arProp['ID'] = intval($arProp['ID']);
			if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			if (!in_array($arProp['CODE'], $arParams['OFFER_TREE_PROPS']))
				continue;
			$arOneSKU = array();
			if ('L' == $arProp['PROPERTY_TYPE'] || 'E' == $arProp['PROPERTY_TYPE'])
			{
				$arOneSKU = array(
					'ID' => $arProp['ID'],
					'CODE' => $arProp['CODE'],
					'NAME' => $arProp['NAME'],
					'SORT' => $arProp['SORT'],
					'TYPE' => $arProp['PROPERTY_TYPE'],
					'VALUES' => array()
				);
				if ('L' == $arProp['PROPERTY_TYPE'])
				{
					$arValues = array();
					$rsPropEnums = CIBlockProperty::GetPropertyEnum($arProp['ID']);
					while ($arEnum = $rsPropEnums->Fetch())
					{
						$arValues[$arEnum['ID']] = array(
							'ID' => $arEnum['ID'],
							'NAME' => $arEnum['VALUE'],
							'SORT' => $arEnum['SORT'],
							'PICT' => false
						);
					}
					$arOneSKU['VALUES'] = $arValues;
					$arOneSKU['VALUES_COUNT'] = count($arValues);
				}
				elseif ('E' == $arProp['PROPERTY_TYPE'])
				{
					$arValues = array();
					$rsPropEnums = CIBlockElement::GetList(
						array('SORT' => 'ASC'),
						array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'),
						false,
						false,
						array('ID', 'NAME', 'PREVIEW_PICTURE')
					);
					while ($arEnum = $rsPropEnums->Fetch())
					{
						$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['PREVIEW_PICTURE']);
						if (!is_array($arEnum['PREVIEW_PICTURE']))
							continue;
						$arValues[$arEnum['ID']] = array(
							'ID' => $arEnum['ID'],
							'NAME' => $arEnum['NAME'],
							'SORT' => $arEnum['SORT'],
							'PICT' => $arEnum['PREVIEW_PICTURE']
						);
					}
					$arOneSKU['VALUES'] = $arValues;
					$arOneSKU['VALUES_COUNT'] = count($arValues);
				}
			}
			$arSKUPropList[] = $arOneSKU;
		}
	}

	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$arNewItem = array();

		$arNewItem = $arItem;
		$arNewItem['SHOW_DOUBLE'] = 'N';
		$arNewItem['DBL_PREVIEW_PICTURE'] = array();
		$arNewItem['SHOW_ARTICUL'] = 'N';
		$arNewItem['ARTICUL'] = '';
		$arNewItem['PRICE_FORMATTED'] = '';
		$arNewItem['CHECK_QUANTITY'] = false;
		$arNewItem['MAX_QUANTITY'] = 0;
		$arNewItem['STEP_QUANTITY'] = 1;
		$arNewItem['PRODUCT_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
		if (CCatalogProduct::TYPE_SET == $arItem['CATALOG_TYPE'])
		{
			$arNewItem['PRODUCT_TYPE'] = CCatalogProduct::TYPE_SET;
		}
		elseif (!empty($arItem['OFFERS']))
		{
			$arNewItem['PRODUCT_TYPE'] = CCatalogProduct::TYPE_SKU;
		}
		else
		{
			$arNewItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
			$arNewItem['MAX_QUANTITY'] = $arItem['CATALOG_QUANTITY'];
		}

		if (empty($arNewItem['PREVIEW_PICTURE']))
			$arNewItem['PREVIEW_PICTURE'] = $arNewItem['DETAIL_PICTURE'];
		if ('' != $arParams['ADD_PICT_PROP'] && isset($arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']]))
		{
			if ('F' == $arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']]['PROPERTY_TYPE'])
			{
				$arProp = $arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']];
				$arPropValue = (is_array($arProp['VALUE']) ? $arProp['VALUE'] : array($arProp['VALUE']));
				foreach ($arPropValue as &$strOneValue)
				{
					$arOneFileValue = CFile::GetFileArray($strOneValue);
					if (!empty($arOneFileValue))
					{
						$arNewItem['SHOW_DOUBLE'] = 'Y';
						$arNewItem['DBL_PREVIEW_PICTURE'] = $arOneFileValue;
						break;
					}
				}
				if (isset($strOneValue))
					unset($strOneValue);
			}
		}
		if ('' != $arParams['ARTICUL_PROP'] && isset($arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]))
		{
			if ('S' == $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['PROPERTY_TYPE'])
			{
				$arProp = $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']];
				$strOneValue = '';
				$boolArr = is_array($arProp['VALUE']);
				if ($boolArr && !empty($arProp['VALUE']))
				{
					$strOneValue = implode(' / ', $arProp['VALUE']);
				}
				elseif (!$boolArr && '' != $arProp['VALUE'])
				{
					$strOneValue = trim($arProp['VALUE']);
				}
				if ('' != $strOneValue)
				{
					$arNewItem['SHOW_ARTICUL'] = 'Y';
					$arNewItem['ARTICUL'] = $strOneValue;
				}
			}
		}

		if (!isset($arItem['OFFERS']) || empty($arItem['OFFERS']))
		{
			if (!empty($arItem['PRICE_MATRIX']) && is_array($arItem['PRICE_MATRIX']))
			{

			}
			elseif (!empty($arItem['PRICES']) && is_array($arItem['PRICES']))
			{
				foreach ($arItem['PRICES'] as $priceKey => $arPrice)
				{
					if ('Y' == $arPrice['MIN_PRICE'])
					{
						$arNewItem['PRICE_FORMATTED'] = $arPrice['PRINT_DISCOUNT_VALUE'];
						break;
					}
				}
			}
		}
		else
		{
			$intCount = 0;
			$arMatrix = array();
			$arOffers = array();
			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
			{
				$arOneRow = array();
				$arNewOffer = $arOffer;
				$arNewOffer['SHOW_DOUBLE'] = 'N';
				$arNewOffer['DBL_PREVIEW_PICTURE'] = array();
				$arNewOffer['SHOW_ARTICUL'] = 'N';
				$arNewOffer['ARTICUL'] = '';
				$arNewOffer['PRICE_FORMATTED'] = '';
				$arNewOffer['CHECK_QUANTITY'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'N' == $arOffer['CATALOG_CAN_BUY_ZERO']);
				$arNewOffer['MAX_QUANTITY'] = $arOffer['CATALOG_QUANTITY'];
				$arNewOffer['STEP_QUANTITY'] = 1;
				$arNewOffer['PRODUCT_TYPE'] = CCatalogProduct::TYPE_OFFER;
				$arNewOffer['TREE'] = array();


				if (!is_array($arNewOffer['PREVIEW_PICTURE']))
					$arNewOffer['PREVIEW_PICTURE'] = CFile::GetFileArray($arNewOffer['PREVIEW_PICTURE']);
				if (empty($arNewOffer['PREVIEW_PICTURE']))
					$arNewOffer['PREVIEW_PICTURE'] = CFile::GetFileArray($arNewOffer['DETAIL_PICTURE']);

				if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
				{
					if ('F' == $arOffer['PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['PROPERTY_TYPE'])
					{
						$arProp = $arOffer['PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']];
						$arPropValue = (is_array($arProp['VALUE']) ? $arProp['VALUE'] : array($arProp['VALUE']));
						foreach ($arPropValue as &$strOneValue)
						{
							$arOneFileValue = CFile::GetFileArray($strOneValue);
							if (!empty($arOneFileValue))
							{
								$arNewOffer['SHOW_DOUBLE'] = 'Y';
								$arNewOffer['DBL_PREVIEW_PICTURE'] = $arOneFileValue;
								break;
							}
						}
						if (isset($strOneValue))
							unset($strOneValue);

					}
				}
				if ('' != $arParams['OFFER_ARTICUL_PROP'] && isset($arOffer['PROPERTIES'][$arParams['OFFER_ARTICUL_PROP']]))
				{
					$arProp = $arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ARTICUL_PROP']];
					if ('S' == $arProp['PROPERTY_TYPE'] && !empty($arProp['VALUE']))
					{
						$arNewOffer['SHOW_ARTICUL'] = 'Y';
						$arNewOffer['ARTICUL'] = ('Y' == $arProp['MULTIPLE']
							? implode(' / ', $arProp['VALUE'])
							: $arProp['VALUE']
						);
						if ('N' == $arNewItem['SHOW_ARTICUL'])
							$arNewItem['SHOW_ARTICUL'] = 'Y';
					}
				}

				if (!empty($arOffer['PRICE_MATRIX']) && is_array($arOffer['PRICE_MATRIX']))
				{

				}
				elseif (!empty($arOffer['PRICES']) && is_array($arOffer['PRICES']))
				{
					foreach ($arOffer['PRICES'] as $priceKey => $arPrice)
					{
						if ('Y' == $arPrice['MIN_PRICE'])
						{
							$arNewOffer['MIN_PRICE'] = $arPrice;
							$arNewOffer['PRICE_FORMATTED'] = $arPrice['PRINT_DISCOUNT_VALUE'];
							if ('' == $arNewItem['PRICE_FORMATTED'])
								$arNewItem['PRICE_FORMATTED'] = $arPrice['PRINT_DISCOUNT_VALUE'];
							break;
						}
					}
				}
				if (!empty($arSKUPropList))
				{
					foreach ($arSKUPropList as $keyProp => $arOneProp)
					{
						if ('L' == $arOneProp['TYPE'])
							$arNewOffer['TREE']['PROP_'.$arOneProp['ID']] = $arOffer['PROPERTIES'][$arOneProp['CODE']]['VALUE_ENUM_ID'];
						else
							$arNewOffer['TREE']['PROP_'.$arOneProp['ID']] = $arOffer['PROPERTIES'][$arOneProp['CODE']]['VALUE'];
					}
				}
				$arOneRow = array(
					'ID' => $arNewOffer['ID'],
					'NAME' => $arNewOffer['NAME'],
					'ARTICUL' => $arNewOffer['ARTICUL'],
					'TREE' => $arNewOffer['TREE'],
					'PRICE' => array(
						'ID' => $arNewOffer['MIN_PRICE']['ID'],
						'PRICE' => $arNewOffer['MIN_PRICE']['PRICE'],
						'CURRENCY' => $arNewOffer['MIN_PRICE']['CURRENCY'],
						'PRICE_FORMATTED' => $arNewOffer['MIN_PRICE']['PRINT_DISCOUNT_VALUE']
					),
					'PICT' => ('Y' == $arNewOffer['SHOW_DOUBLE'] ? $arNewOffer['DBL_PREVIEW_PICTURE'] : $arNewOffer['PREVIEW_PICTURE']),
					'CHECK_QUANTITY' => $arNewOffer['CHECK_QUANTITY'],
					'MAX_QUANTITY' => $arNewOffer['MAX_QUANTITY'],
					'STEP_QUANTITY' => $arNewOffer['STEP_QUANTITY'],
					'QUANTITY_FLOAT' => is_double($arNewOffer['STEP_QUANTITY']),
					'CAN_BUY' => $arNewOffer['CAN_BUY']
				);
				$arMatrix[$intCount] = $arOneRow;
				$arOffers[$intCount] = $arNewOffer;
				$intCount++;
			}
		}
		$arNewItem['OFFERS'] = $arOffers;
		$arNewItem['JS_OFFERS'] = $arMatrix;
		$arNewItem['OFFERS_SELECTED'] = 0;
		if ('Y' != $arNewItem['SHOW_DOUBLE'] && 'Y' == $arNewItem['OFFERS'][$arNewItem['OFFERS_SELECTED']]['SHOW_DOUBLE'])
		{
			$arNewItem['SHOW_DOUBLE'] = 'Y';
			$arNewItem['DBL_PREVIEW_PICTURE'] = $arNewItem['OFFERS'][$arNewItem['OFFERS_SELECTED']]['DBL_PREVIEW_PICTURE'];
		}
		$arNewItemsList[$key] = $arNewItem;
	}
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $arSKUPropList;
}*/
?>