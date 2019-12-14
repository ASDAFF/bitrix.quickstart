<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
{
	ShowError( GetMessage('ERROR_NOT_MODULE_IBLOCK') );
	return;
}
if(!CModule::IncludeModule('catalog'))
{
	ShowError( GetMessage('ERROR_NOT_MODULE_CATALOG') );
	return;
}
if(!CModule::IncludeModule('redsign.quickbuy'))
{
	ShowError( GetMessage('ERROR_EMPTY_MODULE_QUICKBUY') );
	return;
}

global $USER, $APPLICATION;

// Check params
$arParams['IBLOCK_ID'] = (IntVal($arParams['IBLOCK_ID'])>0 ? $arParams['IBLOCK_ID'] : 0);
$arParams['QUANTITY_TRACE'] = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
$OFFERS_IBLOCK_ID = 0;
if($arParams['IBLOCK_ID']>0)
{
	$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
	$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID'] : 0;
}
$arParams['PRICE_VAT_INCLUDE'] = $arParams['PRICE_VAT_INCLUDE'] !== 'N';
$arParams['CONVERT_CURRENCY'] = (isset($arParams['CONVERT_CURRENCY']) && 'Y' == $arParams['CONVERT_CURRENCY'] ? 'Y' : 'N');
$arParams['CURRENCY_ID_FOR_FORMAT'] = $arParams['CURRENCY_ID'];
if('' == $arParams['CURRENCY_ID'])
{
	$arParams['CONVERT_CURRENCY'] = 'N';
}
elseif('N' == $arParams['CONVERT_CURRENCY'])
{
	$arParams['CURRENCY_ID'] = '';
}

$init_jquery = COption::GetOptionString('redsign.quickbuy', 'init_jquery', 'N');
if($init_jquery=='Y')
	CJSCore::Init('jquery');

if($_REQUEST['action']=='ADD2BASKETQUICKBUY' && IntVal($_REQUEST['id'])>0)
{
	if(CModule::IncludeModule('sale') && CModule::IncludeModule('catalog') && CModule::IncludeModule('iblock'))
	{
		$PRODUCT_ID = IntVal($_REQUEST['id']);
		$p = Add2BasketByProductID($PRODUCT_ID);
		LocalRedirect( $APPLICATION->GetCurPageParam('',array('id','action')) );
	}
}

if( $this->StartResultCache() )
{
	$arConvertParams = array();
	if('Y' == $arParams['CONVERT_CURRENCY'])
	{
		if(!CModule::IncludeModule('currency'))
		{
			$arParams['CONVERT_CURRENCY'] = 'N';
			$arParams['CURRENCY_ID'] = '';
		}
		else
		{
			$arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
			if(!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo)))
			{
				$arParams['CONVERT_CURRENCY'] = 'N';
				$arParams['CURRENCY_ID'] = '';
			}
			else
			{
				$arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
				$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
			}
		}
	}
	
	$arOrder = array('DATE_TO' => 'ASC');
	$time = ConvertTimeStamp(time(),'FULL');
	$arFilter = array(
		'DATE_FROM' => $time,
		'DATE_TO' => $time,
		'QUANTITY' => 0,
	);
	$arrIDs = array();
	$arrQuickBuy = array();
	$res = CRSQUICKBUYElements::GetList($arOrder, $arFilter);
	while($data = $res->Fetch())
	{
		$arrIDs[] = $data['ELEMENT_ID'];
		$arrQuickBuy[$data['ELEMENT_ID']] = $data;
	}
	
	if(is_array($arrIDs) && count($arrIDs)>0)
	{
		$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], array($arParams['PRICE_CODE']));
		
		$arOrder = array('SORT'=>'ASC');
		$arFilter = Array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'ID' => $arrIDs,
			'ACTIVE' => 'Y',
		);
		$arNavStartParams = array('nTopCount' => $arParams['COUNT_ELEMENTS']);
		$arSelect = array('*', 'PROPERTY_*', 'CATALOG_GROUP_'.$arResultPrices[$arParams['PRICE_CODE']]['ID']);
		$res2 = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
		$arItem = array();
		while($obItem = $res2->GetNextElement())
		{
			$arItem = $obItem->GetFields();
			$arItem['PROPERTIES'] = $obItem->GetProperties();
			
			$arItem['ADD_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD2BASKETQUICKBUY&id='.$arItem['ID']));
			
			$arItem['CAT_PRICES'] = $arResultPrices;
			$arItem['PRICE_MATRIX'] = CatalogGetPriceTableEx($arItem['ID'], 0, array($arResultPrices[$arParams['PRICE_CODE']]['ID']), 'Y', $arConvertParams);
			$arItem['PRICES'] = CIBlockPriceTools::GetItemPrices($arParams['IBLOCK_ID'], $arItem['CAT_PRICES'], $arItem, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
			
			$arItem['QUICKBUY'] = $arrQuickBuy[$arItem['ID']];
			$arItem['QUICKBUY']['NEW_PRICE_FORMATED'] = FormatCurrency($arItem['QUICKBUY']['NEW_PRICE'], $arParams['CURRENCY_ID_FOR_FORMAT']);
			$arItem['QUICKBUY']['DISCOUNT'] = round($arItem['PRICES'][$arParams['PRICE_CODE']]['VALUE']-$arItem['PRICES'][$arParams['PRICE_CODE']]['DISCOUNT_VALUE']);
			$arItem['QUICKBUY']['DISCOUNT_FORMATED'] = FormatCurrency($arItem['QUICKBUY']['DISCOUNT'], $arParams['CURRENCY_ID_FOR_FORMAT']);
			$arItem['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arItem['QUICKBUY']['DATE_TO']);
			
			if($arItem['PREVIEW_PICTURE']){
				$arItem['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
				if(IntVal($arParams['MAX_WIDTH']) && IntVal($arParams['MAX_HEIGHT']))
					$arItem['PREVIEW_PICTURE']['TRUE_SIZE'] = GetProfiSize($arItem['PREVIEW_PICTURE']['WIDTH'], $arItem['PREVIEW_PICTURE']['HEIGHT'], $arParams['MAX_WIDTH'], $arParams['MAX_HEIGHT']);
			}
			if($arItem['DETAIL_PICTURE']){
				$arItem['DETAIL_PICTURE'] = CFile::GetFileArray($arItem['DETAIL_PICTURE']);
				if(IntVal($arParams['MAX_WIDTH']) && IntVal($arParams['MAX_HEIGHT']))
					$arItem['DETAIL_PICTURE']['TRUE_SIZE'] = GetProfiSize($arItem['DETAIL_PICTURE']['WIDTH'], $arItem['DETAIL_PICTURE']['HEIGHT'], $arParams['MAX_WIDTH'], $arParams['MAX_HEIGHT']);
			}
			$arResult['ITEMS'][] = $arItem;
			$arResult['ELEMENTS'][] = $arItem['ID'];
		}
		
		// offers
		if(
			!empty($arResult['ELEMENTS'])
			&& (
				!empty($arParams['OFFERS_FIELD_CODE'])
				|| !empty($arParams['OFFERS_PROPERTY_CODE'])
			)
		)
		{
			$arOffers = CIBlockPriceTools::GetOffersArray(
				array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
				)
				,$arResult['ELEMENTS']
				,array(
					$arParams['OFFERS_SORT_FIELD'] => $arParams['OFFERS_SORT_ORDER'],
					$arParams['OFFERS_SORT_FIELD2'] => $arParams['OFFERS_SORT_ORDER2'],
				)
				,$arParams['OFFERS_FIELD_CODE']
				,$arParams['OFFERS_PROPERTY_CODE']
				,$arParams['OFFERS_LIMIT']
				,$arResultPrices
				,$arParams['PRICE_VAT_INCLUDE']
				,$arConvertParams
			);
			
			if(!empty($arOffers))
			{
				$arElementOffer = array();
				foreach($arResult['ELEMENTS'] as $i => $id)
				{
					$arResult['ITEMS'][$i]['OFFERS'] = array();
					$arElementOffer[$id] = &$arResult['ITEMS'][$i]['OFFERS'];
				}
				foreach($arOffers as $arOffer)
				{
					if(array_key_exists($arOffer['LINK_ELEMENT_ID'], $arElementOffer))
					{
						$arOffer['ADD_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD2BASKETQUICKBUY&id='.$arOffer['ID']));
						$arItem['IS_OFFER'] = true;
						foreach($arOffer['PRICES'] as $sPriceCode => $arPrice){
							if($arPrice['DISCOUNT_VALUE'] < $arPrice['VALUE']){
								$arOffer['PRICES'][$sPriceCode]['DISCOUNT'] = round($arPrice['VALUE'] - $arPrice['DISCOUNT_VALUE']);
								$arOffer['PRICES'][$sPriceCode]['DISCOUNT_FORMATED'] = FormatCurrency($arOffer['PRICES'][$sPriceCode]['DISCOUNT'], $arPrice['CURRENCY']);
							}
						}

						$arElementOffer[$arOffer['LINK_ELEMENT_ID']][] = $arOffer;

						if('Y' == $arParams['CONVERT_CURRENCY'])
						{
							if(!empty($arOffer['PRICES']))
							{
								foreach ($arOffer['PRICES'] as &$arOnePrices)
								{
									if(isset($arOnePrices['ORIG_CURRENCY']))
										$arCurrencyList[] = $arOnePrices['ORIG_CURRENCY'];
								}
								if(isset($arOnePrices))
									unset($arOnePrices);
							}
						}
					}
				}
			}
		}
		// /offers
		$nTopCount = $arParams['COUNT_ELEMENTS'] - count($arResult['ITEMS']);
		if($OFFERS_IBLOCK_ID && $nTopCount)
		{
			$arResultPrices = CIBlockPriceTools::GetCatalogPrices($OFFERS_IBLOCK_ID, array($arParams['PRICE_CODE']));
			
			$arOrder = array('SORT'=>'ASC');
			$arFilter = Array(
				'IBLOCK_ID' => $OFFERS_IBLOCK_ID,
				'ID' => $arrIDs,
				'ACTIVE' => 'Y',
			);
			$arNavStartParams = array('nTopCount' => $nTopCount);
			$arSelect = array('*', 'PROPERTY_*', 'CATALOG_GROUP_'.$arResultPrices[$arParams['PRICE_CODE']]['ID']);
			$res2 = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
			$arItem = array();
			while($obItem = $res2->GetNextElement())
			{
				$arItem = $obItem->GetFields();
				$arItem['PROPERTIES'] = $obItem->GetProperties();
				
				$arItem['ADD_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD2BASKETQUICKBUY&id='.$arItem['ID']));
				$arItem['IS_OFFER'] = true;
				$arItem['CAT_PRICES'] = $arResultPrices;
				$arItem['PRICE_MATRIX'] = CatalogGetPriceTableEx($arItem['ID'], 0, array($arResultPrices[$arParams['PRICE_CODE']]['ID']), 'Y', $arConvertParams);
				$arItem['PRICES'] = CIBlockPriceTools::GetItemPrices($OFFERS_IBLOCK_ID, $arItem['CAT_PRICES'], $arItem, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
				
				$arItem['QUICKBUY'] = $arrQuickBuy[$arItem['ID']];
				$arItem['QUICKBUY']['NEW_PRICE_FORMATED'] = FormatCurrency($arItem['QUICKBUY']['NEW_PRICE'], $arParams['CURRENCY_ID_FOR_FORMAT']);
				$arItem['QUICKBUY']['DISCOUNT'] = round($arItem['PRICES'][$arParams['PRICE_CODE']]['VALUE']-$arItem['PRICES'][$arParams['PRICE_CODE']]['DISCOUNT_VALUE']);
				$arItem['QUICKBUY']['DISCOUNT_FORMATED'] = FormatCurrency($arItem['QUICKBUY']['DISCOUNT'], $arParams['CURRENCY_ID_FOR_FORMAT']);
				$arItem['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arItem['QUICKBUY']['DATE_TO']);
				
				if($arItem['PREVIEW_PICTURE']){
					$arItem['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
					if(IntVal($arParams['MAX_WIDTH']) && IntVal($arParams['MAX_HEIGHT']))
						$arItem['PREVIEW_PICTURE']['TRUE_SIZE'] = GetProfiSize($arItem['PREVIEW_PICTURE']['WIDTH'], $arItem['PREVIEW_PICTURE']['HEIGHT'], $arParams['MAX_WIDTH'], $arParams['MAX_HEIGHT']);
				}
				if($arItem['DETAIL_PICTURE']){
					$arItem['DETAIL_PICTURE'] = CFile::GetFileArray($arItem['DETAIL_PICTURE']);
					if(IntVal($arParams['MAX_WIDTH']) && IntVal($arParams['MAX_HEIGHT']))
						$arItem['DETAIL_PICTURE']['TRUE_SIZE'] = GetProfiSize($arItem['DETAIL_PICTURE']['WIDTH'], $arItem['DETAIL_PICTURE']['HEIGHT'], $arParams['MAX_WIDTH'], $arParams['MAX_HEIGHT']);
				}
				$arResult['ITEMS'][] = $arItem;
			}
		}
	}
	$this->IncludeComponentTemplate();
}