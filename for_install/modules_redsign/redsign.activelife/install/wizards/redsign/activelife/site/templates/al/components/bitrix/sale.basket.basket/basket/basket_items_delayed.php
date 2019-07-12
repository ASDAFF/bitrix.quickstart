<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */

use \Bitrix\Main\Localization\Loc;

$bPropsColumn = false;
$bDelayColumn = false;
$bDeleteColumn = false;
$bDiscountColumn = false;
$bWeightColumn = false;
$bPriceColumn = false;
$bSumColumn = false;
$bArticleColumn = false;

$arHeadersHide = array('TYPE', 'PROPS', 'DELAY', 'DELETE', 'DISCOUNT', 'WEIGHT'); // some values are not shown in the columns in this template\

foreach ($arResult['GRID']['HEADERS'] as $id => $arHeader)
{
    $arHeader['name'] = (isset($arHeader['name']) ? (string) $arHeader['name'] : '');
    if ($arHeader['name'] == '') {
        $arResult['GRID']['HEADERS'][$id]['name'] = $arHeader['name'] = getMessage('SALE_'.$arHeader['id']);
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
                    continue 2;
                }
            }
        }
    }
}
?>
<div class="panel clearfix" id="basket_items_delayed">
    <div class="panel__head">
        <svg class="panel__icon icon-cart icon-svg"><use xlink:href="#svg-cart"></use></svg>
        <?=getMessage('SALE_BASKET_ITEMS_DELAYED')?>
    </div>
    <div class="panel__body">
        <table id="delayed_items" class="table table_items">
            <?php foreach ($arResult['GRID']['ROWS'] as $arItem): ?>
                <?php if ($arItem['DELAY'] == 'Y' && $arItem['CAN_BUY'] == 'Y'): ?>
                    <tr class="table_item product" id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem['PRODUCT_ID']?>" data-cart-item-id="<?=$arItem['ID']?>">
                    <?php foreach ($arResult['GRID']['HEADERS'] as $arHeader): ?>
                        <?php
                        if (in_array($arHeader['id'], $arHeadersHide)) {
                                continue;
                        }
                        ?>

                        <?php if ($arHeader['id'] == 'NAME'): ?>
                            <td>
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
                                    <?php foreach ($arResult['GRID']['HEADERS'] as $id => $arHeader): ?>
                                        <?php
                                        if (in_array($arHeader['id'], $arHeadersHideExt)) {
                                            continue;
                                        }
                                        ?>

                                        <?php if (!empty($arItem[$arHeader['id']])): ?>
                                            <dt><?=(strlen($arHeader['name']) > 0) ? $arHeader['name'] : getMessage('SALE_'.$arHeader['id'])?>:</dt>
                                            <dd><?=(is_array($arItem[$arHeader['id']]) ? $arItem[$arHeader['id']]['TEXT'] : $arItem[$arHeader['id']])?></dd>
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
                            <td>
                                <?/*<span><?=getMessage('SALE_QUANTITY')?></span>*/?>
                                <div class="table_item__quantity">
                                    <span>x <?=$arItem['QUANTITY']?></span>
                                    <?php if (isset($arItem['MEASURE_TEXT'])): ?>
                                        <span class="js-measure"><?=$arItem['MEASURE_TEXT']?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php elseif ($arHeader["id"] == "PRICE"): ?>
                            <td>
                                <div id="current_price_<?=$arItem['ID']?>" class="price__pdv"><?=$arItem['PRICE_FORMATED']?></div>
                                <?php if ($bDiscountColumn && $arItem['DISCOUNT_PRICE_PERCENT'] > 0): ?>
                                    <div id="discount_value_<?=$arItem['ID']?>" class="price__pv">
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
                        <td>
                            <?php if ($bDeleteColumn): ?>
                                <a class="table_item__link" href="<?=str_replace('#ID#', $arItem['ID'], $arUrls['delete'])?>">
                                    <svg class="btn__icon icon-close icon-svg"><use xlink:href="#svg-close"></use></svg><?=getMessage('SALE_DELETE')?>
                                </a>
                            <?php endif; ?>
                            <a class="table_item__link" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["add"])?>">
                                <svg class="btn__icon icon-refresh icon-svg"><use xlink:href="#svg-refresh"></use></svg><?=getMessage('SALE_ADD_TO_BASKET')?>
                            </a>
                        </td>
                    <?php endif; ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
</div>