<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$templateData = array(
  'CURRENCIES' => CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true)
);

$curJsId = $this->randString(); 
$names = "[".$arResult["ELEMENT"]["ID"]."]".$arResult["ELEMENT"]["NAME"]."; ";
?>

<div id="bx-set-const-<?=$curJsId?>" class="bx-modal-container container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="bx-modal-small-title"><?=GetMessage("CATALOG_SET_BUY_SET")?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="bx-original-item-container catalog_item js-main-elem-set" data-elemmain="<?='['.$arResult['ELEMENT']['ID'].'] '.$arResult['ELEMENT']['NAME'].'; ';?>">
                <div class="catalog_item__inner">
                    <div class="catalog_item__pic">
                        <?if (isset($arResult['ELEMENT']['FIRST_PIC'][0])):?>
                            <img class="catalog_item__img" src="<?=$arResult['ELEMENT']['FIRST_PIC'][0]['RESIZE']['small']['src']?>" style="width: 70px;height: auto;" alt="<?=$arResult['ELEMENT']['NAME']?>">
                        <?else:?>
                            <img class="catalog_item__img" src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png" style="width: 70px;height: auto;" alt="">
                        <?endif?>
                    </div>
                    <div class="catalog_item__head">
                        <div class="catalog_item__name text_fade js-product__name"><?=$arResult["ELEMENT"]["NAME"]?></div>
                        <div class="catalog_item__brand b-text_fade">
                            <?=GetMessage("MEASURE_NUM")?><?=$arResult["ELEMENT"]["BASKET_QUANTITY"];?> <?=$arResult["ELEMENT"]["MEASURE"]["SYMBOL_RUS"];?>
                        </div>
                    </div>
                    <div class="catalog_item__price price">
                        <div class="bx-added-item-new-price price__pdv"><?=$arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"]?></div>
                        <?if ($arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE"] > 0):?>
                            <div class="price__pdd js-price_pdd-2">- <?=$arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE"]?></div>
                        <?endif;?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="bx-added-item-table-container">
                <table class="bx-added-item-table">
                    <tbody data-role="set-items">
                    <?foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem):?>
                        <tr
                            data-id="<?=$arItem["ID"]?>"
                            data-img="<?=(isset($arItem['FIRST_PIC'][0]) ? $arItem['FIRST_PIC'][0]['RESIZE']['small']['src'] : '')?>"
                            data-url="<?=$arItem["DETAIL_PAGE_URL"]?>"
                            data-name="<?=$arItem["NAME"]?>"
                            data-price="<?=$arItem["PRICE_DISCOUNT_VALUE"]?>"
                            data-print-price="<?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?>"
                            data-old-price="<?=$arItem["PRICE_VALUE"]?>"
                            data-print-old-price="<?=$arItem["PRICE_PRINT_VALUE"]?>"
                            data-diff-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"
                            data-diff-print-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE"]?>"
                            data-measure="<?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?>"
                            data-quantity="<?=$arItem["BASKET_QUANTITY"]; ?>"
                        >
                            <td class="bx-added-item-table-cell-img">
                                <?php if (isset($arItem['FIRST_PIC'][0])): ?>
                                    <img
                                        class="img-responsive"
                                        src="<?=$arItem['FIRST_PIC'][0]['RESIZE']['small']['src']?>"
                                        title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" 
                                    >
                                <?php else: ?>
                                    <img
                                        class="img-responsive"
                                        src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png"
                                        title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" 
                                    >
                                <?php endif; ?>
                            </td>
                            <td class="bx-added-item-table-cell-itemname">
                                <a class="tdn" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a><br>
                                <?=GetMessage("MEASURE_NUM")?><?=$arItem["BASKET_QUANTITY"];?> <?=$arItem["MEASURE"]["SYMBOL_RUS"];?>
                            </td>
                            <td class="bx-added-item-table-cell-price price">
                                <?if ($arItem["PRICE_VALUE"] != $arItem["PRICE_DISCOUNT_VALUE"]):?>
                                    <div class="bx-added-item-old-price price__pv"><?=$arItem["PRICE_PRINT_VALUE"]?></div>
                                <?endif?>
                                <span class="bx-added-item-new-price price__pdv"><?= $arItem["PRICE_PRINT_DISCOUNT_VALUE"]?></span>
                                
                            </td>
                            <td class="bx-added-item-table-cell-del"><div class="bx-added-item-delete" data-role="set-delete-btn"></div></td>
                        </tr>
                    <?
                    $names .= '['.$arItem["ID"].']'." ".$arItem["NAME"]."; ";
                    endforeach;?>
                    </tbody>
                </table><div style="display: none;" data-set-message="empty-set"></div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="bx-constructor-container-result detail__buy">
                <div class="detail__price price">
                    <div class="bx-added-item-old-price price__pv" data-role="set-old-price"><?
                        if ($arResult["SET_ITEMS"]["OLD_PRICE"])
                        {
                            ?><?=$arResult["RIGHT_ALL_PRICE"]?><?
                        }
                    ?></div>
                    <div class="bx-item-set-current-price price__pdv" data-role="set-price"><?=$arResult["RIGHT_ALL_PRICE_DISCOUNT"]?></div>
                    <div class="bx-item-set-current-price discount_price_block" <?echo($arResult["RIGHT_ALL_DISCOUNT"] < 1 ? 'style="display:none"' : '');?>>
                        <div><?=GetMessage("CATALOG_SET_DISCOUNT")?></div>
                        <span class="discount_price_block_pr" data-role="set-diff-price">
                            <?=$arResult["RIGHT_ALL_DISCOUNT"]?>
                        </span>
                    </div>
                </div>
                <div class="detail__btns">
                    <a href="javascript:void(0)" data-role="set-buy-btn" class="detail__add2cart add2cart btn js-add2cart">
                        <svg class="icon icon-cart icon-svg"><use xlink:href="#svg-cart"></use></svg>
                        <?=GetMessage("CATALOG_SET_BUY")?>
                    </a>
                    <a class="detail__buy1click buy1click btn js-ajax_link" data-insert_data='{"RS_EXT_FIELD_0":"<?=CUtil::JSEscape($names)?>"}' href="<?=SITE_DIR?>forms/buy1click/" rel="nofollow">
                          <svg class="icon icon-phone icon-svg"><use xlink:href="#svg-phone-big"></use></svg>
                          <?=GetMessage("BUY_1_CLICK")?>                               
                    </a>
                </div>
                <div class="show_more_set">
                    <span class="anchor"><?=GetMessage("DO_OUR_SET");?></span><svg class="icon icon-right icon-svg"><use xlink:href="#svg-right"></use></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bx-catalog-set-topsale-slider-box" style="display:none;">
        <div class="bx-catalog-set-topsale-slider-container">
            <div class="bx-catalog-set-topsale-slids bx-catalog-set-topsale-slids-<?=$curJsId?>" data-role="set-other-items">
                <?
                $first = true;
                foreach($arResult["SET_ITEMS"]["OTHER"] as $key => $arItem):?>
                <div class="bx-catalog-set-item-container bx-catalog-set-item-container-<?=$curJsId?> catalog_item catalog_item__inner"
                    data-id="<?=$arItem["ID"]?>"
                    data-img="<?=(isset($arItem['FIRST_PIC'][0]) ? $arItem['FIRST_PIC'][0]['RESIZE']['small']['src'] : '')?>"
                    data-url="<?=$arItem["DETAIL_PAGE_URL"]?>"
                    data-name="<?=$arItem["NAME"]?>"
                    data-price="<?=$arItem["PRICE_DISCOUNT_VALUE"]?>"
                    data-print-price="<?=$arItem["PRICE_PRINT_DISCOUNT_VALUE"]?>"
                    data-old-price="<?=$arItem["PRICE_VALUE"]?>"
                    data-print-old-price="<?=$arItem["PRICE_PRINT_VALUE"]?>"
                    data-diff-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"]?>"
                    data-diff-print-price="<?=$arItem["PRICE_DISCOUNT_DIFFERENCE"]?>"
                    data-measure="<?=$arItem["MEASURE"]["SYMBOL_RUS"]; ?>"
                    data-quantity="<?=$arItem["BASKET_QUANTITY"]; ?>"<?
                if (!$arItem['CAN_BUY'] && $first)
                {
                    echo 'data-not-avail="yes"';
                    $first = false;
                }
                ?>
                >
                    <div class="bx-catalog-set-item">
                        <div class="bx-catalog-set-item-img">
                            <div class="bx-catalog-set-item-img-container">
                            <?if ($arItem['FIRST_PIC'][0]):?>
                                <img src="<?=$arItem['FIRST_PIC'][0]['RESIZE']['small']["src"]?>" class="img-responsive" alt="<?=$arItem['NAME']?>">
                            <?else:?>
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png" class="img-responsive">
                            <?endif?>
                            </div>
                        </div>
                        <div class="bx-catalog-set-item-title">
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                        </div>
                        <div class="bx-catalog-set-item-price clearfix">
                            <div class="bx-catalog-set-item-price-new price__pdv"><?= $arItem["PRICE_PRINT_DISCOUNT_VALUE"]?></div>
                            <?if ($arItem["PRICE_VALUE"] != $arItem["PRICE_DISCOUNT_VALUE"]):?>
                                <div class="bx-catalog-set-item-price-dics price__pdd js-price_pdd-2">- <?=$arItem["PRICE_DISCOUNT_DIFFERENCE"]?></div>
                            <?endif?>
                        </div>
                        <div class="bx-catalog-set-item-add-btn">
                            <?
                            if ($arItem['CAN_BUY'])
                            {
                                ?><a href="javascript:void(0)" data-role="set-add-btn" class="btn-add catalog_item__add2cart btn btn1 add2cart"><?= GetMessage("CATALOG_SET_BUTTON_ADD") ?></a><?
                            }
                            else
                            {
                                ?><span class="bx-catalog-set-item-notavailable"><? echo GetMessage('CATALOG_SET_MESS_NOT_AVAILABLE'); ?></span><?
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?endforeach?>
            </div>
            
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
<? 
$arJsParams = array(
    "numSliderItems" => count($arResult["SET_ITEMS"]["OTHER"]),
    "numSetItems" => count($arResult["SET_ITEMS"]["DEFAULT"]),
    "jsId" => $curJsId,
    "parentContId" => "bx-set-const-".$curJsId,
    "ajaxPath" => $this->GetFolder().'/ajax.php',
    "currency" => $arResult["ELEMENT"]["PRICE_CURRENCY"],
    "mainElementPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"],
    "mainElementOldPrice" => $arResult["ELEMENT"]["PRICE_VALUE"],
    "mainElementDiffPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
    "mainElementBasketQuantity" => $arResult["ELEMENT"]["BASKET_QUANTITY"],
    "lid" => SITE_ID,
    "iblockId" => $arParams["IBLOCK_ID"],
    "basketUrl" => $arParams["BASKET_URL"],
    "setIds" => $arResult["DEFAULT_SET_IDS"],
    "offersCartProps" => $arParams["OFFERS_CART_PROPERTIES"],
    "itemsRatio" => $arResult["BASKET_QUANTITY"],
    "noFotoSrc" => SITE_TEMPLATE_PATH.'/assets/img/noimg.png',
    "messages" => array(
        "EMPTY_SET" => GetMessage('CT_BCE_CATALOG_MESS_EMPTY_SET'),
        "ADD_BUTTON" => GetMessage("CATALOG_SET_BUTTON_ADD"),
        "MEASURE_NUM" => GetMessage("MEASURE_NUM")
    )
);
?>
<script type="text/javascript">
    BX.ready(function(){
        new BX.Catalog.SetConstructor(<?=CUtil::PhpToJSObject($arJsParams, false, true, true)?>);
    });
</script>