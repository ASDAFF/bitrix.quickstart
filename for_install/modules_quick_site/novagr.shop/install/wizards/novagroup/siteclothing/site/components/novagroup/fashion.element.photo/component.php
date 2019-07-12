<?
    if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

    if( CModule::IncludeModule("iblock") ) {
    } else {
        die(GetMessage("MODULES_NOT_INSTALLED"));
    }

    $arSelect = Array(
        "ID", 
        "NAME", 
        "PROPERTY_PHOTOS",
    );
    if($arParams["CATALOG_IBLOCK_ID"]>0)
    {
        $arFilter = Array(
            "IBLOCK_ID"=>IntVal($arParams["CATALOG_IBLOCK_ID"]), 
            "ID"=>intval($arParams["CATALOG_ELEMENT_ID"])
        );  
    }  else {
        $arFilter = Array(
            "ID"=>intval($arParams["CATALOG_ELEMENT_ID"])
        );   
    }

    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    $photos = array();
    while($ob = $res->GetNext())
    {
        $arResult["ELEMENT"] =  $ob;

        if(is_array($ob["PROPERTY_PHOTOS_VALUE"]))
        foreach($ob["PROPERTY_PHOTOS_VALUE"] as $photo)
        {
            $photos[] = $photo;
        }
    }

    $arResult['PHOTOS'] = $photos;
    $arResult['PHOTO'] = SITE_TEMPLATE_PATH."/images/nophoto.png";;

    foreach($photos as $photo)
    {
        $arResult['PHOTO'] = $photo; break;
    }

    $this -> IncludeComponentTemplate();
?>