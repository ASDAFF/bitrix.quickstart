<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($USER->IsAuthorized() && !$_SESSION["fashionIsAuthorized"]) {
    $arParams["CACHE_TIME"] = 0;
    $_SESSION["fashionIsAuthorized"] = 1;
}

if (!$USER->IsAuthorized() && $_SESSION["fashionIsAuthorized"]) {
    $arParams["CACHE_TIME"] = 0;
    $_SESSION["fashionIsAuthorized"] = 0;
}

if (isset($_REQUEST["iblock_submit"]) && strlen($_REQUEST["iblock_submit"]) > 0) {
    $arParams["CACHE_TIME"] = 0;
}?>

<?$ElementID=$APPLICATION->IncludeComponent(
    "bitrix:catalog.element",
    "",
    Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
        "META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
        "LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
        "LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
        "LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
        "LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],
        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
        "OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
        "OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		
		"SETS_IBLOCK_ID" => $arParams["SETS_IBLOCK_ID"],
    ),
    $component
);?>

<?
if(isset($_SESSION['LAST_ELEMENT'])&&is_numeric($_SESSION['LAST_ELEMENT'])&&$_SESSION['LAST_ELEMENT']!=$ElementID){
    $arViewed = array();
    $db_props = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $ElementID, array("sort" => "asc"), Array("CODE"=>"item_viewed"));
    while($ar_props = $db_props->Fetch()){
        if(intval($ar_props['VALUE'])>0&&intval($ar_props['DESCRIPTION'])>0)
            $arViewed[intval($ar_props['VALUE'])] = intval($ar_props['DESCRIPTION']);
    }
    
    if(!array_key_exists($_SESSION['LAST_ELEMENT'], $arViewed)){
        $arViewed[$_SESSION['LAST_ELEMENT']] = 1;
    }else{
        $arViewed[$_SESSION['LAST_ELEMENT']]++;
    }
    
    arsort($arViewed);
    $arViewed = array_slice($arViewed, 0, 6, true);
    
    $arAdd = array();
    foreach($arViewed as $id => $count){
        $arAdd[] = array('VALUE'=>$id, 'DESCRIPTION'=>$count);
    }
    
    CIBlockElement::SetPropertyValuesEx($ElementID, $arParams["IBLOCK_ID"], array('item_viewed' => $arAdd));
}

$_SESSION['LAST_ELEMENT'] = $ElementID;
?>

<?if($arParams["USE_ALSO_BUY"] == "Y" && IsModuleInstalled("sale") && $ElementID):?>

<?$APPLICATION->IncludeComponent("bitrix:sale.recommended.products", ".default", array(
    "ID" => $ElementID,
    "MIN_BUYES" => $arParams["ALSO_BUY_MIN_BUYES"],
    "ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
    "LINE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
    "DETAIL_URL" => $arParams["DETAIL_URL"],
    "BASKET_URL" => $arParams["BASKET_URL"],
    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "PRICE_CODE" => $arParams["PRICE_CODE"],
    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
    ),
    $component
);

?>
<?endif?>
