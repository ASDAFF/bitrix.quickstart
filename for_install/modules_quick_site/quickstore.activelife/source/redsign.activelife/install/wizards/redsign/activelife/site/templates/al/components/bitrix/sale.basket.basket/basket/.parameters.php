<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arTemplateParameters = array(
	'USE_BUY1CLICK' => array(
		'NAME' => GetMessage('RS_SLINE.USE_BUY1CLICK'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	)
);
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arSKU = false;
	$boolSKU = false;
	$arOfferProps = array();

	// get iblock props from all catalog iblocks including sku iblocks
	$arSkuIblockIDs = array();
	$dbCatalog = CCatalog::GetList(array(), array());
	$arCatalogProperties = array();
	while ($arCatalog = $dbCatalog->GetNext())
	{
		if (0 < intval($arCatalog['IBLOCK_ID'])) {
			$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $arCatalog['IBLOCK_ID'], 'ACTIVE' => 'Y'));
			while ($arr = $rsProp->Fetch()) {
				$arCatalogProperties[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			}
		}
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCatalog['IBLOCK_ID']);

		if (!empty($arSKU) && is_array($arSKU))
		{
			$arSkuIblockIDs[] = $arSKU['IBLOCK_ID'];
			$arSkuData[$arSKU['IBLOCK_ID']] = $arSKU;

			$boolSKU = true;

			$arTemplateParameters['ADDITIONAL_PICT_PROP_'.$arCatalog['IBLOCK_ID']] = array(
				'PARENT' => 'VISUAL',
				'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP', array('#IBLOCK_ID#' => $arCatalog['IBLOCK_ID'])),
				'TYPE' => 'LIST',
				'DEFAULT' => '-',
				'VALUES' => array_merge($defaultListValues, $arCatalogProperties),
			);

			$arTemplateParameters['ARTICLE_PROP_'.$arCatalog['IBLOCK_ID']] = array(
				'PARENT' => 'VISUAL',
				'NAME' => getMessage('RS_SLINE.ITEM_ARTICLE_PROP', array('#IBLOCK_ID#' => $arCatalog['IBLOCK_ID'])),
				'TYPE' => 'LIST',
				'DEFAULT' => '-',
				'VALUES' => array_merge($defaultListValues, $arCatalogProperties),
			);
		}
	}
	// iblock props
	$arProps = array();
	foreach ($arSkuIblockIDs as $iblockID)
	{
		$dbProps = CIBlockProperty::GetList(
			array(
				'SORT'=>'ASC',
				'NAME'=>'ASC'
			),
			array(
				'IBLOCK_ID' => $iblockID,
				'ACTIVE' => 'Y',
				'CHECK_PERMISSIONS' => 'N',
			)
		);

		while ($arProp = $dbProps->GetNext())
		{
			if ($arProp['ID'] == $arSkuData[$arSKU['IBLOCK_ID']]['SKU_PROPERTY_ID'])
				continue;

			if ($arProp['XML_ID'] == 'CML2_LINK')
				continue;

			$strPropName = '['.$arProp['ID'].'] '.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];

            if ($arProp['PROPERTY_TYPE'] != 'F')
			{
				$arOfferProps[$arProp['CODE']] = $strPropName;
            }
            $arOfferAllProps[$arProp['CODE']] = $strPropName;
		}

        $arOfferPropsCurrent = $arOfferAllProps;
        
		if (!empty($arOfferProps) && is_array($arOfferProps))
		{
			$arTemplateParameters['ADDITIONAL_PICT_PROP_'.$iblockID] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => getMessage('RS_SLINE.OFFER_ADDITIONAL_PICT_PROP', array('#IBLOCK_ID#' => $iblockID)),
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arOfferPropsCurrent),
				'DEFAULT' => '-',
			);
			$arTemplateParameters['ARTICLE_PROP_'.$iblockID] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => getMessage('RS_SLINE.ITEM_ARTICLE_PROP', array('#IBLOCK_ID#' => $iblockID)),
				'TYPE' => 'LIST',
				'DEFAULT' => '-',
				'VALUES' => array_merge($defaultListValues, $arOfferPropsCurrent),
			);
			//$arTemplateParameters['OFFER_TREE_PROPS_'.$iblockID] = array(
			$arTemplateParameters['OFFERS_PROPS'] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => getMessage('SALE_PROPERTIES_RECALCULATE_BASKET'),
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arOfferProps),
				'MULTIPLE' => 'Y',
				'DEFAULT' => '-',
			);
			$arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => getMessage('RS_SLINE.OFFER_TREE_COLOR_PROPS'),
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arOfferProps),
				'MULTIPLE' => 'Y',
				'DEFAULT' => '-',
			);
			$arTemplateParameters['OFFER_TREE_BTN_PROPS'] = array(
				'PARENT' => 'OFFERS_PROPS',
				'NAME' => getMessage('RS_SLINE.OFFER_TREE_BTN_PROPS'),
				'TYPE' => 'LIST',
				'VALUES' => array_merge($defaultListValues, $arOfferProps),
				'MULTIPLE' => 'Y',
				'DEFAULT' => '-',
			);
		}
	}
}

?>