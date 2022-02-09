<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="b-catalog-list">
    <?
    $n = 0;
    foreach ($arResult['FIRST_LEVEL_SECTIONS'] as $section) {
        $n++;
        if ($n == 1) {
            ?>    
            <div class="b-catalog-list__line clearfix">
    <? } ?> 
            <div class="b-catalog-list_item m-catalog__section <? if ($n == 3) { ?> m-3n<? } ?>">
                <div class="b-catalog-section__title">
                    <div class="b-compare-added-list__link"><a href="<?= $section['SECTION_PAGE_URL'] ?>"><?= $section['NAME'] ?></a></div>
                </div>
                <div class="b-catalog-section__wrapper clearfix">
                    <div class="b-catalog-section-menu">
                        <?
                        $j = 0;
                        foreach ($arResult['SECTIONS'] as $sect) {
                            if ($sect["IBLOCK_SECTION_ID"] != $section['ID'])
                                continue;
                            if ($j++ >= 6)
                                continue;
                            ?>
                            <div class="b-catalog-section-menu__item"><a href="<?= $sect['SECTION_PAGE_URL'] ?>" title="<?= $sect['NAME'] ?>"><?= $sect['NAME'] ?></a><i class="b-sidebar-menu__line m-catalog-section__line"></i></div>
                        <? } ?>
                        <? if ($j >= 6) { ?>
                            <div class="b-catalog-section-menu__item m-catalog-section-menu__last"><a href="<?= $section['SECTION_PAGE_URL'] ?>">Все разделы</a></div>
                    <? } ?>
                    </div>
    <? if ($arResult['ITEMS'][$section['ID']]) { 
                    $APPLICATION->IncludeComponent(
                                "bitrix:catalog.element",
                                "mini",
                                Array(
                                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                        "ELEMENT_ID" => $arResult['ITEMS'][$section['ID']]['ID'],
                                        "ELEMENT_CODE" => "",
                                        "SECTION_ID" => "",
                                        "SECTION_CODE" => "",
                                        "SECTION_URL" => "",
                                        "DETAIL_URL" => "",
                                        "BASKET_URL" => "/personal/basket.php",
                                        "ACTION_VARIABLE" => "action",
                                        "PRODUCT_ID_VARIABLE" => "id",
                                        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                                        "PRODUCT_PROPS_VARIABLE" => "prop",
                                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                                        "META_KEYWORDS" => "-", 
                                        "META_DESCRIPTION" => "-",
                                        "BROWSER_TITLE" => "-",
                                        "SET_TITLE" => "N",
                                        "SET_STATUS_404" => "N",
                                        "ADD_SECTIONS_CHAIN" => "N",
                                        "PROPERTY_CODE" => array(),
                                        "OFFERS_LIMIT" => "0",
                                        "PRICE_CODE" => array("BASE", "PRICE"),
                                        "USE_PRICE_COUNT" => "N",
                                        "SHOW_PRICE_COUNT" => "1",
                                        "PRICE_VAT_INCLUDE" => "Y",
                                        "PRICE_VAT_SHOW_VALUE" => "N",
                                        "PRODUCT_PROPERTIES" => array(),
                                        "USE_PRODUCT_QUANTITY" => "N",
                                        "LINK_IBLOCK_TYPE" => "",
                                        "LINK_IBLOCK_ID" => "",
                                        "LINK_PROPERTY_SID" => "",
                                        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
                                        "CACHE_TYPE" => "A",
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_GROUPS" => "N",
                                        "USE_ELEMENT_COUNTER" => "N",
                                        "CONVERT_CURRENCY" => "N"
                                )
                        ); 
                    
                    
              } ?>
                </div>
            </div>
        <? if ($n == 3) { ?>
            </div>
            <?
            $n = 0;
        }
    }
    if ($n != 3) {
        ?></div><? } ?> 
</div>