<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

function callback($buffer)
{
    // перестраиваем фильтр только по особой надобности
    if((int)$_REQUEST['FILTER_RESET'] == 1)
    {
        // set current price for filter
        include($_SERVER['DOCUMENT_ROOT']."/include/catalog/incSetGroupPrice.php");
        /**
         * выборка OR для идентификации элементов фильтра которые нужно скрыть
         */
        //инициализация начальных параметров
        if( $_REQUEST['arElementsSearch'][0] != -1)
            $arElementsSearch = $_REQUEST['arElementsSearch'];
        else $arElementsSearch = array();
        // опредлим текущую секцию в каталоге
        if(!empty($_REQUEST['secid']))
            $arResult['CUR_SECTION_CODE'] = $_REQUEST['secid'];
        // сформируем фильтр для каталога
        if(is_array($_REQUEST['arFilter']))
        {
            foreach($_REQUEST['arFilter'] as $val)
                foreach($val as $subkey => $subval)
                    $arParams['arFilterRequest'][$subkey][] = $subval;
        }
        // сформируем фильтр для торговых предложений
        if(is_array($_REQUEST['arOffer']))
        {
            foreach($_REQUEST['arOffer'] as $val)
                foreach($val as $subkey => $subval)
                    $arParams['arOfferRequest'][$subkey][] = $subval;
        }
        // определим ценовой диапазон для торговых предложений
        if( !empty($arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']]) )
        {
            $arParams['arOfferRequest']['>='.$arParams['CATALOG_PRICE']] = $arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']][0];
            unset( $arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']] );
        }
        if( !empty($arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']]) )
        {
            $arParams['arOfferRequest']['<='.$arParams['CATALOG_PRICE']] = $arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']][0];
            unset( $arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']] );
        }
        unset(
        $arParams['arFilterRequest']['minMATERIAL_DESC'],
        $arParams['arFilterRequest']['maxMATERIAL_DESC'],
        $arParams['arFilterRequest']['minMAXIMUM_PRICE'],
        $arParams['arFilterRequest']['maxMAXIMUM_PRICE']
        );

        // выберем свойства каталога с галкой умный фильтр
        $arProps = array();
        foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['CATALOG_IBLOCK_ID'], $_REQUEST['CURRENT_SECTION_ID']) as $PID => $arLink)
        {
            if($arLink['SMART_FILTER'] !== "Y") continue;
            $rsProperty = CIBlockProperty::GetByID($PID);
            $arProperty = $rsProperty->Fetch();
            if($arProperty)
            {
                $ID = $arProperty['LINK_IBLOCK_ID'];
                $arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
            }
        }
        foreach(CIBlockSectionPropertyLink::GetArray($_REQUEST['OFFERS_IBLOCK_ID'], false) as $PID => $arLink)
        {
            if($arLink['SMART_FILTER'] !== "Y") continue;
            $rsProperty = CIBlockProperty::GetByID($PID);
            $arProperty = $rsProperty->Fetch();
            if($arProperty)
            {
                $ID = $arProperty['LINK_IBLOCK_ID'];
                $arProps[] = mb_strtoupper("PROPERTY_".$arProperty['CODE']);
            }
        }
        // зададим поля для выборки
        $arSelect = $arProps;
        $arSelect[] = 'IBLOCK_ID';
        $arSelect[] = 'ID';
        $arSelect[] = 'NAME';
        $arSelect[] = 'PROPERTY_CML2_LINK';
        $arSelect[] = $arParams['CATALOG_GROUP'];
        //deb($arParams);
        //echo"<hr>";
        if( empty($arParams['arFilterRequest']) && empty($arParams['arOfferRequest']) )
        {
            if(empty($_REQUEST['q']))
            {
                if(!empty($_REQUEST['secid']))
                    $arFilter['SECTION_CODE'] = $arResult['CUR_SECTION_CODE'];
                $arFilter['ACTIVE'] = "Y";
                $arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
                $arFilter['INCLUDE_SUBSECTIONS'] = "Y";
                // выберем элементы входящие в фильтр запомним их ID, а также сформируем активные свойства
                //deb($arFilter);
                $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
                $arExtElementId = array(); // массив для ID элементов
                while($arElement = $rsElement -> Fetch())
                {
                    $arExtElementId[ $arElement['ID'] ] = $arElement['ID'];
                    foreach( $arProps as $val)
                    {
                        if( is_array( $arElement[ $val."_VALUE" ] ) )
                            foreach( $arElement[ $val."_VALUE" ] as $subval )
                                $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                        else
                            $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                    }
                }
            }
            // добираем активные свойства из каталога Т.П. по связке ID с каталогом
            $arFilter = array();
            $arFilter['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
            $arFilter['PROPERTY_CML2_LINK'] = array();
            if( is_array($arExtElementId) )
                $arFilter['PROPERTY_CML2_LINK'] = $arExtElementId;
            if ( !empty($arElementsSearch) )
                $arFilter['PROPERTY_CML2_LINK'] = array_merge( $arFilter['PROPERTY_CML2_LINK'], $arElementsSearch);
            if(!empty($arFilter['PROPERTY_CML2_LINK']))
            {
                //deb($arFilter);
                $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
                while($arElement = $rsElement -> Fetch())
                {
                    foreach( $arProps as $val)
                    {
                        if( is_array( $arElement[ $val."_VALUE" ] ) )
                            foreach( $arElement[ $val."_VALUE" ] as $subval )
                                $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                        else
                            $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                    }
                    if((int)$arElement[ $arParams['CATALOG_PRICE'] ] > 0)
                        $arPrice[ $arElement[ $arParams['CATALOG_PRICE'] ] ] = $arElement[ $arParams['CATALOG_PRICE'] ];
                }
            }
        }
        //deb($arResult);
        //echo"<hr>";
        // выбираем, согласно фильтру активные свойства товаров и запоминаем ID этих элементов
        if( !empty($arParams['arFilterRequest']) )
        {
            $arFilter = array();
            $arFilter = array(
                array(
                    'LOGIC' => "OR",
                )
            );
            foreach($arParams['arFilterRequest'] as $key => $val) $arFilter[0][$key] = $val;
            // запишем текущую секцию в каталоге в фильтр
            if(!empty($_REQUEST['secid']))
                $arFilter['SECTION_CODE'] = $arResult['CUR_SECTION_CODE'];
            $arFilter['ACTIVE'] = "Y";
            $arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
            $arFilter['INCLUDE_SUBSECTIONS'] = "Y";

            if( !empty($arParams['arOfferRequest']) )
            {
                $arParams['arOfferRequest']['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
                $arParams['arOfferRequest']['ACTIVE'] = "Y";
                $arParams['arOfferRequest'][">CATALOG_QUANTITY"] = 0;
                $arSubQuery = $arParams['arOfferRequest'];
                $arFilter['ID'] = CIBlockElement::SubQuery(
                    'PROPERTY_CML2_LINK',
                    $arSubQuery
                );
            }

            // выберем элементы входящие в фильтр запомним их ID, а также сформируем активные свойства
            //deb($arFilter);
            $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
            $arExtElementId = array(); // массив для ID элементов
            while($arElement = $rsElement -> Fetch())
            {
                $arExtElementId[ $arElement['ID'] ] = $arElement['ID'];
                foreach( $arProps as $val)
                {
                    if( is_array( $arElement[ $val."_VALUE" ] ) )
                        foreach( $arElement[ $val."_VALUE" ] as $subval )
                            $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                    else
                        $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                }
            }
            // добираем активные свойства из каталога Т.П. по связке ID с каталогом
            $arFilter = array();
            $arFilter['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
            $arFilter['PROPERTY_CML2_LINK'] = $arExtElementId;
            if(!empty($arFilter['PROPERTY_CML2_LINK']))
            {
                //deb($arFilter);
                $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
                while($arElement = $rsElement -> Fetch())
                {
                    foreach( $arProps as $val)
                    {
                        if( is_array( $arElement[ $val."_VALUE" ] ) )
                            foreach( $arElement[ $val."_VALUE" ] as $subval )
                                $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                        else
                            $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                    }
                    if((int)$arElement[ $arParams['CATALOG_PRICE'] ] > 0)
                        $arPrice[ $arElement[ $arParams['CATALOG_PRICE'] ] ] = $arElement[ $arParams['CATALOG_PRICE'] ];
                }
            }
        }
        //deb($arResult);
        //echo"<hr>";
        // выбираем, согласно фильтру активные свойства ТП и запоминаем ID этих элементов
        if( !empty($arParams['arOfferRequest']) )
        {
            $arFilter = array();
            $arFilter = array(
                array(
                    'LOGIC' => "OR",
                )
            );
            foreach($arParams['arOfferRequest'] as $key => $val)
            {
                if($key == ">=".$arParams['CATALOG_PRICE'])
                    $arFilter['>='.$arParams['CATALOG_PRICE']] = $val;
                elseif($key == "<=".$arParams['CATALOG_PRICE'])
                    $arFilter['<='.$arParams['CATALOG_PRICE']] = $val;
                else
                    $arFilter[0][$key] = $val;
            }
            $arFilter['ACTIVE'] = "Y";
            $arFilter['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
            //deb($arFilter);
            // выберем элементы входящие в фильтр по свойствам торговых предложений и запомним их ID
            $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
            $arExtElementId = array(); // массив для ID элементов
            while($arElement = $rsElement -> Fetch())
            {
                $arExtElementId[ $arElement['PROPERTY_CML2_LINK_VALUE'] ] = $arElement['PROPERTY_CML2_LINK_VALUE'];
                foreach( $arProps as $val)
                {
                    if( is_array( $arElement[ $val."_VALUE" ] ) )
                        foreach( $arElement[ $val."_VALUE" ] as $subval )
                            $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                    else
                        $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                }
                if((int)$arElement[ $arParams['CATALOG_PRICE'] ] > 0)
                    $arPrice[ $arElement[ $arParams['CATALOG_PRICE'] ] ] = $arElement[ $arParams['CATALOG_PRICE'] ];
            }
            //
            $arFilter = array();
            //if(!empty($_REQUEST['secid']))
            //	$arFilter['SECTION_CODE'] = $arResult['CUR_SECTION_CODE'];
            $arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
            $arFilter['PROPERTY_CML2_LINK_VALUE'] = $arExtElementId;
            //$arFilter['INCLUDE_SUBSECTIONS'] = "Y";
            //deb($arFilter);
            $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
            while($arElement = $rsElement -> Fetch())
            {
                foreach( $arProps as $val)
                {
                    if( is_array( $arElement[ $val."_VALUE" ] ) )
                        foreach( $arElement[ $val."_VALUE" ] as $subval )
                            $arResult['AVAIL_PROPS'][ $subval ] = $subval;
                    else
                        $arResult['AVAIL_PROPS'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
                }
            }
        }
        //echo"<hr>";
        //deb($arResult['AVAIL_PROPS']);
        //echo"<hr>";
        /*
        * установим максимальную и минимальную цену в выборке
        */
        if( is_array($arPrice) )
        {
            sort($arPrice);		$minPrice = $arPrice[0];
            rsort($arPrice);	$maxPrice = $arPrice[0];
        }else{
            $minPrice = $arPrice;
            $maxPrice = $arPrice;
        }

        /*
        * выборка AND для идентификации элементов фильтра которые скрываются и их можно только отключить
        */
        //$arElementsSearch = $_REQUEST['arElementsSearch'];
        $arParams = array();
        // set current price for filter
        include($_SERVER['DOCUMENT_ROOT']."/include/catalog/incSetGroupPrice.php");
        // сформируем фильтр для каталога
        if(is_array($_REQUEST['arFilter']))
        {
            foreach($_REQUEST['arFilter'] as $val)
                foreach($val as $subkey => $subval)
                    $arParams['arFilterRequest'][$subkey][] = $subval;
        }
        // сформируем фильтр для торговых предложений
        if(is_array($_REQUEST['arOffer']))
        {
            foreach($_REQUEST['arOffer'] as $val)
                foreach($val as $subkey => $subval)
                    $arParams['arOfferRequest'][$subkey][] = $subval;
        }
        // определим ценовой диапазон для торговых предложений
        if( !empty($arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']]) )
        {
            $arParams['arOfferRequest']['>='.$arParams['CATALOG_PRICE']] = $arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']][0];
            unset( $arParams['arFilterRequest']['min'.$arParams['CATALOG_PRICE']] );
        }
        if( !empty($arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']]) )
        {
            $arParams['arOfferRequest']['<='.$arParams['CATALOG_PRICE']] = $arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']][0];
            unset( $arParams['arFilterRequest']['max'.$arParams['CATALOG_PRICE']] );
        }

        if(
            isset($arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'])
            ||
            isset($arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'])
            ||
            isset($arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'])
        )
        {
            $arSpecial[0] ['LOGIC'] = "OR";
        }
        if( isset($arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SPECIALOFFER_VALUE'] = $arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'];
            unset($arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']);
        }
        if( isset($arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_NEWPRODUCT_VALUE'] = $arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'];
            unset($arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']);
        }
        if( isset($arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SALELEADER_VALUE'] = $arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'];
            unset($arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']);
        }

        // сформируем фильтр по каталогу
        $arFilter = array();
        if( !empty($arParams['arFilterRequest']) )
            $arFilter = $arParams['arFilterRequest'];
        if( !empty($arSpecial) ) $arFilter = array_merge($arSpecial, $arFilter);

        // опредлим текущую секцию в каталоге и запишем в фильтр каталога
        if(!empty($_REQUEST['secid']))
        {
            $arResult['CUR_SECTION_CODE'] = $_REQUEST['secid'];
            $arFilter['SECTION_CODE'] = $arResult['CUR_SECTION_CODE'];
        }
        $arFilter['ACTIVE'] = "Y";
        $arFilter['IBLOCK_ID'] = $_REQUEST['CATALOG_IBLOCK_ID'];
        $arFilter['INCLUDE_SUBSECTIONS'] = "Y";
        // сформируем фильтр для торговых предложений
        if ( !empty($arElementsSearch) )
            $arParams['arOfferRequest']['PROPERTY_CML2_LINK']	= $arElementsSearch;
        if( !empty($arParams['arOfferRequest']) )
        {
            $arParams['arOfferRequest']['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
            $arParams['arOfferRequest']['ACTIVE'] = "Y";
            $arParams['arOfferRequest'][">CATALOG_QUANTITY"] = 0;
            $arSubQuery = $arParams['arOfferRequest'];
            $arFilter['ID'] = CIBlockElement::SubQuery(
                'PROPERTY_CML2_LINK',
                $arSubQuery
            );
        }
        //deb($arFilter);
        // выберем элементы входящие в фильтр и запомним их ID
        $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
        $arElementId = array(); // массив для ID элементов
        while($arElement = $rsElement -> Fetch())
        {
            $arElementId[] = $arElement['ID'];
            foreach( $arProps as $val)
            {
                if( is_array( $arElement[ $val."_VALUE" ] ) )
                    foreach( $arElement[ $val."_VALUE" ] as $subval )
                        $arResult['AVAIL_PROPS2'][ $subval ] = $subval;
                else
                    $arResult['AVAIL_PROPS2'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
            }
        }
        $arFilter = $arParams['arOfferRequest'];
        $arFilter['IBLOCK_ID'] = $_REQUEST['OFFERS_IBLOCK_ID'];
        $arFilter['PROPERTY_CML2_LINK'] = $arElementId;

        //deb($arFilter);
        $rsElement = CIBlockElement::GetList(false, $arFilter, false , false, $arSelect);
        while($arElement = $rsElement -> Fetch())
        {
            foreach( $arProps as $val)
            {
                if( is_array( $arElement[ $val."_VALUE" ] ) )
                    foreach( $arElement[ $val."_VALUE" ] as $subval )
                        $arResult['AVAIL_PROPS2'][ $subval ] = $subval;
                else
                    $arResult['AVAIL_PROPS2'][ $arElement[ $val."_VALUE" ] ] = $arElement[ $val."_VALUE" ];
            }
        }
        //deb($arResult['AVAIL_PROPS2']);
        //echo "<hr>";
        //deb(array_diff($arResult['AVAIL_PROPS'], $arResult['AVAIL_PROPS2']));


        /*
        * состаляем массив с новым состоянием фильтра типа OR
        */
        $arFilterState0 = array();
        if( !empty($arParams['arFilterRequest']) && !empty($arParams['arOfferRequest']) )
        {
            $arResult['AVAIL_PROPS0'] = $arResult['AVAIL_PROPS'];
            $arResult['AVAIL_PROPS'] = $arResult['AVAIL_PROPS2'];

            $key = 0;
            foreach($_REQUEST['arFilterValue'] as $val)
            {
                $arFilterState0[$key] = 0;
                if(is_array($arResult['AVAIL_PROPS0']) && in_array($val, $arResult['AVAIL_PROPS0']))
                {
                    $arFilterState0[$key] = 1;
                }
                $key++;
            }
        }
        $arFilterState = array();
        $key = 0;
        foreach($_REQUEST['arFilterValue'] as $val)
        {
            $arFilterState[$key] = 0;
            if(is_array($arResult['AVAIL_PROPS']) && in_array($val, $arResult['AVAIL_PROPS']))
            {
                $arFilterState[$key] = 1;
            }
            $key++;
        }
        // состаляем массив с новым состоянием фильтра
        $arFilterState2 = array();
        $key = 0;
        foreach($_REQUEST['arFilterValue'] as $val)
        {
            $arFilterState2[$key] = 0;
            if(is_array($arResult['AVAIL_PROPS2']) && in_array($val, $arResult['AVAIL_PROPS2']))
            {
                $arFilterState2[$key] = 1;
            }
            $key++;
        }
        //echo"<hr>";
        //deb($arResult['AVAIL_PROPS2']);
    }

    /*
    * фишка с подпиской
    */
    global $USER;
    $subscrArr = array();
    if ($USER->IsAuthorized()) {
        $isAuthorized = 1;

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL", "SUBSCRIBE" => "Y"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY", "SUBSCRIBE",
                "CAN_BUY", "PRICE", "WEIGHT")
        );
        while ($arItems = $dbBasketItems->Fetch())
        {
            $subscrArr[] = $arItems["PRODUCT_ID"];

        }

    }
    else $isAuthorized = 0;

    /*
    * возврат данных в JSON формате
    */
    return '{"workarea":"'.str_replace(array("\\", "\r","\n","\t",'"'),array("&#092;", "","","","'"), $buffer).'","arFilterState0":'.json_encode($arFilterState0).',"arFilterState":'.json_encode($arFilterState).',"arFilterState2":'.json_encode($arFilterState2).', "minPrice":"'.number_format((int)$minPrice,0,".","").'", "maxPrice":"'.number_format((int)$maxPrice,0,"","").'", "isAuthorized":'.$isAuthorized.', "offersSubsribed": '.json_encode($subscrArr).'}';

}
ob_start("callback");
if((int)$_REQUEST['CATALOG_RESET'] == 1)
{
    if ( $_REQUEST['FASHION_MODE'] == "Y" )
    {
        include($_SERVER['DOCUMENT_ROOT']."/include/catalog/inc.collections.php");
        /*
                    $APPLICATION->IncludeComponent("novagr.shop:fashion.list", ".default", array(
                        "FASHION_IBLOCK_TYPE"				=> $_REQUEST['CATALOG_IBLOCK_TYPE'],
                        "FASHION_IBLOCK_ID"					=> $_REQUEST['CATALOG_IBLOCK_ID'],
                        //"CATALOG_IBLOCK_TYPE"				=> "catalog",
                        //"CATALOG_IBLOCK_ID"					=> "15",
                        //"OFFERS_IBLOCK_TYPE"				=> "offers",
                        //"OFFERS_IBLOCK_ID"					=> "17",
                        "FASHION_ROOT_PATH"					=> $_REQUEST['FASHION_ROOT'],
                        "CATALOG_ROOT_PATH"					=> $_REQUEST['CATALOG_ROOT'],
                        "VENDOR_ROOT_PATH"					=> $_REQUEST['BRAND_ROOT'],
                        "INET_MAGAZ_ADMIN_USER_GROUP_ID"	=> "5",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "2592000"
                        ),
                        false
                    );
        */
    } else {
        include($_SERVER['DOCUMENT_ROOT']."/include/catalog/inc.products.php");
        /*
                    $APPLICATION->IncludeComponent(
                        "novagr.shop:catalog.list",
                        "{$arParams['CATALOG_LIST_TEMPLATE']}",
                        array(
                            "CATALOG_IBLOCK_TYPE"	=> $_REQUEST['CATALOG_IBLOCK_TYPE'],//"catalog",
                            "CATALOG_IBLOCK_ID"		=> $_REQUEST['CATALOG_IBLOCK_ID'],//"15",
                            "OFFERS_IBLOCK_TYPE"	=> $_REQUEST['OFFERS_IBLOCK_TYPE'],//"offers",
                            "OFFERS_IBLOCK_ID"		=> $_REQUEST['OFFERS_IBLOCK_ID'],//"17",
                            "nPageSize"             => $_REQUEST['nPageSize'],
                            "ROOT_PATH"				=> SITE_DIR."catalog/",
                            "BRAND_ROOT"			=> $_REQUEST['BRAND_ROOT'],//"/brand/",
            "USE_SEARCH_STATISTIC" => "Y",
            "SHOW_QUANTINY_NULL" => "N",
            "OPT_GROUP_ID" => 8,
            "OPT_PRICE_ID" => 2,
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "2592000",
            "CACHE_GROUPS" => "N"
                        ),
                    false
                    );
        */
    }
}
ob_end_flush();
?>