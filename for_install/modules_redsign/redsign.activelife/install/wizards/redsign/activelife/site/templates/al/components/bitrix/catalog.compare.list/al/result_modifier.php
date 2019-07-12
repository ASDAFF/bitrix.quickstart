<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$defaultParams = array(
	'POSITION_FIXED' => 'Y',
	'POSITION' => 'top left'
);

$arParams = array_merge($defaultParams, $arParams);
unset($defaultParams);
if ($arParams['POSITION_FIXED'] != 'N')
	$arParams['POSITION_FIXED'] = 'Y';

$arParams['POSITION'] = trim($arParams['POSITION']);
$arParams['POSITION'] = explode(' ', $arParams['POSITION']);
if (empty($arParams['POSITION']) || count($arParams['POSITION']) != 2)
	$arParams['POSITION'] = array('top', 'left');
if ($arParams['POSITION'][0] != 'bottom')
	$arParams['POSITION'][0] = 'top';
if ($arParams['POSITION'][1] != 'right')
	$arParams['POSITION'][1] = 'left';

$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID']: 0;

if ($arParams['ADDITIONAL_PICT_PROP'] != '' && $arParams['ADDITIONAL_PICT_PROP'] != '-') {
	$arParams['ADDITIONAL_PICT_PROP'] = array($arParams['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP']);
} else {
    $arParams['ADDITIONAL_PICT_PROP'] = array();
}

if ($OFFERS_IBLOCK_ID) {
    if ($arParams['OFFER_ADDITIONAL_PICT_PROP'] != '' && $arParams['OFFER_ADDITIONAL_PICT_PROP'] != '-') {
		$arParams['ADDITIONAL_PICT_PROP'][$OFFERS_IBLOCK_ID] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
	}
}


if (\Bitrix\Main\Loader::includeModule('redsign.devfunc'))
{
    $params = array(
        'RESIZE' => array(
            'small' => array(
                'MAX_WIDTH' => 64,
                'MAX_HEIGHT' => 64,
            ),
        ),
        'PREVIEW_PICTURE' => true,
        'DETAIL_PICTURE' => true,
        'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
    );
    
    $arElements = array();
    if (!empty($arResult)) {
        foreach ($arResult as $iItemkey => $arItem) {
            if (!isset($arElements[$arItem['IBLOCK_ID']])) {
                $arElements[$arItem['IBLOCK_ID']] = array();
            }
            $arElements[$arItem['IBLOCK_ID']][$arItem['ID']] = &$arResult[$iItemkey];
        }
        
        foreach ($arElements as $iIblockId => $arIBlockElements) {

            $bPictureIsset = false;
            $dbElements = CIBlockElement::getList(
                array(),
                array(
                'IBLOCK_ID' => $iIblockId,
                'ID' => array_keys($arIBlockElements)
                ),
                false,
                false,
                array(
                    'IBLOCK_ID',
                    'ID',
                    'NAME',
                    'PREVIEW_PICTURE',
                    'DETAIL_PICTURE',
                    'PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$iIblockId]
                )
            );

            while ($arElement = $dbElements->getNext()) {

                if (intval($arElement['PREVIEW_PICTURE']) > 0) {
                    $arElements[$iIblockId][$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['PREVIEW_PICTURE'];
                    continue;
                }
                if (intval($arElement['DETAIL_PICTURE']) > 0) {
                    $arElements[$iIblockId][$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['DETAIL_PICTURE'];
                    continue;
                }
                
                if (intval($arElement['PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']].'_VALUE']) > 0) {
                    $arElements[$iIblockId][$arElement['ID']]['PREVIEW_PICTURE'] = $arElement['PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']].'_VALUE'];
                    continue;
                }
            }
        }
        
        $arOffers = CIBlockPriceTools::GetOffersArray(
            array(
                'IBLOCK_ID' => $iIblockId
            ),
            array_keys($arIBlockElements),
            array(
                'CATALOG_PRICE_'.$arParams['SKU_PRICE_SORT_ID'] => 'ASC'
            ),
            array(
                'ID',
                'NAME',
                'PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$OFFERS_IBLOCK_ID]
            ),
            array(
                
            ),
            0,
            array(),
            'N'
        );

        if (!empty($arOffers)) {
            foreach ($arOffers as $key2 => $arOffer) {
                if (
                    isset($arResult[$arOffer['LINK_ELEMENT_ID']]) &&
                    !isset($arResult[$arOffer['LINK_ELEMENT_ID']]['PREVIEW_PICTURE'])
                ) {
                    $arResult[$arOffer['LINK_ELEMENT_ID']]['PREVIEW_PICTURE'] = $arOffer['PROPERTY_'.$arParams['ADDITIONAL_PICT_PROP'][$OFFERS_IBLOCK_ID].'_VALUE'];
                }
            }
        }
        
        foreach ($arResult as $iItemkey => $arItem) {
            $arResult[$iItemkey]['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult[$iItemkey], $params, 1);
        }
    }
}