<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (CModule::IncludeModule("catalog"))
{
	$arSKU = false;
	$boolSKU = false;

	// get iblock props from all catalog iblocks including sku iblocks
	$arSkuIblockIDs = array();
	$dbCatalog = CCatalog::GetList(array(), array());
	while ($arCatalog = $dbCatalog->GetNext())
	{
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCatalog['IBLOCK_ID']);

		if (!empty($arSKU) && is_array($arSKU))
		{
			$arSkuIblockIDs[] = $arSKU["IBLOCK_ID"];
			$arSkuData[$arSKU["IBLOCK_ID"]] = $arSKU;

			$boolSKU = true;
		}
	}

	// iblock props
	$arProps = array();
	foreach ($arSkuIblockIDs as $iblockID)
	{
		$dbProps = CIBlockProperty::GetList(
			array(
				"SORT"=>"ASC",
				"NAME"=>"ASC"
			),
			array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "N",
			)
		);

		while ($arProp = $dbProps->GetNext())
		{
			if ($arProp['ID'] == $arSkuData[$arSKU["IBLOCK_ID"]]["SKU_PROPERTY_ID"])
				continue;

			if ($arProp['XML_ID'] == 'CML2_LINK')
				continue;

			$strPropName = '['.$arProp['ID'].'] '.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];

			if (($arProp['PROPERTY_TYPE'] == 'L' || $arProp['PROPERTY_TYPE'] == 'E' || ($arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory')) && 'N' == $arProp['MULTIPLE'])
				$arOfferProps[$arProp['CODE']] = $strPropName;
		}

		$arTemplateParameters['OFFERS_PROPS'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('SALE_OFFER_PROPS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'SIZE' => '7',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arOfferProps
		);
	}
}

?>