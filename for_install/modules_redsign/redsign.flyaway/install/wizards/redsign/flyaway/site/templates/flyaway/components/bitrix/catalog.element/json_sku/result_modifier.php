<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$arResult['OFFERS_IBLOCK_ID'] = 0;
$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
if (!empty($arSKU) && is_array($arSKU))
{
	$arResult['OFFERS_IBLOCK_ID'] = $arSKU['IBLOCK_ID'];
}

if ('' != $arParams['ADDITIONAL_PICT_PROP'] && '-' != $arParams['ADDITIONAL_PICT_PROP']) {
	$arParams['ADDITIONAL_PICT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP']);
}
else {
	$arParams['ADDITIONAL_PICT_PROP'] = array();
}
if ('' != $arParams['ARTICLE_PROP'] && '-' != $arParams['ARTICLE_PROP']) {
	$arParams['ARTICLE_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ARTICLE_PROP']);
}
else {
	$arParams['ARTICLE_PROP'] = array();
}

if ($arResult['OFFERS_IBLOCK_ID'])
{
	if ('' != $arParams['OFFER_ADDITIONAL_PICT_PROP'] && '-' != $arParams['OFFER_ADDITIONAL_PICT_PROP']) {
		$arParams['ADDITIONAL_PICT_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
	}
	if ('' != $arParams['OFFER_ARTICLE_PROP'] && '-' != $arParams['OFFER_ARTICLE_PROP']) {
		$arParams['ARTICLE_PROP'][$arResult['OFFERS_IBLOCK_ID']] = $arParams['OFFER_ARTICLE_PROP'];
	}
	if (is_array($arParams['OFFER_TREE_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_PROPS'];
		unset($arParams['OFFER_TREE_PROPS']);
		$arParams['OFFER_TREE_PROPS'] = array($arResult['OFFERS_IBLOCK_ID'] => $arProps);
	}
}

if (Bitrix\Main\Loader::includeModule('redsign.devfunc'))
{
	$params = array(
		'RESIZE' => array(
			48 => array(
				'MAX_WIDTH' => 48,
				'MAX_HEIGHT' => 48,
			),
			200 => array(
				'MAX_WIDTH' => 220,
				'MAX_HEIGHT' => 200,
			),
			500 => array(
				'MAX_WIDTH' => 550,
				'MAX_HEIGHT' => 500,
			),
		),
		'PREVIEW_PICTURE' => true,
		'DETAIL_PICTURE' => true,
		'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP']
	);
	RSDevFunc::getElementPictures($arResult, $params);
}
$arElementsIDs = array($arResult['ID']);
$arElement = array();
$arNewOffers = array();
$arSortProps = array();
if (is_array($arResult['OFFERS']) && 0 < count($arResult['OFFERS']))
{
	foreach ($arResult['OFFERS'] as $arOffer)
	{
		$arElementsIDs[] = $arOffer['ID'];
		$arrNewOffer = array(
			'ID' => $arOffer['ID'],
			'NAME' => $arOffer['NAME'],
			'PROPERTIES' => '',
			'PRICES' => '',
			'CAN_BUY' => $arOffer['CAN_BUY'],
			'ADD_URL' => $arOffer['ADD_URL'],
			'CATALOG_MEASURE_RATIO' => $arOffer['CATALOG_MEASURE_RATIO'],
			'CATALOG_MEASURE_NAME' => $arOffer['CATALOG_MEASURE_NAME'],
			'CATALOG_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
		);

		// images
		if ($arOffer['PRODUCT_PHOTO'])
		{
			foreach ($arOffer['PRODUCT_PHOTO'] as $sImageKey => $arImage)
			{
				$arPhotos = array(0 => $arImage['SRC']);
				if ($arImage['RESIZE'])
				{
					foreach ($arImage['RESIZE'] as $sSize => $arPhoto)
					{
						$arPhotos[$sSize] = $arPhoto['src'];
					}
				}
				$arrNewOffer['IMAGES'][$sImageKey] = $arPhotos;
			}
		}

		// article
		if ('' != $arOffer['PROPERTIES'][$arParams['ARTICLE_PROP'][$arOffer['IBLOCK_ID']]]['VALUE'])
		{
			$arrNewOffer['ARTICLE'] = $arOffer['PROPERTIES'][$arParams['ARTICLE_PROP'][$arOffer['IBLOCK_ID']]]['VALUE'];
		}
		
		// properties
		foreach ($arParams['OFFER_TREE_PROPS'][$arResult['OFFERS_IBLOCK_ID']] as $propCode)
		{
			if ('' != $propCode && '' != $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'])
			{
				if (!in_array($propCode, $arSortProps))
				{
					$arSortProps[] = $propCode;
				}
				$arrNewOffer['PROPERTIES'][$propCode] = $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'];
			}
		}
		
		$iPropsCount = 0;
		foreach ($arOffer['DISPLAY_PROPERTIES'] as $arProp)
		{
			$arrNewOffer['DISPLAY_PROPERTIES'][$arProp['ID']] = array(
				'NAME' => $arProp['NAME'],
				'DISPLAY_VALUE' => is_array($arProp['DISPLAY_VALUE']) ? implode(' / ', $arProp['DISPLAY_VALUE']) : $arProp['DISPLAY_VALUE']
				
			);
		}

		// prices
		foreach ($arParams['PRICE_CODE'] as $priceCode)
		{
			if (isset($arOffer['PRICES'][$priceCode]))
			{
				$arrNewOffer['PRICES'][$priceCode] = array(
					'PRICE_ID' => $arOffer['PRICES'][$priceCode]['PRICE_ID'],
					'VALUE' => $arOffer['PRICES'][$priceCode]['VALUE'],
					'PRINT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_VALUE'],
					'DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['DISCOUNT_VALUE'],
					'PRINT_DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_VALUE'],
					'DISCOUNT_DIFF' => $arOffer['PRICES'][$priceCode]['DISCOUNT_DIFF'],
					'PRINT_DISCOUNT' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_DIFF'],
				);
			}
			// min price
			if (isset($arOffer['MIN_PRICE']))
			{
				$arrNewOffer['MIN_PRICE'] = $arOffer['MIN_PRICE'];
			}
		}
		$arNewOffers[$arOffer['ID']] = $arrNewOffer;
	}

	$iTime = ConvertTimeStamp(time(),'FULL');
	// add quickbuy
	if (Bitrix\Main\Loader::includeModule('redsign.quickbuy'))
	{
		$arFilter = array(
			'DATE_FROM' => $iTime,
			'DATE_TO' => $iTime,
			'QUANTITY' => 0,
			'ELEMENT_ID' => $arElementsIDs,
		);
		$dbRes = CRSQUICKBUYElements::GetList(array('ID'=>'SORT'), $arFilter);
		while ($arData = $dbRes->Fetch())
		{
			if ($arData['ELEMENT_ID'] == $arResult['ID'])
			{
				$arElement['QUICKBUY'] = $arData;
				$arElement['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
			}
			elseif (isset($arNewOffers[$arData['ELEMENT_ID']]))
			{
				$arNewOffers[$arData['ELEMENT_ID']]['QUICKBUY'] = $arData;
				$arNewOffers[$arData['ELEMENT_ID']]['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
			}
		}
	}

	// add da2
	if (Bitrix\Main\Loader::includeModule('redsign.daysarticle2'))
	{
		$arFilter = array(
			'DATE_FROM' => $iTime,
			'DATE_TO' => $iTime,
			'QUANTITY' => 0,
			'ELEMENT_ID' => $arElementsIDs,
		);
		$dbRes = CRSDA2Elements::GetList(array('ID'=>'SORT'), $arFilter);
		while ($arData = $dbRes->Fetch())
		{
			if ($arData['ELEMENT_ID'] == $arResult['ID'])
			{
				$arElement['DAYSARTICLE2'] = $arData;
				$arElement['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
			}
			elseif (isset($arNewOffers[$arData['ELEMENT_ID']]))
			{
				$arNewOffers[$arData['ELEMENT_ID']]['DAYSARTICLE2'] = $arData;
				$arNewOffers[$arData['ELEMENT_ID']]['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
			}
		}
	}
}
$arResult['JSON_EXT'] = array(
	'PARAMS' => array(
		'USE_STORE' => ('N' != $arParams['USE_STORE'] ? true : false),
		'USE_MIN_AMOUNT' => ('N' != $arParams['USE_MIN_AMOUNT'] ? true : false),
		'MIN_AMOUNT' => intval($arParams['MIN_AMOUNT'])
	),
	'ELEMENT' => $arElement,
	'SORT_PROPS' => $arSortProps,
	'OFFERS' => $arNewOffers,
);