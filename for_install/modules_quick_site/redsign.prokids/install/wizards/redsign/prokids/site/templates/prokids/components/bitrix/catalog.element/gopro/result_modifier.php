<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;

if(!empty($arResult))
{
	if(is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0)
	{
		// Get sorted properties
		$arResult['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties($arResult['OFFERS'],$arParams['PROPS_ATTRIBUTES']);
		// /Get sorted properties
	}
	
	// compare URL fix
	$arResult['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam('action=ADD_TO_COMPARE_LIST&id='.$arItem['ID'], array('action', 'id', 'ajaxpages', 'ajaxpagesid')));
	// /compare URL fix
	
	// get other data
	$params = array(
		'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
		'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
		'MAX_WIDTH' => 90,
		'MAX_HEIGHT' => 90,
		'PAGE' => 'detail',
	);
	$arItems = array(0 => &$arResult);
	RSDevFunc::GetDataForProductItem($arItems,$params);
	// /get other data
	
	// get no photo
	$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIGHT' => 210, 'MAX_HEIGHT' => 140));
	// /get no photo
	
	// quantity for bitrix:catalog.store.amount
	$arQuantity[$arResult['ID']] = $arResult['CATALOG_QUANTITY'];
	foreach($arResult['OFFERS'] as $key => $arOffer)
	{
		$arQuantity[$arOffer['ID']] = $arOffer['CATALOG_QUANTITY'];
	}
	$arResult['DATA_QUANTITY'] = $arQuantity;
	
	// get SKU_IBLOCK_ID
	$arResult['OFFERS_IBLOCK'] = 0;
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
	if( !empty($arSKU) && is_array($arSKU) )
	{
		$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
	}
	
	// QB and DA2
	$arResult['HAVE_DA2'] = 'N';
	$arResult['HAVE_QB'] = 'N';
	if(is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0)
	{
		foreach($arResult['OFFERS'] as $arOffer)
		{
			if( isset($arOffer['DAYSARTICLE2']) )
			{
				$arResult['HAVE_DA2'] = 'Y';
			}
			if( isset($arOffer['QUICKBUY']) )
			{
				$arResult['HAVE_QB'] = 'Y';
			}
			
		}
	}
	if( isset($arResult['DAYSARTICLE2']) )
	{
		$arResult['HAVE_DA2'] = 'Y';
	}
	if( isset($arResult['QUICKBUY']) )
	{
		$arResult['HAVE_QB'] = 'Y';
	}
	// /QB and DA2
}

// tabs
$arResult['TABS'] = array(
	'DETAIL_TEXT' => false,				// description
	'DISPLAY_PROPERTIES' => false,		// grouped props
	'SET' => false,						// set
	'PROPS_TABS' => false,				// tabs from properties
);
if($arResult['HAVE_SET'])
{
	$arResult['TABS']['SET'] = true;
}
if(is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0)
{
	foreach($arResult['OFFERS'] as $arOffer)
	{
		if($arOffer['HAVE_SET'])
		{
			$arResult['TABS']['SET'] = true;
			break;
		}
	}
}
if($arResult['DETAIL_TEXT']!='')
{
	$arResult['TABS']['DETAIL_TEXT'] = true;
}
if(is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES'])>0)
{
	$arResult['TABS']['DISPLAY_PROPERTIES'] = true;
}
if(is_array($arParams['PROPS_TABS']) && count($arParams['PROPS_TABS'])>0)
{
	foreach($arParams['PROPS_TABS'] as $sPropCode)
	{
		if(	$sPropCode!='' &&
			(
				(isset($arResult['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE'])) ||
				($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='F' && isset($arResult['PROPERTIES'][$sPropCode]['VALUE'])) ||
				($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='E' && isset($arResult['PROPERTIES'][$sPropCode]['VALUE']))
			)
		)
		{
			$arResult['TABS']['PROPS_TABS'] = true;
			if( $arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='F' )
			{
				if( is_array($arResult['PROPERTIES'][$sPropCode]['VALUE']) )
				{
					foreach($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $keyF => $fileID)
					{
						$rsFile = CFile::GetByID($fileID);
						if($arFile = $rsFile->Fetch())
						{
							$arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF] = $arFile;
							$arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
							$tmp = explode('.', $arFile['FILE_NAME']);
							$tmp = end($tmp);
							$type = 'other';
							$type2 = '';
							switch($tmp){
								case 'docx':
									$type = 'word';
									break;
								case 'doc':
									$type = 'word';
									break;
								case 'pdf':
									$type = 'pdf';
									break;
								case 'xls':
									$type = 'excel';
									break;
								case 'xlsx':
									$type = 'excel';
									break;
							}
							$arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['TYPE'] = $type;
							$arResult['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
						}
					}
				} else {
					$fileID = $arResult['PROPERTIES'][$sPropCode]['VALUE'];
					$rsFile = CFile::GetByID($fileID);
					if($arFile = $rsFile->Fetch())
					{
						$arResult['PROPERTIES'][$sPropCode]['VALUE'] = array();
						$arResult['PROPERTIES'][$sPropCode]['VALUE'][0] = $arFile;
						$arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
						$tmp = explode('.', $arFile['FILE_NAME']);
						$tmp = end($tmp);
						$type = 'other';
						$type2 = '';
						switch($tmp){
							case 'doc':
							case 'docx':
								$type = 'doc';
								break;
							case 'xls':
							case 'xlsx':
								$type = 'excel';
								break;
							case 'pdf':
								$type = 'pdf';
								break;
						}
						$arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['TYPE'] = $type;
						$arResult['PROPERTIES'][$sPropCode]['VALUE'][0]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
					}
				}
			}
		}
	}
}