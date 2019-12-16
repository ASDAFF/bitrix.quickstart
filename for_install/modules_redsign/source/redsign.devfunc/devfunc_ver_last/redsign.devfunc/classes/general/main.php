<?
/************************************
*
* Universal Extensions
* last update 25.07.2016
*
************************************/

use Bitrix\Main\Loader;


IncludeModuleLangFile(__FILE__);

class RSDevFunc {
    public static function Init($arInit) {
        global $APPLICATION;
        
        if(!is_array($arInit))
            $arInit = array($arInit);
        
        if(in_array("jsfunc",$arInit)) {
            $APPLICATION->AddHeadString("<script>var RSDevFunc_BasketEndWord_end1 = \"".GetMessage("RSDF_END_1")."\";var RSDevFunc_BasketEndWord_end2 = \"".GetMessage("RSDF_END_2")."\";var RSDevFunc_BasketEndWord_end3 = \"".GetMessage("RSDF_END_3")."\";</script>");
            $APPLICATION->AddHeadScript("/bitrix/js/redsign.devfunc/script.js");
        }
    }
    
    public static function GetDataForProductItem(&$arItems, $params=array()) {

        $bCatalog = Loader::includeModule('catalog');

        if(Loader::includeModule('iblock') && is_array($arItems) && count($arItems)>0) {
            // prepare data
            $arElements = array();
            $arElementsIDs = array();
            foreach($arItems as $iKeyItem => $arItem) {
                $arElementsIDs[] = $arItem['ID'];
                $arElements[$arItems[$iKeyItem]['ID']] = &$arItems[$iKeyItem];
                if(!empty($arItems[$iKeyItem]['OFFERS']) && is_array($arItems[$iKeyItem]['OFFERS'])) {
                    foreach($arItems[$iKeyItem]['OFFERS'] as $iOfferKey => $arOffer) {
                        $arElementsIDs[] = $arOffer['ID'];
                        $arElements[$arOffer['ID']] = &$arItems[$iKeyItem]['OFFERS'][$iOfferKey];
                    }
                }
            }
            // /prepare data
            
            $iTime = ConvertTimeStamp(time(),'FULL');
            // add quickbuy
            if(Loader::includeModule('redsign.quickbuy')) {
                $arFilter = array(
                    'DATE_FROM' => $iTime,
                    'DATE_TO' => $iTime,
                    'QUANTITY' => 0,
                    'ELEMENT_ID' => $arElementsIDs,
                );
                $dbRes = CRSQUICKBUYElements::GetList( array('ID'=>'SORT'), $arFilter);
                while($arData = $dbRes->Fetch()) {
                    if(array_key_exists($arData['ELEMENT_ID'], $arElements)) {
                        $arElements[$arData['ELEMENT_ID']]['QUICKBUY'] = $arData;
                        $arElements[$arData['ELEMENT_ID']]['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData);
                    }
                }
            }

            // add da2
            if(Loader::includeModule('redsign.daysarticle2')) {
                $arFilter = array(
                    'DATE_FROM' => $iTime,
                    'DATE_TO' => $iTime,
                    'QUANTITY' => 0,
                    'ELEMENT_ID' => $arElementsIDs,
                );
                $dbRes = CRSDA2Elements::GetList(array('ID'=>'SORT'), $arFilter);
                while($arData = $dbRes->Fetch()) {
                    if(array_key_exists($arData['ELEMENT_ID'], $arElements)) {
                        $arElements[$arData['ELEMENT_ID']]['DAYSARTICLE2'] = $arData;
                        $arElements[$arData['ELEMENT_ID']]['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData);
                    }
                }
            }

            $arFilesIds = array();
            foreach ($arElements as $iElementId => $arElement) {
                $CODE = false;
                if (isset($arElement['PROPERTIES'][$params['PROP_MORE_PHOTO']]['ID'])) {
                    $CODE = $params['PROP_MORE_PHOTO'];
                } elseif (isset($arElement['PROPERTIES'][$params['PROP_SKU_MORE_PHOTO']]['ID'])) {
                    $CODE = $params['PROP_SKU_MORE_PHOTO'];
                }

                if ($CODE && isset($arElement['PROPERTIES'][$CODE]['VALUE']) && !is_array($arElement['PROPERTIES'][$CODE]['VALUE']) && IntVal($arElement['PROPERTIES'][$CODE]['VALUE'])>0) {
                    $iFileId = $arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'];
                    $arFilesIds[$iFileId] = $iFileId;
                } elseif ($CODE && isset($arElement['PROPERTIES'][$CODE]['VALUE']) && is_array($arElement['PROPERTIES'][$CODE]['VALUE']) && count($arElement['PROPERTIES'][$CODE]['VALUE'])>0) {
                    foreach ($arElement['PROPERTIES'][$CODE]['VALUE'] as $iFileKey => $iFileId) {
                        $arFilesIds[$iFileId] = $iFileId;
                    }
                }

                if (!is_array($arElements[$iElementId]['PREVIEW_PICTURE']) && intval($arElements[$iElementId]['PREVIEW_PICTURE']) > 0) {
                    $iFileId = $arElements[$iElementId]['PREVIEW_PICTURE'];
                    $arFilesIds[$iFileId] = $iFileId;
                }
                if (!is_array($arElements[$iElementId]['DETAIL_PICTURE']) && intval($arElements[$iElementId]['DETAIL_PICTURE']) > 0) {
                    $iFileId = $arElements[$iElementId]['DETAIL_PICTURE'];
                    $arFilesIds[$iFileId] = $iFileId;
                }
            }

            // get files arrays
            $res = Bitrix\Main\FileTable::getList(array(
                'order' => array('ID' => 'asc'),
                'filter' => array('ID' => $arFilesIds)
            ));
            while ($row = $res->fetch()) {
                $arFilesIds[$row['ID']] = $row;
                if (empty($arFilesIds[$row['ID']]['SRC'])) {
                    $arFilesIds[$row['ID']]['SRC'] = CFile::GetFileSRC($row);
                }
            }

            foreach ($arElements as $iElementId => $arElement) {
                $CODE = false;
                if (isset($arElement['PROPERTIES'][$params['PROP_MORE_PHOTO']]['ID'])) {
                    $CODE = $params['PROP_MORE_PHOTO'];
                } elseif (isset($arElement['PROPERTIES'][$params['PROP_SKU_MORE_PHOTO']]['ID'])) {
                    $CODE = $params['PROP_SKU_MORE_PHOTO'];
                }

                // add images
                if ($CODE && isset($arElement['PROPERTIES'][$CODE]['VALUE']) && !is_array($arElement['PROPERTIES'][$CODE]['VALUE']) && IntVal($arElement['PROPERTIES'][$CODE]['VALUE'])>0) {
                    $arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'] = array(0 => array('RESIZE' => CFile::ResizeImageGet($arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array())));
                } elseif($CODE && isset($arElement['PROPERTIES'][$CODE]['VALUE']) && is_array($arElement['PROPERTIES'][$CODE]['VALUE']) && count($arElement['PROPERTIES'][$CODE]['VALUE'])>0) {
                    foreach($arElement['PROPERTIES'][$CODE]['VALUE'] as $iFileKey => $iFileId) {
                        $arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey] = $arFilesIds[$iFileId];
                        $arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey]['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['PROPERTIES'][$CODE]['VALUE'][$iFileKey],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
                    }
                }
                if (!empty($arElements[$iElementId]['PREVIEW_PICTURE'])) {
                    $arElements[$iElementId]['PREVIEW_PICTURE'] = (is_array($arElements[$iElementId]['PREVIEW_PICTURE'])>0 ? $arElements[$iElementId]['PREVIEW_PICTURE'] : $arFilesIds[$arElements[$iElementId]['PREVIEW_PICTURE']]);
                    $arElements[$iElementId]['PREVIEW_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['PREVIEW_PICTURE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
                }
                if (!empty($arElements[$iElementId]['DETAIL_PICTURE'])) {
                    $arElements[$iElementId]['DETAIL_PICTURE'] = (is_array($arElements[$iElementId]['DETAIL_PICTURE'])>0 ? $arElements[$iElementId]['DETAIL_PICTURE'] : $arFilesIds[$arElements[$iElementId]['DETAIL_PICTURE']]);
                    $arElements[$iElementId]['DETAIL_PICTURE']['RESIZE'] = CFile::ResizeImageGet($arElements[$iElementId]['DETAIL_PICTURE'],array('width'=>$params['MAX_WIDTH'],'height'=>$params['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
                }

                // PRICE_MATRIX
                if ($params['USE_PRICE_COUNT']) {
                    self::getPriceMatrixEx($arElements[$iElementId], 0, $params['FILTER_PRICE_TYPES'], 'Y', $params['CURRENCY_PARAMS']);
                }
                
                // add ratio min price
                CIBlockPriceTools::setRatioMinPrice($arElements[$iElementId], false);
            }

            // have set?
            if ($bCatalog && ($params['PAGE'] == 'detail' || $params['DETAIL_PICTURE'])) {
                $resSet = CCatalogProductSet::GetList(array(), array('ITEM_ID' => $arElementsIDs));
                while ($arFields = $resSet->fetch()) {
                    $iElementId = $arFields['ITEM_ID'];
                    $arElements[$iElementId]['HAVE_SET'] = true;
                    if (!empty($arElements[$iElementId]['OFFERS'])) {
                        foreach ($arElements[$iElementId]['OFFERS'] as $iOfferKey => $arOffer) {
                            $arElements[$iElementId]['OFFERS'][$iOfferKey]['HAVE_SET'] = true;
                        }
                    }
                }
            }
            
            foreach($arItems as $iKeyItem => $arItem) {
                $CODE = $params['PROP_MORE_PHOTO'];
                $HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
                if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
                // first image
                $arItems[$iKeyItem]['FIRST_PIC'] = false;
                $arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = false;
                if(isset($PRODUCT['PREVIEW_PICTURE']['RESIZE']) && is_array($PRODUCT['PREVIEW_PICTURE']['RESIZE']) && $params['PAGE']!='detail') {
                    $arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['PREVIEW_PICTURE'];
                } elseif(isset($PRODUCT['DETAIL_PICTURE']['RESIZE']) && is_array($PRODUCT['DETAIL_PICTURE']['RESIZE'])) {
                    $arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['DETAIL_PICTURE'];
                    $arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $PRODUCT['DETAIL_PICTURE'];
                } elseif($CODE && isset($PRODUCT['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE']) && is_array($PRODUCT['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE'])) {
                    $arItems[$iKeyItem]['FIRST_PIC'] = $PRODUCT['PROPERTIES'][$CODE]['VALUE'][0];
                    $arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $PRODUCT['PROPERTIES'][$CODE]['VALUE'][0];
                } elseif(isset($arItem['PREVIEW_PICTURE']['RESIZE']) && is_array($arItem['PREVIEW_PICTURE']['RESIZE']) && $params['PAGE']!='detail') {
                    $arItems[$iKeyItem]['FIRST_PIC'] = $arItem['PREVIEW_PICTURE'];
                } elseif(isset($arItem['DETAIL_PICTURE']['RESIZE']) && is_array($arItem['DETAIL_PICTURE']['RESIZE'])) {
                    $arItems[$iKeyItem]['FIRST_PIC'] = $arItem['DETAIL_PICTURE'];
                    $arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $arItem['DETAIL_PICTURE'];
                } elseif(!empty($arItem['OFFERS'])) {
                    $CODE = $params['PROP_SKU_MORE_PHOTO'];
                    foreach($arItem['OFFERS'] as $arOffer) {
                        if(isset($arOffer['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE']) && is_array($arOffer['PROPERTIES'][$CODE]['VALUE'][0]['RESIZE'])) {
                            $arItems[$iKeyItem]['FIRST_PIC'] = $arOffer['PROPERTIES'][$CODE]['VALUE'][0];
                            $arItems[$iKeyItem]['FIRST_PIC_DETAIL'] = $arOffer['PROPERTIES'][$CODE]['VALUE'][0];
                            break;
                        }
                    }
                }
            }
        }
    }

    public static function getPriceMatrixEx(&$arElement, $filterQuantity = 0, $arFilterType = array(), $VAT_INCLUDE = 'Y', $arCurrencyParams = array()) {

        $bCatalog = Loader::includeModule('catalog');

        // OFFERS PRICE_MATRIX fix
        if (!isset($arElement['PRICE_MATRIX'])) {

            if ($bCatalog) {
                $arElement['PRICE_MATRIX'] = CatalogGetPriceTableEx($arElement['ID'], $filterQuantity, $arFilterType, $VAT_INCLUDE, $arCurrencyParams);


                if (isset($arElement['PRICE_MATRIX']['COLS']) && is_array($arElement['PRICE_MATRIX']['COLS'])) {
                    foreach ($arElement['PRICE_MATRIX']['COLS'] as $sKeyColumn => $arColumn) {
                        $arElement['PRICE_MATRIX']['COLS'][$sKeyColumn]['NAME_LANG'] = htmlspecialcharsbx($arColumn['NAME_LANG']);
                    }
                }
            }
        }
        
        if (isset($arElement['PRICE_MATRIX'])) {
            if (!empty($arElement['PRICE_MATRIX']['ROWS'])) {
                foreach ($arElement['PRICE_MATRIX']['ROWS'] as $sKeyRow => $arRow) {
                    $strMinCode = '';
                    $boolStartMin = true;
                    $dblMinPrice = 0;

                    foreach ($arElement['PRICE_MATRIX']['CAN_BUY'] as $iPriceTypeId) {
                        
                        if (isset($arElement['PRICE_MATRIX']['MATRIX'][$iPriceTypeId][$sKeyRow])) {
                            if ($boolStartMin) {
                                $dblMinPrice = $arElement['PRICE_MATRIX']['MATRIX'][$iPriceTypeId][$sKeyRow]['DISCOUNT_PRICE'];
                                $strMinCode = $iPriceTypeId;
                                $boolStartMin = false;
                            } else {
                                $dblComparePrice = $arElement['PRICE_MATRIX']['MATRIX'][$iPriceTypeId][$sKeyRow]['DISCOUNT_PRICE'];
                                if ($dblMinPrice > $dblComparePrice) {
                                    $dblMinPrice = $dblComparePrice;
                                    $strMinCode = $iPriceTypeId;
                                }
                            }
                        }
                    }
                    
                    
                    if ('' != $strMinCode) {
                        $arElement['PRICE_MATRIX']['MATRIX'][$strMinCode][$sKeyRow]['MIN_PRICE'] = 'Y';
                    }
                }
            }
        }

        if (isset($arElement['PRICE_MATRIX']['COLS']) && count($arElement['PRICE_MATRIX']['COLS']) > 0) {

            foreach ($arElement['PRICE_MATRIX']['MATRIX'] as $iCol => $arRow) {

                foreach ($arRow as $iRow => $arPrice) {
                    $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['DISCOUNT_DIFF'] = $arPrice['PRICE'] - $arPrice['DISCOUNT_PRICE'];
                    $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['DISCOUNT_DIFF_PERCENT'] = roundEx(
                        100 * $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['DISCOUNT_DIFF'] / $arPrice['PRICE'],
                        0
                    );
                    $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['PRINT_VALUE'] = FormatCurrency(
                            $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['PRICE'],
                            $arPrice['CURRENCY']
                    );
                    $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['PRINT_DISCOUNT_VALUE'] = FormatCurrency(
                            $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['DISCOUNT_PRICE'],
                            $arPrice['CURRENCY']
                    );
                    $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['PRINT_DISCOUNT_DIFF'] = FormatCurrency(
                            $arElement['PRICE_MATRIX']['MATRIX'][$iCol][$iRow]['DISCOUNT_DIFF'],
                            $arPrice['CURRENCY']
                    );
                }

            }
        }
    }

    public static function getElementPictures(&$arItem, $params, $iLimit = false) {
        $iCount = 0;
        $arPics = array();
        if (is_array($arItem['OFFERS']) && 0 < count($arItem['OFFERS'])) {
            if (!isset($arItem['OFFERS_SELECTED']) || 0 > intval($arItem['OFFERS_SELECTED'])) {
                $arItem['OFFERS_SELECTED'] = 0;
            }
            $arElements[] = &$arItem['OFFERS'][$arItem['OFFERS_SELECTED']];
            $arElements[] = &$arItem;
            foreach ($arItem['OFFERS'] as $iOfferKey => $arOffer) {
                if ($arItem['OFFERS_SELECTED'] != $iOfferKey) {
                    $arElements[] = &$arItem['OFFERS'][$iOfferKey];
                }
            }
        } else {
            $arElements[] = &$arItem;
        }
        
        foreach ($arElements as $iElementKey => $arElement) {
            if ($params['PREVIEW_PICTURE'] && !empty($arElement['PREVIEW_PICTURE'])) {
                if (!is_array($arElement['PREVIEW_PICTURE'])) {
                    $arElements[$iElementKey]['PREVIEW_PICTURE'] = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);
                }
                foreach ($params['RESIZE'] as $iResize => $arResize) {
                    $arElements[$iElementKey]['PREVIEW_PICTURE']['RESIZE'][$iResize] = CFile::ResizeImageGet($arElements[$iElementKey]['PREVIEW_PICTURE'], array('width' => $arResize['MAX_WIDTH'], 'height' => $arResize['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                }
                if ($iLimit) {
                    $arPics[] = $arElements[$iElementKey]['PREVIEW_PICTURE'];
                    if ($iLimit == ++$iCount) {
                        return $arPics;
                    }
                } else {
                    $arElements[$iElementKey]['PRODUCT_PHOTO']['p'] = $arElements[$iElementKey]['PREVIEW_PICTURE'];
                }
            }
            if ($params['DETAIL_PICTURE'] && !empty($arElement['DETAIL_PICTURE'])) {
                if (!is_array($arElement['DETAIL_PICTURE'])) {
                    $arElements[$iElementKey]['DETAIL_PICTURE'] = CFile::GetFileArray($arElement['DETAIL_PICTURE']);
                }
                foreach ($params['RESIZE'] as $iResize => $arResize) {
                    $arElements[$iElementKey]['DETAIL_PICTURE']['RESIZE'][$iResize] = CFile::ResizeImageGet($arElements[$iElementKey]['DETAIL_PICTURE'], array('width' => $arResize['MAX_WIDTH'], 'height' => $arResize['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                }
                if ($iLimit) {
                    $arPics[] = $arElements[$iElementKey]['DETAIL_PICTURE'];
                    if ($iLimit == ++$iCount) {
                        return $arPics;
                    }
                } else {
                    $arElements[$iElementKey]['PRODUCT_PHOTO']['d'] = $arElements[$iElementKey]['DETAIL_PICTURE'];
                }
            }
            if ('' != $params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]
                && isset($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]])
                && 'F' == $arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['PROPERTY_TYPE']
            ) {
                
                if (isset($arElement['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'])
                    && !empty($arElement['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'])
                ) {
                    if (isset($arElement['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE']['ID'])) {
                        foreach($params['RESIZE'] as $iResize => $arResize) {
                            $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE']['RESIZE'][$iResize] = CFile::ResizeImageGet($arElement['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'], array('width' => $arResize['MAX_WIDTH'], 'height' => $arResize['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        if ($iLimit) {
                            $arPics[] = $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'];
                            if ($iLimit == ++$iCount) {
                                return $arPics;
                            }
                        } else {
                            $arElements[$iElementKey]['PRODUCT_PHOTO'][] = $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'];
                        }
                    } else {
                        foreach ($arElement['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'] as $iFileKey => $arFile) {
                            foreach ($params['RESIZE'] as $iResize => $arResize) {
                                $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'][$iFileKey]['RESIZE'][$iResize] = CFile::ResizeImageGet($arFile, array('width' => $arResize['MAX_WIDTH'], 'height' => $arResize['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            if ($iLimit) {
                                $arPics[] = $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'][$iFileKey];
                                if ($iLimit == ++$iCount) {
                                    return $arPics;
                                }
                            }
                            else{
                                $arElements[$iElementKey]['PRODUCT_PHOTO'][] = $arElements[$iElementKey]['DISPLAY_PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['FILE_VALUE'][$iFileKey];
                            }
                        }
                    }
                } else {
                    if (is_array($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'])) {
                        if (!is_array($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'])
                            && intval($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE']) > 0) {
                            $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'] = CFile::GetFileArray($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE']);
                            foreach($params['RESIZE'] as $iResize => $arResize) {
                                $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE']['RESIZE'][$iResize] = CFile::ResizeImageGet($arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'], array('width' => $params['MAX_WIDTH'], 'height' => $params['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            if ($iLimit) {
                                $arPics[] = $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'];
                                if ($iLimit == ++$iCount) {
                                    return $arPics;
                                }
                            }
                            else{
                                $arElements[$iElementKey]['PRODUCT_PHOTO'][] = $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'];
                            }
                        }
                        else{
                            foreach($arElement['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'] as $iFileKey => $iFileId) {
                                $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'][$iFileKey] = CFile::GetFileArray($iFileId);
                                foreach($params['RESIZE'] as $iResize => $arResize) {
                                    $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'][$iFileKey]['RESIZE'][$iResize] = CFile::ResizeImageGet($arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'][$iFileKey], array('width' => $arResize['MAX_WIDTH'], 'height' => $arResize['MAX_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                                }
                                if ($iLimit) {
                                    $arPics[] = $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'][$iFileKey];
                                    if ($iLimit == ++$iCount) {
                                        return $arPics;
                                    }
                                }
                                else{
                                    $arElements[$iElementKey]['PRODUCT_PHOTO'][] = $arElements[$iElementKey]['PROPERTIES'][$params['ADDITIONAL_PICT_PROP'][$arElement['IBLOCK_ID']]]['VALUE'][$iFileKey];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public static function GetNoPhoto($arSizes) {
        $return = false;
        $fileid = COption::GetOptionInt('redsign.devfunc', 'no_photo_fileid', 0);
        if($fileid>0) {
            $return = CFile::ResizeImageGet($fileid,array('width'=>$arSizes['MAX_WIDTH'],'height'=>$arSizes['MAX_HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
        }
        return $return;
    }
    
    public static function BasketEndWord($num,$end1="",$end2="",$end3="") {
        if($end1=='') $end1 = GetMessage('RSDF.END_1');
        if($end2=='') $end2 = GetMessage('RSDF.END_2');
        if($end3=='') $end3 = GetMessage('RSDF.END_3');
        $status = array($end1,$end2,$end3);
        $array = array(2,0,1,1,1,2);
        return $status[($num%100>4 && $num%100<20)? 2 : $array[($num%10<5)?$num%10:5]];
    }
    
    public static function DeviceDetect() {
        $return = array(
            "DEVICE" => "pc",
            //"OS" => "",
            //"BROWSER" => "",
        );
        $wap_profile = $_SERVER["HTTP_X_WAP_PROFILE"];
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        if(strpos($user_agent,"Windows Phone")>0) {
            $return = array(
                "DEVICE" => "smartphone",
            );
        } elseif(strpos($user_agent,"Android")>0) {
            if(isset($wap_profile) && $wap_profile!="") {
                $return = array(
                    "DEVICE" => "smartphone",
                );
            } else {
                $return = array(
                    "DEVICE" => "tab",
                );
            }
        } elseif(strpos($user_agent,"iPhone")) {
            $return = array(
                "DEVICE" => "smartphone",
            );
        } elseif(strpos($user_agent,"iPad")) {
            $return = array(
                "DEVICE" => "tab",
            );
        } elseif(strpos($user_agent,"Windows")>0) {
            $return = array(
                "DEVICE" => "pc",
            );
        } 
        return $return;
    }
}
