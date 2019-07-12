<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!$arResult['MODULES']['catalog'])
	return;

$arDefaultParams = array(
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'SHOW_MAX_QUANTITY' => 'N',
	'DISPLAY_COMPARE' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_COMPARE' => '',
	'MESS_NOT_AVAILABLE' => '',
	'USE_VOTE_RATING' => 'N',
	'VOTE_DISPLAY_AS_RATING' => 'rating',
	'USE_COMMENTS' => 'N',
	'BLOG_USE' => 'N',
	'VK_USE' => 'N',
	'FB_USE' => 'N',
);
$arParams = array_merge($arDefaultParams, $arParams);

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'N';

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
if ('Y' != $arParams['SHOW_MAX_QUANTITY'])
	$arParams['SHOW_MAX_QUANTITY'] = 'N';
if ('Y' != $arParams['DISPLAY_COMPARE'])
	$arParams['DISPLAY_COMPARE'] = 'N';
$arParams['DISPLAY_COMPARE'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ('Y' != $arParams['USE_VOTE_RATING'])
	$arParams['USE_VOTE_RATING'] = 'N';
if ('vote_avg' != $arParams['VOTE_DISPLAY_AS_RATING'])
	$arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
if ('Y' != $arParams['USE_COMMENTS'])
	$arParams['USE_COMMENTS'] = 'N';
if ('Y' != $arParams['BLOG_USE'])
	$arParams['BLOG_USE'] = 'N';
if ('Y' != $arParams['VK_USE'])
	$arParams['VK_USE'] = 'N';
if ('Y' != $arParams['FB_USE'])
	$arParams['FB_USE'] = 'N';
if ('Y' == $arParams['USE_COMMENTS'])
{
	if ('N' == $arParams['BLOG_USE'] && 'N' == $arParams['VK_USE'] && 'N' == $arParams['FB_USE'])
		$arParams['USE_COMMENTS'] = 'N';
}

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

	if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']))
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
			$arSKUPropList[] = $arOneSKU;
			$arSKUPropIDs[] = $arOneSKU['CODE'];
		}
	}
}

$arResult['SHOW_SLIDER'] = false;
$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
	$arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
	$arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
	0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
	? floatval($arResult['CATALOG_QUANTITY'])
	: intval($arResult['CATALOG_QUANTITY'])
);
$arResult['CATALOG'] = false;
$arResult['LABEL'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
	$arResult['CATALOG_SUBSCRIPTION'] = 'N';

if ('' != $arParams['LABEL_PROP'] && isset($arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]))
{
	$arResult['LABEL'] = true;
	$arProp = $arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']];
	if ('N' == $arProp['MULTIPLE'] && 'L' == $arProp['PROPERTY_TYPE'] && 'C' == $arProp['LIST_TYPE'])
	{
		$arResult['LABEL_VALUE'] = $arProp['NAME'];
	}
	else
	{
		$arResult['LABEL_VALUE'] = (is_array($arProp['DISPLAY_VALUE'])
			? implode(' / ', $arProp['DISPLAY_VALUE'])
			: $arProp['DISPLAY_VALUE']
		);
	}
	unset($arResult['DISPLAY_PROPERTIES'][$arParams['LABEL_PROP']]);
}

if ($arResult['MODULES']['catalog'])
{
	$arResult['CATALOG'] = true;
	if (!isset($arResult['CATALOG_TYPE']))
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
	if (
		(CCatalogProduct::TYPE_PRODUCT == $arResult['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['CATALOG_TYPE'])
		&& !empty($arResult['OFFERS'])
	)
	{
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
	}
	switch ($arResult['CATALOG_TYPE'])
	{
		case CCatalogProduct::TYPE_SET:
			$arResult['OFFERS'] = array();
			$arResult['CATALOG_MEASURE_RATIO'] = 1;
			$arResult['CATALOG_QUANTITY'] = 0;
			$arResult['CHECK_QUANTITY'] = false;
			break;
		case CCatalogProduct::TYPE_SKU:
			break;
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
	}
}
else
{
	$arResult['CATALOG_TYPE'] = 0;
	$arResult['OFFERS'] = array();
}

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	$boolSKUDisplayProps = false;

	$arResultSKUPropIDs = array();
	foreach ($arResult['OFFERS'] as &$arOffer)
	{
		foreach ($arSKUPropIDs as &$strOneCode)
		{
			if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
				$arResultSKUPropIDs[$strOneCode] = true;
		}
		unset($strOneCode);
	}
	unset($arOffer);

	$arSKUPropIDs = array();
	$arFilterProp = array();
	foreach ($arSKUPropList as $arOneSKU)
	{
		if (!isset($arResultSKUPropIDs[$arOneSKU['CODE']]))
			continue;
		if ('L' == $arOneSKU['PROPERTY_TYPE'])
		{
			$arOneSKU['SHOW_MODE'] = 'LIST';
			$arValues = array();
			$rsPropEnums = CIBlockProperty::GetPropertyEnum($arOneSKU['ID']);
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
		elseif ('E' == $arOneSKU['PROPERTY_TYPE'])
		{
			$arOneSKU['SHOW_MODE'] = 'PICT';
			$arValues = array();
			$rsPropEnums = CIBlockElement::GetList(
				array('SORT' => 'ASC'),
				array('IBLOCK_ID' => $arOneSKU['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'),
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
			$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $arOneSKU['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
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
			$arValues[0] = array(
				'ID' => 0,
				'SORT' => PHP_INT_MAX,
				'NA' => true,
				'NAME' => ($boolPict ? '' : '-'),
				'XML_ID' => '',
				'PICT' => ($boolPict ? $arEmptyPreview : false)
			);
			if (!$boolName)
				continue;
			$arOneSKU['VALUES'] = $arValues;
			$arOneSKU['VALUES_COUNT'] = count($arValues);
			$arOneSKU['PROPERTY_TYPE'] = ($boolPict ? 'E' : 'L');
			$arOneSKU['XML_MAP'] = $arXmlMap;
		}
		$arFilterProp[] = $arOneSKU;
		$arSKUPropIDs[] = $arOneSKU['CODE'];
		$arSKUPropKeys[$arOneSKU['CODE']] = false;
	}
	$arSKUPropList = $arFilterProp;
	unset($arFilterProp);

	$arMatrixFields = $arSKUPropKeys;
	$arMatrix = array();

	$arNewOffers = array();

	$arIDS = array();
	$arOfferSet = array();
	$arResult['OFFER_GROUP'] = false;

	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		$arOffer['ID'] = intval($arOffer['ID']);
		$arIDS[] = $arOffer['ID'];
		$arResult['OFFERS_PROP'] = false;
		$boolSKUDisplayProperties = false;
		$arOffer['OFFER_GROUP'] = false;

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
		if (!empty($arOffer['DETAIL_PICTURE']) && !is_array($arOffer['DETAIL_PICTURE']))
			$arOffer['DETAIL_PICTURE'] = CFile::GetFileArray($arOffer['DETAIL_PICTURE']);
		if (empty($arOffer['PREVIEW_PICTURE']) && !empty($arOffer['DETAIL_PICTURE']))
			$arOffer['PREVIEW_PICTURE'] = $arOffer['DETAIL_PICTURE'];

		$arOffer['MORE_PHOTO'] = array();
		$arOffer['MORE_PHOTO_COUNT'] = 0;
		if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
		{
			if ('F' == $arOffer['PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['PROPERTY_TYPE'])
			{
				if (
					isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'])
					&& !empty($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'])
				)
				{
					$boolOnePict = isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE']['ID']);
					if ($boolOnePict)
					{
						$arOneFileValue = $arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'];
						$arPict = array(
							'ID' => intval($arOneFileValue['ID']),
							'SRC' => $arOneFileValue['SRC'],
							'WIDTH' => intval($arOneFileValue['WIDTH']),
							'HEIGHT' => intval($arOneFileValue['HEIGHT'])
						);
						$arOffer["MORE_PHOTO"][] = $arPict;
						$arOffer['MORE_PHOTO_COUNT'] = 1;
						if (empty($arOffer['PREVIEW_PICTURE']))
						{
							$arOffer['PREVIEW_PICTURE'] = $arPict;
						}
						if (empty($arOffer['DETAIL_PICTURE']))
						{
							$arOffer['DETAIL_PICTURE'] = $arPict;
						}
					}
					else
					{
						$boolFirst = true;
						foreach ($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]['FILE_VALUE'] as $arOneFileValue)
						{
							$arPict = array(
								'ID' => intval($arOneFileValue['ID']),
								'SRC' => $arOneFileValue['SRC'],
								'WIDTH' => intval($arOneFileValue['WIDTH']),
								'HEIGHT' => intval($arOneFileValue['HEIGHT'])
							);
							$arOffer["MORE_PHOTO"][] = $arPict;
							if ($boolFirst)
							{
								if (empty($arOffer['PREVIEW_PICTURE']))
								{
									$arOffer['PREVIEW_PICTURE'] = $arPict;
								}
								if (empty($arOffer['DETAIL_PICTURE']))
								{
									$arOffer['DETAIL_PICTURE'] = $arPict;
								}
							}
							$boolFirst = false;
						}
						$arOffer['MORE_PHOTO_COUNT'] = count($arOffer["MORE_PHOTO"]);
					}
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
							if (empty($arOffer['PREVIEW_PICTURE']))
							{
								$arOffer['PREVIEW_PICTURE'] = $arPict;
							}
							if (empty($arOffer['DETAIL_PICTURE']))
							{
								$arOffer['DETAIL_PICTURE'] = $arPict;
							}
							$arOffer["MORE_PHOTO"][] = $arPict;
						}
					}
					if (isset($strOneValue))
						unset($strOneValue);
					$arOffer['MORE_PHOTO_COUNT'] = count($arOffer["MORE_PHOTO"]);
				}
			}
			if (isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
				unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);
		}
		if (0 < $arOffer['MORE_PHOTO_COUNT'])
			$arResult['SHOW_SLIDER'] = true;

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

		if (!empty($arOffer['DISPLAY_PROPERTIES']))
			$boolSKUDisplayProps = true;

		$arNewOffers[$keyOffer] = $arOffer;
	}
	$arResult['OFFERS'] = $arNewOffers;
	$arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

	$arUsedFields = array();
	$arSortFields = array();

	foreach ($arSKUPropIDs as $propkey => $strOneCode)
	{
		$boolExist = $arMatrixFields[$strOneCode];
		foreach ($arMatrix as $keyOffer => $arRow)
		{
			if ($boolExist)
			{
				if (!isset($arResult['OFFERS'][$keyOffer]['TREE']))
					$arResult['OFFERS'][$keyOffer]['TREE'] = array();
				$arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$propkey]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
				$arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
				$arUsedFields[$strOneCode] = true;
				$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
			}
			else
			{
				unset($arMatrix[$keyOffer][$strOneCode]);
			}
		}
	}
	$arResult['OFFERS_PROP'] = $arUsedFields;

	\Bitrix\Main\Type\Collection::sortByColumn($arResult['OFFERS'], $arSortFields);

	if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet'))
	{
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arIDS,
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		while ($arSet = $rsSets->Fetch())
		{
			$arOfferSet[$arSet['OWNER_ID']] = true;
			$arResult['OFFER_GROUP'] = true;
		}
	}

	$arMatrix = array();
	$intSelected = -1;
	$arResult['MIN_PRICE'] = false;
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		if (empty($arResult['MIN_PRICE']) && $arOffer['CAN_BUY'])
		{
			$intSelected = $keyOffer;
			$arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];
		}
		$arSKUProps = false;
		if (!empty($arOffer['DISPLAY_PROPERTIES']))
		{
			$boolSKUDisplayProps = true;
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
		if (isset($arOfferSet[$arOffer['ID']]))
		{
			$arOffer['OFFER_GROUP'] = true;
			$arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
		}
		$arOneRow = array(
			'ID' => $arOffer['ID'],
			'NAME' => $arOffer['~NAME'],
			'TREE' => $arOffer['TREE'],
			'PRICE' => $arOffer['MIN_PRICE'],
			'DISPLAY_PROPERTIES' => $arSKUProps,
			'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
			'DETAIL_PICTURE' => $arOffer['DETAIL_PICTURE'],
			'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
			'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
			'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
			'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
			'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
			'OFFER_GROUP' => $arOffer['OFFER_GROUP'],
			'CAN_BUY' => $arOffer['CAN_BUY'],
			'SLIDER' => $arOffer['MORE_PHOTO'],
			'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
			'BUY_URL' => $arOffer['~BUY_URL']
		);
		$arMatrix[$keyOffer] = $arOneRow;
	}
	if (-1 == $intSelected)
		$intSelected = 0;
	$arResult['JS_OFFERS'] = $arMatrix;
	$arResult['OFFERS_SELECTED'] = $intSelected;

	$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
}

if (!is_array($arResult['PREVIEW_PICTURE']))
	$arResult['PREVIEW_PICTURE'] = $arResult['DETAIL_PICTURE'];
if (empty($arResult['PREVIEW_PICTURE']))
	$arResult['PREVIEW_PICTURE'] = $arEmptyPreview;

if (!isset($arResult['OFFERS']) || empty($arResult['OFFERS']))
{
	if (!empty($arResult["MORE_PHOTO"]))
	{
		$arResult['SHOW_SLIDER'] = true;
		$arResult["MORE_PHOTO_COUNT"] = count($arResult["MORE_PHOTO"]);
	}
}

if (!empty($arResult['DISPLAY_PROPERTIES']))
{
	foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
	{
		if ('F' == $arDispProp['PROPERTY_TYPE'])
			unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
	}
}

$arResult['SKU_PROPS'] = $arSKUPropList;
?>