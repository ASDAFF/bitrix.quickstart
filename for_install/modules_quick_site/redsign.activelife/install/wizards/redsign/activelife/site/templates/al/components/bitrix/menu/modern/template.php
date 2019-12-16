<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!function_exists('showSubItems')) {
    function showSubItems($parentItem, &$arParams, $isRoot = false) {
        global $APPLICATION;

        if(empty($parentItem['SUB_ITEMS']) || count($parentItem['SUB_ITEMS']) <= 0) return;

        ?>
        <div class="modern-menu__toggle-submenu js-mm__toggle-submenu"></div>
        <div class="modern-menu__subitems js-mm__subitems">
            <?php foreach($parentItem['SUB_ITEMS'] as $index=>$arItem): ?>
                <div class="modern-menu__item js-mm__item" data-index=<?=$index?>>
                    <a href="<?=$arItem['LINK']?>">
                        <?=$arItem['TEXT'].($arParams['COUNT_ELEMENTS'] && $arItem['ELEMENT_CNT'] > 0 ? '&nbsp;('.$arItem['ELEMENT_CNT'].')' : '')?>
                    </a>
                    <?php showSubItems($arItem, $arParams); ?>
                </div>
            <?php endforeach; ?>
            <?php
            if($isRoot):
                if($parentItem['PARAMS']['ELEMENT'] == 'Y'){
                    $APPLICATION->IncludeComponent(
                        "bitrix:catalog.element",
                        "in_menu",
                        array(
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "PROPERTY_CODE" => array(
                                0 => "",
                                1 => "",
                            ),
                            "META_KEYWORDS" => "-",
                            "META_DESCRIPTION" => "-",
                            "BROWSER_TITLE" => "-",
                            "BASKET_URL" => "",
                            "ACTION_VARIABLE" => "ppc",
                            "PRODUCT_ID_VARIABLE" => "",
                            "SECTION_ID_VARIABLE" => "",
                            "PRODUCT_QUANTITY_VARIABLE" => "",
                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                            "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                            "SET_TITLE" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "SET_STATUS_404" => "N",
                            "PRICE_CODE" => array($arParams['PRICE_CODE']),

                            "USE_PRICE_COUNT" => "N",
                            "SHOW_PRICE_COUNT" => "",
                            "PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
                            "PRICE_VAT_SHOW_VALUE" => "N",
                            "USE_PRODUCT_QUANTITY" => "Y",
                            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                            "OFFERS_FIELD_CODE" => array(0 => 'ID'),
                            "OFFERS_PROPERTY_CODE" => array(
                            0 => "SKU_MORE_PHOTO",
                            ),
                            "OFFERS_SORT_FIELD" => "catalog_PRICE_".$arParams["SKU_PRICE_SORT_ID"],
                            "OFFERS_SORT_ORDER" => "ASC",
                            "OFFERS_LIMIT" => "0",
                            "ELEMENT_ID" => $parentItem["PARAMS"]["ELEMENT_ID"],
                            "ELEMENT_CODE" => "",
                            "SECTION_ID" => "",
                            "SECTION_CODE" => "",
                            "SECTION_URL" => "",
                            "DETAIL_URL" => "",
                            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                            "USE_ELEMENT_COUNTER" => "N",
                            "USE_COMPARE" => "N",
                            "COMPARE_URL" => "",
                            "COMPARE_NAME" => "",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADDITIONAL_PICT_PROP" => $arParams["ADDITIONAL_PICT_PROP"],
                            "OFFER_ADDITIONAL_PICT_PROP" => $arParams["OFFER_ADDITIONAL_PICT_PROP"],
                            "HIDE_NOT_AVAILABLE" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "SET_META_KEYWORDS" => "N",
                            "SET_META_DESCRIPTION" => "N",
                            "ADD_ELEMENT_CHAIN" => "N",
                            "ADD_PROPERTIES_TO_BASKET" => "Y",
                            "PRODUCT_PROPS_VARIABLE" => "prop",
                            "PARTIAL_PRODUCT_PROPERTIES" => "N",
                            "PRODUCT_PROPERTIES" => array(
                            ),
                            "LINK_IBLOCK_TYPE" => "",
                            "LINK_IBLOCK_ID" => "",
                            "LINK_PROPERTY_SID" => "",
                            "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#"
                        ),
                        false
                    );
                }
            endif;
            ?>
        </div>
        <?php
    }
}

?>

<div class="modern-menu js-modern-menu">

    <a href="#" onclick="return false;" class="modern-menu__toggle js-mm__toggle-button">
        <?=Loc::getMessage('RS_SLINE.BM_MODERN.CATALOGUE');?>
    </a>

    <div class="modern-menu__items js-mm__items">
        <?php foreach($arResult as $rootItem): ?>
            <div class="modern-menu__root-item js-mm__root-item">
                <a href="<?=$rootItem['LINK']?>"><?=$rootItem['TEXT']?></a>
                <?php showSubItems($rootItem, $arParams, true); ?>
            </div>
        <?php endforeach; ?>
            <div class="modern-menu__more-btn js-mm__more-btn">
                <a href="javascript:void(0);" onclick="return false;">&#149; &#149; &#149;</a>
                <div class="modern-menu__more-container js-mm__more-container">
                </div>
            </div>
    </div>
</div>
