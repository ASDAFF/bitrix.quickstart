<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule('redsign.digital') && CModule::IncludeModule('redsign.devfunc')){

	$arData = array(
		'PROPCODE_COLOR' => $arParams['PROPCODE_COLOR'],
		'PROPCODE_IMAGES' => $arParams['PROPCODE_IMAGES'],
		'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],
		'PRICE_CODE' => $arParams['PRICE_CODE'],
		'MAX_WIDTH' => $arParams['MAX_WIDTH'],
		'MAX_HEIGHT' => $arParams['MAX_HEIGHT'],
		'COLORS' => $rgb_colors
	);
	RSDevFuncOffersExtension::GetDataForProductItem($arResult['ITEMS'], $arData);
	if(!empty($arResult['OFFERS']))
	{
		// Get sorted properties
		$arResult['OFFERS_EXT'] = RSDevFuncOffersExtension::GetSortedProperties($arResult['OFFERS'],$arParams['OFFERS_PROPERTY_CODE']);
		// /Get sorted properties

		foreach($arResult['OFFERS'] as $keyss2 => $arOffer)
		{
			// Get all pictures [offers]
			$arAllPicParams = array('MORE_PHOTO_CODE'=>$arParams['PROPCODE_IMAGES'],'PAGE'=>'element');
			$arResult['OFFERS'][$keyss2]['IMAGES'] = RSDevFuncOffersExtension::GetAllPictures(array(),$arOffer,$arAllPicParams);
			$arResult['OFFERS'][$keyss2]['IMAGES_SMALL'] = RSDevFuncOffersExtension::GetAllPictures($arSizes,$arOffer,$arAllPicParams);
			// /Get all pictures [offers]
		}
	}
}

foreach($arResult['ITEMS'] as $iItemKey => $arItem){
	foreach($arItem['OFFERS'] as $iOfferKey => $arOffer){
		if(empty($arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'])) {
			continue;
		}
		foreach($arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'] as $iImageKey => $arImage){
			$arResult['ITEMS'][$iItemKey]['OFFERS'][$iOfferKey]['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][$iImageKey] = array(
				'RESIZE' => CFile::ResizeImageGet($arImage, array('width'=>$arParams['MAX_WIDTH'], 'height'=>$arParams['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true)
			);
		}
	}

	if (is_array($arItem['PREVIEW_PICTURE'])) {
		$arResult['ITEMS'][$iItemKey]['PREVIEW_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>$arParams['MAX_WIDTH'], 'height'=>$arParams['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	} elseif(is_array($arItem['DETAIL_PICTURE'])) {
		$arResult['ITEMS'][$iItemKey]['DETAIL_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width'=>$arParams['MAX_WIDTH'], 'height'=>$arParams['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	}
	elseif(!empty($arItem['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'])){
		foreach($arItem['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'] as $iFileKey => $iFileId){
			$arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][$iFileKey] = CFile::GetFileArray($iFileId);
			$arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][$iFileKey]['RESIZE'] = CFile::ResizeImageGet($arResult['ITEMS'][$iItemKey]['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][$iFileKey], array('width'=>$arParams['MAX_WIDTH'], 'height'=>$arParams['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		}
	}
}

$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$arParams['MAX_WIDTH'],'MAX_HEIGHT'=>$arParams['MAX_HEIGHT']));
