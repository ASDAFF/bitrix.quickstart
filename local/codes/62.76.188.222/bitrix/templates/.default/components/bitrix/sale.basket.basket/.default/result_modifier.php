<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("iblock");

foreach(array("DelDelCanBuy", "AnDelCanBuy") as $key)
    foreach($arResult["ITEMS"][$key] as &$arBasketItem){

        $pic = false;
        $ar_res = CIBlockElement::GetByID($arBasketItem["PRODUCT_ID"])->GetNext();
        if($ar_res['PREVIEW_PICTURE'])
            $pic = $ar_res['PREVIEW_PICTURE'];
        elseif($ar_res['DETAIL_PICTURE'])
            $pic = $ar_res['DETAIL_PICTURE'];  
        
        if($pic)
            $arBasketItem['PICTURE'] = CFile::ResizeImageGet($pic, array('width'=>100, 'height'=>100), BX_RESIZE_IMAGE_PROPORTIONAL, true);   


        $db_props = CIBlockElement::GetProperty($ar_res['IBLOCK_ID'], 
                $ar_res['ID'],
                array("sort" => "asc"),
                Array("CODE"=>"SHOP"));

        while($ar_props = $db_props->Fetch()) 
            if($ar_props["VALUE_XML_ID"])
               $arBasketItem['SHOP'][] = $ar_props;
        
         
        // для столбца "Стоимость" 
        $arBasketItem['COST'] = CurrencyFormat( $arBasketItem['PRICE'] * $arBasketItem["QUANTITY"],  $arBasketItem["CURRENCY"]);


    }