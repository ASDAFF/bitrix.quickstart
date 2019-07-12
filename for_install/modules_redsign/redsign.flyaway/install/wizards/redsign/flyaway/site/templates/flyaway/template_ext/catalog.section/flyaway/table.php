<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {

    if ($arParams['IS_AJAX'] == "Y") {
        $this->SetViewTarget("products");
    }

    if ($arParams['IS_AJAX'] != 'Y' || ($arParams['IS_AJAX'] == 'Y' && $arParams['AJAX_ONLY_ELEMENTS'] != 'Y')) {
        if(!empty($arParams['CONTENT_TITLE'])) {
            ?><h2 class="product-content__title"><?=$arParams['CONTENT_TITLE']?></h2><?
        }
	    ?><div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?>" id="<?=$arParams['AJAX_ID_ELEMENTS']?>"><?
    }

    foreach ($arResult['ITEMS'] as $key1 => $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strDelete, $arDeleteParams);
        $strMainID = $this->GetEditAreaId($arItem['ID']);
        
        if (empty($arItem['OFFERS'])){ $HAVE_OFFERS = false; $PRODUCT = &$arItem; } else { $HAVE_OFFERS = true; $PRODUCT = &$arItem['OFFERS'][0]; }

        ?><div class="view-table products__item js-element js-elementid<?=$arItem['ID']?> js-compare col col-xs-12" <?
            ?>data-elementid="<?=$arItem['ID']?>" <?
            ?>id="<?=$strMainID?>"<?
            ?>data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>" <?
            ?>data-elementid="<?=$arItem['ID']?>" <?
        ?>><?
            ?><div class="row"><?
                ?><div class="col col-md-12"><?
                    ?><div class="products__in"><?
                        ?><div class="row"><?

                            // picture
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
                            ?><div class="col col-xs-4 col-sm-2 col-md-2"><?
                                ?><div class="products__col products__pic text-center"><?
                                    ?><a class="js-compare-label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
                                        if (isset($arItem['FIRST_PIC'][0])):
                                            ?><img class="products__img js-preview" src="<?=$arItem['FIRST_PIC'][0]['RESIZE'][0]['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
                                        else:
                                            ?><img class="products__img js-preview" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>" /><?
                                        endif;
                                    ?></a><?
                                ?></div><?
                            ?></div><?

                            // other
                            ?><div class="col col-xs-8 col-sm-10 col-md-10"><?
                                ?><div class="products__data"><?
                                    ?><div class="row"><?
                                        ?><div class="col <?
                                        ?>col-sm-7 col-md-6 col-lg-6 <?
                                        ?>products__col<?
                                        ?>"><?
                                            ?><div class="products__name"><?
                                                ?><a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br /><?
                                            ?></div><?

                                            if ($arItem['PREVIEW_TEXT'] != ''):
                                                ?><div class="products__description hidden-xs"><?=$arItem['PREVIEW_TEXT']?></div><?
                                            endif;

                                            ?><div class="products-foot hidden-xs"><?
                                                ?><div class="row"><?
                                                    ?><div class="col col-xs-12"><?
                                                        $itemArticle = null;
                                                        if($HAVE_OFFERS && !empty($PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']])) {
                                                            $itemArticle = $PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']]['VALUE'];
                                                        } elseif(!empty($arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']])) {
                                                            $itemArticle = $arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']]['VALUE'];
                                                        }
                                                        if (!empty($itemArticle)):
                                                            ?><span class="products-foot__item"><?
                                                                ?><span class="identifer text-nowrap"><?
                                                                    ?><?=Loc::getMessage('RS.FLYAWAY.ARTICLE')?>: <span class="js-article"><?=$itemArticle?></span><?
                                                                ?></span><?
                                                            ?></span><?
                                                        endif;

                                                        if ($arParams['USE_STORE'] == 'Y') {
                                                            ?><span class="products-foot__item">
                                                            <?php
                                                            if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {
                                                            
                                                                if ($HAVE_OFFERS) {
                                                            
                                                                    $APPLICATION->includeComponent(
                                                                        'bitrix:catalog.product.subscribe',
                                                                        'flyaway',
                                                                        array(
                                                                            'PRODUCT_ID' => $arItem['ID'],
                                                                            'BUTTON_ID' => $strMainID.'_subscribe',
                                                                            'BUTTON_CLASS' => 'bx_bt_button bx_medium',
                                                                            'DEFAULT_DISPLAY' => !$PRODUCT['CAN_BUY'],
                                                                        ),
                                                                        $component,
                                                                        array('HIDE_ICONS' => 'Y')
                                                                    );
                                                            
                                                                } else {
                                                            
                                                                    if (!$arItem['CAN_BUY']) {
                                                                        $APPLICATION->includeComponent(
                                                                            'bitrix:catalog.product.subscribe',
                                                                            'flyaway',
                                                                            array(
                                                                                'PRODUCT_ID' => $arItem['ID'],
                                                                                'BUTTON_ID' => $strMainID.'_subscribe',
                                                                                'BUTTON_CLASS' => 'bx_bt_button bx_medium',
                                                                                'DEFAULT_DISPLAY' => true,
                                                                            ),
                                                                            $component,
                                                                            array('HIDE_ICONS' => 'Y')
                                                                        );
                                                                    }
                                                            
                                                                }
                                                            }
                                                            
                                                            $APPLICATION->IncludeComponent(
                                                                'bitrix:catalog.store.amount',
                                                                'catalog',
                                                                array(
                                                                    "ELEMENT_ID" => $arItem["ID"],
                                                                    "STORE_PATH" => $arParams["STORE_PATH"],
                                                                    "CACHE_TYPE" => "A",
                                                                    "CACHE_TIME" => "36000",
                                                                    "MAIN_TITLE" => $arParams["MAIN_TITLE"],
                                                                    "USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
                                                                    "SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
                                                                    "USE_MIN_AMOUNT" => "N",
                                                                    "FLYAWAY_USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                                                                    "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                                                                    "SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
                                                                    "SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                                                                    "USER_FIELDS" => $arParams['USER_FIELDS'],
                                                                    "FIELDS" => $arParams['STORES_FIELDS'],
                                                                    // flyaway
                                                                    'DATA_QUANTITY' => $arItem['DATA_QUANTITY'],
                                                                    'FIRST_ELEMENT_ID' => $PRODUCT['ID'],
                                                                    'CATALOG_SUBSCRIBE' => $arItem['CATALOG_SUBSCRIBE'],
                                                                ),
                                                                $component,
                                                                array('HIDE_ICONS'=>'Y')
                                                            );
                                                            ?></span><?
                                                        }

                                                        if ($arParams['DISPLAY_COMPARE'] == 'Y'):
                                                            ?><span class="products-foot__item"><?
                                                                ?><span class="icon-east js-compare-box"><?
                                                                    ?><a class="js-compare-switcher" href="<?=$arItem['COMPARE_URL']?>"><?
                                                                        ?><i class="fa fa-align-left"></i><?
                                                                        ?><span class="hidden-when-need-early"><?=Loc::getMessage('RS.FLYAWAY.COMPARE')?></span><?
                                                                        ?><span class="icon-east__label hidden-when-need-early"><?=Loc::getMessage('RS.FLYAWAY.IN_COMPARE')?></span><?
                                                                    ?></a><?
                                                                    ?><span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_COMPARE')?></span><?
                                                                    ?><span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_COMPARE')?></span><?
                                                                ?></span><?
                                                            ?></span><?
                                                        endif;

                                                        if ($arParams['RSFLYAWAY_USE_FAVORITE'] == "Y"):
                                                            ?><span class="products-foot__item"><?
                                                                ?><span class="icon-east js-favorite js-favorite-heart"
                                                                    data-elementid = "<?=$arItem['ID']?>"
                                                                    data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>"
                                                                    ><?
                                                                    ?><a href="javascript:;"><?
                                                                        ?><i class="fa fa-heart"></i><?
                                                                        ?><span class="hidden-when-need-early"><?=Loc::getMessage('RS.FLYAWAY.FAVORITE')?></span><?
                                                                        ?><span class="icon-east__label hidden-when-need-early"><?=Loc::getMessage('RS.FLYAWAY.IN_FAVORITE')?></span><?
                                                                    ?></a><?
                                                                    ?><span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_FAVORITE')?></span><?
                                                                    ?><span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_FAVORITE')?></span><?
                                                                ?></span><?
                                                            ?></span><?
                                                        endif;

                                                    ?></div><?
                                                ?></div><?
                                            ?></div><?
                                        ?></div><?

                                        ?><div class="col <?
                                        ?>col-sm-2 col-md-3 col-lg-4 <?
                                        ?>text-center <?
                                        ?>products__col <?
                                        ?>products__col_last <?
                                        ?>products__col-prices<?
                                        ?>"><?
                                            // PRICES
                                            ?><div class="products__prices"><?
                                                if (count($PRODUCT['PRICES']) > 1) {
                                                    foreach ($arResult['PRICES'] as $key1 => $titlePrices) {
                                                        if (isset($PRODUCT['PRICES'][$key1])) {
                                                            ?><div class="products__prices-item <?php if ($PRODUCT['PRICES'][$key1]['DISCOUNT_DIFF'] > 0) echo " __discount"?>"><?
                                                                ?><div class="prices"><?
                                                                    ?><div class="hidden-xs prices__title"><?=$titlePrices['TITLE']?></div><?
                                                                    ?><div class="prices__values"><?
                                                                        if ($PRODUCT['PRICES'][$key1]['DISCOUNT_DIFF'] > 1) {
                                                                            ?><div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['PRICES'][$key1]['PRINT_VALUE']?></div><?
                                                                            ?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div><?
                                                                        } else {
                                                                            ?><div class="prices__val prices__val_cool"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div><?
                                                                        }
                                                                    ?></div><?
                                                                ?></div><?
                                                            ?></div><?
                                                        }
                                                    }
                                                } else {
                                                    if (isset($PRODUCT['MIN_PRICE'])) {
                                                        ?><div class="products__prices-item <?php if (IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0) echo " __discount"?>"><?
                                                            ?><div class="prices"><?
                                                                ?><div class="hidden-xs prices__title"></div><?
                                                                ?><div class="prices__values"><?
                                                                    if (IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0) {
                                                                        ?><div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div><?
                                                                        ?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
                                                                    } else {
                                                                        ?><div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
                                                                    }
                                                                ?></div><?
                                                            ?></div><?
                                                        ?></div><?
                                                    }
                                                }
                                            ?></div><?

                                        ?></div><?

                                        ?><div class="col <?
                                        ?>col-sm-3 col-md-3 col-lg-2 <?
                                        ?>text-center text-left-xs <?
                                        ?>hidden-xs <?
                                        ?>products__col <?
                                        ?>products__col_last<?
                                        ?>"><?
                                            ?><div class="products-box"><?
                                                if ($HAVE_OFFERS) {
                        ?><a class="btn btn-default btn2 products-button" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
                          ?><?=Loc::getMessage('RS.FLYAWAY.BTN_MORE')?><?
                        ?></a><?
                                                } else {
                                                    ?><noindex><?
                                                        ?><form class="add2basketform js-buyform<?=$arItem['ID']?><?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?><?if($arParams['USE_PRODUCT_QUANTITY'] == "Y"):?> usequantity<?endif;?>" name="add2basketform"><?
                                                            ?><input type="hidden" name="action" value="ADD2BASKET"><?
                                                            ?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?

                                                            ?><div class="loss-menu-right quantity-block"><?
                                                                ?><div class="dropdown dropdown_digit select js-select" data-select="{'classUndisabled':'select-btn_undisabled'}"><?
                                                                    ?><div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button"><?
                                                                        ?><input class="select-input js-select-input js-quantity" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO'];?>" type="text" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" name="quantity" autocomplete="off" /><?
                                                                        ?><span class="select-unit"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
                                                                        ?><i class="fa fa-angle-down hidden-xs icon-angle-down select-icon"></i><?
                                                                        ?><i class="fa fa-angle-up hidden-xs icon-angle-up select-icon"></i><?
                                                                    ?></div><?
                                                                    ?><ul class="dropdown-menu list-unstyled select-menu" role="menu" aria-labelledby="dLabel"><?
                                                                        for ($i = 1; $i < 10; $i++) {
                                                                            ?><li><a class="js-select-label" href="javascript:;"><?=$PRODUCT['CATALOG_MEASURE_RATIO']*$i;?></a></li><?
                                                                        }
                                                                        ?><li><a class="js-select-labelmore" href="javascript:;"><?echo $PRODUCT['CATALOG_MEASURE_RATIO']*10;?>+</a></li><?
                                                                    ?></ul><?
                                                                ?></div><?
                                                            ?></div><?

                                                            ?><div class="loss-menu-right loss-menu-right_last views"><?
                                                                ?><button class="selected js-add2basketlink add2basketlink" type="submit" rel="nofollow" value="" data-loading-text="..." title="<?=GetMessage('RS.FLYAWAY.BTN_BUY')?>" data-popup=<?=$arParams["RSFLYAWAY_HIDE_BASKET_POPUP"] == "Y"? "N": "Y"?>><i class="fa fa-shopping-cart"></i></button><?
                                                            ?></div><?
                                                            ?><div class="loss-menu-right loss-menu-right_last views active"><?
                                                                ?><a class="selected inbasket"  href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage('RS.FLYAWAY.BTN_GO2BASKET')?>"><i class="fa fa-shopping-cart"></i></a><?
                                                            ?></div><?
                                                            ?><div class="hidden loss-menu-right loss-menu-right_last views"><?
                                                                ?><a class="selected js-morebtn" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=GetMessage('RS.FLYAWAY.BTN_MORE')?>"><i class="fa fa-search"></i></a><?
                                                            ?></div><?
                                                        ?></form><?
                                                    ?></noindex><?
                                                }
                                            ?></div><?

                                        ?></div><?
                                    ?></div><?
                                ?></div><?
                            ?></div><?
                        ?></div><?

                    ?></div><?
                ?></div><?
            ?></div><?
        ?></div><?
    }

    if ($arParams['IS_AJAX'] != 'Y' || ($arParams['IS_AJAX'] == 'Y' && $arParams['AJAX_ONLY_ELEMENTS'] != 'Y')) {
        ?></div><!-- tableview --><?
    }

	if ($arParams['IS_AJAX'] == 'Y') {
        $this->EndViewTarget();
        $cssId = ($arParams['AJAX_ONLY_ELEMENTS'] == "Y" ? $arParams["AJAX_ID_ELEMENTS"] : $arParams["AJAX_ID_SECTION"]);
		$templateData[$cssId] = $APPLICATION->GetViewContent('products');
    }

	?><div class="row"><?
		if (IntVal($arResult['NAV_RESULT']->NavPageNomer) < IntVal($arResult['NAV_RESULT']->NavPageCount)) {
            ?><div class="col col-xs-10 visible-xs"><?
                ?><div class="ajaxpages <?=($arParams['USE_AUTO_AJAXPAGES'] == 'Y' ? 'auto' : '')?>"><?
                    ?><a class="btn btn-default btn-button btn-button_wide" rel="nofollow" href="#" <?
                        ?>data-ajaxurl="<?=$arResult['AJAXPAGE_URL']?>" <?
                        ?>data-ajaxpagesid="<?=$arParams['AJAX_ID_ELEMENTS']?>" <?
                        ?>data-navpagenomer="<?=($arResult['NAV_RESULT']->NavPageNomer)?>" <?
                        ?>data-navpagecount="<?=($arResult['NAV_RESULT']->NavPageCount)?>" <?
                        ?>data-navnum="<?=($arResult['NAV_RESULT']->NavNum)?>"<?
                    ?>><i class="animashka"></i><span><?=Loc::getMessage('AJAXPAGES_LOAD_MORE')?></span></a><?
                ?></div><?
            ?></div><?
        }
		?><div class="col col-xs-2 visible-xs"><?
			?><div class="loss-menu-right loss-menu-right_top views js-top"><a class="selected" href="#"><i class="fa fa-arrow-up"></i></a></div><?
		?></div><?
	?></div><?

    $this->EndViewTarget();
    $templateData['ajaxpages'] = $APPLICATION->GetViewContent('ajaxpages');

    if ($arParams['DISPLAY_BOTTOM_PAGER'] == 'Y') {
        $this->SetViewTarget('paginator');
        echo $arResult['NAV_STRING'];
        $this->EndViewTarget();
        $templateData['paginator'] = $APPLICATION->GetViewContent('paginator');
    }

} elseif ($arParams['SHOW_ERROR_EMPTY_ITEMS'] == 'Y') {

    $this->SetViewTarget("products");

	?><div class="col col-md-12 js-no-products"><div class="alert alert-info" role="alert"><?=Loc::getMessage('RS.FLYAWAY.NO_PRODUCTS')?></div></div><?
    ?><div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?> <?
    if ($arParams['RSFLYAWAY_PROP_OFF_POPUP'] != 'N') {
        ?>products_compact <?
    }
    ?>" <?
    ?>id="<?=$arParams['AJAX_ID_ELEMENTS']?>"><?
    ?></div><?
    $this->EndViewTarget("products");

    if ($arParams['IS_AJAX'] == "Y") {
        $cssId = $arParams["AJAX_ID_SECTION"];
		$templateData[$cssId] = $APPLICATION->GetViewContent('products');
    } else {
        $APPLICATION->ShowViewContent('products');
    }
}
