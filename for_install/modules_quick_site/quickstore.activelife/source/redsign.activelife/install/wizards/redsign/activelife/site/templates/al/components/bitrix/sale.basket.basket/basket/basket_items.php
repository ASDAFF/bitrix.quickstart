<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */

use \Bitrix\Sale\DiscountCouponsManager;
use \Bitrix\Main\Localization\Loc;

if (!empty($arResult['ERROR_MESSAGE'])) {
    ShowError($arResult['ERROR_MESSAGE']);
}
$bPropsColumn = false;
$bDelayColumn = false;
$bDeleteColumn = false;
$bDiscountColumn = false;
$bWeightColumn = false;
$bPriceColumn = false;
$bSumColumn = false;
$bArticleColumn = false;

$arHeadersHide = array('TYPE', 'PROPS', 'DELAY', 'DELETE', 'DISCOUNT', 'WEIGHT'); // some values are not shown in the columns in this template\

if ($normalCount > 0):
?>
<?php
foreach ($arResult['GRID']['HEADERS'] as $id => $arHeader)
{
    $arHeader['name'] = (isset($arHeader['name']) ? (string) $arHeader['name'] : '');
    if ($arHeader['name'] == '') {
        $arResult['GRID']['HEADERS'][$id]['name'] = $arHeader['name'] = getMessage('SALE_'.$arHeader['id']);
    }
    $arBasketJSParams['HEADERS'][$id] = $arHeader['name'];
    if (strpos($arHeader['id'], 'PROPERTY_') !== false) {
        $arHeaders[] = substr($arHeader['id'], 0, -6);
    } else {
        $arHeaders[] = $arHeader['id'];
    }

    if ($arHeader['id'] == 'PROPS') {
        $bPropsColumn = true;
    } elseif ($arHeader['id'] == 'DELAY') {
        $bDelayColumn = true;
    } elseif ($arHeader['id'] == 'DELETE') {
        $bDeleteColumn = true;
    } elseif ($arHeader['id'] == 'DISCOUNT') {
        $bDiscountColumn = true;
    } elseif ($arHeader['id'] == 'WEIGHT') {
        $bWeightColumn = true;
    } elseif ($arHeader['id'] == 'PRICE') {
        $bPriceColumn = true;
    } elseif ($arHeader['id'] == 'SUM') {
        $bSumColumn = true;
    } else {
        if (is_array($arParams['ARTICLE_PROP'])) {
            foreach ($arParams['ARTICLE_PROP'] as $sPropCode) {
                if ($arHeader['id'] == 'PROPERTY_'.$sPropCode.'_VALUE') {
                    $arHeadersHide[] = $arHeader['id'];
                    $bArticleColumn  = true;
                }
            }
        }
    }
}
?>
<div class="panel clearfix" id="basket_items_list">
    <div class="panel__head">
        <svg class="panel__icon icon-cart icon-svg"><use xlink:href="#svg-cart"></use></svg>
        <?=getMessage('SALE_BASKET_ITEMS')?>
    </div>
    <div class="panel__body">
        <table id="basket_items" class="table table_items">
            <?php foreach ($arResult['GRID']['ROWS'] as $arItem): ?>
                <?php if ($arItem['DELAY'] == 'N' && $arItem['CAN_BUY'] == 'Y'): ?>
                    <tr class="table_item product" id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem['PRODUCT_ID']?>" data-cart-item-id="<?=$arItem['ID']?>">
                    <?php foreach ($arResult['GRID']['HEADERS'] as $arHeader): ?>
                        <?php
                        if (in_array($arHeader['id'], $arHeadersHide)) {
                                continue;
                        }
                        ?>

                        <?php if ($arHeader['id'] == 'NAME'): ?>
                            <td class="table_item__item">
                                <div class="table_item__pic">
                                    <?php
                                    if (isset($arItem['FIRST_PIC'][0])) {
                                        $sPictureSrc = $arItem['FIRST_PIC'][0]['RESIZE']['small']['src'];
                                    } elseif (strlen($arItem['DETAIL_PICTURE_SRC']) > 0) {
                                        $sPictureSrc = $arItem['DETAIL_PICTURE_SRC'];
                                    } else {
                                        $sPictureSrc = SITE_TEMPLATE_PATH.'/assets/img/noimg.png';
                                    }
                                    ?>
                                    <?php if (strlen($arItem['DETAIL_PAGE_URL']) > 0): ?>
                                        <a href="<?=$arItem['DETAIL_PAGE_URL'] ?>">
                                            <img class="table_item__img" src="<?=$sPictureSrc?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>">
                                        </a>
                                    <?php  else: ?>
                                        <img class="table_item__img" src="<?=$sPictureSrc?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>">
                                    <?php endif; ?>
                                </div>
                                <div class="table_item__head">
                                    <h4 class="table_item__name">
                                        <?php if (strlen($arItem['DETAIL_PAGE_URL']) > 0): ?>
                                            <a href="<?=$arItem['DETAIL_PAGE_URL'] ?>"><?=$arItem['NAME']?></a>
                                        <?php else: ?>
                                            <?=$arItem['NAME']?>
                                        <?php endif; ?>
                                    </h4>

                                    <?php if ($bArticleColumn && isset($arItem['PROPERTY_'.$arParams['ARTICLE_PROP'][$arItem['IBLOCK_ID']].'_VALUE'])): ?>
                                        <div class="product__article">
                                        <?php
                                        echo Loc::getMessage('RS_SLINE.BSBB_BASKET.ITEM_ARTICLE').': '.
                                             $arItem['PROPERTY_'.$arParams['ARTICLE_PROP'][$arItem['IBLOCK_ID']].'_VALUE']
                                        ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                                <dl class="table_item__props dl-list">
                                    <?php $arHeadersHideExt = array_merge($arHeadersHide, array('NAME', 'QUANTITY', 'PRICE', 'SUM')); ?>
                                    <?php foreach ($arResult['GRID']['HEADERS'] as $id => $arHeader1): ?>
                                        <?php
                                        if (in_array($arHeader1['id'], $arHeadersHideExt)) {
                                            continue;
                                        }
                                        ?>

                                        <?php if (!empty($arItem[$arHeader1['id']])): ?>
                                            <dt><?=(strlen($arHeader1['name']) > 0) ? $arHeader1['name'] : getMessage('SALE_'.$arHeader1['id'])?>:</dt>
                                            <dd><?=(is_array($arItem[$arHeader1['id']]) ? $arItem[$arHeader1['id']]['TEXT'] : $arItem[$arHeader1['id']])?></dd>
                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                    <?php if ($bWeightColumn && $arItem['WEIGHT']): ?>
                                        <dt><?=getMessage('SALE_WEIGHT')?>:</dt>
                                        <dd><?=$arItem['WEIGHT_FORMATED']?></dd>
                                    <?php endif; ?>

                                    <?php if ($bPropsColumn): ?>
                                        <?php foreach ($arItem['PROPS'] as $val): ?>
                                            <?php
                                            if (is_array($arItem['SKU_DATA'])) {
                                                $bSkip = false;
                                                foreach ($arItem['SKU_DATA'] as $arProp) {
                                                    if ($arProp['CODE'] == $val['CODE']) {
                                                        $bSkip = true;
                                                        break;
                                                    }
                                                }
                                                if ($bSkip) {
                                                    continue;
                                                }
                                            }
                                            ?>
                                            <dt><?=$val['NAME']?>:</dt>
                                            <dd><?=(is_array($val['VALUE']) ? $val['VALUE']['TEXT'] : $val['VALUE'])?></dd>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </dl>

                                <?php if (is_array($arItem['SKU_DATA'])): ?>
                                    <div class="table_item__offer_props">
                                        <?php foreach ($arItem['SKU_DATA'] as $propId => $arProp): ?>
                                            <?php
                                            $bIsColor = $bIsBtn = false;
                                            $sOfferPropClass= 'offer_prop';
                                            if (in_array($arProp['CODE'], $arParams['OFFER_TREE_COLOR_PROPS'])) {
                                                $bIsColor = true;
                                                $sOfferPropClass .= ' offer_prop-color';
                                            } elseif (in_array($arProp['CODE'], $arParams['OFFER_TREE_BTN_PROPS'])) {
                                                $bIsBtn = true;
                                                $sOfferPropClass .= ' offer_prop-btn';
                                            } else {
                                                foreach ($arProp['VALUES'] as $id => $arVal) {
                                                    if (isset($arVal['PICT']) && !empty($arVal['PICT'])) {
                                                        $bIsColor = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>

                                            <?php if ($bIsColor || $bIsBtn): ?>
                                                <div class="<?=$sOfferPropClass?> js-offer_prop" data-code="<?=$arProp['CODE']?>">
                                                    <div class="offer_prop__name"><?=$arProp['NAME']?>:</div>
                                                    <ul class="offer_prop__values clearfix" id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>">
                                                        <?php
                                                        foreach ($arProp['VALUES'] as $arValue):
                                                            $sOfferPropValueClass = 'offer_prop__value';
                                                            foreach ($arItem['PROPS'] as $arItemProp) {
                                                                if ($arItemProp['CODE'] == $arItem['SKU_DATA'][$propId]['CODE']) {
                                                                    if ($arItemProp['VALUE'] == $arValue['NAME'] || $arItemProp['VALUE'] == $arValue['XML_ID']) {
                                                                        $sOfferPropValueClass .= ' checked';
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        ?>
                                                            <li
                                                                class="<?=$sOfferPropValueClass?>"
                                                                data-value-id="<?=(isset($arValue['XML_ID']) ? $arValue['XML_ID'] : htmlspecialcharsbx($arValue['NAME']))?>"
                                                                data-element="<?=$arItem["ID"]?>"
                                                                data-property="<?=$arProp["CODE"]?>"
                                                            >
                                                                <?php if ($bIsColor): ?>
                                                                    <?php
                                                                    $sOfferPropIcon = isset($arValue['PICT']) && !empty($arValue['PICT'])
                                                                        ? 'background-image:url('.$arValue['PICT']['SRC'].')'
                                                                        : 'background-color:'.$arResult['COLORS_TABLE'][ToUpper($arValue['NAME'])]['RGB'];
                                                                    ?>
                                                                        <span class="offer_prop__icon">
                                                                            <span class="offer_prop__img" title="<?=$arValue['NAME']?>" style="<?=$sOfferPropIcon?>"></span>
                                                                        </span>
                                                                <?php else: ?>
                                                                    <?=$arValue['NAME']?>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php else: ?>
                                                <?php $dropdownId = $this->getEditAreaId('offer_prop_'.$arItem['PRODUCT_ID'].'_'.$arProp['ID']) ?>
                                                <div class="offer_prop js-offer_prop" data-code="<?=$arProp['CODE']?>">
                                                    <div class="offer_prop__name"><?=$arProp['NAME']?>:</div>
                                                    <div class="dropdown select">
                                                        <ul class="offer_prop__values dropdown-menu" aria-labelledby="<?=$dropdownId?>" id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>">
                                                            <?php
                                                            foreach ($arProp['VALUES'] as $arValue):
                                                                foreach ($arItem['PROPS'] as $arItemProp) {
                                                                    if ($arItemProp['CODE'] == $arItem['SKU_DATA'][$propId]['CODE']) {
                                                                        if ($arItemProp['VALUE'] == $arValue['NAME'] || $arItemProp['VALUE'] == $arValue['XML_ID']):
                                                                        ?>
                                                                            <li class="offer_prop__value checked" data-value="<?=htmlspecialcharsbx($arValue['NAME'])?>">
                                                                                <a href="#"><?=$arValue['NAME']?></a>
                                                                                <?php ob_start(); ?>
                                                                                    <span class="offer_prop__checked"><?=$arValue['NAME']?></span>
                                                                                <?php $sOfferPropChecked = ob_get_clean();?>
                                                                            </li>
                                                                        <?php
                                                                            continue 2;
                                                                        endif;
                                                                    }
                                                                }
                                                            ?>

                                                            <li
                                                                class="offer_prop__value"
                                                                data-value-id="<?=(isset($arValue['XML_ID']) ? $arValue['XML_ID'] : htmlspecialcharsbx($arValue['NAME']))?>"
                                                                data-element="<?=$arItem["ID"]?>"
                                                                data-property="<?=$arProp["CODE"]?>"
                                                            >
                                                                <a href="#"><?=$arValue['NAME']?></a>
                                                            </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <div class="dropdown-toggle select__btn" id="<?=$dropdownId?>" data-toggle="dropdown" aria-expanded="true" aria-haspopup="true" role="button">
                                                            <svg class="select__icon icon-svg"><use xlink:href="#svg-down-round"></use></svg><?=$sOfferPropChecked?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                        <?php elseif ($arHeader['id'] == 'QUANTITY'): ?>
                            <td class="table_item__quantity">
                                <?php
                                $useFloatQuantity = ($arParams['QUANTITY_FLOAT'] == 'Y') ? true : false;
                                $useFloatQuantityJS = ($useFloatQuantity ? 'true' : 'false');
                                $ratio = isset($arItem['MEASURE_RATIO']) ? $arItem['MEASURE_RATIO'] : 1;
                                ?>
                                <?/*
                                <span>
                                    <?=getMessage('SALE_QUANTITY')?>
                                    :
                                </span>
                                */?>
                                <span class="quantity">
                                    <i class="quantity__minus js-basket-minus"></i><input
                                        class="quantity__input js-quantity"
                                        type="number"
                                        size="3"
                                        id="QUANTITY_INPUT_<?=$arItem['ID']?>"
                                        name="QUANTITY_INPUT_<?=$arItem['ID'] ?>"
                                        min="0"
                                        <?php if(isset($arItem['AVAILABLE_QUANTITY']) && intval($arItem['AVAILABLE_QUANTITY']) > 0): ?>
                                            max="<?=$arItem['AVAILABLE_QUANTITY']?>"
                                        <?php endif; ?>
                                        step="<?=$ratio?>"
                                        value="<?=$arItem['QUANTITY']?>"
                                        onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem['ID']?>', '<?=$arItem['ID']?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)"
                                    /><i class="quantity__plus js-basket-plus"></i>
                                    <input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem['QUANTITY']?>">
                                </span>
                                <?php if (isset($arItem['MEASURE_TEXT'])): ?>
                                    <span class="js-measure"><?=$arItem['MEASURE_TEXT']?></span>
                                <?php endif; ?>
                            </td>
                        <?php elseif ($arHeader["id"] == "PRICE"): ?>
                            <td class="table_item__price price">
                                <div>
                                    <div id="current_price_<?=$arItem['ID']?>" class="price__pdv"><?=$arItem['PRICE_FORMATED']?></div>
                                    <?php if ($arItem['DISCOUNT_PRICE_PERCENT'] > 0): ?>
                                        <div class="price__pv" id="old_price_<?=$arItem["ID"]?>"><?
                                            if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0) {
                                                echo $arItem["FULL_PRICE_FORMATED"];
                                            }
                                        ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($bDiscountColumn && $arItem['DISCOUNT_PRICE_PERCENT'] > 0): ?>
                                     <div id="discount_value_<?=$arItem['ID']?>" class="price__pdd">
                                        <?=getMessage('SALE_DISCOUNT')?>: <?=$arItem['DISCOUNT_PRICE_PERCENT_FORMATED']?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php elseif ($arHeader["id"] == "SUM"): ?>
                            <td class="table_item__sum price">
                                <span class="table_item__title"><?=Loc::getMessage('SALE_SUM')?>:</span>
                                <span class="price__pdv" id="sum_<?=$arItem["ID"]?>"><?=$arItem[$arHeader["id"]]?></span>
                            </td>
                        <?php endif;?>
                    <?php endforeach; ?>

                    <?php if ($bDelayColumn || $bDeleteColumn): ?>
                        <td class="table_item__total">
                            <?php if ($bDeleteColumn): ?>
                                <a class="table_item__link" href="<?=str_replace('#ID#', $arItem['ID'], $arUrls['delete'])?>">
                                    <svg class="btn__icon icon-close icon-svg"><use xlink:href="#svg-close"></use></svg><?=getMessage('SALE_DELETE')?>
                                </a>
                            <?php endif; ?>
                            <?php if ($bDelayColumn): ?>
                                <a class="table_item__link" href="<?=str_replace('#ID#', $arItem['ID'], $arUrls['delay'])?>">
                                    <svg class="btn__icon icon-lock icon-svg"><use xlink:href="#svg-lock"></use></svg><?=getMessage('SALE_DELAY')?>
                                </a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
        <table class="cart__total table">
            <tr>
                <td><b><?=getMessage('SALE_TOTAL')?> (<?=$normalCount.'&nbsp;'.getMessage('RS_SLINE.BSBB_BASKET.ITEM').RSDevFunc::BasketEndWord($normalCount)?>):</b> </td>
                <td>
                    <span class="allSum_FORMATED price__pdv"><?=str_replace(' ', '&nbsp;', $arResult['allSum_FORMATED'])?></span>
                </td>
            </tr>
            <?php if (floatval($arResult['DISCOUNT_PRICE_ALL']) > 0): ?>
                <tr>
                    <td></td>
                    <td class="PRICE_WITHOUT_DISCOUNT price__pv"><?=getMessage('SALE_DISCOUNT')?>: <?=$arResult['DISCOUNT_PRICE_ALL_FORMATED']?></td>
                </tr>
            <?php endif; ?>
            <?php if ($bWeightColumn && $arItem['allWeight'] > 0): ?>
                <tr>
                    <td><?=getMessage('SALE_TOTAL_WEIGHT')?></td>
                    <td class="allWeight_FORMATED"><?=$arResult['allWeight_FORMATED']?></td>
                </tr>
            <?php endif; ?>
            <?php if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'): ?>
                <tr>
                    <td><?=getMessage('SALE_VAT_EXCLUDED')?></td>
                    <td class="allSum_wVAT_FORMATED"><?=$arResult['allSum_wVAT_FORMATED']?></td>
                </tr>
                <tr>
                    <td><?=getMessage('SALE_VAT_INCLUDED')?></td>
                    <td class="allVATSum_FORMATED"><?=$arResult['allVATSum_FORMATED']?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>">
<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams['OFFERS_PROPS'], ","))?>">
<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams['ACTION_VARIABLE'])?>">
<input type="hidden" id="quantity_float" value="<?=$arParams['QUANTITY_FLOAT']?>">
<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams['COUNT_DISCOUNT_4_ALL_QUANTITY'] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="price_vat_show_value" value="<?=($arParams['PRICE_VAT_SHOW_VALUE'] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="hide_coupon" value="<?=($arParams['HIDE_COUPON'] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="use_prepayment" value="<?=($arParams['USE_PREPAYMENT'] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="auto_calculation" value="<?=($arParams["AUTO_CALCULATION"] == "N") ? "N" : "Y"?>">

<div class="row">

    <div class="cart__coupons col-xs-12 col-sm-5" id="coupons_block">
        <?php if ($arParams['HIDE_COUPON'] != 'Y'): ?>
            <div class="coupon">
                <label class="coupon__btn btn btn3" for="coupon" onclick="enterCoupon(this.control);">Ok</label>
                <div class="l-context">
                    <input class="coupon__input form-control" id="coupon" type="text" name="COUPON" value="" onchange="enterCoupon(this);" placeholder="<?=getMessage('STB_COUPON_PROMT')?>">
                </div>
            </div>


            <?php
            if (!empty($arResult['COUPON_LIST'])):
                foreach ($arResult['COUPON_LIST'] as $oneCoupon):
                    $couponClass = 'disabled';
                    switch ($oneCoupon['STATUS']) {
                        case DiscountCouponsManager::STATUS_NOT_FOUND:
                        case DiscountCouponsManager::STATUS_FREEZE:
                            $couponClass = 'bad';
                            break;
                        case DiscountCouponsManager::STATUS_APPLYED:
                            $couponClass = 'good';
                            break;
                    }
            ?>
                    <div class="coupon">
                        <span class="coupon__del <?=$couponClass?>" data-coupon="<?=htmlspecialcharsbx($oneCoupon['COUPON'])?>"></span>
                        <div class="l-context">
                            <input class="coupon__input form-control <?=$couponClass?>" disabled readonly type="text" name="OLD_COUPON[]" value="<?=htmlspecialcharsbx($oneCoupon['COUPON'])?>">
                        </div>
                        <div class="coupon__note">
                        <?php
                        if (isset($oneCoupon['CHECK_CODE_TEXT'])) {
                            echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
                        }
                        ?>
                        </div>
                    </div>

            <?php
                endforeach;
                unset($couponClass, $oneCoupon);
            endif;
        endif;
        ?>
    </div>

    <div class="cart__btns clearfix col-xs-12 col-sm-7">
        <?if ($arParams["USE_PREPAYMENT"] == "Y" && strlen($arResult["PREPAY_BUTTON"]) > 0):?>
            <?=$arResult["PREPAY_BUTTON"]?>
            <span><?=GetMessage("SALE_OR")?></span>
        <?endif;?>

        <?
        if ($arParams["AUTO_CALCULATION"] != "Y")
        {
            ?>
            <button href="javascript:void(0)" onclick="updateBasket();" class="btn btn1"><?=GetMessage("SALE_REFRESH")?></button>
            <?
        }
        ?>
        <button class="btn btn1" onclick="checkOut();">
            <svg class="btn__icon icon-reply icon-svg"><use xlink:href="#svg-reply"></use></svg><?=getMessage('SALE_ORDER')?>
        </button>
        <?php if ('Y' == $arParams['USE_BUY1CLICK']): ?>
            <a class="btn btn2 js-ajax_link" href="<?=SITE_DIR?>buy1click/" data-insert_data="<?=CUtil::PhpToJSObject(array("RS_EXT_FIELD_0" => $arResult['BUY1CLICK_STRING']))?>" data-fancybox-title="<?=Loc::getMessage('RS_SLINE.BSBB_BASKET.BUY1CLICK')?>">
                <svg class="btn__icon icon-phone-big icon-svg"><use xlink:href="#svg-phone-big"></use></svg><?=getMessage('RS_SLINE.BSBB_BASKET.BUY1CLICK')?>
            </a>
            <script>BX.message({rsOrderListTmpl: '<?=getMessageJS('RS_SLINE.BSBB_BASKET.BUY1CLICK_TMPL')?>'});</script>
        <?php endif;?>
    </div>


</div>
<?php else: ?>
    <div style="text-align:center"><?=getMessage('SALE_NO_ITEMS')?></div>
<?php endif; ?>