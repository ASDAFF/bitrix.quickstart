<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {

    if ($arParams['IS_AJAX'] == "Y") {
        $this->SetViewTarget("products");
    }

	if ($arParams['IS_AJAX'] != 'Y' || ($arParams['IS_AJAX'] == 'Y' && $arParams['AJAX_ONLY_ELEMENTS'] != 'Y')) {
    ?>
		<!-- showcase -->
		<div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?> <?
		if ($arParams['RSFLYAWAY_PROP_OFF_POPUP'] != 'N') {
			?>products_compact <?
        }
		?>" <?
		?>id="<?=$arParams['AJAX_ID_ELEMENTS']?>"><?
    }

    foreach ($arResult['ITEMS'] as $key1 => $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strDelete, $arDeleteParams);
        $strMainID = $this->GetEditAreaId($arItem['ID']);
        
        if (empty($arItem['OFFERS'])){ $HAVE_OFFERS = false; $PRODUCT = &$arItem; } else { $HAVE_OFFERS = true; $PRODUCT = &$arItem['OFFERS'][0]; }

        //TIMER
        $arTimers = array();
        if ($arItem['HAVE_DA2'] == 'Y') {
            if (isset($arItem['DAYSARTICLE2']) ) {
                $arTimers[] = $arItem['DAYSARTICLE2'];
            } elseif ($HAVE_OFFERS) {
                foreach ($arItem['OFFERS'] as $arOffer) {
                    if (isset($arOffer['DAYSARTICLE2'])) {
                        $arTimers[] = $arOffer['DAYSARTICLE2'];
                    }
                }
            }
        } elseif( $arItem['HAVE_QB'] == 'Y' ) {
            if (isset($arItem['QUICKBUY'])) {
                $arTimers[] = $arItem['QUICKBUY'];
            } elseif($HAVE_OFFERS) {
                foreach($arItem['OFFERS'] as $arOffer) {
                    if (isset($arOffer['QUICKBUY'])) {
                        $arTimers[] = $arOffer['QUICKBUY'];
                    }
                }
            }
        }

        $IS_PREVIEW_TEXT = 'N';
        if (isset($arItem['PREVIEW_TEXT']) && $arItem['PREVIEW_TEXT'] != ''):
            $IS_PREVIEW_TEXT = 'Y';
        endif;
        
        $sItemClass = 'view-case products__item col';
        if ($arParams['PARAM_VIEW_MOB'] != 'Y') {
            $sItemClass .= ' half';
        }
        if ($IS_PREVIEW_TEXT == 'Y') {
            $sItemClass .= ' products__item_wide';
        }
        if (isset($arItem['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) {
            $sItemClass .= ' da2';
        }
        if (isset($arItem['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) {
            $sItemClass .= ' qb';
        }
        $sItemClass .= ' js-element js-elementid'.$arItem['ID'].' js-compare js-toggle';
        ?>
        <div class="<?=$sItemClass?>" id="<?=$strMainID?>" <?
            ?>data-elementid="<?=$arItem['ID']?>" <?
            ?>data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>" <?
            ?>data-toggle="{'classActive': 'products__item_active', 'onevent': 'mouseover focus active', 'unevent': 'mouseout'}" <?
        ?>><?

            ?><div class="row"><?
                ?><div class="col col-md-12"><?
                    ?><div class="products__in"><?
                        // PICTURE
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
                        ?><div class="products__pic"><?
                            ?><a class="js-compare-label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
                                if (isset($arItem['FIRST_PIC'][0])):
                                    ?><img class="products__img js-preview" src="<?=$arItem['FIRST_PIC'][0]['RESIZE'][0]['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
                                else:
                                    ?><img class="products__img js-preview" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>" /><?
                                endif;
                            ?></a><?

                            ?><div class="stickers"><?
                                ?><div class="da2_icon hidden-xs"><?=GetMessage('DA2_ICON_TITLE')?></div><?
                                ?><div class="qb_icon hidden-xs"><?=GetMessage('QB_ICON_TITLE')?></div><?
                                if ($arItem['OUT_PRICE']['DISCOUNT_DIFF'] > 0) {
                                    ?><div class="discount_icon hidden-xs"><?='-'.$arItem['OUT_PRICE']['DISCOUNT_DIFF_PERCENT'].'%'?></div><?
                                }
                            ?></div><?

                            ?><div class="marks"><?
                                if ($arItem['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y'):
                                    ?><span class="marks__item marks__item_action"><?=Loc::getMessage('RS_ACTION_ITEM');?></span><?
                                endif;

                                if ($arItem['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y'):
                                    ?><span class="marks__item marks__item_hit"><?=Loc::getMessage('RS_BESTSELLER_ITEM');?></span><?
                                endif;

                                if ($arItem['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y'):
                                    ?><span class="marks__item marks__item_new"><?=Loc::getMessage('RS_NEW_ITEM');?></span><?
                                endif;
                            ?></div>

                            <? if ($arParams['DISPLAY_COMPARE'] == 'Y' && $arParams['PARAM_VIEW_MOB'] == 'Y'): ?>
                            <div class="icons visible-xs">
                                <div class="icons__item">
                                    <div class="loss-menu-right loss-menu-right_bottom views js-compare-box">
                                        <a class="selected js-compare-switcher" href="<?=$arItem['COMPARE_URL']?>">
                                            <i class="fa fa-align-left"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php if(!$HAVE_OFFERS): ?>
                                <div class="icons__item">
                                    <div class="loss-menu-right loss-menu-right_bottom views js-basket-box">
                                        <a class="selected" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                            <i class="fa fa-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <? endif; ?>

                            <?//Timer
                            if (!empty($arTimers)) {
                                $timer = $arTimers['0'];
                                $KY = 'TIMER';
                                if (isset($timer['DINAMICA_EX'])) {
                                    $KY = 'DINAMICA_EX';
                                }
                                $jsTimer = array(
                                    'DATE_FROM' => $timer[$KY]['DATE_FROM'],
                                    'DATE_TO' => $timer[$KY]['DATE_TO'],
                                    'AUTO_RENEWAL' => $timer['AUTO_RENEWAL'],
                                );
                                if (isset($arTimer['DINAMICA'])) {
                                    $jsTimer['DINAMICA_DATA'] = $arTimer['DINAMICA'] == 'custom' ? array_flip(unserialize($arTimer['DINAMICA_DATA'])) : $arTimer['DINAMICA'];
                                }
                                ?><span class="hidden-xs products-counter"><?
                                    ?><span class="counter js-timer" data-timer='<?=json_encode($jsTimer)?>'><?
                                        ?><span class="timer<?if ($timer['QUANTITY'] <= 0) {?> timer_simple<?}?>"><?
                                            ?><span class="timer__item"><?
                                                ?><span class="timer__item__digit days">0</span><?
                                                ?><span class="timer__item__label"><?=GetMessage('QB_AND_DA2_DAY')?></span><?
                                            ?></span><?
                                            ?><span class="timer__item"><?
                                                    if ($timer[$KY]['HOUR'] > 0 && $timer[$KY]['HOUR'] < 10) {
                                                        $timer[$KY]['HOUR'] = '0'.$timer[$KY]['HOUR'];
                                                    }
                                                ?><span class="timer__item__digit hour">0</span><?
                                                ?><span class="timer__item__label"><?=GetMessage('QB_AND_DA2_HOUR')?></span><?
                                            ?></span><?
                                            ?><span class="timer__item"><?
                                                    if ($timer[$KY]['MINUTE'] > 0 && $timer[$KY]['MINUTE'] < 10) {
                                                        $timer[$KY]['MINUTE'] = '0'.$timer[$KY]['MINUTE'];
                                                    }
                                                    ?><span class="timer__item__digit minute">0</span><?
                                                    ?><span class="timer__item__label"><?=GetMessage('QB_AND_DA2_MIN')?></span><?
                                            ?></span><?
                                            ?><span class="timer__item" style="display:none;"><?
                                                ?><span class="timer__item__digit second">0</span><?
                                                ?><span class="timer__item__label"><?=GetMessage('QB_AND_DA2_SEC')?></span><?
                                            ?></span><?
                                            ?><span class="timer__item"><?
                                                ?><span class="timer__item__digit"><?echo ($timer['QUANTITY'] > 99) ? '99+' : sprintf('%02d', $timer['QUANTITY']);?></span><?
                                                ?><span class="timer__item__label"><?=GetMessage('QB_AND_DA2_SHT')?></span><?
                                            ?></span><?
                                    ?></span><?
                                    ?><span class="progress-bar"><?
                                            if ($KY == 'DINAMICA_EX') {
                                                ?><span class="progress-bar__indicator" style="width:50%;"></span><?
                                            } else {
                                                ?><span class="progress-bar__indicator progress-bar__indicator_cheap" style="width:56%;"></span><?
                                            }
                                    ?></span><?
                                ?></span><?
                            ?></span><?
                            }
                            //-/Timer?>
                        </div>

                        <div class="products__data"><?
                            // NAME
                            ?><div class="products__name"><?
                                ?><a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br><?
                            ?></div><?

                            ?><div class="hidden-xs products__category separator"><?
                            if ($arParams['SHOW_SECTION_URL'] == 'Y' && isset($arResult['SECTIONS'][$arItem['~IBLOCK_SECTION_ID']])):
                                ?><a class="category-label" href="<?=$arResult['SECTIONS'][$arItem['~IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>"><?
                                    echo $arResult['SECTIONS'][$arItem['~IBLOCK_SECTION_ID']]['NAME'];
                                ?></a><?
                            endif;
                            ?></div><?

                            ?><div class="visible-xs separator"></div><?

                            if ($IS_PREVIEW_TEXT == 'Y'):
                                ?><div class="products__description"><?=$arItem['PREVIEW_TEXT']?></div><?
                            endif;

                            // PRICES
                            ?><div class="products__prices"><?
                                if (count($PRODUCT['PRICES']) > 1) {
                                    foreach ($arResult['PRICES'] as $key1 => $titlePrices) {
                                        if (isset($PRODUCT['PRICES'][$key1])) {
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
                                        }
                                    }
                                } else {
                                    if (isset($PRODUCT['MIN_PRICE'])) {

                                        if (
                                            !empty($PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE']]['VALUE']) &&
                                            !empty($PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']]['VALUE'])
                                        ) {
                                            ?><div class="row"><?
                                                ?><div class="col col-xs-6"><?
                                                
                                                    ?><div class="prices"><?
                                                        ?><div class="hidden-xs prices__title"><?=Loc::getMessage('RS.FLYAWAY.PRICE_PER_UNIT', array('#UNIT#' => $PRODUCT['CATALOG_MEASURE_NAME']))?></div><?
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
                                                ?><div class="col col-xs-6"><?
                                                    ?><div class="prices"><?
                                                        $measureRation = $PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO']]['VALUE'];
                                                        $measureValue = $PRODUCT['MIN_PRICE']['VALUE'] * $measureRation;
                                                        $measureFormatValue = FormatCurrency($measureValue, $PRODUCT['MIN_PRICE']['CURRENCY']);
                                                        ?><div class="hidden-xs prices__title"><?=Loc::getMessage('RS.FLYAWAY.PRICE_PER_UNIT', array('#UNIT#' => $PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ADDITIONAL_MEASURE']]['VALUE']))?></div><?
                                                        ?><div class="prices__values"><?
                                                            if (IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0) {
                                                                ?><div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div><?
                                                                ?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
                                                            } else {
                                                                ?><div class="prices__val prices__val_cool"><?=$measureFormatValue?></div><?
                                                            }
                                                        ?></div><?
                                                    ?></div><?
                                                    
                                                ?></div><?
                                            ?></div><?
                                        } else {                                            
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
                                        }
                                    }
                                }
                            ?></div><?
                            // PROPERTIES
                            if ($arParams['RSFLYAWAY_PROP_OFF_POPUP'] == 'N' && is_array($arItem['OFFERS_EXT']['PROPERTIES']) && count($arItem['OFFERS_EXT']['PROPERTIES'])>0) {

                                ?><div class="rs_sku hidden-xs hidden-sm js-sku_props clearfix"><?
                                     foreach ($arItem['OFFERS_EXT']['PROPERTIES'] as $propCode => $arProperty) {

                                         $isColor = false;
                                         if (
                                             is_array($arParams['OFFER_TREE_COLOR_PROPS'][$PRODUCT['IBLOCK_ID']]) &&
                                             in_array($propCode,$arParams['OFFER_TREE_COLOR_PROPS'][$PRODUCT['IBLOCK_ID']])
                                          ) {
                                             $isColor = true;
                                         }

                                         if($isColor) {

                                              ?><div style="display: block"<?
                                                   ?>class="rs_sku-prop js-sku_prop"<?
                                                   ?>data-code="<?=$propCode?>"<?
                                                   ?>data-type="color"<?
                                              ?>><?

                                                  ?><i class="rs_sku-prop_name rs_sku-prop_name_color"><?=$arItem['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </i><?
                                                  ?><ul class="rs_sku-options"><?
                                                      $firstVal = false;

                                                      foreach ($arProperty as $value => $arValue) {
                                                          ?><li <?
                                                          ?>class="rs_sku-option js-sku_option<?
                                                             if ('Y' == $arValue['FIRST_OFFER']) {
                                                                 ?> checked<?
                                                                 $firstVal = $arValue;
                                                             } elseif ('Y' == $arValue['DISABLED_FOR_FIRST']) {
                                                                 ?> disabled<?
                                                             }
                                                          ?>" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>"><?

                                                              if (isset($arValue['PICT']) && !empty($arValue['PICT']['SRC'])) {
                                                                 ?><a class="rs_sku-val" href="javascript:;"><?
                                                                     ?><div class="rs_sku-icon" style="background-image:url('<?=$arValue['PICT']['SRC']?>')" title="<?=$ar['VALUE'];?>"></div><?
                                                                 ?></a><?
                                                              }

                                                          ?></li><?
                                                      }

                                                  ?></ul><?
                                              ?></div><?
                                         } else {

                                             ?><div <?
                                                  ?>class="loss-menu-right rs_sku-prop  js-sku_prop"<?
                                                  ?>data-code="<?=$propCode?>"<?
                                                  ?>data-type="list"<?
                                             ?>><?
                                                 ?><i class="rs_sku-prop_name rs_sku-prop_name"><?=$arItem['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </i><?
                                                 ?><div class="dropdown dropdown_wide rs_select js_select views"><?
                                                     ?><ul class="dropdown-menu list-unstyled rs_select-options" role="menu" aria-labelledby="dropdownMenuOutput"><?

                                                         $firstVal = false;
                                                         foreach ($arProperty as $value => $arValue) {
                                                             ?><li class="views-item rs_select-option js-sku_option<?
                                                                 if ('Y' == $arValue['FIRST_OFFER']) {
                                                                       ?> checked<?
                                                                       $firstVal = $arValue;
                                                                   }
                                                                   elseif ('Y' == $arValue['DISABLED_FOR_FIRST']) {
                                                                       ?> disabled<?
                                                                   }
                                                                 ?>" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>"><?

                                                                 ?><a class="rs_select-val" href="javascript:;"><?=$arValue['VALUE']?></a><?
                                                         }

                                                     ?></ul><?

                                                     if (is_array($firstVal)) {
                                                         ?><button class="btn btn-default dropdown-toggle rs_select-checked"<?
                                                                 ?>id="dropdownMenuOutput"<?
                                                                 ?>type="button" <?
                                                                 ?>data-toggle="dropdown" <?
                                                                 ?>aria-expanded="true" ><?
                                                             ?><span class="rs_icon-arr_sd"></span><?
                                                             ?><span class="rs_select-val js_select-val"><?=$firstVal['VALUE']?></span><?
                                                             ?><i class="fa fa-angle-down icon-angle-down"></i><?
                                                             ?><i class="fa fa-angle-up icon-angle-up"></i><?
                                                         ?></button><?
                                                     }

                                                 ?></div><?

                                             ?></div><?
                                         }

                                    }
                                 ?></div><?
                            }

                            ?><div class="products__detail"><?
                                ?><div class="row clearfix buttons-section"><?
                                    // ADD2BASKET
                                    ?><div class="hidden-xs">

                                            <!--noindex-->
                                                <form class="add2basketform js-buyform<?=$arItem['ID']?><?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?><?if($arParams['USE_PRODUCT_QUANTITY'] == "Y"):?> usequantity<?endif;?>" name="add2basketform"><?
                                                    ?><input type="hidden" name="action" value="ADD2BASKET"><?
                                                    ?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?
                                                    if($HAVE_OFFERS && $arParams['RSFLYAWAY_PROP_OFF_POPUP'] != 'N') {
                                                        ?><div class="pull-right"><?
                                                            ?><a class="btn btn-default btn2" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('RS.FLYAWAY.BTN_MORE')?></a><?
                                                        ?></div><?
                                                    } else {
                                                        ?><div class="pull-left quantity-block"><?
                                                            ?><div class="loss-menu-right loss-menu-right_last"><?
                                                                ?><div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}"><?
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
                                                        ?></div><?

                                                        ?><div class="pull-right"><?
                                                            ?><button class="btn btn-default btn2 products-button js-add2basketlink add2basketlink" data-loading-text="<?=GetMessage('RS.FLYAWAY.ADDING2BASKET')?>" data-popup=<?=$arParams["RSFLYAWAY_HIDE_BASKET_POPUP"] == "Y"? "N": "Y"?> type="submit" rel="nofollow" value=""><?=GetMessage('RS.FLYAWAY.BTN_BUY')?></button><?
                                                            ?><a class="btn btn-default btn2 products-button inbasket"  href="<?=$arParams['BASKET_URL']?>"><?=GetMessage('RS.FLYAWAY.BTN_GO2BASKET')?></a><?
                                                            ?><a class="btn btn-default btn2 products-button js-morebtn buybtn" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('RS.FLYAWAY.BTN_MORE')?></a><?
                                                        ?></div><?
                                                    }
                                                ?></form>
                                            <!--/noindex-->
                                    </div><?
                                ?></div><?

                                ?><div class="row clearfix quantity-wrapper"><?
                                    ?><div class="col col-xs-4 col-left"><?
                                        ?><span class="identifer"><?
                                            $itemArticle = null;
                                            if($HAVE_OFFERS && !empty($PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']])) {
                                                $itemArticle = $PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']]['VALUE'];
                                            } elseif(!empty($arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']])) {
                                                $itemArticle = $arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']]['VALUE'];
                                            }
                                            if (!empty($itemArticle)):
                                                echo Loc::getMessage('RS.FLYAWAY.ARTICLE').': <span class="js-article">'.$itemArticle.'</span>';
                                            endif;
                                        ?></span><?
                                    ?></div>
                                    
                                    <div class="col col-xs-8 col-right text-right pull-right">
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

                                        if ($arParams['USE_STORE'] == 'Y') {

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
                                        }
                                        ?>
                                    </div>
                                </div><?

                                ?><div class="row clearfix compare-wrapper"><?
                                    // COMPARE
                                    ?><div class="col col-xs-6 col-left"><?
                                        if ($arParams['DISPLAY_COMPARE'] == 'Y'):
                                            ?><span class="icon-east js-compare-box"><?
                                                ?><a class="js-compare-switcher js-toggle-switcher" href="<?=$arItem['COMPARE_URL']?>"><?
                                                    ?><i class="fa fa-align-left"></i><?
                                                    ?><span><?=Loc::getMessage('RS.FLYAWAY.COMPARE')?></span><?
                                                    ?><span class="icon-east__label"><?=Loc::getMessage('RS.FLYAWAY.IN_COMPARE')?></span><?
                                                ?></a><?
                                                ?><span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_COMPARE')?></span><?
                                                ?><span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_COMPARE')?></span><?
                                            ?></span><?
                                        endif;
                                    ?></div><?

                                    // FAVORITE
                                    ?><div class="col col-xs-6 col-right text-right"><?
                                        if ($arParams['RSFLYAWAY_USE_FAVORITE'] == "Y"):
                                            ?><span class="icon-east js-favorite js-favorite-heart"
                                                data-elementid = "<?=$arItem['ID']?>"
                                                data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>"
                                                ><?
                                                ?><a href="javascript:;"><?
                                                    ?><i class="fa fa-heart"></i><?
                                                    ?><span class="hidden-sm"><?=Loc::getMessage('RS.FLYAWAY.FAVORITE')?></span><?
                                                    ?><span class="icon-east__label hidden-sm"><?=Loc::getMessage('RS.FLYAWAY.IN_FAVORITE')?></span><?
                                                ?></a><?
                                                ?><span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_FAVORITE')?></span><?
                                                ?><span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_FAVORITE')?></span><?
                                            ?></span><?
                                        endif;
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
        ?></div><!-- showcase --><?
    }

	if ($arParams['IS_AJAX'] == 'Y') {
        $this->EndViewTarget();
        $cssId = ($arParams['AJAX_ONLY_ELEMENTS'] == "Y" ? $arParams["AJAX_ID_ELEMENTS"] : $arParams["AJAX_ID_SECTION"]);
		$templateData[$cssId] = $APPLICATION->GetViewContent('products');
    }

    $this->SetViewTarget('ajaxpages');

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
                        ?>data-loading-text=<?=Loc::getMessage('AJAXPAGES_LOADING');?><?
                    ?>><span><?=Loc::getMessage('AJAXPAGES_LOAD_MORE')?></span></a><?
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

	?><div class="js-no-products"><div class="alert alert-info" role="alert"><?=Loc::getMessage('RS.FLYAWAY.NO_PRODUCTS')?></div></div><?
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
        echo $APPLICATION->GetViewContent('products');
    }
}
