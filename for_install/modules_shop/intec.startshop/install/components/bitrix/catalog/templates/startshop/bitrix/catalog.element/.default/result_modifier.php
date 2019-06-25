<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	use Bitrix\Iblock;

	$arEmptyPreview = null;
	$arDefaultParams = array(

	);

	$arParams = array_merge($arDefaultParams, $arParams);

    CStartShopTheme::ApplyTheme(SITE_ID);

    if (!empty($arResult['ID'])) {
        $arProduct = CStartShopCatalogProduct::GetByID(
            $arResult['ID'],
            array(),
            array(),
            ($arParams['USE_COMMON_CURRENCY'] == "Y" && !empty($arParams['CURRENCY']) ? $arParams['CURRENCY'] : false),
            $arParams['PRICE_CODE']
        )->Fetch();

        if (!empty($arProduct)) {
            $arResult['STARTSHOP'] = $arProduct['STARTSHOP'];
        }
    }

    $arResult['PICTURE'] = CStartShopToolsIBlock::GetItemPicture($arResult, 450, 450);
	$arResult['MORE_PHOTO'] = array();

    $arMorePictures = $arResult['PROPERTIES'][$arParams['PROPERTY_MORE_PICTURES']]['VALUE'];

	if (!empty($arParams['PROPERTY_MORE_PICTURES']))
		if (!empty($arMorePictures) && is_array($arMorePictures))
			foreach ($arMorePictures as $iPicture)
                $arResult['MORE_PHOTO'][] = CFile::GetPath($iPicture);

	$arResult['MORE_PHOTO_COUNT'] = count($arResult['MORE_PHOTO']);

    if (!empty($arResult['STARTSHOP']['OFFERS'])) {
        foreach ($arResult['STARTSHOP']['OFFERS'] as $iOfferID => $arOffer) {
            $arResult['STARTSHOP']['OFFERS'][$iOfferID]['PICTURE'] = CStartShopToolsIBlock::GetItemPicture($arOffer, 450, 450);
            $arResult['STARTSHOP']['OFFERS'][$iOfferID]['MORE_PHOTO'] = array();

            $arMorePictures = $arOffer['PROPERTIES'][$arParams['PROPERTY_MORE_PICTURES_OFFERS']]['VALUE'];

            if (!empty($arParams['PROPERTY_MORE_PICTURES_OFFERS']))
                if (!empty($arMorePictures) && is_array($arMorePictures))
                    foreach ($arMorePictures as $iPicture)
                        $arResult['STARTSHOP']['OFFERS'][$iOfferID]['MORE_PHOTO'][] = CFile::GetPath($iPicture);
        }
    }

    unset($arMorePictures, $iPicture, $iOfferID, $arOffer);

    $arPropertiesExtend = array(
        'PROPERTY_ARTICLE' => 'ARTICLE'
    );

    $arResult['TEMPLATE_PROPERTIES'] = array();

    foreach ($arPropertiesExtend as $sPropertyExtend => $sTemplateProperty) {
        $sPropertyExtendKey = $arParams[$sPropertyExtend];

        if (!empty($sPropertyExtendKey))
            if (!empty($arResult['PROPERTIES'][$sPropertyExtendKey]))
                $arResult['TEMPLATE_PROPERTIES'][$sTemplateProperty] = $arResult['PROPERTIES'][$sPropertyExtendKey];
    }
	
	$sComparePath = parse_url($arResult['COMPARE_URL']);
    $sComparePath = $sComparePath['path'];
    $arResult['COMPARE_REMOVE_URL'] = $sComparePath.'?'.$arParams['ACTION_VARIABLE'].'=DELETE_FROM_COMPARE_LIST&'.$arParams['PRODUCT_ID_VARIABLE'].'='.$arResult['ID'];
?>