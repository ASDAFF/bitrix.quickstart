<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

if(\Bitrix\Main\Loader::includeModule('iblock')
	&& \Bitrix\Main\Loader::includeModule('catalog'))
{

	$iblockMap = array();
	$iblockIterator = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
	while ($iblock = $iblockIterator->fetch())
	{
		$iblockMap[$iblock['ID']] = $iblock;
	}
	$catalogs = array();
	$catalogIterator = CCatalog::GetList(
			array('IBLOCK_ID' => 'ASC'),
			array('@IBLOCK_ID' => array_keys($iblockMap)),
			false,
			false,
			array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID')
	);
	while($catalog = $catalogIterator->fetch())
	{
		if((int)$catalog['PRODUCT_IBLOCK_ID'] > 0)
		{
			$catalogs[] = $catalog;
		}
		else
		{
			$catalogs[] = $catalog;
		}
	}
	foreach($catalogs as $catalog)
	{
		$arProperty = array();
		if(0 < intval($catalog['IBLOCK_ID']))
		{
			$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $catalog['IBLOCK_ID'], 'ACTIVE' => 'Y'));
			while($arr=$rsProp->Fetch())
			{
				$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			}
		}

		$arTemplateParameters['ARTICLE_PROP_'.$catalog['IBLOCK_ID']] = array(
			'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
			'NAME' => getMessage('RS_SLINE.ITEM_ARTICLE_PROP').'('.getMessage('RS_SLINE.IBLOCK').' '.$catalog['IBLOCK_ID'].')',
			'TYPE' => 'LIST',
			'VALUES' => array_merge($defaultListValues, $arProperty),
			'DEFAULT' => '-',
		);
		$arTemplateParameters['ADDITIONAL_PICT_PROP_'.$catalog['IBLOCK_ID']] = array(
			'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
			'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP').'('.getMessage('RS_SLINE.IBLOCK').' '.$catalog['IBLOCK_ID'].')',
			'TYPE' => 'LIST',
			'VALUES' => array_merge($defaultListValues, $arProperty),
			'DEFAULT' => '-',
		);
		
		if(0 < (int)$catalog['SKU_PROPERTY_ID'])
		{
			$arTemplateParameters['ARTICLE_PROP_'.$catalog['IBLOCK_ID']] = array(
				'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
				'NAME' => getMessage('RS_SLINE.ITEM_ARTICLE_PROP').'('.getMessage('RS_SLINE.IBLOCK').' '.$catalog['IBLOCK_ID'].')',
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arProperty),
				'DEFAULT' => '-',
			);
			$arTemplateParameters['OFFER_TREE_PROPS_'.$catalog['IBLOCK_ID']] = array(
				'PARENT' => 'CATALOG_PPARAMS_'.$catalog['IBLOCK_ID'],
				'NAME' => getMessage('RS_SLINE.OFFER_TREE_PROPS').'('.getMessage('RS_SLINE.IBLOCK').' '.$catalog['IBLOCK_ID'].')',
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arProperty),
				'MULTIPLE' => 'Y',
				'DEFAULT' => '-',
			);
		}
	}
}

if (\Bitrix\Main\Loader::includeModule('sale'))
{
	$dbStat = CSaleStatus::GetList(array('sort' => 'asc'), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
	$statList = array();
	while ($item = $dbStat->Fetch())
		$statList[$item['ID']] = $item['NAME'];

	$statList['PSEUDO_CANCELLED'] = 1;	

	$availColors = array(
		'green' => GetMessage('SPO_STATUS_COLOR_GREEN'),
		'yellow' => GetMessage('SPO_STATUS_COLOR_YELLOW'),
		'red' => GetMessage('SPO_STATUS_COLOR_RED'),
		'gray' => GetMessage('SPO_STATUS_COLOR_GRAY'),
	);

	$colorDefaults = array(
		'N' => 'green', // new
		'P' => 'yellow', // payed
		'F' => 'gray', // finished
		'PSEUDO_CANCELLED' => 'red' // cancelled
	);

	foreach ($statList as $id => $name)
		$arTemplateParameters['STATUS_COLOR_'.$id] = array(
			'NAME' => $id == 'PSEUDO_CANCELLED' ? GetMessage('SPO_PSEUDO_CANCELLED_COLOR') : GetMessage('SPO_STATUS_COLOR').' "'.$name.'"',
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'VALUES' => $availColors,
			'DEFAULT' => empty($colorDefaults[$id]) ? 'gray' : $colorDefaults[$id],
		);
}