<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arParams['NOVELTY_TIME'] = intval($arParams['NOVELTY_TIME']);
if ($arParams['NOVELTY_TIME'] < 0) {
	$arParams['NOVELTY_TIME'] = 0;
}

if (1 > strlen($arParams['TEMPLATE_AJAXID']))
{
	$arParams['TEMPLATE_AJAXID'] = 'compare';
}

if ('' != $arParams['ICON_NOVELTY_PROP'] && '-' != $arParams['ICON_NOVELTY_PROP']) {
	$arParams['ICON_NOVELTY_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP']);
}
if ('' != $arParams['ICON_DEALS_PROP'] && '-' != $arParams['ICON_DEALS_PROP']) {
	$arParams['ICON_DEALS_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP']);
}
if ('' != $arParams['ICON_DISCOUNT_PROP'] && '-' != $arParams['ICON_DISCOUNT_PROP']) {
	$arParams['ICON_DISCOUNT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP']);
}
if ('' != $arParams['ICON_HITS_PROP'] && '-' != $arParams['ICON_HITS_PROP']) {
	$arParams['ICON_HITS_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ICON_HITS_PROP']);
}
if ('' != $arParams['ADDITIONAL_PICT_PROP'] && '-' != $arParams['ADDITIONAL_PICT_PROP']) {
	$arParams['ADDITIONAL_PICT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP']);
}
if ($arResult['OFFERS_IBLOCK_ID']) {
	if ('' != $arParams['OFFER_ADDITIONAL_PICT_PROP'] && '-' != $arParams['OFFER_ADDITIONAL_PICT_PROP']) {
		$arParams['ADDITIONAL_PICT_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
	}
	if (is_array($arParams['OFFER_TREE_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_PROPS'];
		unset($arParams['OFFER_TREE_PROPS']);
		$arParams['OFFER_TREE_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
	}
	if (is_array($arParams['OFFER_TREE_COLOR_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_COLOR_PROPS'];
		unset($arParams['OFFER_TREE_COLOR_PROPS']);
		$arParams['OFFER_TREE_COLOR_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
	}
	if (is_array($arParams['OFFER_TREE_BTN_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_BTN_PROPS'];
		unset($arParams['OFFER_TREE_BTN_PROPS']);
		$arParams['OFFER_TREE_BTN_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
	}
}

// new catalog components fix
if (!isset($arResult['PRICES'])) {
    $arResult['PRICES'] = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
}

$arProperties = array();
if (\Bitrix\Main\Loader::includeModule('redsign.grupper'))
{
	$arGroups = array();
	$rsGroups = CRSGGroups::GetList(array('SORT' => 'ASC','ID' => 'ASC'), array());
	while ($arGroup = $rsGroups->Fetch())
	{
		$arGroups[$arGroup['ID']] = $arGroup;
		$arGroups[$arGroup['ID']]['IS_SHOW'] = false;
	}
	if (!empty($arGroups))
	{
		$rsBinds = CRSGBinds::GetList(array('ID' => 'ASC'));
		while ($arBind = $rsBinds->Fetch())
		{
			$arGroups[$arBind['GROUP_ID']]['BINDS'][$arBind['IBLOCK_PROPERTY_ID']] = $arBind['IBLOCK_PROPERTY_ID'];
			$arProperties[$arBind['IBLOCK_PROPERTY_ID']] = $arBind['GROUP_ID'];
		}
		$arResult['PROPERTIES_GROUPS'] = $arGroups;
	}
}

if (\Bitrix\Main\Loader::includeModule('redsign.devfunc'))
{
	$params = array(
		'PREVIEW_PICTURE' => true,
		'DETAIL_PICTURE' => true,
		'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
		'RESIZE' => array(
			0 => array(
                'MAX_WIDTH' => 207,
                'MAX_HEIGHT' => 160,
			)
		)
	);
	if (is_array($arResult['ITEMS']))
	{
		RSDevFunc::GetDataForProductItem($arResult['ITEMS']);
		foreach ($arResult['ITEMS'] as $iItemKey => $arItem)
		{
			if (isset($arItem['OFFER_FIELDS']['PREVIEW_PICTURE']) && 0 < intval($arItem['OFFER_FIELDS']['PREVIEW_PICTURE']))
			{
				$arResult['ITEMS'][$iItemKey]['PREVIEW_PICTURE'] = $arItem['OFFER_FIELDS']['PREVIEW_PICTURE'];
			}
			else if (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && 0 < intval($arItem['FIELDS']['PREVIEW_PICTURE']))
			{
				$arResult['ITEMS'][$iItemKey]['PREVIEW_PICTURE'] = $arItem['OFFER_FIELDS']['PREVIEW_PICTURE'];
			}
			
			
			if (isset($arItem['FIELDS']['DETAIL_PICTURE']) && 0 < intval($arItem['FIELDS']['DETAIL_PICTURE']))
			{
				$arResult['ITEMS'][$iItemKey]['DETAIL_PICTURE'] = $arItem['FIELDS']['DETAIL_PICTURE'];
			}
			else if (isset($arItem['FIELDS']['DETAIL_PICTURE']) && 0 < intval($arItem['FIELDS']['DETAIL_PICTURE']))
			{
				$arResult['ITEMS'][$iItemKey]['DETAIL_PICTURE'] = $arItem['FIELDS']['DETAIL_PICTURE'];
			}
			
			$arResult['ITEMS'][$iItemKey]['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult['ITEMS'][$iItemKey], $params, 1);
		}
	}
}

use Bitrix\Main\Type\Collection;

$arResult['ALL_FIELDS'] = array();
$existShow = !empty($arResult['SHOW_FIELDS']);
$existDelete = !empty($arResult['DELETED_FIELDS']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_FIELDS'] as $propCode)
		{
			$arResult['SHOW_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'N',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode);
		$arResult['ALL_FIELDS'] = $arResult['SHOW_FIELDS'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_FIELDS'] as $propCode)
		{
			$arResult['ALL_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'Y',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode, $arResult['DELETED_FIELDS']);
	}
	Collection::sortByColumn($arResult['ALL_FIELDS'], array('SORT' => SORT_ASC));
}

$arResult['ALL_PROPERTIES'] = array();
$existShow = !empty($arResult['SHOW_PROPERTIES']);
$existDelete = !empty($arResult['DELETED_PROPERTIES']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['SHOW_PROPERTIES'][$propCode]['IS_DELETED'] = 'N';
			$arResult['SHOW_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_PROPERTY_TEMPLATE']);

			$arResult['SHOW_PROPERTIES'][$propCode]['IS_SHOW'] = true;
			if ($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult['ITEMS'] as &$arElement)
				{
					$arPropertyValue = $arElement['DISPLAY_PROPERTIES'][$propCode]['VALUE'];
					if (is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(' / ', $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$arResult['SHOW_PROPERTIES'][$propCode]['IS_SHOW'] = (count(array_unique($arCompare)) > 1);
			}
			$groupCode = isset($arProperties[$arProp['ID']]) ? $arProperties[$arProp['ID']] : 'NOT_GRUPED_PROPS';

			if ($arResult['SHOW_PROPERTIES'][$propCode]['IS_SHOW'])
			{
				$arResult['PROPERTIES_GROUPS'][$groupCode]['IS_SHOW'] = true;
			}
			$arResult['PROPERTIES_GROUPS'][$groupCode]['BINDS'][$arProp['ID']] = $propCode;
		}
		
		$arResult['ALL_PROPERTIES'] = $arResult['SHOW_PROPERTIES'];
	}
	unset($arProp, $propCode);
	if ($existDelete)
	{
		foreach ($arResult['DELETED_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['DELETED_PROPERTIES'][$propCode]['IS_DELETED'] = 'Y';
			$arResult['DELETED_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_PROPERTY_TEMPLATE']);
			$arResult['ALL_PROPERTIES'][$propCode] = $arResult['DELETED_PROPERTIES'][$propCode];
		}
		unset($arProp, $propCode, $arResult['DELETED_PROPERTIES']);
	}
	Collection::sortByColumn($arResult['ALL_PROPERTIES'], array('SORT' => SORT_ASC, 'ID' => SORT_ASC));
}

$arResult['ALL_OFFER_FIELDS'] = array();
$existShow = !empty($arResult['SHOW_OFFER_FIELDS']);
$existDelete = !empty($arResult['DELETED_OFFER_FIELDS']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_OFFER_FIELDS'] as $propCode)
		{
			$arResult['SHOW_OFFER_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'N',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode);
		$arResult['ALL_OFFER_FIELDS'] = $arResult['SHOW_OFFER_FIELDS'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_OFFER_FIELDS'] as $propCode)
		{
			$arResult['ALL_OFFER_FIELDS'][$propCode] = array(
				'CODE' => $propCode,
				'IS_DELETED' => 'Y',
				'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_FIELD_TEMPLATE']),
				'SORT' => $arResult['FIELDS_SORT'][$propCode]
			);
		}
		unset($propCode, $arResult['DELETED_OFFER_FIELDS']);
	}
	Collection::sortByColumn($arResult['ALL_OFFER_FIELDS'], array('SORT' => SORT_ASC));
}

$arResult['ALL_OFFER_PROPERTIES'] = array();
$existShow = !empty($arResult['SHOW_OFFER_PROPERTIES']);
$existDelete = !empty($arResult['DELETED_OFFER_PROPERTIES']);
if ($existShow || $existDelete)
{
	if ($existShow)
	{
		foreach ($arResult['SHOW_OFFER_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['SHOW_OFFER_PROPERTIES'][$propCode]['IS_DELETED'] = 'N';
			$arResult['SHOW_OFFER_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_PROPERTY_TEMPLATE']);
			
			$arResult['SHOW_OFFER_PROPERTIES'][$propCode]['IS_SHOW'] = true;
			if ($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult['ITEMS'] as &$arElement)
				{
					$arPropertyValue = $arElement['OFFER_DISPLAY_PROPERTIES'][$propCode]['VALUE'];
					if(is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(' / ', $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$arResult['SHOW_OFFER_PROPERTIES'][$propCode]['IS_SHOW'] = (count(array_unique($arCompare)) > 1);
			}
			
			$groupCode = isset($arProperties[$arProp['ID']]) ? $arProperties[$arProp['ID']] : 'NOT_GRUPED_PROPS';
			if ($arResult['SHOW_OFFER_PROPERTIES'][$propCode]['IS_SHOW'])
			{
				$arResult['PROPERTIES_GROUPS'][$groupCode]['IS_SHOW'] = true;
			}
			$arResult['PROPERTIES_GROUPS'][$groupCode]['BINDS'][$arProp['ID']] = $propCode;
		}
		unset($arProp, $propCode);
		$arResult['ALL_OFFER_PROPERTIES'] = $arResult['SHOW_OFFER_PROPERTIES'];
	}
	if ($existDelete)
	{
		foreach ($arResult['DELETED_OFFER_PROPERTIES'] as $propCode => $arProp)
		{
			$arResult['DELETED_OFFER_PROPERTIES'][$propCode]['IS_DELETED'] = 'Y';
			$arResult['DELETED_OFFER_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_PROPERTY_TEMPLATE']);
			$arResult['ALL_OFFER_PROPERTIES'][$propCode] = $arResult['DELETED_OFFER_PROPERTIES'][$propCode];
		}
		unset($arProp, $propCode, $arResult['DELETED_OFFER_PROPERTIES']);
	}
	Collection::sortByColumn($arResult['ALL_OFFER_PROPERTIES'], array('SORT' => SORT_ASC, 'ID' => SORT_ASC));
}
