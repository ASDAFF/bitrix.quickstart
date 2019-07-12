<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

foreach ($arParams as $name => $prop) {
    if (preg_match('/^ICON_NOVELTY_PROP_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['ICON_NOVELTY_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^ICON_DEALS_PROP_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_DEALS_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^ICON_DISCOUNT_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_DISCOUNT_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^BRAND_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['BRAND_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^OFFER_TREE_COLOR_PROPS_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['OFFER_TREE_COLOR_PROPS'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^ICON_MEN_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_MEN_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
    if (preg_match('/^ICON_WOMEN_PROP_(\d+)$/', $name, $arMatches)){
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID){
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]){
            $arParams['ICON_WOMEN_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
}

if (!empty($arResult['ITEMS']))
{

    $arSKUPropList = array();
    $arSKUPropIDs = array();
    $arSKUPropKeys = array();
    //
    $skuPropList = array(); // array("id_catalog" => array(...))
    $skuPropIds = array(); // array("id_catalog" => array(...))
    $skuPropKeys = array(); // array("id_catalog" => array(...))

    foreach($arResult['CATALOGS'] as $catalog)
    {
        $offersCatalogId = (int)$catalog['OFFERS_IBLOCK_ID'];
        $offersPropId = (int)$catalog['OFFERS_PROPERTY_ID'];
        $catalogId = (int)$catalog['IBLOCK_ID'];
        $sku = false;
        if ($offersCatalogId > 0 && $offersPropId > 0)
            $sku = array("IBLOCK_ID" => $offersCatalogId, "SKU_PROPERTY_ID" => $offersPropId, "PRODUCT_IBLOCK_ID" => $catalogId);


        if (!empty($sku) && is_array($sku))
        {
            $skuPropList[$catalogId] = CIBlockPriceTools::getTreeProperties(
                $sku,
                $arParams['OFFER_TREE_PROPS'][$offersCatalogId],
                array(
                    'PICT' => $arEmptyPreview,
                    'NAME' => '-'
                )
            );

            $needValues = array();
            CIBlockPriceTools::getTreePropertyValues($skuPropList[$catalogId], $needValues);

            $skuPropIds[$catalogId] = array_keys($skuPropList[$catalogId]);
            if (!empty($skuPropIds[$catalogId]))
                $skuPropKeys[$catalogId] = array_fill_keys($skuPropIds[$catalogId], false);
        }
    }

    $arNewItemsList = array();

    foreach ($arResult['ITEMS'] as $key => $arItem)
    {
        $arItem['CATALOG'] = true;
        if (!isset($arItem['CATALOG_TYPE']))
        {
            $arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
        }
        if (
            (CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
            && !empty($arItem['OFFERS'])
        )
        {
            $arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
        }
        switch ($arItem['CATALOG_TYPE'])
        {
            case CCatalogProduct::TYPE_SET:
                $arItem['OFFERS'] = array();
                $arItem['CATALOG_MEASURE_RATIO'] = 1;
                $arItem['CATALOG_QUANTITY'] = 0;
                $arItem['CHECK_QUANTITY'] = false;
                break;
            case CCatalogProduct::TYPE_SKU:
                break;
            case CCatalogProduct::TYPE_PRODUCT:
            default:
                $arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
                break;
        }

        // Offers
        if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
        {
            $arSKUPropIDs = isset($skuPropIds[$arItem['IBLOCK_ID']]) ? $skuPropIds[$arItem['IBLOCK_ID']] : array();
            $arSKUPropList = isset($skuPropList[$arItem['IBLOCK_ID']]) ? $skuPropList[$arItem['IBLOCK_ID']] : array();
            $arSKUPropKeys = isset($skuPropKeys[$arItem['IBLOCK_ID']]) ? $skuPropKeys[$arItem['IBLOCK_ID']] : array();

            $arMatrixFields = $arSKUPropKeys;
            $arMatrix = array();

            foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
            {
                $arRow = array();
                foreach ($arSKUPropIDs as $propkey => $strOneCode)
                {
                    $arCell = array(
                        'VALUE' => 0,
                        'SORT' => PHP_INT_MAX,
                        'NA' => true
                    );

                    if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
                    {
                        $arMatrixFields[$strOneCode] = true;
                        $arCell['NA'] = false;
                        if ('directory' == $arSKUPropList[$strOneCode]['USER_TYPE'])
                        {
                            $intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
                            $arCell['VALUE'] = $intValue;
                        }
                        elseif ('L' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
                        {
                            $arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
                        }
                        elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
                        {
                            $arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
                        }
                        $arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
                    }
                    $arRow[$strOneCode] = $arCell;
                }
                $arMatrix[$keyOffer] = $arRow;
            }

            $arSortFields = array();

            foreach ($arSKUPropIDs as $propkey => $strOneCode)
            {
                $boolExist = $arMatrixFields[$strOneCode];
                foreach ($arMatrix as $keyOffer => $arRow)
                {
                    if ($boolExist)
                    {
                        $arItem['OFFERS'][$keyOffer]['SKU_SORT_' . $strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
                        $arSortFields['SKU_SORT_' . $strOneCode] = SORT_NUMERIC;
                    }
                }
            }

            \Bitrix\Main\Type\Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

            // Find Selected offer
            foreach($arItem['OFFERS']  as $ind => $offer)
            {
                if ($offer['SELECTED'])
                {
                    $arItem['OFFERS_SELECTED'] = $ind;
                    break;
                }
            }
        }
        $arNewItemsList[$key] = $arItem;
    }
    $arResult['ITEMS'] = $arNewItemsList;
}

/*
if (!isset($arParams['SECTION_TITLE'])) {
    $arParams['SECTION_TITLE'] = getMessage('RS_SLINE.BCBP_AL.BIGDATA_TITLE');
}
*/


$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/result_modifier.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}