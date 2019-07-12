<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

if(!function_exists('showCollectionItem')) {
    function showCollectionItem($arItem, $product, &$arResult, &$arParams) {
        $isExistOffers = !empty($arItem['OFFERS']);
        ?>
        <div class="rs-collection-item js-element" data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>">
            <div class="rs-collection-item__icons views">
                 <?php if($product['CAN_BUY']): ?>
                 <form class="add2basketform js-buyform<?=$arItem['ID']?>" name="add2basketform">
                     <input type="hidden" name="action" value="ADD2BASKET">
                     <input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$product['ID']?>">
                     <button type="submit" rel="nofollow" class="rs-collection-item__basket-icon submit js-add2basketlink add2basketlink" value="" data-loading-text="..." data-popup=<?=$arParams["RSFLYAWAY_HIDE_BASKET_POPUP"] == "Y"? "N": "Y"?>><i class="fa fa-shopping-cart"></i></button>
                     <a class="inbasket rs-collection-item__basket-icon " href="<?=$arParams['BASKET_URL']?>"><i class="fa fa-shopping-cart"></i></a>
                 </form>
                 <?php endif; ?>
            </div>
            <div class="rs-collection-item__pic">
                <?php
                $strTitle = (
                    isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != ''
                    ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
                    : $arItem['NAME']
                );
                $strAlt = (
                    isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != ''
                    ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]
                    : $arItem['NAME']
                );
                $arImage = null;
                if($isExistOffers && !empty($product['PREVIEW_PICTURE'])) {
                     $arImage = $product['PREVIEW_PICTURE']['RESIZE'];
                } elseif($isExistOffers && !empty($product['PROPERTIES'][$arParams['RSMONOPOLY_PROP_SKU_MORE_PHOTO']]['VALUE'][0])) {
                    $arImage = $product['PROPERTIES'][$arParams['RSMONOPOLY_PROP_SKU_MORE_PHOTO']]['VALUE'][0]['RESIZE'];
                } elseif(!empty($arItem['FIRST_PIC'])) {
                    $arImage = $arItem['FIRST_PIC']['RESIZE'];
                } else {
                    $arImage = $arResult['NO_PHOTO'];
                }
                ?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <img src="<?=$arImage['src']?>" alt="<?=$arImage['ALT']?>" title="<?=$arImage['TITLE']?>">
                </a>
            </div>
            <div class="rs-collection-item__data">
                <div class="rs-collection-item__name">
                    <?php $name = !empty($product['NAME']) ? $product['NAME'] : $arItem['NAME']; ?>
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$name?>">
                        <?=$name?>
                    </a>
                </div>
                <div class="rs-collection-item__prices">
                    <div class="prices__values">
                        <?php if((int) $product['MIN_PRICE']['DISCOUNT_DIFF']): ?>
                            <div class="hidden-xs prices__val prices__val_old"><?=$product['MIN_PRICE']['PRINT_VALUE']?></div>
                            <div class="prices__val prices__val_cool prices__val_new"><?=$product['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php else: ?>
                            <div class="prices__val prices__val_cool"><?=$product['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
<?php if(count($arResult['ITEMS']) > 0): ?>
<div class="rs-collection">
    <h2 class="product-content__title"><?=Loc::getMessage('RS.FLYAWAY.ELEMENTS_OF_COLLECTION');?></h2>

    <div class="owlslider products-owl products-owl-slider products products_showcase">
        <?php
        foreach($arResult['ITEMS'] as $arItem):
            if($arItem['OFFERS'] && count($arItem['OFFERS']) > 0 && $arItem['OFFERS'][0]) {
                $arItemShow = &$arItem['OFFERS'][0];
            } else {
                $arItemShow = &$arItem;
            }
        ?>
        <div
            class="products__item item products__item_wide js-element JS-Compare JS-Toggle js-elementid<?=$arItem['ID']?>"
            data-elementid="<?=$arItem['ID']?>"
            id="<?=$this->GetEditAreaId($arItem["ID"]);?>"
            data-toggle="{'classActive': 'products__item_active'}"
        >

            <div class="products__in">
                <div class="products__pic">
                    <a class="JS-Compare-Label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <?php
                        $strTitle = (
                            isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != ''
                            ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
                            : $arItem['NAME']
                        );
                        $strAlt = (
                            isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != ''
                            ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]
                            : $arItem['NAME']
                        );
                        ?>
                        <?php if (isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src']) != ''): ?>
                            <img class="products__img" src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>">
                        <?php else: ?>
                            <img class="products__img" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>">
                        <?php endif; ?>
                    </a>
                    <div class="marks">
                        <?php if ($arItem['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y'): ?>
                            <span class="marks__item marks__item_action"><?=Loc::getMessage('RS_ACTION_ITEM');?></span>
                        <?php endif; ?>
                        <?php if ($arItem['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y'): ?>
                            <span class="marks__item marks__item_hit"><?=Loc::getMessage('RS_BESTSELLER_ITEM');?></span>
                        <?php endif; ?>
                        <?php if ($arItem['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y'): ?>
                            <span class="marks__item marks__item_new"><?=Loc::getMessage('RS_NEW_ITEM');?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="products__data">
                    <div class="products__name">
                        <a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br>
                    </div>
                    <div class="hidden-xs products__category separator">
                        <?php if ('Y' == $arParams['SHOW_SECTION_URL'] && isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])): ?>
                        <a class="category-label" href="<? echo $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>">
                            <?=$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME'];?>
                        </a>
                        <?php endif; ?>
                        <div class="visible-xs separator"></div>
                    </div>
                    <div class="products__prices">
                        <?php if (count($arItemShow['PRICES']) > 1):?>
                            <?php foreach ($arResult['PRICES'] as $key1 => $titlePrices): ?>
                                <?php if (isset($arItemShow['PRICES'][$key1])): ?>
                                    <div class="prices">
                                        <div class="hidden-xs prices__title"><?=$titlePrices['TITLE']?></div>
                                        <div class="prices__values">
                                            <?php if ($arItemShow['PRICES'][$key1]['DISCOUNT_DIFF'] > 1): ?>
                                                <div class="hidden-xs prices__val prices__val_old"><?=$arItemShow['PRICES'][$key1]['PRINT_VALUE']?></div>
                                                <div class="prices__val prices__val_cool prices__val_new"><?=$arItemShow['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div>
                                            <?php else: ?>
                                                <div class="prices__val prices__val_cool"><?=$arItemShow['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php elseif(isset($arItemShow['MIN_PRICE'])):?>
                            <div class="prices">
                                <div class="hidden-xs prices__title"></div>
                                <div class="prices__values">
                                    <?php if (intval($arItemShow['MIN_PRICE']['DISCOUNT_DIFF']) > 0): ?>
                                        <div class="hidden-xs prices__val prices__val_old"><?=$arItemShow['MIN_PRICE']['PRINT_VALUE']?></div>
                                        <div class="prices__val prices__val_cool prices__val_new"><?=$arItemShow['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                                    <?php else: ?>
                                        <div class="prices__val prices__val_cool"><?=$arItemShow['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <div class="tabs">
        <ul class="nav nav-tabs ">
            <?php $activeSection = null; ?>
            <?php foreach($arResult['SECTIONS'] as $id => $arSection): ?>
                <li class="tabs-item <?php if(!$activeSection) { echo 'active'; $activeSection=$id;}?>">
                    <a class="tabs-item__label" href="#section_<?=$id?>" aria-controls="section_<?=$id?>" data-toggle="tab">
                        <?=$arSection['NAME']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="tab-content">
        <?php
        foreach($arResult['SECTIONS'] as $id => $arSection):
            if(count($arSection) < 1) {
                continue;
            }
        ?>
        <div  role="tabpanel"  class="tab-pane <?=$activeSection == $id ? 'active' : ''?>" id="section_<?=$id?>">
            <?php if(count($arSection['ITEMS']) > 0): ?>
                <div class="rs-collection-items js-collection-items">
                <?php
                foreach($arSection['ITEMS']  as $arItem) {
                    if(!empty($arItem['OFFERS']) && is_array($arItem['OFFERS'])) {
                        foreach($arItem['OFFERS'] as $product) {
                            showCollectionItem($arItem, $product, $arResult, $arParams);
                        }
                    } else {
                        showCollectionItem($arItem, $arItem, $arResult, $arParams);
                    }
                }
                ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
