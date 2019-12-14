<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!\Bitrix\Main\Loader::includeModule('redsign.devfunc')) {
    return;
}

foreach ($arParams as $name => $prop) {
    if (preg_match('/^ADDITIONAL_PICT_PROP_(\d+)$/', $name, $arMatches)) {
        $iBlockID = (int)$arMatches[1];
        if (0 >= $iBlockID) {
            continue;
        }
        if ('' != $arParams[$name] && '-' != $arParams[$name]) {
            $arParams['ADDITIONAL_PICT_PROP'][$iBlockID] = $arParams[$name];
        }
        unset($arParams[$arMatches[0]]);
    }
}

$params = array(
    'RESIZE' => array(
        'small' => array(
            'MAX_WIDTH' => 210,
            'MAX_HEIGHT' => 160,
        )
    ),
    'PREVIEW_PICTURE' => true,
    'DETAIL_PICTURE' => true,
    'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP']
);
$arResult['ELEMENT']['FIRST_PIC'] = RSDevFunc::getElementPictures($arResult['ELEMENT'], $params, 1);

// $arResult['SECTIONS'] = array($arResult['ELEMENT']['IBLOCK_SECTION_ID'] => $arResult['ELEMENT']['IBLOCK_SECTION_ID']);

/*
if ($arResult["ELEMENT"]['DETAIL_PICTURE'] || $arResult["ELEMENT"]['PREVIEW_PICTURE'])
{
    $arFileTmp = CFile::ResizeImageGet(
        $arResult["ELEMENT"]['DETAIL_PICTURE'] ? $arResult["ELEMENT"]['DETAIL_PICTURE'] : $arResult["ELEMENT"]['PREVIEW_PICTURE'],
        array("width" => "150", "height" => "180"),
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );
    $arResult["ELEMENT"]['DETAIL_PICTURE'] = $arFileTmp;
}
*/

$arDefaultSetIDs = array($arResult["ELEMENT"]["ID"]);
$newPrDisc = 0;


foreach (array('DEFAULT', 'OTHER') as $type) {
    foreach ($arResult['SET_ITEMS'][$type] as $iItemKey => $arItem) {
        if ($type == 'DEFAULT') {
            $arDefaultSetIDs[] = $arItem['ID'];
            $newPrDisc += $arItem["BASKET_QUANTITY"] * $arItem["PRICE_DISCOUNT_VALUE"];
            $newPr += $arItem["BASKET_QUANTITY"] * $arItem["PRICE_VALUE"];
        }
        $arElement = array(
            "ID"=>$arItem["ID"],
            "NAME" =>$arItem["NAME"],
            "DETAIL_PAGE_URL"=>$arItem["DETAIL_PAGE_URL"],
            "DETAIL_PICTURE"=>$arItem["DETAIL_PICTURE"],
            "PREVIEW_PICTURE"=> $arItem["PREVIEW_PICTURE"],
            "PRICE_CURRENCY" => $arItem["PRICE_CURRENCY"],
            "PRICE_DISCOUNT_VALUE" => $arItem["PRICE_DISCOUNT_VALUE"],
            "PRICE_PRINT_DISCOUNT_VALUE" => $arItem["PRICE_PRINT_DISCOUNT_VALUE"],
            "PRICE_VALUE" => $arItem["PRICE_VALUE"],
            "PRICE_PRINT_VALUE" => $arItem["PRICE_PRINT_VALUE"],
            "PRICE_DISCOUNT_DIFFERENCE_VALUE" => $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
            "PRICE_DISCOUNT_DIFFERENCE" => $arItem["PRICE_DISCOUNT_DIFFERENCE"],
            "CAN_BUY" => $arItem['CAN_BUY'],
            "SET_QUANTITY" => $arItem['SET_QUANTITY'],
            "MEASURE_RATIO" => $arItem['MEASURE_RATIO'],
            "BASKET_QUANTITY" => $arItem['BASKET_QUANTITY'],
            "MEASURE" => $arItem['MEASURE'],
            'FIRST_PIC' => RSDevFunc::getElementPictures($arItem, $params, 1)
        );

        if ($arItem['PRICE_CONVERT_DISCOUNT_VALUE']) {
            $arElement['PRICE_CONVERT_DISCOUNT_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_VALUE'];
        }
        if ($arItem['PRICE_CONVERT_VALUE']) {
            $arElement['PRICE_CONVERT_VALUE'] = $arItem['PRICE_CONVERT_VALUE'];
        }
        if ($arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE']) {
            $arElement['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'];
        }

        // $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = $arItem['IBLOCK_SECTION_ID'];

        /*
        if ($arItem['DETAIL_PICTURE'] || $arItem['PREVIEW_PICTURE']) {
          $arFileTmp = CFile::ResizeImageGet(
            $arItem['DETAIL_PICTURE'] ? $arItem['DETAIL_PICTURE'] : $arItem['PREVIEW_PICTURE'],
            array("width" => "150", "height" => "180"),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
          );
          $arElement['DETAIL_PICTURE'] = $arFileTmp;
        }
        */

        $arResult['SET_ITEMS'][$type][$iItemKey] = $arElement;
    }
}

$arResult["DEFAULT_SET_IDS"] = $arDefaultSetIDs;

$newPrDisc += $arResult["BASKET_QUANTITY"][$arResult["ELEMENT"]["ID"]] * $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"];
$newPr += $arResult["BASKET_QUANTITY"][$arResult["ELEMENT"]["ID"]] * $arResult["ELEMENT"]["PRICE_VALUE"];
$discount = $newPr - $newPrDisc;
$arResult["RIGHT_ALL_DISCOUNT"] = CurrencyFormat($discount, $arParams["CURRENCY_ID"]);
$arResult["RIGHT_ALL_PRICE"] = CurrencyFormat($newPr, $arParams["CURRENCY_ID"]);
$arResult["RIGHT_ALL_PRICE_DISCOUNT"] = CurrencyFormat($newPrDisc, $arParams["CURRENCY_ID"]);
