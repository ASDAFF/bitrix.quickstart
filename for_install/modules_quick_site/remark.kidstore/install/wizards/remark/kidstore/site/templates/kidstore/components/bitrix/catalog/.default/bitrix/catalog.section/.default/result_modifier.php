<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arDefaultParams = array(
	'PRODUCT_DISPLAY_MODE' => 'N',
	'ADD_PICT_PROP' => '-',
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_DETAIL' => '',
	'MESS_NOT_AVAILABLE' => ''
);
$arParams = array_merge($arDefaultParams, $arParams);

if (!isset($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;
$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);
if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
	$arParams['LINE_ELEMENT_COUNT'] = 3;

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'N';

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
	$arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
	$arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
if (!is_array($arParams['OFFER_TREE_PROPS']))
	$arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
{
	$value = (string)$value;
	if ('' == $value || '-' == $value)
		unset($arParams['OFFER_TREE_PROPS'][$key]);
}
if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']))
{
	$arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
	foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
	{
		$value = (string)$value;
		if ('' == $value || '-' == $value)
			unset($arParams['OFFER_TREE_PROPS'][$key]);
	}
}
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
	$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
	$arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
	$arParams['SHOW_OLD_PRICE'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_DETAIL'] = trim($arParams['MESS_BTN_DETAIL']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);

if (!empty($arResult['ITEMS']))
{
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
	if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
	{
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
		if (!empty($arSizes))
		{
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}
	unset($strEmptyPreview);

	$arSKUPropList = array();
	$arSKUPropIDs = array();
	$arSKUPropKeys = array();
	$boolSKU = false;
	$strBaseCurrency = '';
	$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
	if ($arResult['MODULES']['catalog'])
	{
		if (!$boolConvert)
			$strBaseCurrency = CCurrency::GetBaseCurrency();

		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
		$boolSKU = !empty($arSKU) && is_array($arSKU);
		if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']) && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'])
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
				if ('' == $arProp['CODE'])
					$arProp['CODE'] = $arProp['ID'];
				if (!in_array($arProp['CODE'], $arParams['OFFER_TREE_PROPS']))
					continue;
				if ('Y' == $arProp['MULTIPLE'])
					continue;
				$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
				if ('L' != $arProp['PROPERTY_TYPE'] && 'E' != $arProp['PROPERTY_TYPE'] && !('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE']))
					continue;
				if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'])
				{
					if (!isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
						continue;
					if (!CModule::IncludeModule('highloadblock'))
						continue;
				}
				$arOneSKU = array(
					'ID' => intval($arProp['ID']),
					'CODE' => $arProp['CODE'],
					'NAME' => $arProp['NAME'],
					'SORT' => intval($arProp['SORT']),
					'PROPERTY_TYPE' => $arProp['PROPERTY_TYPE'],
					'USER_TYPE' => $arProp['USER_TYPE'],
					'LINK_IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
					'USER_TYPE_SETTINGS' => $arProp['USER_TYPE_SETTINGS'],
					'VALUES' => array()
				);
				if ('L' == $arProp['PROPERTY_TYPE'])
				{
					$arOneSKU['SHOW_MODE'] = 'LIST';
					$arValues = array();
					$rsPropEnums = CIBlockProperty::GetPropertyEnum($arProp['ID']);
					while ($arEnum = $rsPropEnums->Fetch())
					{
						$arEnum['ID'] = intval($arEnum['ID']);
						$arValues[$arEnum['ID']] = array(
							'ID' => $arEnum['ID'],
							'NAME' => $arEnum['VALUE'],
							'SORT' => intval($arEnum['SORT']),
							'PICT' => false
						);
					}
					$arValues[0] = array(
						'ID' => 0,
						'SORT' => PHP_INT_MAX,
						'NA' => true,
						'NAME' => '-',
						'PICT' => false
					);
					$arOneSKU['VALUES'] = $arValues;
					$arOneSKU['VALUES_COUNT'] = count($arValues);
				}
				elseif ('E' == $arProp['PROPERTY_TYPE'])
				{
					$arOneSKU['SHOW_MODE'] = 'PICT';
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
						$arEnum['ID'] = intval($arEnum['ID']);
						$arValues[$arEnum['ID']] = array(
							'ID' => $arEnum['ID'],
							'NAME' => $arEnum['NAME'],
							'SORT' => intval($arEnum['SORT']),
							'PICT' => array(
								'SRC' => $arEnum['PREVIEW_PICTURE']['SRC'],
								'WIDTH' => intval($arEnum['PREVIEW_PICTURE']['WIDTH']),
								'HEIGHT' => intval($arEnum['PREVIEW_PICTURE']['HEIGHT'])
							)
						);
					}
					$arValues[0] = array(
						'ID' => 0,
						'SORT' => PHP_INT_MAX,
						'NA' => true,
						'NAME' => '',
						'PICT' => $arEmptyPreview
					);
					$arOneSKU['VALUES'] = $arValues;
					$arOneSKU['VALUES_COUNT'] = count($arValues);
				}
				else
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
					if (!isset($hlblock['ID']))
						continue;
					$arValues = array();
					$arXmlMap = array();
					$boolName = true;
					$boolPict = true;
					$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
					$entity_data_class = $entity->getDataClass();
					$rsPropEnums = $entity_data_class::getList(array());
					while ($arEnum = $rsPropEnums->fetch())
					{
						if (!isset($arEnum['UF_NAME']))
						{
							$boolName = false;
							break;
						}
						$arEnum['PREVIEW_PICTURE'] = false;
						if (!isset($arEnum['UF_FILE']))
							$boolPict = false;
						if ($boolPict)
							$arEnum['PREVIEW_PICTURE'] = CFile::GetFileArray($arEnum['UF_FILE']);
						$arEnum['ID'] = intval($arEnum['ID']);
						$arValues[$arEnum['ID']] = array(
							'ID' => $arEnum['ID'],
							'NAME' => $arEnum['UF_NAME'],
							'SORT' => intval($arEnum['UF_SORT']),
							'XML_ID' => $arEnum['UF_XML_ID'],
							'PICT' => ($boolPict ?
								array(
									'SRC' => $arEnum['PREVIEW_PICTURE']['SRC'],
									'WIDTH' => intval($arEnum['PREVIEW_PICTURE']['WIDTH']),
									'HEIGHT' => intval($arEnum['PREVIEW_PICTURE']['HEIGHT'])
								)
								: false
							)
						);
						$arXmlMap[$arEnum['UF_XML_ID']] = $arEnum['ID'];
					}
					if (!$boolName)
						continue;
					$arValues[0] = array(
						'ID' => 0,
						'SORT' => PHP_INT_MAX,
						'NA' => true,
						'NAME' => ($boolPict ? '' : '-'),
						'XML_ID' => '',
						'PICT' => ($boolPict ? $arEmptyPreview : false)
					);
					$arOneSKU['VALUES'] = $arValues;
					$arOneSKU['VALUES_COUNT'] = count($arValues);
					$arOneSKU['PROPERTY_TYPE'] = ($boolPict ? 'E' : 'L');
					$arOneSKU['XML_MAP'] = $arXmlMap;
				}
				$arSKUPropList[] = $arOneSKU;
				$arSKUPropIDs[] = $arOneSKU['CODE'];
				$arSKUPropKeys[$arOneSKU['CODE']] = false;
			}
			if (empty($arSKUPropIDs))
				$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
		}
	}

	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$arItem['SECOND_PICT'] = false;
		$arItem['PREVIEW_PICTURE_SECOND'] = false;
		$arItem['CHECK_QUANTITY'] = false;
		if (!isset($arItem['CATALOG_MEASURE_RATIO']))
			$arItem['CATALOG_MEASURE_RATIO'] = 1;
		if (!isset($arItem['CATALOG_QUANTITY']))
			$arItem['CATALOG_QUANTITY'] = 0;
		$arItem['CATALOG_QUANTITY'] = (
			0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
			? floatval($arItem['CATALOG_QUANTITY'])
			: intval($arItem['CATALOG_QUANTITY'])
		);
		$arItem['CATALOG'] = false;
		$arItem['LABEL'] = false;
		if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
			$arItem['CATALOG_SUBSCRIPTION'] = 'N';

		if ('' != $arParams['LABEL_PROP'] && isset($arItem['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]))
		{
			$arItem['LABEL'] = true;
			$arProp = $arItem['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']];
			if ('N' == $arProp['MULTIPLE'] && 'L' == $arProp['PROPERTY_TYPE'] && 'C' == $arProp['LIST_TYPE'])
			{
				$arItem['LABEL_VALUE'] = $arProp['NAME'];
			}
			else
			{
				$arItem['LABEL_VALUE'] = (is_array($arProp['DISPLAY_VALUE'])
					? implode(' / ', $arProp['DISPLAY_VALUE'])
					: $arProp['DISPLAY_VALUE']
				);
			}
			unset($arItem['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]);
		}

		if ($arResult['MODULES']['catalog'])
		{
			$arItem['CATALOG'] = true;
			if (!isset($arItem['CATALOG_TYPE']))
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
			if (
				(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
				&& !empty($arItem['OFFERS'])
			)
			{
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
			}
			switch ($arItem['CATALOG_TYPE'])
			{
				case CCatalogProduct::TYPE_SET:
					$arItem['OFFERS'] = array();
					$arItem['CATALOG_MEASURE_RATIO'] = 1;
					$arItem['CATALOG_QUANTITY'] = 0;
					$arItem['CHECK_QUANTITY'] = false;
					break;
				case CCatalogProduct::TYPE_SKU:
					break;
				case CCatalogProduct::TYPE_PRODUCT:
				default:
					$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
					break;
			}
		}
		else
		{
			$arItem['CATALOG_TYPE'] = 0;
			$arItem['OFFERS'] = array();
		}

		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
			{
				$arMatrixFields = $arSKUPropKeys;
				$arMatrix = array();

				$arNewOffers = array();
				$boolPictSecond = false;
				$boolSKUDisplayProperties = false;
				$arItem['OFFERS_PROP'] = false;

				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					$arRow = array();
					foreach ($arSKUPropIDs as $propkey => $strOneCode)
					{
						$arCell = array(
							'VALUE' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true
						);
						if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
						{
							$arMatrixFields[$strOneCode] = true;
							$arCell['NA'] = false;
							if ('directory' == $arSKUPropList[$propkey]['USER_TYPE'])
							{
								$intValue = $arSKUPropList[$propkey]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
								$arCell['VALUE'] = $intValue;
							}
							elseif ('L' == $arSKUPropList[$propkey]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
							}
							elseif ('E' == $arSKUPropList[$propkey]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
							}
							$arCell['SORT'] = $arSKUPropList[$propkey]['VALUES'][$arCell['VALUE']]['SORT'];
						}
						$arRow[$strOneCode] = $arCell;
					}
					$arMatrix[$keyOffer] = $arRow;

					if (!empty($arParams['OFFER_TREE_PROPS']))
					{
						foreach ($arParams['OFFER_TREE_PROPS'] as &$strOneCode)
						{
							if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
								unset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]);
						}
						if (isset($strOneCode))
							unset($strOneCode);
					}

					$arOffer['SECOND_PICT'] = false;
					$arOffer['PREVIEW_PICTURE_SECOND'] = false;
					$arOffer['CHECK_QUANTITY'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'N' == $arOffer['CATALOG_CAN_BUY_ZERO']);
					if (!isset($arOffer['CATALOG_MEASURE_RATIO']))
						$arOffer['CATALOG_MEASURE_RATIO'] = 1;
					if (!isset($arOffer['CATALOG_QUANTITY']))
						$arOffer['CATALOG_QUANTITY'] = 0;
					$arOffer['CATALOG_QUANTITY'] = (
						0 < $arOffer['CATALOG_QUANTITY'] && is_float($arOffer['CATALOG_MEASURE_RATIO'])
						? floatval($arOffer['CATALOG_QUANTITY'])
						: intval($arOffer['CATALOG_QUANTITY'])
					);
					$arOffer['CATALOG_TYPE'] = CCatalogProduct::TYPE_OFFER;

					if (!empty($arOffer['PREVIEW_PICTURE']) && !is_array($arOffer['PREVIEW_PICTURE']))
						$arOffer['PREVIEW_PICTURE'] = CFile::GetFileArray($arOffer['PREVIEW_PICTURE']);
					if (empty($arOffer['PREVIEW_PICTURE']) && !empty($arOffer['DETAIL_PICTURE']))
						$arOffer['PREVIEW_PICTURE'] = CFile::GetFileArray($arOffer['DETAIL_PICTURE']);
					if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
					{
						if ('F' == $arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['PROPERTY_TYPE'])
						{
							if (
								isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'])
								&& !empty($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'])
							)
							{
								$arOneFileValue = (isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE']['ID'])
									? $arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE']
									: current($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'])
								);
								$arPict = array(
									'ID' => intval($arOneFileValue['ID']),
									'SRC' => $arOneFileValue['SRC'],
									'WIDTH' => intval($arOneFileValue['WIDTH']),
									'HEIGHT' => intval($arOneFileValue['HEIGHT'])
								);
								if (empty($arOffer['PREVIEW_PICTURE']))
								{
									$arOffer['PREVIEW_PICTURE'] = $arPict;
								}
								else
								{
									$arOffer['SECOND_PICT'] = true;
									$boolPictSecond = true;
									$arOffer['PREVIEW_PICTURE_SECOND'] = $arPict;
								}
							}
						}
						unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);
					}
					$arNewOffers[$keyOffer] = $arOffer;
				}
				$arItem['OFFERS'] = $arNewOffers;

				$arUsedFields = array();
				$arSortFields = array();

				foreach ($arSKUPropIDs as $propkey => $strOneCode)
				{
					$boolExist = $arMatrixFields[$strOneCode];
					foreach ($arMatrix as $keyOffer => $arRow)
					{
						if ($boolExist)
						{
							if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
								$arItem['OFFERS'][$keyOffer]['TREE'] = array();
							$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$propkey]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
							$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
							$arUsedFields[$strOneCode] = true;
							$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
						}
						else
						{
							unset($arMatrix[$keyOffer][$strOneCode]);
						}
					}
				}
				$arItem['OFFERS_PROP'] = $arUsedFields;

				\Bitrix\Main\Type\Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

				$boolSetPict = true;
				$arMatrix = array();
				$intSelected = -1;
				$arItem['MIN_PRICE'] = false;
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					if (empty($arItem['MIN_PRICE']) && $arOffer['CAN_BUY'])
					{
						$intSelected = $keyOffer;
						$arItem['MIN_PRICE'] = $arOffer['MIN_PRICE'];
					}
					$arSKUProps = false;
					if (!empty($arOffer['DISPLAY_PROPERTIES']))
					{
						$boolSKUDisplayProperties = true;
						$arSKUProps = array();
						foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
						{
							if ('F' == $arOneProp['PROPERTY_TYPE'])
								continue;
							$arSKUProps[] = array(
								'NAME' => $arOneProp['NAME'],
								'VALUE' => $arOneProp['DISPLAY_VALUE']
							);
						}
						unset($arOneProp);
					}
					$arOneRow = array(
						'ID' => $arOffer['ID'],
						'NAME' => $arOffer['~NAME'],
						'TREE' => $arOffer['TREE'],
						'DISPLAY_PROPERTIES' => $arSKUProps,
						'PRICE' => $arOffer['MIN_PRICE'],
						'SECOND_PICT' => $arOffer['SECOND_PICT'],
						'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
						'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
						'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
						'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
						'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
						'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
						'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
						'CAN_BUY' => $arOffer['CAN_BUY'],
						'BUY_URL' => $arOffer['~BUY_URL'],
						'ADD_URL' => $arOffer['~ADD_URL'],
					);
					if ($boolPictSecond)
					{
						$arItem['SECOND_PICT'] = true;
					}
					$arMatrix[$keyOffer] = $arOneRow;
				}
				if (-1 == $intSelected)
					$intSelected = 0;
				$arItem['JS_OFFERS'] = $arMatrix;
				$arItem['OFFERS_SELECTED'] = $intSelected;
				$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
			}
			else
			{
				$arMinPrice = false;
				$dblMinPrice = 0;
				$strMinCurrency = ($boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency);

				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					if (!$arOffer['CAN_BUY'])
						continue;
					if (empty($arMinPrice))
					{
						$dblMinPrice = ($boolConvert || ($arOffer['MIN_PRICE']['CURRENCY'] == $strMinCurrency)
							? $arOffer['MIN_PRICE']['DISCOUNT_VALUE']
							: CCurrencyRates::ConvertCurrency($arOffer['MIN_PRICE']['DISCOUNT_VALUE'], $arOffer['MIN_PRICE']['CURRENCY'], $strMinCurrency)
						);
						$arMinPrice = $arOffer['MIN_PRICE'];
					}
					else
					{
						$dblComparePrice = ($boolConvert || ($arOffer['MIN_PRICE']['CURRENCY'] == $strMinCurrency)
							? $arOffer['MIN_PRICE']['DISCOUNT_VALUE']
							: CCurrencyRates::ConvertCurrency($arOffer['MIN_PRICE']['DISCOUNT_VALUE'], $arOffer['MIN_PRICE']['CURRENCY'], $strMinCurrency)
						);
						if ($dblMinPrice > $dblComparePrice)
						{
							$dblMinPrice = $dblComparePrice;
							$arMinPrice = $arOffer['MIN_PRICE'];
						}
					}
				}
				$arItem['MIN_PRICE'] = $arMinPrice;
			}
		}

		if (empty($arItem['PREVIEW_PICTURE']))
			$arItem['PREVIEW_PICTURE'] = $arItem['DETAIL_PICTURE'];
		if ('' != $arParams['ADD_PICT_PROP'] && isset($arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']]))
		{
			if ('F' == $arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']]['PROPERTY_TYPE'])
			{
				$arPict = false;
				if (isset($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]))
				{
					if (
						isset($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]['FILE_VALUE'])
						&& !empty($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]['FILE_VALUE'])
					)
					{
						$arOneFileValue = (isset($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]['FILE_VALUE']['ID'])
							? $arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]['FILE_VALUE']
							: current($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]['FILE_VALUE'])
						);
						$arPict = array(
							'ID' => intval($arOneFileValue['ID']),
							'SRC' => $arOneFileValue['SRC'],
							'WIDTH' => intval($arOneFileValue['WIDTH']),
							'HEIGHT' => intval($arOneFileValue['HEIGHT'])
						);
					}
					unset($arItem['DISPLAY_PROPERTIES'][$arParams['ADD_PICT_PROP']]);
				}
				else
				{
					$arPropValue = $arItem['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'];
					if (!is_array($arPropValue))
						$arPropValue = array($arPropValue);
					foreach ($arPropValue as &$strOneValue)
					{
						$arOneFileValue = CFile::GetFileArray($strOneValue);
						if (!empty($arOneFileValue))
						{
							$arPict = array(
								'ID' => intval($arOneFileValue['ID']),
								'SRC' => $arOneFileValue['SRC'],
								'WIDTH' => intval($arOneFileValue['WIDTH']),
								'HEIGHT' => intval($arOneFileValue['HEIGHT'])
							);
							break;
						}
					}
					if (isset($strOneValue))
						unset($strOneValue);
				}
				if (!empty($arPict))
				{
					if (empty($arItem['PREVIEW_PICTURE']))
					{
						$arItem['PREVIEW_PICTURE'] = $arPict;
					}
					else
					{
						$arItem['SECOND_PICT'] = true;
						$arItem['PREVIEW_PICTURE_SECOND'] = $arPict;
					}
				}
			}
		}
		if (empty($arItem['PREVIEW_PICTURE']))
		{
			$arItem['PREVIEW_PICTURE'] = $arEmptyPreview;
			if ($arItem['SECOND_PICT'])
				$arItem['PREVIEW_PICTURE_SECOND'] = $arEmptyPreview;
		}

		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}

		$arNewItemsList[$key] = $arItem;
	}
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $arSKUPropList;
}
?>