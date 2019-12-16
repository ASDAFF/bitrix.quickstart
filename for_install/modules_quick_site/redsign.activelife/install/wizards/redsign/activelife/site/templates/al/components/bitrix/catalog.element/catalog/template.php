<?php

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Web\Json;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (!function_exists('getStringCatalogStoreAmountEx')){
	function getStringCatalogStoreAmountEx($amount, $minAmount, $arReturn){
		$message = $arReturn[1];
		if (intval($amount) == 0){
			$message = $arReturn[0];
		} elseif (intval($amount) >= $minAmount){
			$message = $arReturn[2];
		}
		return $message;
	}
}

$arPropsHide = array();
$bHaveOffer = false;
if (empty($arResult['OFFERS'])) {
    $arItemShow = &$arResult;
} else {
    $bHaveOffer = true;
    if (!$arResult['OFFERS_SELECTED']) {
        $arResult['OFFERS_SELECTED'] = 0;
    }
    $arItemShow = &$arResult['OFFERS'][$arResult['OFFERS_SELECTED']];
}

$arItemShowPrice = array();
if ($arParams['USE_PRICE_COUNT']) {

    foreach ($arItemShow['PRICE_MATRIX']['COLS'] as $typeID => $arType) {
        if ($arItemShow['PRICE_MATRIX']['MATRIX'][$typeID][0]['MIN_PRICE'] != 'Y') {
            continue;
        }
        $arItemShowPrice = $arItemShow['PRICE_MATRIX']['MATRIX'][$typeID][0];
        $arItemShowPrice['PRICE_ID'] = $arType['ID'];
        $arItemShowPrice['DISCOUNT_VALUE'] = $arItemShowPrice['DISCOUNT_PRICE'];
        break;
    }
} else {
    foreach ($arItemShow['PRICES'] as $arPrice) {
        if ($arPrice['MIN_PRICE'] != 'Y') {
            continue;
        }
        $arItemShowPrice = $arPrice;
        break;
    }
}

$arSKU = array();

ob_start();
?>
<div class="detail row" itemscope itemtype="http://schema.org/Product">
<div class="col-xs-12">
<?php
$strEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$strDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$arDeleteParams = array('CONFIRM' => Loc::getMessage('RS_SLINE.BCE_CATALOG.ELEMENT_DELETE_CONFIRM'));

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], $strEdit);
$this->AddDeleteAction($arResult['ID'], $arResult['DELETE_LINK'], $strDelete, $arDeleteParams);
$strMainID = $this->GetEditAreaId($arResult['ID']);
$sItemClass = 'detail__product product js-product row clearfix';
if (isset($arResult['DAYSARTICLE2']) || isset($arItemShow['DAYSARTICLE2'])) {
    $sItemClass .= ' da';
}
if (isset($arResult['QUICKBUY']) || isset($arItemShow['QUICKBUY'])) {
    $sItemClass .= ' qb';
}
?>
<div class="<?=$sItemClass?>"
    id="<?=$strMainID?>"
    data-product-id="<?=$arResult['ID']?>"
    <?php if ($bHaveOffer): ?>data-offer-id="<?=$arItemShow['ID']?>"<?php endif; ?>
    data-detail="<?=$arResult['DETAIL_PAGE_URL']?>"
>
    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 pull-right">

        <?php
        $sBrandPropCode = $arParams['BRAND_PROP'][$arResult['IBLOCK_ID']];

        if (!empty($arResult['PROPERTIES'][$sBrandPropCode]['VALUE'])):
        ?>
            <div class="detail__brand">

            <?php
            if (is_array($arResult['PROPERTIES'][$sBrandPropCode]['VALUE'])):
                echo implode(' / ', array_map(
                    function($sName, $sLink) {
                        return '<a href="' . $sLink . '">' . $sName . '</a>';
                    },
                    $arResult['PROPERTIES'][$sBrandPropCode]['VALUE'],
                    $arResult['PROPERTIES'][$sBrandPropCode]['FILTER_URL']
                ));
            else: ?>
                <a href="<?=$arResult['PROPERTIES'][$sBrandPropCode]['FILTER_URL']?>">
                    <?php if (isset($arResult['PROPERTIES'][$sBrandPropCode]['PICT'])): ?>
                        <img src="<?=$arResult['PROPERTIES'][$sBrandPropCode]['PICT']['SRC']?>" alt="<?=$arResult['PROPERTIES'][$sBrandPropCode]['ALT']?>">
                    <?php elseif (isset($arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP'][$arResult['IBLOCK']]]['PICT'])): ?>
                        <img src="<?=$arResult['PROPERTIES'][$arParams['BRAND_LOGO_PROP'][$arResult['IBLOCK']]]['PICT']['SRC']?>" alt="<?=$arResult['PROPERTIES'][$sBrandPropCode]['VALUE']?>">
                    <?php
                    else:
                        if (isset($arResult['DISPLAY_PROPERTIES'][$sBrandPropCode]['DISPLAY_VALUE'])) {
                            echo $arResult['DISPLAY_PROPERTIES'][$sBrandPropCode]['DISPLAY_VALUE'];
                        } else {
                            echo $arResult['PROPERTIES'][$sBrandPropCode]['VALUE'];
                        }
                    endif;
                    ?>
                </a>
            <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="catalog__head">
            <h1 class="detail__name webpage__title js-product__name" itemprop="name">
                <?php
                echo (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
                ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                : $arResult["NAME"]);
                ?>
            </h1>
            <?php if ($arParams['USE_LIKES'] == 'Y'): ?>
                <span class="detail__favorite favorite js-favorite">
                    <svg class="favorite__icon icon icon-heart icon-svg"><use xlink:href="#svg-heart"></use></svg>
                    <span class="favorite__cnt">
                        <?php
                        if (intval($arResult['PROPERTIES'][$arParams['LIKES_COUNT_PROP']]['VALUE']) > 0) {
                            echo $arResult['PROPERTIES'][$arParams['LIKES_COUNT_PROP']]['VALUE'];
                        }
                        ?>
                    </span>
                </span>
            <?php endif; ?>

            <div class="product__article">
                <?php
                if (
                    isset($arItemShow['PROPERTIES'][$arParams['ARTICLE_PROP'][$arItemShow['IBLOCK_ID']]]) &&
                    $arItemShow['PROPERTIES'][$arParams['ARTICLE_PROP'][$arItemShow['IBLOCK_ID']]]['VALUE'] != ''
                ):
                ?>
                    <span class="sku_prop__name"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.ITEM_ARTICLE')?>:</span>
                    <span class="sku_prop__val_<?=$arItemShow['PROPERTIES'][$arParams['ARTICLE_PROP'][$arItemShow['IBLOCK_ID']]]['ID']?>"><?=$arItemShow['PROPERTIES'][$arParams['ARTICLE_PROP'][$arItemShow['IBLOCK_ID']]]['VALUE']?></span>
                <?php
                elseif (
                    isset($arResult['PROPERTIES'][$arParams['ARTICLE_PROP'][$arResult['IBLOCK_ID']]]) &&
                    $arResult['PROPERTIES'][$arParams['ARTICLE_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] != ''
                ):
                ?>
                    <?=Loc::getMessage('RS_SLINE.BCE_CATALOG.ITEM_ARTICLE')?>:
                    <span class="js_product-article"><?=$arResult['PROPERTIES'][$arParams['ARTICLE_PROP'][$arResult['IBLOCK_ID']]]['VALUE']?></span>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
        <div class="detail__picbox picbox">
            <div class="picbox__pic">
                <div class="picbox__frame glass">
                    <div class="picbox__carousel">
                    <?php
                        $sDetailPictureClass = 'picbox__canvas';
                        if ($arParams['USE_PICTURE_ZOOM'] == 'Y'){
                            $sDetailPictureClass .= ' js_picture_glass';
                        }
                        if ($arParams['USE_PICTURE_GALLERY'] == 'Y'){
                            $sDetailPictureClass .= ' js_gallery-link fancybox.ajax';
                        }

                        $bCarouselImgIsset = false;
                        $sCarouselDefaultHTML = '';
                    ?>
                    <?php if ($bHaveOffer):?>
                        <?php foreach ($arResult['OFFERS'] as $iOfferKey => $arOffer): ?>
                            <?php
                            if (
                                ($iOfferKey == $arResult['OFFERS_SELECTED'] || !$bCarouselImgIsset && $sCarouselDefaultHTML == '') &&
                                is_array($arOffer['PRODUCT_PHOTO']) && count($arOffer['PRODUCT_PHOTO']) > 0
                            ):
                            ?>
                                <?php
                                if ($iOfferKey == $arResult['OFFERS_SELECTED']) {
                                    $bCarouselImgIsset = true;
                                } else if ($sCarouselDefaultHTML == '') {
                                    ob_start();
                                }
                                ?>

                                <?php foreach ($arOffer['PRODUCT_PHOTO'] as $arPhoto): ?>
                                    <a class="<?=$sDetailPictureClass?>"<?php if ($arParams['USE_PICTURE_GALLERY'] == 'Y'):?> href="<?=$arResult['DETAIL_PAGE_URL']; endif?>" data-dot="<img class='owl-preview' src='<?=$arPhoto['RESIZE']['small']['src']?>'>" data-offer-id="<?=$arOffer['ID']?>">
                                        <img class="picbox__img" src="<?=$arPhoto['RESIZE']['big']['src']?>" data-large="<?=$arPhoto['SRC']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" itemprop="image">
                                    </a>
                                <?php endforeach; ?>

                                <?php
                                if (!$bCarouselImgIsset && $sCarouselDefaultHTML == '') {
                                    $sCarouselDefaultHTML = ob_get_clean();
                                }
                                ?>

                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($arResult['PRODUCT_PHOTO'])): ?>
                        <?php $bCarouselImgIsset = true; ?>
                        <?php foreach ($arResult['PRODUCT_PHOTO'] as $arPhoto): ?>
                            <a class="<?=$sDetailPictureClass?>"<?php if ($arParams['USE_PICTURE_GALLERY'] == 'Y'):?> href="<?=$arResult['DETAIL_PAGE_URL']; endif?>" data-dot="<img class='owl-preview' src='<?=$arPhoto['RESIZE']['small']['src']?>'>">
                                <img class="picbox__img" src="<?=$arPhoto['RESIZE']['big']['src']?>" data-large="<?=$arPhoto['SRC']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" itemprop="image">
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!$bCarouselImgIsset): ?>
                        <?php if ($sCarouselDefaultHTML != ''): ?>
                            <?=$sCarouselDefaultHTML?>
                        <?php else: ?>
                            <span class="<?=$sDetailPictureClass?>" data-dot="<img class='owl-preview' src='<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png'>">
                                <img class="picbox__img" src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png">
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>

                    <span class="catalog__corner corner"><span class="corner__in"><span class="corner__text">
                    <?php
                    if (isset($arResult['DAYSARTICLE2']) || isset($arItemShow['DAYSARTICLE2'])) {
                        echo Loc::getMessage('RS_SLINE.BCE_CATALOG.DAYSARTICLE');
                    } elseif (isset($arResult['QUICKBUY']) || isset($arItemShow['QUICKBUY'])) {
                        echo Loc::getMessage('RS_SLINE.BCE_CATALOG.QUICKBUY');
                    }
                    ?>
                    </span></span></span>

                    <div class="glass_lupa"></div>

                    <?php if (
                        isset($arResult['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arResult['IBLOCK_ID']]]) &&
                        $arResult['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] != '' ||
                        isset($arResult['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arResult['IBLOCK_ID']]]) &&
                        $arResult['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] != ''
                    ): ?>
                        <span class="detail__gender gender">

                        <?php
                        if (
                            isset($arResult['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arResult['IBLOCK_ID']]]) &&
                            $arResult['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] != ''
                        ):
                        ?>
                            <svg class="icon icon-men icon-svg"><use xlink:href="#svg-men"></use></svg>
                        <?php endif; ?>

                        <?php
                        if (
                            isset($arResult['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arResult['IBLOCK_ID']]]) &&
                            $arResult['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] != ''
                        ):
                        ?>
                            <svg class="icon icon-women icon-svg"><use xlink:href="#svg-women"></use></svg>
                        <?php endif; ?>

                        </span>
                    <?php endif; ?>

                    <span class="detail__stickers js_swap_hide"
                        <?php if (isset($arResult['DAYSARTICLE2']) || isset($arResult['QUICKBUY'])): ?>
                            style="display:none;"
                        <?php endif; ?>
                    >
                        <?php
                        if (
                            $arParams['ICON_NOVELTY_PROP'][$arResult['IBLOCK_ID']] &&
                            $arResult['PROPERTIES'][$arParams['ICON_NOVELTY_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] == 'Y' ||
                            $arParams['NOVELTY_TIME'] && $arParams['NOVELTY_TIME'] >= (floor($_SERVER['REQUEST_TIME'] - MakeTimeStamp($arResult['DATE_ACTIVE_FROM'])) / 3600)
                        ):
                        ?>
                            <span class="sticker new">
                                <span class="sticker__text">
                                    <?=$arResult['PROPERTIES'][$arParams['ICON_NOVELTY_PROP'][$arResult['IBLOCK_ID']]]['NAME']?>
                                </span>
                            </span>
                        <?php endif; ?>

                        <?php
                        if (
                            $arParams['ICON_DISCOUNT_PROP'][$arResult['IBLOCK_ID']] &&
                            $arResult['PROPERTIES'][$arParams['ICON_DISCOUNT_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] == 'Y' ||
                            $arItemShowPrice['DISCOUNT_DIFF_PERCENT']
                        ):
                        ?>
                            <span class="sticker discount">
                                <span class="sticker__text">
                                    <?=$arResult['PROPERTIES'][$arParams['ICON_DISCOUNT_PROP'][$arResult['IBLOCK_ID']]]['NAME']?>
                                </span>
                            </span>
                        <?php endif; ?>

                        <?php
                        if (
                            $arParams['ICON_DEALS_PROP'][$arResult['IBLOCK_ID']] &&
                            $arResult['PROPERTIES'][$arParams['ICON_DEALS_PROP'][$arResult['IBLOCK_ID']]]['VALUE'] == 'Y'
                        ):
                        ?>
                            <span class="sticker action">
                                <span class="sticker__text">
                                    <?=$arResult['PROPERTIES'][$arParams['ICON_DEALS_PROP'][$arResult['IBLOCK_ID']]]['NAME']?>
                                </span>
                            </span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="picbox__mini">
                    <div class="picbox__scroll">
                        <div class="picbox__dots"></div>
                    </div>
                    <div class="scroll-element picbox__bar">
                        <div class="scroll-arrow scroll-arrow_less">
                            <svg class="icon icon-left icon-svg"><use xlink:href="#svg-left"></use></svg>
                        </div>
                        <div class="scroll-arrow scroll-arrow_more">
                            <svg class="icon icon-right icon-svg"><use xlink:href="#svg-right"></use></svg>
                        </div>
                        <div class="scroll-element_outer">
                            <div class="scroll-element_size"></div>
                            <div class="scroll-element_track"></div>
                            <div class="scroll-bar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 pull-right">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-7">
                <?php if ($bHaveOffer): ?>
                    <?php if (is_array($arResult['OFFERS_EXT']['PROPERTIES']) && 0 < count($arResult['OFFERS_EXT']['PROPERTIES'])): ?>
                        <div class="detail__offer_props clearfix">
                            <?php foreach ($arResult['OFFERS_EXT']['PROPERTIES'] as $sPropCode => $arProperty): ?>
                                <?php
                                $bIsColor = $bIsBtn = false;
                                $sOfferPropClass= 'offer_prop';
                                if (
                                    is_array($arParams['OFFER_TREE_COLOR_PROPS'][$arItemShow['IBLOCK_ID']]) &&
                                    in_array($sPropCode, $arParams['OFFER_TREE_COLOR_PROPS'][$arItemShow['IBLOCK_ID']])
                                ) {
                                    $bIsColor = true;
                                    $sOfferPropClass .= ' offer_prop-color';
                                } elseif (
                                    is_array($arParams['OFFER_TREE_BTN_PROPS'][$arItemShow['IBLOCK_ID']]) &&
                                    in_array($sPropCode, $arParams['OFFER_TREE_BTN_PROPS'][$arItemShow['IBLOCK_ID']])
                                ) {
                                    $bIsBtn = true;
                                    $sOfferPropClass .= ' offer_prop-btn';
                                }
                                ?>
                                <?php if ($bIsColor || $bIsBtn): ?>
                                    <div class="<?=$sOfferPropClass?> js-offer_prop" data-code="<?=$sPropCode?>">
                                        <div class="offer_prop__name"><?=$arResult['OFFERS_EXT']['PROPS'][$sPropCode]['NAME']?>:</div>
                                        <ul class="offer_prop__values clearfix">
                                            <?php foreach ($arProperty as $value => $arValue): ?>
                                                <?php
                                                $sOfferPropValueClass = 'offer_prop__value';
                                                if ($arValue['FIRST_OFFER'] == 'Y') {
                                                    $sOfferPropValueClass .= ' checked';
                                                } elseif ($arValue['DISABLED_FOR_FIRST'] == 'Y') {
                                                    $sOfferPropValueClass .= ' disabled';
                                                }
                                                ?>
                                                <li class="<?=$sOfferPropValueClass?>" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>">
                                                    <?php if ($bIsColor): ?>
                                                        <?php
                                                        $sOfferPropIcon = is_array($arValue['PICT'])
                                                            ? 'background-image:url('.$arValue['PICT']['SRC'].')'
                                                            : 'background-color:'.$arResult['COLORS_TABLE'][ToUpper($arValue['VALUE'])]['RGB'];
                                                        ?>
                                                            <span class="offer_prop__icon">
                                                                <span class="offer_prop__img" title="<?=$arValue['VALUE']?>" style="<?=$sOfferPropIcon?>"></span>
                                                            </span>
                                                    <?php else: ?>
                                                        <?=$arValue['VALUE']?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <?php $dropdownId = $this->getEditAreaId('offer_prop_'.$arResult['ID'].'_'.$arResult['OFFERS_EXT']['PROPS'][$sPropCode]['ID']) ?>
                                    <div class="offer_prop prop_<?=$sPropCode?> js-offer_prop" data-code="<?=$sPropCode?>">
                                        <div class="offer_prop__name"><?=$arResult['OFFERS_EXT']['PROPS'][$sPropCode]['NAME']?>:</div>
                                        <div class="dropdown select">
                                            <ul class="offer_prop__values dropdown-menu" aria-labelledby="<?=$dropdownId?>">
                                            <?php foreach ($arProperty as $value => $arValue): ?>
                                                <?php if($arValue['FIRST_OFFER'] == 'Y'): ?>
                                                    <li class="offer_prop__value checked" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>">
                                                        <a href="#"><?=$arValue['VALUE']?></a>
                                                        <?php ob_start(); ?>
                                                            <span class="offer_prop__checked"><?=$arValue['VALUE']?></span>
                                                         <?php $sOfferPropChecked = ob_get_clean();?>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="offer_prop__value<?php if ($arValue['DISABLED_FOR_FIRST'] == 'Y'): ?> disabled<?php endif; ?>" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>">
                                                        <a href="#"><?=$arValue['VALUE']?></a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            </ul>
                                            <a class="dropdown-toggle select__btn" id="<?=$dropdownId?>" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" role="button" href="#">
                                                <svg class="select__icon icon icon-svg"><use xlink:href="#svg-down-round"></use></svg><?=$sOfferPropChecked?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <?php $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']); ?>
                    <?php if ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$emptyProductProperties): ?>

                        <div class="detail__product_props product_props">

                            <?php if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])): ?>
                                <?php foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo): ?>
                                    <input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']; ?>[<?=$propID; ?>]" value="<?=htmlspecialcharsbx($propInfo['ID']); ?>">
                                    <?php
                                    if (isset($arResult['PRODUCT_PROPERTIES'][$propID])) {
                                        unset($arResult['PRODUCT_PROPERTIES'][$propID]);
                                    }
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']); ?>

                            <?php if (!$emptyProductProperties): ?>
                                <?php foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo): ?>
                                    <?php
                                    $bIsColor = $bIsBtn = false;
                                    $sOfferPropClass= 'offer_prop';
                                    if (
                                        $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE'] == 'S' &&
                                        $arResult['PROPERTIES'][$propID]['USER_TYPE'] == 'directory'
                                    ) {
                                        $bIsColor = true;
                                        $sOfferPropClass .= ' offer_prop-color';
                                    }
                                    ?>
                                    <div class="<?=$sOfferPropClass?>" data-code="<?=$propID?>">
                                        <div class="offer_prop__name"><?=$arResult['PROPERTIES'][$propID]['NAME']?>:</div>
                                        <ul class="offer_prop__values clearfix">
                                        <?php
                                        if (
                                            'L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE']
                                            && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']
                                            || $bIsColor
                                        ):
                                        ?>
                                            <?php foreach($propInfo['VALUES'] as $valueID => $arValue): ?>
                                                <?php $sOfferPropValueClass = 'offer_prop__value'; ?>
                                                <li class="<?=$sOfferPropValueClass?>">
                                                    <label>
                                                        <input
                                                            class="js-product_prop"
                                                            type="radio"
                                                            name="<?=$arParams['PRODUCT_PROPS_VARIABLE']; ?>[<?=$propID; ?>]"
                                                            value="<?=$valueID; ?>"
                                                            <?=($valueID == $propInfo['SELECTED'] ? 'checked' : ''); ?>
                                                        >
                                                        <?php if ($bIsColor): ?>
                                                            <?php
                                                            $sOfferPropIcon = is_array($arValue['PICT'])
                                                                ? 'background-image:url('.$arValue['PICT']['SRC'].')'
                                                                : 'background-color:'.$arResult['COLORS_TABLE'][ToUpper($arValue['VALUE'])]['RGB'];
                                                            ?>
                                                                <span class="offer_prop__icon">
                                                                    <span class="offer_prop__img" title="<?=$arValue['NAME']?>" style="<?=$sOfferPropIcon?>"></span>
                                                                </span>
                                                        <?php else: ?>
                                                            <?=$arValue?>
                                                        <?php endif; ?>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>

                                        <?php else: ?>
                                            <select class="js-product_prop" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']; ?>[<?=$propID; ?>]"><?
                                            foreach($propInfo['VALUES'] as $valueID => $value)
                                            {
                                                ?><option value="<?=$valueID; ?>" <?=($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><?=$value; ?></option><?
                                            }
                                            ?>
                                            </select>
                                        <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>

                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if ($arResult['PREVIEW_TEXT'] != ''): ?>
                    <div class="detail__preview" itemprop="description"><?=$arResult['PREVIEW_TEXT']?></div>
                    <?php if ($arResult['DETAIL_TEXT'] != ''): ?>
                        <a class="detail__preview_link anchor" href="#detail_detail"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MORE_LINK')?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
                <div class="detail__buy" itemprop="offers" itemscope itemtype="http://schema.org/<?=($bHaveOffer ? 'AggregateOffer' : 'Offer')?>">

                    <?php if (!empty($arItemShowPrice)): ?>
                        <div class="detail__price price">
                            <?php if ($arParams['SHOW_OLD_PRICE'] == 'Y'): ?>
                            <div class="price__pv js-price_pv-<?=$arItemShowPrice['PRICE_ID']?>">
                                <?php
                                if ($arItemShowPrice['DISCOUNT_DIFF']) {
                                    echo $arItemShowPrice['PRINT_VALUE'];
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                            <div class="price__pdv js-price_pdv-<?=$arItemShowPrice['PRICE_ID']?>" itemprop="<?=($bHaveOffer ? 'lowPrice' : 'price')?>"><?=$arItemShowPrice['PRINT_DISCOUNT_VALUE']?></div>
                            <?/*<meta itemprop="<?=($bHaveOffer ? 'lowPrice' : 'price')?>" content="<?=$arItemShowPrice['DISCOUNT_VALUE']?>">*/?>
                            <meta itemprop="priceCurrency" content="<?=$arItemShowPrice['CURRENCY']?>">
                        </div>
                    <?php endif; ?>


                    <?php if ($arParams['USE_STORE'] == 'Y'): ?>
                        <div class="detail__stocks">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:catalog.store.amount",
                                "al",
                                array(
                                    "ELEMENT_ID" => $arResult['ID'],
                                    "STORE_PATH" => $arParams['STORE_PATH'],
                                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                                    "MAIN_TITLE" => $arParams['MAIN_TITLE'],
                                    "USE_MIN_AMOUNT" =>  'N',
                                    "USE_MIN_AMOUNT_TMPL" =>  $arParams['USE_MIN_AMOUNT'],
                                    "MIN_AMOUNT" => $arParams['MIN_AMOUNT'],
                                    "STORES" => $arParams['STORES'],
                                    "SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
                                    "SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                                    "USER_FIELDS" => $arParams['USER_FIELDS'],
                                    "FIELDS" => $arParams['FIELDS'],
                                    "OFFER_ID" => $arItemShow['ID']
                                ),
                                $component,
                                array('HIDE_ICONS'=>'Y')
                            );?>
                        </div>
                    <?php elseif ($arParams['USE_QUANTITY_AND_STORES'] == 'Y'): ?>
                        <?php
                        $arMessage = array(getMessage('RS_SLINE.BCE_CATALOG.OUT_OF_STOCK'), getMessage('RS_SLINE.BCE_CATALOG.LIMITED_AVAILABILITY'), getMessage('RS_SLINE.BCE_CATALOG.IN_STOCK'));
                        $arClasses = array('is-outofstock', 'is-limited', 'is-instock');
                        $arSchemaAvailability = array('http://schema.org/OutOfStock', 'http://schema.org/LimitedAvailability', 'http://schema.org/InStock');
                        ?>
                        <div class="detail__stocks stocks tooltip">
                            <span><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.QUANTITY')?></span>
                            <span class="stocks__amount tooltip__link anchor"><?php
                                echo ($arParams['USE_MIN_AMOUNT'] == 'Y')
                                    ? getStringCatalogStoreAmountEx($arItemShow['CATALOG_QUANTITY'], $arParams['MIN_AMOUNT'], $arMessage)
                                    : $arItemShow['CATALOG_QUANTITY'];
                            ?></span><?php
                            ?><span class="stocks__sacle scale <?=getStringCatalogStoreAmountEx($arItemShow['CATALOG_QUANTITY'], $arParams['MIN_AMOUNT'], $arClasses)?>">
                                <svg class="scale__icon icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                <span class="scale__over" <?if($arItemShow['CATALOG_QUANTITY'] > 0){ echo ' style="width:100%"';}?>>
                                    <svg class="scale__icon icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                </span>
                            </span>
                            <link itemprop="availability" href="<?=getStringCatalogStoreAmountEx($arItemShow['CATALOG_QUANTITY'], $arParams['MIN_AMOUNT'], $arSchemaAvailability)?>">
<script>
BX.message({
RS_SLINE_STOCK_IN_STOCK: '<?=GetMessageJS('RS_SLINE.BCE_CATALOG.IN_STOCK')?>',
RS_SLINE_STOCK_LIMITED_AVAILABILITY: '<?=GetMessageJS('RS_SLINE.BCE_CATALOG.LIMITED_AVAILABILITY')?>',
RS_SLINE_STOCK_OUT_OF_STOCK: '<?=GetMessageJS('RS_SLINE.BCE_CATALOG.OUT_OF_STOCK')?>',
});
</script>
                        </div>
                    <?php endif; ?>

                    <!--noindex-->
                    <form name="buy_form">
                        <input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET">
                        <input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-product_id" value="<?=$arItemShow['ID']?>">

                        <?php if ($arParams['USE_PRODUCT_QUANTITY']): ?>
                            <div class="detail__quantity clearfix">
                                <span><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.PRODUCT_QUANTITY')?></span>
                                <div class="quantity<?php if (!$arItemShow['CAN_BUY']): ?> disabled<?php endif ?> clearfix">
                                    <i class="quantity__minus js-basket-minus"></i>
                                    <input
                                        type="number"
                                        class="quantity__val quantity__input js-quantity<?php if ($arParams['USE_PRICE_COUNT']): ?> js-use_count<?php endif; ?>"
                                        name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>"
                                        value="<?=$arItemShow['CATALOG_MEASURE_RATIO']?>"
                                        step="<?=$arItemShow['CATALOG_MEASURE_RATIO']?>"
                                        min="<?=$arItemShow['CATALOG_MEASURE_RATIO']?>"
                                        <?/* max="<?=$arItemShow['CATALOG_QUANTITY']?>"*/?>
                                    >
                                    <i class="quantity__plus js-basket-plus"></i>
                                </div>
                                <span class="detail__measure js-measure"><?=$arResult['CATALOG_MEASURE_NAME']?></span>
                            </div>
                        <?php endif; ?>

                        <div class="detail__btns">

                            <?php
                            if ($arResult['CATALOG_SUBSCRIBE'] == 'Y') {

                                if ($bHaveOffer) {

                                    $APPLICATION->includeComponent(
                                        'bitrix:catalog.product.subscribe',
                                        'al',
                                        array(
                                            'PRODUCT_ID' => $arResult['ID'],
                                            'BUTTON_ID' => $strMainID.'_subscribe',
                                            'BUTTON_CLASS' => 'btn detail__subscr js-subscribe',
                                            'DEFAULT_DISPLAY' => !$arItemShow['CAN_BUY'],
                                        ),
                                        $component,
                                        array('HIDE_ICONS' => 'Y')
                                    );

                                } else {

                                    if (!$arResult['CAN_BUY']) {
                                        $APPLICATION->includeComponent(
                                            'bitrix:catalog.product.subscribe',
                                            'al',
                                            array(
                                                'PRODUCT_ID' => $arResult['ID'],
                                                'BUTTON_ID' => $strMainID.'_subscribe',
                                                'BUTTON_CLASS' => 'btn detail__subscr js-subscribe',
                                                'DEFAULT_DISPLAY' => true,
                                            ),
                                            $component,
                                            array('HIDE_ICONS' => 'Y')
                                        );
                                    }

                                }
                            }
                            ?>

                            <a class="detail__add2cart added2cart btn" href="<?=$arParams['BASKET_URL']?>" title="<?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_IN_BASKET_TITLE')?>" rel="nofollow">
                                <svg class="icon icon-incart icon-svg"><use xlink:href="#svg-incart"></use></svg><br />
                                <?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_IN_BASKET')?>
                            </a>

                            <button class="detail__add2cart add2cart btn<?php if (!$arItemShow['CAN_BUY']): ?> disabled<?php endif; ?> js-add2cart" type="submit"<?php if (!$arItemShow['CAN_BUY']): ?> disabled<?php endif; ?>>
                                <svg class="icon icon-cart icon-svg"><use xlink:href="#svg-cart"></use></svg><?=($arParams['MESS_BTN_ADD_TO_BASKET'] != '' ? $arParams['MESS_BTN_ADD_TO_BASKET'] : Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_ADD_TO_BASKET'))?>
                            </button>

                            <?php
                            if ($arParams['USE_BUY1CLICK'] == 'Y'):
                                $name = '['.$arResult['ID'].'] '.$arResult['NAME'];
                                $arBuy1click = array(
                                    'RS_EXT_FIELD_0' => '['.$arResult['ID'].'] '.htmlspecialcharsbx($arResult['NAME'])
                                );
                            ?>
                                <a class="detail__buy1click buy1click btn js-buy1click js-ajax_link <?if (!$arItemShow['CAN_BUY']){?> disabled<?}?>"
                                    data-insert_data="<?=CUtil::PhpToJSObject($arBuy1click)?>"
                                    href="<?=SITE_DIR?>buy1click/"
                                    data-name="<?=$arResult['NAME']?>"
                                    rel="nofollow"
                                    data-fancybox-title="<?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_BUY1CLICK')?>"
                                >
                                    <svg class="icon icon-phone icon-svg"><use xlink:href="#svg-phone-big"></use></svg><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_BUY1CLICK')?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <!--/noindex-->

                    <?php
                    if (isset($arResult['DAYSARTICLE2'])) {
                        $arTimers[] = $arResult['DAYSARTICLE2'];
                    }
                    if (isset($arResult['QUICKBUY'])) {
                        $arTimers[] = $arResult['QUICKBUY'];
                    }
                    if (is_array($arResult['OFFERS'])) {
                        foreach ($arResult['OFFERS'] as $arOffer) {
                            if (isset($arOffer['DAYSARTICLE2'])) {
                                $arTimers[] = $arOffer['DAYSARTICLE2'];
                            }
                            if (isset($arOffer['QUICKBUY'])) {
                                $arTimers[] = $arOffer['QUICKBUY'];
                            }
                        }
                    }

                    if (is_array($arTimers) && 0 < count($arTimers)):
                        $have_vis = false;

                        foreach ($arTimers as $arTimer):
                            $KY = 'TIMER';
                            if (isset($arTimer['DINAMICA_EX'])) {
                                $KY = 'DINAMICA_EX';
                            }
                            $jsTimer = array(
                                'DATE_FROM' => $arTimer[$KY]['DATE_FROM'],
                                'DATE_TO' => $arTimer[$KY]['DATE_TO'],
                                'AUTO_RENEWAL' => $arTimer['AUTO_RENEWAL'],
                            );
                            if (isset($arTimer['DINAMICA'])) {
                                $jsTimer['DINAMICA_DATA'] = $arTimer['DINAMICA'] == 'custom' ? array_flip(unserialize($arTimer['DINAMICA_DATA'])) : $arTimer['DINAMICA'];
                            }
                            ?>
                            <div class="detail__timer timer js_timer timer_bg" style="display:<?
                                echo (($arResult['ID'] == $arTimer['ELEMENT_ID'] || $arItemShow['ID'] == $arTimer['ELEMENT_ID']) && !$have_vis) ? 'block' : 'none'
                            ?>;" data-offer-id="<?=$arTimer['ELEMENT_ID']?>" data-timer="<?=CUtil::PhpToJSObject($jsTimer)?>">
                                <div class="timer__data">
                                    <div class="timer__cell" style="display:none">
                                        <div class="timer__val js_timer-d">00</div>
                                        <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_DAY')?></div>
                                    </div>
                                    <div class="timer__cell" style="display:none">:</div>
                                    <div class="timer__cell">
                                        <div class="timer__val js_timer-H">00</div>
                                        <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_HOUR')?></div>
                                    </div>
                                    <div class="timer__cell">:</div>
                                    <div class="timer__cell">
                                        <div class="timer__val js_timer-i">00</div>
                                        <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_MIN')?></div>
                                    </div>
                                    <div class="timer__cell">:</div>
                                    <div class="timer__cell">
                                        <div class="timer__val js_timer-s">00</div>
                                        <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_SEC')?></div>
                                    </div>
                                    <?php if (isset($arTimer['TIMER']) && $arQuickbuy['DATA']['QUANTITY'] > 0): ?>
                                        <div class="timer__cell timer__sep"></div>
                                        <div class="timer__cell">
                                            <div>
                                                <div class="timer__val"><?=($arTimer['QUANTITY'] > 99 ? $arTimer['QUANTITY'] : sprintf('%02d', $arTimer['QUANTITY']))?></div>
                                                <div class="sq"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_MEASURE')?></div>
                                            </div>
                                            <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_LEFT')?></div>
                                        </div>
                                    <?php elseif (isset($arTimer['DINAMICA_EX'])): ?>
                                        <div class="timer__cell timer__sep"></div>
                                        <div class="timer__cell">
                                            <div class="timer__val js_timer-progress">0%</div>
                                            <div class="timer__note"><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_SOLD')?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="timer__bottom">
                                    <?=Loc::getMessage('RS_SLINE.BCE_CATALOG.TIMER_PRICE_DIFF');?>
                                    <span class="discount"><?=$arItemShowPrice['PRINT_DISCOUNT_DIFF']?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($arParams['USE_SHARE'] == 'Y'): ?>
                        <div class="detail__share ya-share2"
                            <?php if ($arParams['SOCIAL_COUNTER'] == 'Y'): ?>
                                data-counter
                            <?php endif; ?>
                            <?php if ($arParams['SOCIAL_COPY'] != 'last'): ?>
                                data-copy="<?=$arParams['SOCIAL_COPY']?>"
                            <?php endif; ?>
                            <?php if (intval($arParams['SOCIAL_LIMIT']) > 0): ?>
                                data-limit="<?=$arParams['SOCIAL_LIMIT']?>"
                            <?php endif; ?>
                            <?php if (is_array($arParams['SOCIAL_SERVICES'])): ?>
                                data-services="<?=implode(',', $arParams['SOCIAL_SERVICES']);?>"
                            <?php endif; ?>
                            <?php if (intval($arParams['SOCIAL_SIZE']) > 0): ?>
                                data-size="<?=$arParams['SOCIAL_SIZE']?>"
                            <?php endif; ?>
                            data-lang="<?=LANGUAGE_ID?>"
                        <?/*?> data-bare=""*/?>></div>
                    <?php endif; ?>

                    <?php if ($arParams['USE_KREDIT'] == 'Y' && $arParams['KREDIT_URL'] != ''): ?>
                        <a class="detail__credit" href="<?=$arParams['KREDIT_URL']?>">
                            <i class="icon-png"></i><?=Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_BUY_KREDIT')?>
                        </a>
                    <?php endif; ?>

                    <?php
                    if (
                        isset($arResult['PROPERTIES'][$arParams['DELIVERY_PROP']]) &&
                        $arResult['DISPLAY_PROPERTIES'][$arParams['DELIVERY_PROP']]['DISPLAY_VALUE'] != ''
                    ):
                    ?>
                        <div class="detail__delivery" href="<?=$arParams['KREDIT_URL']?>">
                            <i class="icon-png"></i><?=$arResult['DISPLAY_PROPERTIES'][$arParams['DELIVERY_PROP']]['DISPLAY_VALUE']?>
                        </div>
                    <?php elseif ($arResult['PROPERTIES'][$arParams['DELIVERY_PROP']]['VALUE'] != ''): ?>
                        <div class="detail__delivery delivery">
                            <i class="icon-png"></i><?=$arResult['PROPERTIES'][$arParams['DELIVERY_PROP']]['VALUE']?>
                        </div>
                    <?php endif; ?>

                    <?php if ($arParams['DISPLAY_COMPARE'] == 'Y'): ?>
                        <a class="cmp__link js-compare" href="<?=str_replace('#ID#', $arItemShow['ID'], $arResult['COMPARE_URL_TEMPLATE'])?>" rel="nofollow">
                            <svg class="cmp__icon icon icon-cmp icon-svg"><use xlink:href="#svg-cmp"></use></svg><?=($arParams['MESS_BTN_COMPARE'] != '' ? $arParams['MESS_BTN_COMPARE'] : Loc::getMessage('RS_SLINE.BCE_CATALOG.MESS_BTN_COMPARE'))?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
            <div class="col-xs-12">
                <a class="detail__detail_link" href="<?=$arResult['DETAIL_PAGE_URL']?>">
                <?=Loc::getMessage('GO2DETAIL_FROM_POPUP')?>
                </a>
            </div>
        </div>
    </div>
</div></div>
<?php $templateData['TEMPLATE_ELEMENT'] = ob_get_flush(); ?>

    <?php
    if (is_array($arParams['TAB_IBLOCK_PROPS']) && count($arParams['TAB_IBLOCK_PROPS'])>0)
    {
        foreach ($arParams['TAB_IBLOCK_PROPS'] as $sPropCode)
        {
            $arPropsHide[$sPropCode] = $sPropCode;
        }
    }
    $arPropsShow = array_diff_key($arResult['DISPLAY_PROPERTIES'], $arPropsHide);
    ?>

    <div class="detail__tabs col-xs-12">
        <ul class="nav-tabs" role="tablist">

            <?php if ($arResult['TABS']['DETAIL_TEXT']): ?>
                <li class="active">
                    <a rel="nofollow" href="#detail_detail" data-toggle="tab"><?=Loc::getMessage('TAB_NAME_DESCRIPTION')?></a>
                </li>
            <?php endif; ?>

            <?php if (!empty($arPropsShow)): ?>
                <li<?php if (!$arResult['TABS']['DETAIL_TEXT']): ?> class="active"<?php endif; ?>>
                    <a rel="nofollow" href="#detail_props" data-toggle="tab"><?=Loc::getMessage('TAB_NAME_PROPERTIES')?></a>
                </li>
            <?php endif; ?>

            <?php if ($arResult['TABS']['TAB_PROPERTIES']): ?>
                <?php foreach ($arParams['TAB_IBLOCK_PROPS'] as $iPropKey => $sPropCode): ?>
                    <?php if ($sPropCode != '' && !empty($arResult['PROPERTIES'][$sPropCode]['VALUE'])): ?>
                        <li<?php if (!$arResult['TABS']['DETAIL_TEXT'] && !$arResult['TABS']['DISPLAY_PROPERIES'] && $iPropKey == 0): ?> class="active"<?php endif; ?>>
                            <a rel="nofollow" href="#detail_<?=strtolower($sPropCode)?>" data-toggle="tab"><?=$arResult['PROPERTIES'][$sPropCode]['NAME']?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($arResult['TABS']['REVIEW']): ?>
                <li<?php if (!$arResult['TABS']['DETAIL_TEXT'] && !$arResult['TABS']['DISPLAY_PROPERIES'] && !$arResult['TABS']['TAB_PROPERTIES']): ?> class="active"<?php endif; ?>>
                    <a rel="nofollow" href="#detail_reviews" data-toggle="tab"><?=Loc::getMessage('TAB_NAME_REVIEWS')?></a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <?php if ($arResult['TABS']['DETAIL_TEXT']): ?>
                <div id="detail_detail" class="tab-pane fade in active">
                    <?=$arResult['DETAIL_TEXT']?>
                </div>
            <?php endif; ?>

            <?php if (!empty($arPropsShow)): ?>
                <div id="detail_props" class="tab-pane fade <?php if (!$arResult['TABS']['DETAIL_TEXT']): ?> in active<?php endif; ?>">
                    <?$APPLICATION->IncludeComponent(
                        'redsign:grupper.list',
                        'al',
                        array(
                            'DISPLAY_PROPERTIES' => $arPropsShow,
                            'CACHE_TIME' => 36000
                        ),
                        $component
                    );?>
                </div>
            <?php endif; ?>

            <?php if ($arResult['TABS']['TAB_PROPERTIES']): ?>
                <?php foreach ($arParams['TAB_IBLOCK_PROPS'] as $iPropKey => $sPropCode): ?>
                    <?php if ($sPropCode != '' && !empty($arResult['PROPERTIES'][$sPropCode]['VALUE'])): ?>
                        <div id="detail_<?=strtolower($sPropCode)?>" class="tab-pane fade <?php if (!$arResult['TABS']['DETAIL_TEXT'] && !$arResult['TABS']['DISPLAY_PROPERIES'] && $iPropKey == 0): ?> in active<?php endif; ?>">
                        <?php if ($arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'F'): ?>
                            <div class="row">
                                <?php foreach ($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $arDoc): ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="doc">
                                            <div class="doc__type">
                                                <i class="doc__icon <?=$arDoc['TYPE']?>"></i>
                                            </div>
                                            <div class="doc__inner">
                                                <a class="doc__name" href="<?=$arDoc['FULL_PATH']?>" target="_blank" rel="nofollow">
                                                    <?=($arDoc['DESCRIPTION'] == '' ? $arDoc['ORIGINAL_NAME'] : $arDoc['DESCRIPTION'])?>
                                                </a>
                                                <div class="doc__size">(<?if ($arDoc['TYPE2'] != ''){ echo $arDoc['TYPE2'].', '; } echo $arDoc['SIZE']?>)</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php
                        elseif (
                            $arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE'] == 'E' &&
                            count($arResult['PROPERTIES'][$sPropCode]['VALUE']) > 0
                        ):
                        ?>
                            <?php
                            $IBLOCK_ID = $arResult['PROPERTIES'][$sPropCode]['IBLOCK_ID'];
                            if (!isset($arSKU[$IBLOCK_ID])){
                                $arSKU[$IBLOCK_ID] = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
                            }
                            ?>
                             <?$APPLICATION->IncludeComponent(
                                "bitrix:catalog.recommended.products",
                                "al",
                                array(
                                    "LINE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
                                    "ID" => $arResult['ID'],
                                    "PROPERTY_LINK" => $sPropCode,
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "BASKET_URL" => $arParams["BASKET_URL"],
                                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                                    "PAGE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
                                    "SHOW_OLD_PRICE" => "Y",//need
                                    "SHOW_DISCOUNT_PERCENT" => "Y",//need
                                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                                    "PRODUCT_SUBSCRIPTION" => 'N',
                                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                                    "SHOW_NAME" => "Y",
                                    "SHOW_IMAGE" => "Y",
                                    "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                                    "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                                    "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                                    "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                                    "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
                                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                    "OFFER_TREE_PROPS_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"][$arSKU[$IBLOCK_ID]['IBLOCK_ID']],
                                    "OFFER_TREE_COLOR_PROPS_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["OFFER_TREE_COLOR_PROPS"][$arSKU[$IBLOCK_ID]['IBLOCK_ID']],
                                    "ADDITIONAL_PICT_PROP_".$IBLOCK_ID => $arParams['ADDITIONAL_PICT_PROP'][$arParams['IBLOCK_ID']],
                                    "PROPERTY_CODE_".$arParams['IBLOCK_ID'] => $arParams["LIST_PROPERTY_CODE"],
                                    "BRAND_PROP_".$arParams['IBLOCK_ID'] => $arParams['BRAND_PROP'][$arParams['IBLOCK_ID']],
                                    "ICON_NOVELTY_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP'][$arParams['IBLOCK_ID']],
                                    "ICON_DEALS_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP'][$arParams['IBLOCK_ID']],
                                    "ICON_DISCOUNT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP'][$arParams['IBLOCK_ID']],
                                    "ICON_MEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP'][$arParams['IBLOCK_ID']],
                                    "ICON_WOMEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP'][$arParams['IBLOCK_ID']],
                                    "ADDITIONAL_PICT_PROP_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
                                    "PROPERTY_CODE_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                    'USE_LIKES' => $arParams['USE_LIKES'],
                                    'USE_SHARE' => $arParams['USE_SHARE'],
                                    'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
                                    'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                                    'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                                    'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                                    'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
                                    'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );?>

                        <?php elseif ($arResult['PROPERTIES'][$sPropCode]['MULTIPLE'] == 'Y'): ?>
                            <div class="props_group">
                                <div class="props_group__name"><?=$arrValue['GROUP']['NAME']?></div>
                                <table class="props_group__props">
                                    <tbody>
                                    <?php foreach ($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $iPropKey => $sProp): ?>
                                        <tr>
                                            <th><?=$arResult['PROPERTIES'][$sPropCode]["DESCRIPTION"][$iPropKey]?></th>
                                            <td><span><?=$sProp?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <?php
                            if ($arResult['PROPERTIES'][$sPropCode]['VALUE']['TYPE'] == 'text') {
                                echo $arResult['PROPERTIES'][$sPropCode]['VALUE']['TEXT'];
                            } else {
                                echo $arResult['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE'];
                            }
                            ?>
                        <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
    <? // --> component_epilog.php continue ?>

<?php
$this->SetViewTarget('rs_detail-linked_items');
foreach ($arParams['LINKED_ITEMS_PROPS'] as $sPropCode){
    if (!empty($arResult['PROPERTIES'][$sPropCode]['VALUE'])){
        $IBLOCK_ID = $arResult['PROPERTIES'][$sPropCode]['IBLOCK_ID'];
        if (!isset($arSKU[$IBLOCK_ID])){
            $arSKU[$IBLOCK_ID] = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
        }

        $APPLICATION->IncludeComponent(
            "bitrix:catalog.recommended.products",
            "al",
            array(
                "LINE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
                "ID" => $arResult['ID'],
                "PROPERTY_LINK" => $sPropCode,
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                "PAGE_ELEMENT_COUNT" => $arParams["ALSO_BUY_ELEMENT_COUNT"],
                "SHOW_OLD_PRICE" => "Y",//need
                "SHOW_DISCOUNT_PERCENT" => "Y",//need
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRODUCT_SUBSCRIPTION" => 'N',
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                "SHOW_NAME" => "Y",
                "SHOW_IMAGE" => "Y",
                "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "OFFER_TREE_PROPS_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"][$arSKU[$IBLOCK_ID]['IBLOCK_ID']],
                "OFFER_TREE_COLOR_PROPS_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["OFFER_TREE_COLOR_PROPS"][$arSKU[$IBLOCK_ID]['IBLOCK_ID']],
                "ADDITIONAL_PICT_PROP_".$IBLOCK_ID => $arParams['ADDITIONAL_PICT_PROP'][$arParams['IBLOCK_ID']],
                "PROPERTY_CODE_".$arParams['IBLOCK_ID'] => $arParams["LIST_PROPERTY_CODE"],
                "BRAND_PROP_".$arParams['IBLOCK_ID'] => $arParams['BRAND_PROP'][$arParams['IBLOCK_ID']],
                "ICON_NOVELTY_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_NOVELTY_PROP'][$arParams['IBLOCK_ID']],
                "ICON_DEALS_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DEALS_PROP'][$arParams['IBLOCK_ID']],
                "ICON_DISCOUNT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_DISCOUNT_PROP'][$arParams['IBLOCK_ID']],
                "ICON_MEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_MEN_PROP'][$arParams['IBLOCK_ID']],
                "ICON_WOMEN_PROP_".$arParams['IBLOCK_ID'] => $arParams['ICON_WOMEN_PROP'][$arParams['IBLOCK_ID']],
                "ADDITIONAL_PICT_PROP_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
                "PROPERTY_CODE_".$arSKU[$IBLOCK_ID]['IBLOCK_ID'] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                'USE_LIKES' => $arParams['USE_LIKES'],
                'USE_SHARE' => $arParams['USE_SHARE'],
                'SOCIAL_SERVICES' => $arParams['LIST_SOCIAL_SERVICES'],
                'SOCIAL_COUNTER' => $arParams['SOCIAL_COUNTER'],
                'SOCIAL_COPY' => $arParams['SOCIAL_COPY'],
                'SOCIAL_LIMIT' => $arParams['SOCIAL_LIMIT'],
                'SOCIAL_SIZE' => $arParams['SOCIAL_SIZE'],
                'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
                "SECTION_TITLE" => $arResult['PROPERTIES'][$sPropCode]["NAME"],
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
    }
}
$this->EndViewTarget();

//$templateData = $arResult;
