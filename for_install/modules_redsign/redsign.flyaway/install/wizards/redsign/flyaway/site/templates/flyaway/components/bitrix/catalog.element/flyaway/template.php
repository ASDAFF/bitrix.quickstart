<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$strMainID = $this->GetEditAreaId($arResult['ID']);

if (empty($arResult['OFFERS'])) { $HAVE_OFFERS = false; $PRODUCT = &$arResult; } else { $HAVE_OFFERS = true; $PRODUCT = &$arResult['OFFERS'][0]; }

// pictures
$arImages = array();
if ($HAVE_OFFERS) {
    foreach ($arResult['OFFERS'] as $key1 => $arOffer) {
        if (is_array($arOffer['DETAIL_PICTURE']['RESIZE'])) {
            $arImages[] = array(
                'DATA' => array(
                    'OFFER_KEY' => $key1,
                    'OFFER_ID' => $arOffer['ID'],
                ),
                'PIC' => $arOffer['DETAIL_PICTURE'],
            );
        }

        if (is_array($arOffer['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO']]['VALUE'][0]['RESIZE'])) {
            foreach($arOffer['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO']]['VALUE'] as $arImage) {
                $arImages[] = array(
                    'DATA' => array(
                        'OFFER_KEY' => $key1,
                        'OFFER_ID' => $arOffer['ID'],
                    ),
                    'PIC' => $arImage,
                );
            }
        }
    }
}

if (is_array($arResult['DETAIL_PICTURE']['RESIZE'])) {
    $arImages[] = array(
        'DATA' => array(
            'OFFER_KEY' => 0,
            'OFFER_ID' => 0,
        ),
        'PIC' => $arResult['DETAIL_PICTURE'],
    );
}

if (is_array($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_MORE_PHOTO']]['VALUE'][0]['RESIZE'])) {
  foreach ($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_MORE_PHOTO']]['VALUE'] as $arImage) {
    $arImages[] = array(
        'DATA' => array(
            'OFFER_KEY' => 0,
            'OFFER_ID' => 0,
        ),
        'PIC' => $arImage,
    );
  }
}

// multy price
$i = 0;

if (is_array($arResult['CAT_PRICES']) && count($arResult['CAT_PRICES']) > 0) {
  foreach ($arResult['CAT_PRICES'] as $PRICE_CODE => $arPrice) {
    if (!$arPrice['CAN_VIEW']) {
      continue;
    }
    $i++;
  }
}

$multyPrice = ( $i>1 ? true : false );
$multyPrice = ( $multyPrice && is_array($PRODUCT['PRICES']) && count($PRODUCT['PRICES'])>1 ? true : false );

// TIMERS
$arTimers = array();
if ($arResult['HAVE_DA2'] == 'Y') {
  if (isset($arResult['DAYSARTICLE2'])) {
    $arTimers[] = $arResult['DAYSARTICLE2'];
  } elseif ($HAVE_OFFERS) {
    foreach ($arResult['OFFERS'] as $arOffer) {
      if ( isset($arOffer['DAYSARTICLE2']) ) {
        $arTimers[] = $arOffer['DAYSARTICLE2'];
      }
    }
  }
}

if ( $arResult['HAVE_QB']=='Y' ) {
  if ( isset($arResult['QUICKBUY']) ) {
    $arTimers[] = $arResult['QUICKBUY'];
  } elseif ($HAVE_OFFERS) {
    foreach ($arResult['OFFERS'] as $arOffer) {
      if ( isset($arOffer['QUICKBUY']) ) {
        $arTimers[] = $arOffer['QUICKBUY'];
      }
    }
  }
}

$arPropsDiff = array_fill_keys($arParams['PROPS_TABS'], 0);
$arResult['DISPLAY_PROPERTIES_SHOW'] = array_diff_key(
	$arResult['DISPLAY_PROPERTIES'],
	$arPropsDiff
);


$tabDescription = ($arResult['DETAIL_TEXT']!='') ? true : false ;
$tabProperties = (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES'])>0) ? true : false;
$tabDocs = false;

global $recommendedFilter;
$recommendedFilter = array('PROPERTY_WE_RECCOMENDED_VALUE' => 'Y');
?>
<div
  class="
    row js-detail js-compare js-toggle js-element js-elementid<?=$arResult['ID']?> product
    <?php if ( isset($arResult['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo ' da2'; } ?>
    <?php if ( isset($arResult['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }?> clearfix
  "
  data-elementid="<?=$arResult['ID']?>"
  data-detailpageurl="<?=$arResult['DETAIL_PAGE_URL']?>"
  data-elementname="<?=CUtil::JSEscape($arResult['NAME'])?>"
  data-curerntofferid="<?=$PRODUCT['ID']?>"
>
    <div class="col col-xs-12 col-sm-12 col-md-6 col-lg-5 product-gallery">
        <div class="product-detail-carousel">
            <div class="product-detail-carousel__images">
                <div class="product-detail-carousel__carousel js-detail-carousel owl-carousel">
                    <?php
                    $strTitle = (
                          isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
                          ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
                          : $arResult['NAME']
                      );
                    $strAlt = (
                          isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
                          ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
                          : $arResult['NAME']
                    );

                    if(is_array($arImages) && count($arImages) > 0):
                    foreach ($arImages as $arImage):
                      if (IntVal($arImage['DATA']['OFFER_ID'])>0 && $arImage['DATA']['OFFER_ID']!=$PRODUCT['ID']) {
                        continue;
                      }
                      ?>
                      <div class="preview-wrap" data-dot="<img class='owl-preview' data-picture-id='<?=$arImage['PIC']['ID']?>' src='<?=$arImage['PIC']['SRC']?>'>">
                        <a class="js-open_popupgallery" href="<?= $arResult["DETAIL_PAGE_URL"] ?>" title="<?=$strTitle?>">
                            <img class="preview" src="<?=$arImage['PIC']['SRC']?>" alt=<?=$strAlt?> title="<?=$strTitle?>">
                        </a>
                      </div>
                    <?php endforeach; else: ?>
                        <div class="preview-wrap" data-dot="<img class='owl-preview' src='<?=$arResult['NO_PHOTO']['src']?>' data-picture-id='0'">
                          <a class="" title="<?=$strTitle?>">
                              <img class="preview" src="<?=$arResult['NO_PHOTO']['src']?>" alt=<?=$strAlt?> title="<?=$strTitle?>">
                          </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="stickers">
                    <div class="da2_icon hidden-xs"><?=Loc::getMessage('DA2_ICON_TITLE')?></div>
                    <div class="qb_icon hidden-xs"><?=Loc::getMessage('QB_ICON_TITLE')?></div>
                      <?php if($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF'] > 0): ?>
                        <div class="discount_icon hidden-xs"><?='-'.$PRODUCT['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'].'%'?></div>
                      <?php endif; ?>
                </div>
                <div class="marks">
                    <?php if ($arResult['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y'): ?>
                      <span class="marks__item marks__item_action"><?=Loc::getMessage('RSFLYAWAY_SALE');?></span>
                    <?php endif; ?>

                    <?php if ($arResult['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y'): ?>
                      <span class="marks__item marks__item_hit"><?=Loc::getMessage('RSFLYAWAY_HIT');?></span>
                    <?php endif;?>

                    <?php if ($arResult['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y'): ?>
                      <span class="marks__item marks__item_new"><?=Loc::getMessage('RSFLYAWAY_NEW');?></span>
                    <?php endif; ?>
                </div>
                <div class="loss-menu-right views compare-mobile js-compare-box">
                    <a class="selected js-compare-label js-compare-switcher js-toggle-switcher" href="<?=$arResult['DETAIL_PAGE_URL']?>">
                      <i class="fa fa-chart"></i>
                    </a>
                </div>
                <div class="loss-menu-right views order-mobile js-favorite js-favorite-heart" data-elementid="<?=$arResult['ID']?>" data-detailpageurl="<?=$arResult['DETAIL_PAGE_URL']?>">
                    <a class="selected " href="javascript:;">
                      <i class="fa fa-heart"></i>
                    </a>
                </div>
            </div>
            <div class="product-detail-carousel__nav-wrap">
                <div class="product-detail-carousel__nav js-detail-carousel-nav"></div>
            </div>
        </div>
        <div class="product-detail-carousel__bottom">
            <span class="rs_detail-podimg"><?=Loc::getMessage('RSFLYAWAY_LUPA')?></span><br>
            <?php // Timer
            if (!empty($arTimers)):
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
                ?>
                <div class="item_timer hidden-xs">
                	<div class="item_timer__icon"></div>
                    <span class="hidden-xs products-counter">
                        <span class="counter js-timer" data-timer='<?=json_encode($jsTimer)?>'>

                            <span class="timer<?if ($timer['QUANTITY'] <= 0) {?> timer_simple<?}?>">

                                <span class="timer__item">
                                    <span class="timer__item__digit days">0</span>
                                    <span class="timer__item__label"><?=GetMessage('QB_AND_DA2_DAY')?></span>
                                </span>

                                <span class="timer__item">
                                    <?php
                                    if ($timer[$KY]['HOUR'] > 0 && $timer[$KY]['HOUR'] < 10) {
                                      $timer[$KY]['HOUR'] = '0'.$timer[$KY]['HOUR'];
                                    }
                                    ?>
                                    <span class="timer__item__digit hour">0</span>
                                    <span class="timer__item__label"><?=GetMessage('QB_AND_DA2_HOUR')?></span>
                                </span>

                                <span class="timer__item">
                                    <?php
                                    if ($timer[$KY]['MINUTE'] > 0 && $timer[$KY]['MINUTE'] < 10) {
                                      $timer[$KY]['MINUTE'] = '0'.$timer[$KY]['MINUTE'];
                                    }
                                    ?>
                                    <span class="timer__item__digit minute">0</span>
                                    <span class="timer__item__label"><?=GetMessage('QB_AND_DA2_MIN')?></span>
                                </span>

                                <span class="timer__item" style="display:none;">
                                    <span class="timer__item__digit second">0</span>
                                    <span class="timer__item__label"><?=GetMessage('QB_AND_DA2_SEC')?></span>
                                </span>

                                <span class="timer__item">
                                    <span class="timer__item__digit"><?echo ($timer['QUANTITY'] > 99) ? '99+' : sprintf('%02d', $timer['QUANTITY']);?></span>
                                    <span class="timer__item__label"><?=GetMessage('QB_AND_DA2_SHT')?></span>
                                </span>

                            </span>

                            <span class="progress-bar">
                                <?php if ($KY == 'DINAMICA_EX'):?>
                                  <span class="progress-bar__indicator" style="width:50%;"></span>
                                <?php else: ?>
                                  <span class="progress-bar__indicator progress-bar__indicator_cheap" style="width:56%;"></span>
                                <?php endif; ?>
                            </span>

                        </span>
                    </span>
                </div>
                <?php endif; // -/Timer ?>
            </div>
    </div>

    <div class="col col-xs-12 col-sm-12 col-md-6 col-lg-5 product-description">

        <div class="product-box">

            <?php
            $itemArticle = null;
            if($HAVE_OFFERS && !empty($PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']])) {
                $itemArticle = $PRODUCT['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_ARTICLE']]['VALUE'];
            } elseif(!empty($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']])) {
                $itemArticle = $arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']]['VALUE'];
            }
            if(!empty($itemArticle)):
              ?><div class="product-code"><?=Loc::getMessage('RS.FLYAWAY.ARTICLE')?>:<span class="js-article"><?=$itemArticle?></span></div><?php
            endif;
            ?>

            <div class="product-params">
                <span class="product-params__brand">
                <?php
                  $brands = $arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_BRAND']]['VALUE'];
                  if( isset($brands) && $brands!='' ):
                    ?><?=$brands?><?php
                  endif;
                ?>
                </span>
                <span class="product-params__rating">
                    <?$APPLICATION->IncludeComponent(
                      "bitrix:iblock.vote",
                      "flyaway",
                      array(
                        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                        "ELEMENT_ID" => $arResult['ID'],
                        "ELEMENT_CODE" => "",
                        "MAX_VOTE" => "5",
                        "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                        "SET_STATUS_404" => "N",
                        "DISPLAY_AS_RATING" => $arParams['VOTE_DISPLAY_AS_RATING'],
                        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                        "CACHE_TIME" => $arParams['CACHE_TIME']
                      ),
                      $component,
                      array("HIDE_ICONS" => "Y")
                    );?>
                </span>
                <div class="clearfix"></div>
            </div>

        </div>

        <div class="products__prices">
            <?php if (count($PRODUCT['PRICES']) > 1): ?>
                <?php foreach ($arResult['CAT_PRICES'] as $key1 => $titlePrices): ?>
                    <?php if (isset($PRODUCT['PRICES'][$key1])): ?>
                    <div class="prices">
                        <div class="prices__title"><?=$titlePrices['TITLE']?></div>
                        <div class="prices__values">
                            <?php if ($PRODUCT['PRICES'][$key1]['DISCOUNT_DIFF'] > 1):?>
                            <div class="prices__val prices__val_old"><?=$PRODUCT['PRICES'][$key1]['PRINT_VALUE']?></div>
                            <div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div>
                            <?php else: ?>
                            <div class="prices__val prices__val_cool"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php elseif(isset($PRODUCT['MIN_PRICE'])): ?>
                <?php if (isset($arResult['RS_ADD_MEASURE'])): ?>
                <div class="prices">
                    <div class="prices__title"><?=Loc::getMessage('RS.FLYAWAY.PRICE_PER_UNIT', array("#UNIT#" => $PRODUCT['CATALOG_MEASURE_NAME']))?></div>
                    <div class="prices__values">
                        <?php if (intval($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0): ?>
                        <div class="prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div>
                        <div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php else: ?>
                        <div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="prices">
                    <div class="prices__title"><?=Loc::getMessage('RS.FLYAWAY.PRICE_PER_UNIT', array("#UNIT#" => $arResult['RS_ADD_MEASURE']['MEASURE_NAME']))?></div>
                    <div class="prices__values">
                        <?php if (intval($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0): ?>
                        <div class="prices__val prices__val_old"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_VALUE']?></div>
                        <div class="prices__val prices__val_cool prices__val_new js-add-measure__price" data-price="<?=$PRODUCT['MIN_PRICE']['DISCOUNT_VALUE']?>"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_DISCOUNT_VALUE']?></div>
                        <?php else: ?>
                        <div class="prices__val prices__val_cool js-add-measure__price" data-price="<?=$PRODUCT['MIN_PRICE']['DISCOUNT_VALUE']?>"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_DISCOUNT_VALUE']?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="prices">
                    <div class="prices__title"><?=Loc::getMessage('RS.FLYAWAY.TOTAL')?></div>
                    <div class="prices__values">
                        <?php if (intval($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0): ?>
                        <div class="prices__val prices__val_old"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_TOTAL_VALUE']?></div>
                        <div class="prices__val prices__val_cool prices__val_new"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_TOTAL_DISCOUNT_VALUE']?></div>
                        <?php else: ?>
                        <div class="prices__val prices__val_cool js-add-measure__total"><?=$arResult['RS_ADD_MEASURE']['PRICE']['FORMAT_TOTAL_DISCOUNT_VALUE']?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="prices">
                    <div class="prices__title"></div>
                    <div class="prices__values">
                        <?php if (intval($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0): ?>
                        <div class="prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div>
                        <div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php else: ?>
                        <div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (is_array($arResult['OFFERS_EXT']['PROPERTIES']) && count($arResult['OFFERS_EXT']['PROPERTIES'])>0): ?>
        <div class="rs_sku js-sku_props clearfix">
            <?php
            foreach ($arResult['OFFERS_EXT']['PROPERTIES'] as $propCode => $arProperty):
                $isColor = false;
                if (
                    is_array($arParams['OFFER_TREE_COLOR_PROPS']) &&
                    in_array($propCode, $arParams['OFFER_TREE_COLOR_PROPS'])
                 ) {
                    $isColor = true;
                }
            ?>
            <?php if($isColor): ?>
            <div style="display: block"
                    class="rs_sku-prop js-sku_prop"
                    data-code="<?=$propCode?>"
                    data-type="color"
            >
                <i class="rs_sku-prop_name rs_sku-prop_name_color"><?=$arResult['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </i>
                <ul class="rs_sku-options">
                    <?php $firstVal = false; ?>
                    <?php foreach ($arProperty as $value => $arValue): ?>
                    <li
                      class="rs_sku-option js-sku_option
                        <?php
                        if ('Y' == $arValue['FIRST_OFFER']) {
                            ?> checked<?php
                            $firstVal = $arValue;
                        } elseif ('Y' == $arValue['DISABLED_FOR_FIRST']) {
                            ?> disabled<?php
                        }
                        ?>
                      "
                      data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>"
                    >
                        <?php  if (isset($arValue['PICT']) && !empty($arValue['PICT']['SRC'])): ?>
                        <a class="rs_sku-val" href="javascript:;">
                            <div class="rs_sku-icon" style="background-image:url('<?=$arValue['PICT']['SRC']?>')" title="<?=$ar['VALUE'];?>"></div>
                        </a>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

            </div>
            <?php else: ?>
            <div
                class="loss-menu-right rs_sku-prop js-sku_prop"
                data-code="<?=$propCode?>"
                data-type="list"
            >
                <i class="rs_sku-prop_name rs_sku-prop_name"><?=$arResult['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </i>
                <div class="dropdown dropdown_wide rs_select js_select views">
                    <ul class="dropdown-menu list-unstyled rs_select-options" role="menu" aria-labelledby="dropdownMenuOutput">
                        <?php
                        $firstVal = false;
                        foreach ($arProperty as $value => $arValue):
                        ?>
                        <li class="views-item rs_select-option js-sku_option
                            <?php if ('Y' == $arValue['FIRST_OFFER']): $firstVal = $arValue; ?>
                               checked
                            <?php elseif('Y' == $arValue['DISABLED_FOR_FIRST']): ?>
                               disabled
                            <?php endif; ?>"
                         data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>"
                        >
                            <a class="rs_select-val" href="javascript:;"><?=$arValue['VALUE']?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (is_array($firstVal)): ?>
                    <button class="btn btn-default dropdown-toggle rs_select-checked"
                            id="dropdownMenuOutput"
                            type="button"
                            data-toggle="dropdown"
                            aria-expanded="true"
                    >
                        <span class="rs_icon-arr_sd"></span>
                        <span class="rs_select-val js_select-val"><?=$firstVal['VALUE']?></span>
                        <i class="fa fa-angle-down icon-angle-down"></i>
                        <i class="fa fa-angle-up icon-angle-up"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="product-buyblock">
        <?php $name = '['.$arResult['ID'].'] '.$arResult['NAME']; ?>
            <form class="add2basketform js-buyform<?=$arReslt['ID']?><?php if(!$PRODUCT['CAN_BUY']):?> cantbuy<?php endif; ?><?php if($arParams['USE_PRODUCT_QUANTITY'] == "Y"):?> usequantity<?php endif; ?> clearfix" name="add2basketform">
                <input type="hidden" name="action" value="ADD2BASKET">
                <input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>">

                <div  class="loss-menu-right loss-menu-right_last quantity-block">
                    <div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}">
                        <div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button">
                            <input <?php if (isset($arResult['RS_ADD_MEASURE'])): ?>data-add-measure-factor="<?=$arResult['RS_ADD_MEASURE']['MEASURE_FACTOR'];?>"<?php endif; ?> class="select-input js-select-input js-quantity" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO'];?>" type="text" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" name="quantity" autocomplete="off">
                            <span class="select-unit"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><i class="fa fa-angle-down hidden-xs icon-angle-down select-icon"></i><i class="fa fa-angle-up hidden-xs icon-angle-up select-icon"></i>
                        </div>
                        <ul class="dropdown-menu list-unstyled select-menu" role="menu" aria-labelledby="dLabel">
                            <?php for ($i = 1; $i < 10; $i++): ?>
                                <li><a class="js-select-label" href="javascript:;"><?=$PRODUCT['CATALOG_MEASURE_RATIO']*$i;?></a></li>
                            <?php endfor; ?><li><a class="js-select-labelmore" href="javascript:;"><?php echo $PRODUCT['CATALOG_MEASURE_RATIO']*10;?>+</a></li>
                        </ul>
                    </div>
                </div>
                <?php  if (isset($arResult['RS_ADD_MEASURE'])): ?>
                    <div class="product-buyblock__measure">= <span class="js-additional-factor"><?=$arResult['RS_ADD_MEASURE']['MEASURE_FACTOR'];?></span> <?=$arResult['RS_ADD_MEASURE']['MEASURE_NAME']?></div>
                <?php endif ; ?>
                <?php /**<div class="loss-menu-right loss-menu-right_last quantity-block">
                    <div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}">
                        <div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button">
                            <input class="select-input js-select-input js-quantity" data-ratio="<?=$arResult['RS_ADD_MEASURE']['MEASURE_FACTOR'];?>" type="text" value="<?=$arResult['RS_ADD_MEASURE']['MEASURE_FACTOR']?>" name="quantity" autocomplete="off">
                            <span class="select-unit"><?=$arResult['RS_ADD_MEASURE']['MEASURE_NAME']?></span><i class="fa fa-angle-down hidden-xs icon-angle-down select-icon"></i><i class="fa fa-angle-up hidden-xs icon-angle-up select-icon"></i>
                        </div>
                        <ul class="dropdown-menu list-unstyled select-menu" role="menu" aria-labelledby="dLabel">
                            <?php for ($i = 1; $i < 10; $i++): ?>
                                <li><a class="js-select-label" href="javascript:;"><?=$arResult['RS_ADD_MEASURE']['MEASURE_FACTOR']*$i;?></a></li>
                            <?php endfor; ?><li><a class="js-select-labelmore" href="javascript:;"><?php echo $arResult['RS_ADD_MEASURE']['MEASURE_FACTOR']*10;?>+</a></li>
                        </ul>
                    </div>
                </div>
                <?php endif; **/?>

				<button type="submit" rel="nofollow" class="btn btn-default btn2 product-buyblock__addcart submit js-add2basketlink add2basketlink" value="" data-loading-text="<?=Loc::getMessage('RS.FLYAWAY.ADDING2BASKET')?>" data-popup=<?=$arParams["RSFLYAWAY_HIDE_BASKET_POPUP"] == "Y"? "N": "Y"?>><?=Loc::getMessage('RS.FLYAWAY.BTN_BUY1')?></button>
                <a class="btn btn-default btn2 products-button inbasket product-buyblock__addcart" href="<?=$arParams['BASKET_URL']?>"><?=Loc::getMessage('RS.FLYAWAY.BTN_GO2BASKET')?></a>

				<a class="btn btn-default btn-button product-buyblock__buy1click js-buy1click JS-Popup-Ajax" data-insertdata='{"RS_EXT_FIELD_0":"<?=CUtil::JSEscape($name)?>"}' role="button" href="/forms/buy1click/" title="<?=getMessage('RS.FLYAWAY.BUY_1_CLICK')?>"><?=Loc::getMessage('RS.FLYAWAY.BUY_1_CLICK')?></a>
            </form>

			<?php if( $arParams['USE_STORE']=='Y' ): ?>
                <? $APPLICATION->IncludeComponent(
                      'bitrix:catalog.store.amount',
                      'flyaway',
                      array(
                        "ELEMENT_ID" => $arResult["ID"],
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
                        'DATA_QUANTITY' => $arResult['DATA_QUANTITY'],
                        'FIRST_ELEMENT_ID' => $PRODUCT['ID'],
                        'CATALOG_SUBSCRIBE' => $arResult['CATALOG_SUBSCRIBE'],
                      ),
                      $component,
                      array('HIDE_ICONS'=>'Y')
                ); ?>
            <?php endif; ?>

			<?php
			if ($arResult['CATALOG_SUBSCRIBE'] == 'Y') {

			    if ($HAVE_OFFERS) {

			        $APPLICATION->includeComponent(
                        'bitrix:catalog.product.subscribe',
                        'flyaway',
                        array(
                            'PRODUCT_ID' => $arResult['ID'],
                            'BUTTON_ID' => $strMainID.'_subscribe',
                            'BUTTON_CLASS' => 'bx_bt_button bx_medium',
                            'DEFAULT_DISPLAY' => !$PRODUCT['CAN_BUY'],
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );

			    } else {

			        if (!$arResult['CAN_BUY']) {
			            $APPLICATION->includeComponent(
                            'bitrix:catalog.product.subscribe',
                            'flyaway',
                            array(
                                'PRODUCT_ID' => $arResult['ID'],
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
			?>
			<span class="product-actions">
                <span class="product__compare compare icon-east js-compare-box hidden-xs">
                    <a class="js-compare-label js-compare-switcher js-toggle-switcher" href="<?=$arResult['DETAIL_PAGE_URL']?>">
                        <i class="fa fa-align-left"></i>
                        <span><?=Loc::getMessage('RS.FLYAWAY.COMPARE')?></span>
                        <span class="icon-east__label"><?=Loc::getMessage('RS.FLYAWAY.IN_COMPARE')?></span>
                    </a>
                    <span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_COMPARE')?></span>
                    <span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_COMPARE')?></span>
                </span>

                <span class="product__favorite icon-east js-favorite js-favorite-heart hidden-xs"
                  data-elementid = "<?=$arResult['ID']?>"
                  data-detailpageurl="<?=$arResult['DETAIL_PAGE_URL']?>"
                >
                    <a href="javascript:;">
                        <i class="fa fa-heart"></i>
                        <span class=""><?=Loc::getMessage('RS.FLYAWAY.FAVORITE')?></span>
                        <span class="icon-east__label"><?=Loc::getMessage('RS.FLYAWAY.IN_FAVORITE')?></span>
                    </a>
                    <span class="tooltip"><?=Loc::getMessage('RS.FLYAWAY.ADD_FAVORITE')?></span>
                    <span class="tooltip tooltip_hidden"><?=Loc::getMessage('RS.FLYAWAY.DEL_FAVORITE')?></span>
                </span>
            </span>
            <?php if(empty($arParams['RSFLYAWAY_SHOW_DELIVERY']) || $arParams['RSFLYAWAY_SHOW_DELIVERY'] != 'N'): ?>
                <?php if(empty($arParams['RSFLYAWAY_DELIVERY_MODE']) || $arParams['RSFLYAWAY_DELIVERY_MODE'] == 'include_areas'): ?>
                    <?php
                    $sDeliveryHtml = $APPLICATION->GetFileContent(
                        $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/catalog_delivery.php",
                        array(),
                        array(
                            "MODE" => "html",
                            "HIDE_ICONS" => "Y"
                        )
                    );
                    ?>
                    <?php if ($sDeliveryHtml): ?>
                        <div class="product-delivery media">
                            <div class="media-left product-delivery__pic hidden-xs">
                                <div class="product-delivery__icon"></div>
                            </div>
                            <div class="media-body product-delivery__body">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath(SITE_DIR."include_areas/catalog_delivery.php"),
                                array(),
                                array(
                                    "MODE" => "html",
                                    "HIDE_ICONS" => "Y"

                               )
                            );?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?$APPLICATION->IncludeComponent(
                        "redsign:delivery.calculator",
                        "flyaway",
                        array(
                            "CURRENCY" => $arParams['DELIVERY_CURRENCY_ID'],
                            "ELEMENT_ID" => $PRODUCT['ID'],
                            "QUANTITY" => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : 1,
                            "DELIVERY" => array(),
                            "SHOW_DELIVERY_PAYMENT_INFO" => $arParams["RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO"],
                            "DELIVERY_LINK" => $arParams['RSFLYAWAY_DELIVERY_LINK'],
                            "PAYMENT_LINK" => $arParams['RSFLYAWAY_PAYMENT_LINK'],
                            "TAB_DELIVERY" => $arParams['RSFLYAWAY_TAB_DELIVERY']
                        ),
                        false
                    );?>
                <?php endif; ?>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>

        <?php
        if (strlen($arResult['PREVIEW_TEXT'])> 500):
            $rest = substr($arResult['PREVIEW_TEXT'], 0, 500);
        ?>
        <div class="product-announce"><?=$rest;?>...</div>
        <a href="#description" class="product-linkdetail"><?=Loc::getMessage('RS.FLYAWAY.MORE')?></a>
        <?php else: ?>
        <div class="product-announce"><?=$arResult['PREVIEW_TEXT']?></div>
        <?php endif; ?>

        <div class="product-social">
            <div class="product-social__title"><?=Loc::getMessage('RS.FLYAWAY.YASHARE')?></div>
            <script>
                (function() {
                    if (window.pluso)if (typeof window.pluso.start == "function") return;
                    if (window.ifpluso==undefined) { window.ifpluso = 1;
                      var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
                      s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
                      s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
                      var h=d[g]('body')[0];
                      h.appendChild(s);
                }})();
            </script>
            <div class="pluso" data-background="transparent" data-options="big,round,line,horizontal,nocounter,theme=04" data-services="twitter,facebook,google,odnoklassniki,vkontakte"></div>
        </div>
    </div>

    <div class="col col-xs-12 col-md-12 col-lg-2  product-bar">

        <?php $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "recommended",
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
                "FILTER_NAME" => "recommendedFilter",
                "INCLUDE_SUBSECTIONS" => "Y",
                "SHOW_ALL_WO_SECTION" => "Y",
                "HIDE_NOT_AVAILABLE" => "N",
                "PAGE_ELEMENT_COUNT" => "20",
                "LINE_ELEMENT_COUNT" => "5",
                "PROPERTY_CODE" => array(
                    0 => "WE_RECCOMENDED",
                    1 => "",
                ),
                "OFFERS_FIELD_CODE" => array(
                    0 => "PREVIEW_TEXT",
                    1 => "",
                ),
                "OFFERS_PROPERTY_CODE" => $arParams['OFFERS_PROPERTY_CODE'],
                "OFFERS_SORT_FIELD" => $arParams['OFFERS_SORT_FIELD'],
                "OFFERS_SORT_ORDER" => $arParams['OFFERS_SORT_ORDER'],
                "OFFERS_SORT_FIELD2" => $arParams['OFFERS_SORT_FIELD2'],
                "OFFERS_SORT_ORDER2" => $arParams['OFFERS_SORT_ORDER2'],
                "OFFERS_LIMIT" => $arParams['OFFERS_LIMIT'],
                "SECTION_URL" => "",
                "DETAIL_URL" => "",
                "BASKET_URL" => $arParams['BASKET_URL'],
                "ACTION_VARIABLE" => $arParams['ACTION_VARIABLE'],
                "PRODUCT_ID_VARIABLE" => $arParams['PRODUCT_ID_VARIABLE'],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                "CACHE_TIME" => $arParams['CACHE_TIME'],
                "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                "META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
                "META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
                "BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
                "ADD_SECTIONS_CHAIN" => "N",
                "DISPLAY_COMPARE" => "N",
                "SET_TITLE" => "N",
                "SET_STATUS_404" => "N",
                "CACHE_FILTER" => "N",
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "PRODUCT_PROPERTIES" => array(
                    0 => "WE_RECCOMENDED",
                ),
                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                "OFFERS_CART_PROPERTIES" => $arParams['OFFERS_CART_PROPERTIES'],
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "PAGER_TITLE" => Loc::getMessage('RECOMMENDED_PRODUCTS'),
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams['CACHE_TIME'],
                "PAGER_SHOW_ALL" => "N",
                "TITLE_LANG_CODE" => "",
                "MAX_WIDTH" => "200",
                "MAX_HEIGHT" => "200",
                "DISPLAY_TITLE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "SET_META_KEYWORDS" => "Y",
                "SET_META_DESCRIPTION" => "Y",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "DISPLAY_TITLE_TEXT" => Loc::getMessage('RECOMMENDED_PRODUCTS'),
                "PAGE_URL_LIST" => "we_reccomended/",
                "PROPCODE_IMAGES" => "SKU_MORE_PHOTO"
            ),
            false
          );?>

    </div>

    <div class="col col-xs-12 col col-md-9 col-lg-10">
        <div class="tabs" id="product-detail-tabs">
            <ul class="nav nav-tabs ">
                <?php if ($tabDescription): ?>
                    <li class="tabs-item">
                        <a class="tabs-item__label detailtext" href="#description" data-toggle="tab">
                          <?=Loc::getMessage('RS.FLYAWAY.DESCRIPTION')?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($tabProperties): ?>
                    <li class="tabs-item">
                        <a class="tabs-item__label properties" href="#properties" data-toggle="tab">
                          <?=Loc::getMessage('RS.FLYAWAY.PROPERTIES')?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($PRODUCT['HAVE_SET']): ?>
                    <li class="tabs-item">
                        <a class="tabs-item__label detailtext" href="#product-set" data-toggle="tab">
                          <?=Loc::getMessage('RS.FLYAWAY.SET')?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($arResult['TABS']['PROPS_TABS']): ?>
                    <?php foreach($arParams['PROPS_TABS'] as $propCode): ?>
                        <?php if (!empty($arResult['PROPERTIES'][$propCode]['VALUE'])): ?>
                            <li class="tabs-item">
                                <a class="tabs-item__label <?=$propCode?>" href="#prop<?=$propCode?>" data-toggle="tab">
                                  <?=$arResult['PROPERTIES'][$propCode]['NAME']?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if($arParams['RSFLYAWAY_TAB_DELIVERY'] == 'Y' && $arParams['RSFLYAWAY_SHOW_DELIVERY'] != 'N'): ?>
                  <li class="tabs-item">
                      <a class="tabs-item__label" href="#delivery-tab" data-toggle="tab">
                        <?=Loc::getMessage('TITLE_DELIVERY')?>
                      </a>
                  </li>
                <?php endif; ?>
            </ul>

            <div class="tab-content">

                <?php if ($tabDescription): ?>
                <div class="tab-pane" id="description">
                    <div>
                        <h2 class="product-content__title"><?=Loc::getMessage('SET_DETAIL_DESCRIPTION')?></h2>
                        <?=$arResult['DETAIL_TEXT']?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($tabProperties): ?>
                <div class="tab-pane" id="properties">
                    <h2 class="product-content__title"><?=Loc::getMessage('SET_DETAIL_PROPERTYS')?></h2>
                    <?php
                    $APPLICATION->IncludeComponent('redsign:grupper.list',
                        'flyaway',
                        array(
                          'DISPLAY_PROPERTIES' => $arResult['DISPLAY_PROPERTIES_SHOW'],
                          'CACHE_TIME' => 36000,
                        ),
                        $component,
                        array('HIDE_ICONS'=>'Y')
                      );
                    ?>
                </div>
                <?php endif; ?>

                <div class="tab-pane" id="product-set">

                    <?php if ($PRODUCT['HAVE_SET']): ?>
                        <?php if ($HAVE_OFFERS): ?>
                            <?php foreach ($arResult['OFFERS'] as $arOffer): ?>
                            <span id="<? echo 'set_'.$arOffer['ID']; ?>" style="<?php if($PRODUCT['ID'] != $arOffer['ID']) echo 'display: none'; ?>">
                                <?php $APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
                                    "flyaway",
                                    array(
                                        "IBLOCK_ID" => $arOffer["IBLOCK_ID"],
                                        "ELEMENT_ID" => $arOffer['ID'],
                                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                                        "BASKET_URL" => $arParams["BASKET_URL"],
                                        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                        "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                                        "CURRENCY_ID" => $arParams["CURRENCY_ID"]
                                    ),
                                    $component,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if($arResult['HAVE_SET']): ?>
                                <?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
                                    "flyaway",
                                    array(
                                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                        "ELEMENT_ID" => $arResult['ID'],
                                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                                        "BASKET_URL" => $arParams["BASKET_URL"],
                                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                        "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                                        "CURRENCY_ID" => $arParams["CURRENCY_ID"]
                                    ),
                                    $component,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($arResult['DOCUMENT']) && count($arResult['DOCUMENT'])> 0): ?>
                <div id="documentation" class="documentation tab-pane">
                    <h2 class="product-content__title"><?=Loc::getMessage('DOCUMENT_DETAIL')?></h2>
                    <ul class="documentation__list clearfix">
                        <?php foreach($arResult['DOCUMENT'] as $key1=>$arDoc): ?>
                            <?php
                            foreach($arDoc['FILE_VALUE'] as $val):
                                $arVal = explode('.',$val['FILE_NAME']);
                                $end = end($arVal);
                                $name = substr($val['ORIGINAL_NAME'],0, -(strlen($end)+1));
                            ?>
                            <li class="documentation__item">
                                <a class="documentation__label" href="<?=$val['SRC'];?>">
                                    <span class="documentation-icon"></span>
                                    <span class="documentation__detail"><?=$name?></span>
                                    <span class="documentation__link"><?=Loc::getMessage('RSFLYAWAY_DS_DL'); ?> <?=$end.' '.round( $val['FILE_SIZE'] / 1024).Loc::getMessage('RSFLYAWAY_DS_KB');?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale")): ?>

                    <div class="tab-pane panel-collapse collapse" id="GIFTS_DETAIL">
                    	<?$APPLICATION->IncludeComponent(
                    	    "bitrix:sale.gift.product",
                    	    ".default",
                        	    array(
                        			'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                        			'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                        			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
                        			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                        			'SUBSCRIBE_URL_TEMPLATE' => $arResult['~SUBSCRIBE_URL_TEMPLATE'],
                        			'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],

                        			"SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                        			"SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
                        			"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                        			"LINE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                        			"HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
                        			"BLOCK_TITLE" => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
                        			"TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
                        			"SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
                        			"SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
                        			"MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

                        			"SHOW_PRODUCTS_{$arParams['IBLOCK_ID']}" => "Y",
                        			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                        			"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
                        			"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                        			"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
                        			"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                        			"PRICE_CODE" => $arParams["PRICE_CODE"],
                        			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                        			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                        			"BASKET_URL" => $arParams["BASKET_URL"],
                        			"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
                        			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        			"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                        			"USE_PRODUCT_QUANTITY" => 'N',
                        			"OFFER_TREE_PROPS_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFER_TREE_PROPS'],
                        			"CART_PROPERTIES_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFERS_CART_PROPERTIES'],
                        			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        			"POTENTIAL_PRODUCT_TO_BUY" => array(
                        			'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
                        			'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
                        			'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
                        			'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
                        			'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

                        			'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
                        			'SECTION' => array(
                          				'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
                          				'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
                          				'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
                          				'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
                    			    ),
                        		)
                        	),
                        	$component,
                        	array("HIDE_ICONS" => "Y")
                        );?>
                    </div>
                <?php endif; ?>

                <?php if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale")): ?>
					            <div class="tab-pane panel-collapse collapse" id="GIFTS_DETAIL">
                    	<?$APPLICATION->IncludeComponent(
                			"bitrix:sale.gift.main.products",
                			".default",
                			array(
                				"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                				"BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

                				"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                				"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],

                				"AJAX_MODE" => $arParams["AJAX_MODE"],
                				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                				"IBLOCK_ID" => $arParams["IBLOCK_ID"],

                				"ELEMENT_SORT_FIELD" => 'ID',
                				"ELEMENT_SORT_ORDER" => 'DESC',
                				//"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                				//"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                				"FILTER_NAME" => 'searchFilter',
                				"SECTION_URL" => $arParams["SECTION_URL"],
                				"DETAIL_URL" => $arParams["DETAIL_URL"],
                				"BASKET_URL" => $arParams["BASKET_URL"],
                				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],

                				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
                				"CACHE_TIME" => $arParams["CACHE_TIME"],

                				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                				"SET_TITLE" => $arParams["SET_TITLE"],
                				"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                				"PRICE_CODE" => $arParams["PRICE_CODE"],
                				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
                				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                				"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),

                				"ADD_PICT_PROP" => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),

                				"LABEL_PROP" => (isset($arParams["LABEL_PROP"]) ? $arParams["LABEL_PROP"] : ""),
                				"OFFER_ADD_PICT_PROP" => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
                				"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : ""),
                				"SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
                				"SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
                				"MESS_BTN_BUY" => (isset($arParams["MESS_BTN_BUY"]) ? $arParams["MESS_BTN_BUY"] : ""),
                				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["MESS_BTN_ADD_TO_BASKET"]) ? $arParams["MESS_BTN_ADD_TO_BASKET"] : ""),
                				"MESS_BTN_DETAIL" => (isset($arParams["MESS_BTN_DETAIL"]) ? $arParams["MESS_BTN_DETAIL"] : ""),
                				"MESS_NOT_AVAILABLE" => (isset($arParams["MESS_NOT_AVAILABLE"]) ? $arParams["MESS_NOT_AVAILABLE"] : ""),
                				'ADD_TO_BASKET_ACTION' => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
                				'SHOW_CLOSE_POPUP' => (isset($arParams["SHOW_CLOSE_POPUP"]) ? $arParams["SHOW_CLOSE_POPUP"] : ""),
                				'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
                				'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
                			)
                			+ array(
                				'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']) ? $arResult['ID'] : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
                				'SECTION_ID' => $arResult['SECTION']['ID'],
                				'ELEMENT_ID' => $arResult['ID'],
                			),
                			$component,
                			array("HIDE_ICONS" => "Y")
                    	);?>
                	</div>
                <?php endif; ?>

                <?php if ($arResult['TABS']['PROPS_TABS']): ?>
                    <?php
                    foreach ($arParams['PROPS_TABS'] as $propCode):
                        if (empty($arResult['PROPERTIES'][$propCode]['VALUE'])) continue;
                    ?>
                    <div class="tab-pane panel-collapse collapse" id="prop<?=$propCode?>">
                        <?php
                        if (
                            $arResult['PROPERTIES'][$propCode]['PROPERTY_TYPE'] == 'E' &&
                            count($arResult['PROPERTIES'][$propCode]['VALUE']) > 0
                        ):
                        global $lightFilter;
                        $lightFilter = array(
                          'ID' => $arResult['PROPERTIES'][$propCode]['VALUE']
                        );
                        ?>
                        <h2 class="product-content__title"><?=$arResult['PROPERTIES'][$propCode]['NAME']?></h2>
                        <?php
                        $APPLICATION->includeComponent(
                          'bitrix:catalog.section',
                          'light',
                          array(
                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                            'ELEMENT_SORT_FIELD' => $arParams['ELEMENT_SORT_FIELD'],
                            'ELEMENT_SORT_ORDER' => $arParams['ELEMENT_SORT_ORDER'],
                            'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
                            'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
                            'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
                            'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
                            'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
                            'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
                            'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
                            'BASKET_URL' => $arParams['BASKET_URL'],
                            'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                            'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                            'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
                            'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                            'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
                            'FILTER_NAME' => 'lightFilter',
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME'],
                            'CACHE_FILTER' => $arParams['CACHE_FILTER'],
                            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                            'SET_TITLE' => $arParams['SET_TITLE'],
                            'SET_STATUS_404' => $arParams['SET_STATUS_404'],
                            'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
                            'PAGE_ELEMENT_COUNT' => $arParams['PAGE_ELEMENT_COUNT'],
                            'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
                            'PRICE_CODE' => $arParams['PRICE_CODE'],
                            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                            'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

                            'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
                            'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                            'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
                            'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
                            'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],

                            'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
                            'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
                            'PAGER_TITLE' => $arParams['PAGER_TITLE'],
                            'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
                            'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
                            'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
                            'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                            'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],

                            'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
                            'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
                            'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],
                            'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
                            'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
                            'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
                            'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
                            'OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'],

                            'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                            'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
                            'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
                            'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
                            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                            'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
                            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                            "SHOW_ALL_WO_SECTION" => "Y",
                            "RSFLYAWAY_USE_FAVORITE" => $arParams['RSFLYAWAY_USE_FAVORITE'],
                            // store
                            'USE_STORE' => $arParams['USE_STORE'],
                            'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
                            'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
                            'MAIN_TITLE' => $arParams['MAIN_TITLE'],
                            'SHOW_GENERAL_STORE_INFORMATION' => 'Y',
                            //"STORES_FIELDS" => $arParams['FIELDS'],
                            // flyaway
                            "RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
                            "RSFLYAWAY_PROP_SKU_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_SKU_MORE_PHOTO"],
                            "RSFLYAWAY_PROP_ARTICLE" => $arParams["RSFLYAWAY_PROP_ARTICLE"],
                            "SIDEBAR" => $arResult["SIDEBAR"],
                            "RSFLYAWAY_TEMPLATE" => $alfaCTemplate,
                            "RSFLYAWAY_USE_FAVORITE" => $arParams['RSFLYAWAY_USE_FAVORITE'],

                            "RSFLYAWAY_PROP_PRICE" => $arParams["RSFLYAWAY_PROP_PRICE"],
                            "RSFLYAWAY_PROP_DISCOUNT" => $arParams["RSFLYAWAY_PROP_DISCOUNT"],
                            "RSFLYAWAY_PROP_CURRENCY" => $arParams["RSFLYAWAY_PROP_CURRENCY"],
                            "RSFLYAWAY_PROP_PRICE_DECIMALS" => $arParams["RSFLYAWAY_PROP_PRICE_DECIMALS"],
                            "RSFLYAWAY_PROP_QUANTITY" => $arParams["RSFLYAWAY_PROP_QUANTITY"],
                            "RSFLYAWAY_PROP_BRAND" => $arParams["RSFLYAWAY_PROP_BRAND"],
                            "RSFLYAWAY_PROP_OFF_POPUP" => $arParams["RSFLYAWAY_PROP_OFF_POPUP"],
                          ),
                          $component
                        );
                        ?>
                        <?php elseif(
                            $arResult['PROPERTIES'][$propCode]['PROPERTY_TYPE']=='F' &&
                            count($arResult['PROPERTIES'][$propCode]['VALUE']) > 0 &&
                            $arResult['PROPERTIES'][$propCode]['VALUE']
                        ): ?>
                        <h2 class="product-content__title"><?=$arResult['PROPERTIES'][$propCode]['NAME']?></h2>
                        <ul class="documentation__list clearfix">
                            <?php foreach($arResult['PROPERTIES'][$propCode]['VALUE'] as $arFile):?>
                                <li class="documentation__item">
                                    <a class="documentation__label" href="<?=$arFile['SRC'];?>">
                                        <span class="documentation-icon"></span>
                                        <span class="documentation__detail"><?=$arFile['ORIGINAL_NAME']?></span>
                                        <span class="documentation__link"><?=Loc::getMessage('RSFLYAWAY_DS_DL'); ?> <?=end($arFile).' '.round( $arFile['FILE_SIZE'] / 1024).Loc::getMessage('RSFLYAWAY_DS_KB');?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <h2 class="product-content__title"><?=$arResult['PROPERTIES'][$propCode]['NAME']?></h2>
                            <?=$arResult['DISPLAY_PROPERTIES'][$propCode]["DISPLAY_VALUE"]?>
                        <?php endif; ?>
                    </div>


                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="tab-pane" id="delivery-tab">
                    <br><a class="btn btn2 js-calc_delivery"><?=Loc::getMessage('RS.FLYAWAY.CALCULATE_DELIVERY');?></a>
                </div>

            </div>

        </div>

    </div>

    <div class="col col-xs-12 col col-md-9 col-lg-10">
    <?php
    if(
        !empty($arParams['USE_CUSTOM_COLLECTION']) && $arParams['USE_CUSTOM_COLLECTION'] == "Y" &&
        !empty($arResult['PROPERTIES']['ELEMENTS_OF_COLLECTION']) &&
        is_array($arResult['PROPERTIES']['ELEMENTS_OF_COLLECTION']['VALUE']) &&
        count($arResult['PROPERTIES']['ELEMENTS_OF_COLLECTION']['VALUE']) > 0
    ):
        global $collectionFilter;
        $collectionFilter = array(
          'ID' => $arResult['PROPERTIES']['ELEMENTS_OF_COLLECTION']['VALUE']
        );
        $collectionIblockId = $arResult['PROPERTIES']['ELEMENTS_OF_COLLECTION']['IBLOCK_ID'];
        $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            'collection',
            array(
                'IBLOCK_TYPE' => '',
                'IBLOCK_ID' => $collectionIblockId,
                "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                "INCLUDE_SUBSECTIONS" => "Y",
                "SHOW_ALL_WO_SECTION" => "Y",
                "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "FILTER_NAME" => "collectionFilter",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
                "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
                "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                'USE_STORE' => $arParams['USE_STORE'],
                'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
                'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
                'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                "STORES_FIELDS" => $arParams['STORES_FIELDS'],
                'PAGE_ELEMENT_COUNT' => '100',
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams['CACHE_TIME'],
                "PAGER_SHOW_ALL" => "N",
                "BASKET_URL" => $arParams["BASKET_URL"],
                "RSFLYAWAY_PROP_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
                "RSFLYAWAY_PROP_SKU_MORE_PHOTO" => $arParams["RSFLYAWAY_PROP_SKU_MORE_PHOTO"],
            ),
            $component
        );

      endif;
      ?>
    </div>

    <div class="col col-xs-12 visible-xs visible-sm">
        <div class="product-recom">
            <div class="product-recom-title"><?=Loc::getMessage('RECOMMENDED_PRODUCTS'); ?></div>
        </div>

        <div class="reccom_mobile">

            <?php $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "light",
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
                    "FILTER_NAME" => "recommendedFilter",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "PAGE_ELEMENT_COUNT" => "20",
                    "LINE_ELEMENT_COUNT" => "5",
                    "PROPERTY_CODE" => array(
                        0 => "WE_RECCOMENDED",
                        1 => "",
                    ),
                    "OFFERS_FIELD_CODE" => array(
                        0 => "PREVIEW_TEXT",
                        1 => "",
                    ),
                    "OFFERS_PROPERTY_CODE" => $arParams['OFFERS_PROPERTY_CODE'],
                    "OFFERS_SORT_FIELD" => $arParams['OFFERS_SORT_FIELD'],
                    "OFFERS_SORT_ORDER" => $arParams['OFFERS_SORT_ORDER'],
                    "OFFERS_SORT_FIELD2" => $arParams['OFFERS_SORT_FIELD2'],
                    "OFFERS_SORT_ORDER2" => $arParams['OFFERS_SORT_ORDER2'],
                    "OFFERS_LIMIT" => $arParams['OFFERS_LIMIT'],
                    "SECTION_URL" => "",
                    "DETAIL_URL" => "",
                    "BASKET_URL" => $arParams['BASKET_URL'],
                    "ACTION_VARIABLE" => $arParams['ACTION_VARIABLE'],
                    "PRODUCT_ID_VARIABLE" => $arParams['PRODUCT_ID_VARIABLE'],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                    "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                    "META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
                    "META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
                    "BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
                    "ADD_SECTIONS_CHAIN" => "N",
                    "DISPLAY_COMPARE" => "N",
                    "SET_TITLE" => "N",
                    "SET_STATUS_404" => "N",
                    "CACHE_FILTER" => "N",
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "PRODUCT_PROPERTIES" => array(
                        0 => "WE_RECCOMENDED",
                    ),
                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                    "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                    "OFFERS_CART_PROPERTIES" => $arParams['OFFERS_CART_PROPERTIES'],
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => Loc::getMessage('RECOMMENDED_PRODUCTS'),
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams['CACHE_TIME'],
                    "PAGER_SHOW_ALL" => "N",
                    "TITLE_LANG_CODE" => "",
                    "MAX_WIDTH" => "200",
                    "MAX_HEIGHT" => "200",
                    "DISPLAY_TITLE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "SET_META_KEYWORDS" => "Y",
                    "SET_META_DESCRIPTION" => "Y",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "DISPLAY_TITLE_TEXT" => Loc::getMessage('RECOMMENDED_PRODUCTS'),
                    "PAGE_URL_LIST" => "we_reccomended/",
                    "PROPCODE_IMAGES" => "SKU_MORE_PHOTO"
                ),
                false
            );?>
        </div>

    </div>

</div>
<?php if ('Y' == $arParams['USE_COMMENTS']): ?>
    <?php $this->SetViewTarget('comments'); ?>
    <div class="col col-xs-12 col col-md-9 col-lg-10">
            <?$APPLICATION->IncludeComponent(
            "bitrix:catalog.comments",
            "flyaway",
            array(
                "ELEMENT_ID" => $arResult['ID'],
                "ELEMENT_CODE" => "",
                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                "SHOW_DEACTIVATED" => 'N',
                "URL_TO_COMMENT" => "",
                "WIDTH" => "",
                "COMMENTS_COUNT" => "15",
                "BLOG_USE" => 'Y',
                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                "CACHE_TIME" => $arParams['CACHE_TIME'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                "BLOG_TITLE" => "",
                "BLOG_URL" => $arParams['BLOG_URL'],
                "PATH_TO_SMILE" => "",
                "EMAIL_NOTIFY" => $arParams['BLOG_EMAIL_NOTIFY'],
                "AJAX_POST" => "Y",
                "SHOW_SPAM" => "Y",
                "SHOW_RATING" => "N",
            ),
            $component,
            array("HIDE_ICONS" => "Y")
        );?>
    </div>
    <?php $this->EndViewTarget(); ?>
<?php endif; ?>

<script>
    new CatalogElement(<?=$arResult['ID']?>, <?=CUtil::PhpToJSObject($arResult['JSON_EXT']) ?>);
    <?php
    if (isset($arResult['RS_ADD_MEASURE'])) {
        $currencyFormat = CCurrencyLang::GetFormatDescription($arResult['RS_ADD_MEASURE']['CURRENCY']);
        ?>
        BX.Currency.defaultCurrency = '<?=$arResult['RS_ADD_MEASURE']['CURRENCY']?>';
        BX.Currency.setCurrencyFormat(BX.Currency.defaultCurrency, <?=CUtil::PhpToJSObject($currencyFormat, false, true); ?>);
        <?
    }
    ?>

    if(!window.rsFlyaway.products) {
        window.rsFlyaway.products = {};
    }
    window.rsFlyaway.products['<?=$arResult['ID']?>'] = {'pictures':<?=CUtil::PhpToJSObject($arImages)?>};
</script>
