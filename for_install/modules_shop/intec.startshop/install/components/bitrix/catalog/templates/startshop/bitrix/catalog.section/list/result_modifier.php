<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
	global $APPLICATION;

	$arDefaultParams = array(
			"USE_COMMON_CURRENCY" => "N",
			"CURRENCY" => ""
	);

	$arParams = array_merge($arDefaultParams, $arParams);

	if (!isset($arParams['LINE_ELEMENT_COUNT']))
		$arParams['LINE_ELEMENT_COUNT'] = 3;

	$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);

	if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
		$arParams['LINE_ELEMENT_COUNT'] = 3;
	
	foreach ($arResult['ITEMS'] as $iKey => $arItem)
    {
        $sComparePath = parse_url($arResult['ITEMS'][$iKey]['COMPARE_URL']);
        $sComparePath = $sComparePath['path'];
        $arResult['ITEMS'][$iKey]['COMPARE_REMOVE_URL'] = $sComparePath.'?'.$arParams['ACTION_VARIABLE'].'=DELETE_FROM_COMPARE_LIST&'.$arParams['PRODUCT_ID_VARIABLE'].'='.$arItem['ID'];
    }
	
	if (!CModule::IncludeModule('intec.startshop'))
		return;

	CStartShopTheme::ApplyTheme(SITE_ID);

	$arItemsIDs = array();
	foreach ($arResult['ITEMS'] as $arItem)
		$arItemsIDs[] = $arItem['ID'];

	if (!empty($arItemsIDs))
	{
		$arProducts = array();
		$dbProducts = CStartShopCatalogProduct::GetList(
				array(),
				array('ID' => $arItemsIDs),
				array(),
				array(),
				($arParams['USE_COMMON_CURRENCY'] == "Y" && !empty($arParams['CURRENCY']) ? $arParams['CURRENCY'] : false),
				$arParams['PRICE_CODE']
		);

		while ($arProduct = $dbProducts->GetNext())
			$arProducts[$arProduct['ID']] = $arProduct;

		foreach ($arResult['ITEMS'] as $sKey => $arItem)
			if (!empty($arProducts[$arItem['ID']]))
				$arResult['ITEMS'][$sKey]['STARTSHOP'] = $arProducts[$arItem['ID']]['STARTSHOP'];
	}
?>
