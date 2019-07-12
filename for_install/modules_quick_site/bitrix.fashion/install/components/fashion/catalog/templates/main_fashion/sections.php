<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$arDefaultUrlTemplates404 = array(
    "sections" => "",
    "section" => "#SECTION_ID#/",
    "element" => "#SECTION_ID#/#ELEMENT_ID#/",
    "compare" => "compare.php?action=COMPARE",
);

$SEF_URL_TEMPLATES = array(
    "sections" => "",
    "section" => "#SECTION_CODE#/",
    "element" => "#SECTION_CODE#/#ELEMENT_CODE#/",
    "color"   => "#SECTION_CODE#/#ELEMENT_CODE#/#COLOR#/",
    "compare" => "compare.php?action=#ACTION_CODE#",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "SECTION_ID",
    "SECTION_CODE",
    "ELEMENT_ID",
    "ELEMENT_CODE",
    "action",
);

if($arParams["SEF_MODE"] == "Y"){
    $arVariables = array();

    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $SEF_URL_TEMPLATES);
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, array());

    $componentPage = CComponentEngine::ParseComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );
    
    $arResult["VARIABLES"] = $arVariables;
}?>

<?if (isset($arVariables["COLOR"]) && strlen($arVariables["COLOR"]) > 0) {?>

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
        "COLOR" => $arResult["VARIABLES"]["COLOR"]
    ),
    $component
);
?>
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
    $arViewed = array_slice($arViewed, 0, 6);

    $arAdd = array();
    foreach($arViewed as $id => $count){
        $arAdd[] = array('VALUE'=>$id, 'DESCRIPTION'=>$count);
    }

    $_SESSION['ADDED_ELEMENT'][] = $_SESSION['LAST_ELEMENT'];
    CIBlockElement::SetPropertyValuesEx($ElementID, $arParams["IBLOCK_ID"], array('item_viewed' => $arAdd));
}

$_SESSION['LAST_ELEMENT'] = $ElementID;
?>

<?} else {//section list?>

<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section.list",
    "",
    Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"]
    ),
    $component
);
?>

<?if($arParams["SHOW_TOP_ELEMENTS"]!="N"):?>
<hr />
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.top",
    "",
    Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ELEMENT_SORT_FIELD" => $arParams["TOP_ELEMENT_SORT_FIELD"],
        "ELEMENT_SORT_ORDER" => $arParams["TOP_ELEMENT_SORT_ORDER"],
        "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
        "ELEMENT_COUNT" => $arParams["TOP_ELEMENT_COUNT"],
        "LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
        "PROPERTY_CODE" => $arParams["TOP_PROPERTY_CODE"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
        "OFFERS_FIELD_CODE" => $arParams["TOP_OFFERS_FIELD_CODE"],
        "OFFERS_PROPERTY_CODE" => $arParams["TOP_OFFERS_PROPERTY_CODE"],
        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
        "OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
    ),
$component
);?>
<?endif?>

<?}?>