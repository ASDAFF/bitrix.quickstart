<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$arrIDs = array();
if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0) {
	foreach($arResult["ITEMS"] as $arItem) {
		$arrIDs[$arItem["ELEMENT_ID"]] = "Y";
	}
	?><script>rsFlyaway_FAVORITE = <?=json_encode($arrIDs)?>;</script><?php
}

global $favoriteFilter;
$favoriteFilter = array();
if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
	foreach($arResult['ITEMS'] as $arItem) {
		$favoriteFilter['ID'][] = $arItem['ELEMENT_ID'];
	}
} else {
    $favoriteFilter['ID'][] = 0;
}

?>
    <?php if($arParams['SHOW_POPUP'] == "Y"): ?>

        <div class="dropdown-favorite loss-menu-right" id="dropdown_favorite">
        <?php
        $APPLICATION->IncludeComponent("bitrix:catalog.section",
            "favorite_popup",
            array(
                "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "SECTION_USER_FIELDS" => array(
                    0 => "",
                    1 => "",
                ),
                "ELEMENT_SORT_FIELD" => "sort",
                "ELEMENT_SORT_ORDER" => "asc",
                "ELEMENT_SORT_FIELD2" => "id",
                "ELEMENT_SORT_ORDER2" => "desc",
                "FILTER_NAME" => "favoriteFilter",
                "INCLUDE_SUBSECTIONS" => "Y",
                "SHOW_ALL_WO_SECTION" => "Y",
                "HIDE_NOT_AVAILABLE" => "N",
                "PAGE_ELEMENT_COUNT" => "30",
                "LINE_ELEMENT_COUNT" => "3",
                "PROPERTY_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_LIMIT" => "5",
                "SECTION_URL" => "",
                "DETAIL_URL" => "",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SEF_MODE" => "N",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "SET_TITLE" => "Y",
                "SET_BROWSER_TITLE" => "Y",
                "BROWSER_TITLE" => "-",
                "SET_META_KEYWORDS" => "Y",
                "META_KEYWORDS" => "-",
                "SET_META_DESCRIPTION" => "Y",
                "META_DESCRIPTION" => "-",
                "SET_LAST_MODIFIED" => "N",
                "USE_MAIN_ELEMENT_SECTION" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "CACHE_FILTER" => "N",
                "ACTION_VARIABLE" => "favorite_action",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRICE_CODE" => $arParams['PRICE_CODE'],
                "USE_PRICE_COUNT" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "PRICE_VAT_INCLUDE" => "Y",
                "CONVERT_CURRENCY" => "Y",
                "BASKET_URL" => "/personal/cart/",
                "USE_PRODUCT_QUANTITY" => "N",
                "PRODUCT_QUANTITY_VARIABLE" => "",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRODUCT_PROPERTIES" => "",
                "PAGER_TEMPLATE" => "flyaway",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => "Товары",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_FIELD2" => "id",
                "RSFLYAWAY_USE_FAVORITE" => "Y",
                "CURRENCY_ID" => "RUB",
                "SIDEBAR" => "Y",
                "SEF_RULE" => "",
                "SECTION_CODE_PATH" => "",
                "PAGER_BASE_LINK" => "",
                "PAGER_PARAMS_NAME" => "arrPager",
                "OFFERS_FIELD_CODE" => array(
                    0 => "ID",
                    1 => "",
                ),
                "OFFERS_PROPERTY_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_SORT_ORDER2" => "desc",
                "RSFLYAWAY_PROP_SKU_ARTICLE" => "-",
                "OFFERS_CART_PROPERTIES" => "",
                "PATH_TO_FAVORITE" => $arParams['PATH_TO_FAVORITE']
            ),
            false,
            array(
            "ACTIVE_COMPONENT" => "Y"
            )
        );
        ?>
        </div>
    <?php endif; ?>
