<?php
use \Bitrix\Main\Localization\Loc;
use Bitrix\Sale\DiscountCouponsManager;

$skipCollumns = array(
    'PROPS', 'DELAY', 'DELETE', 'WEIGHT'
);
?>
<table class="table basket-table" id="basket_items">
    <tbody>
        <?php
        foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):

            if($arItem['DELAY'] == 'Y' || $arItem['CAN_BUY'] == 'N') {
                continue;
            }

            $ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 1;
            $useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
            $useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");

        ?>
        <tr id="<?=$arItem['ID']?>" class="js-element">
            <?php
            foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):

                if(in_array($arHeader['id'], $skipCollumns)) {
                    continue;
                }

            ?>

                <?php if($arHeader['id'] == 'NAME'): // PICTURE AND ITEM BLOCK ?>

                <td class="basket-table__itemphoto">
                    <div class="hidden">
                        <?php var_dump($arItem); ?>
                    </div>
                    <?php
                    $itemPictureUrl = $arResult['NO_PHOTO']['src'];
                    if(strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0) {
                        $itemPictureUrl = $arItem["PREVIEW_PICTURE_SRC"];;
                    } elseif(strlen($arItem["DETAIL_PICTURE_SRC"]) > 0) {
                        $itemPictureUrl = $arItem["DETAIL_PICTURE_SRC"];
                    }
                    ?>

                    <div class="basket-table__photo js-item_picture" style="background-image: url(<?=$itemPictureUrl?>)"> </div>
                </td>

                <td class="basket-table__item">

                    <div class="row">

                        <div class="basket-table__name col-xs-11 col-md-12">
                            <?php if(strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"] ?>" class="js-item_name">
                                    <?=$arItem["NAME"]?>
                                </a>
                            <?php else: ?>
                                <?=$arItem["NAME"]?>
                            <?php endif; ?>
                        </div>

                        <div class="basket-table__menu col-xs-1 hidden-md hidden-lg">
                            <a href="#itemedit_<?=$arItem['ID']?>" role="button" data-toggle="collapse">
                                <span></span>
                            </a>
                        </div>

                        <div class="basket-table__descr hidden-xs hidden-sm col col-xs-12">
                            <?php if(!empty($arResult['DESCRIPTIONS'][$arItem['PRODUCT_ID']])): ?>
                                <?=$arResult['DESCRIPTIONS'][$arItem['PRODUCT_ID']]?>
                            <?php endif; ?>
                        </div>

                        <div class="col col-xs-8  hidden-md hidden-lg">
                            <div class="basket-table__itemprice">
                                <i class="small"><?=$arItem["NOTES"]?></i><br>
                                <span class="js-item-price prices__val prices__val_normal"><?=$arItem["PRICE_FORMATED"]?></span>
                            </div>
                            <div class="basket-table__itemsum">
                                <i class="small"><?=Loc::getMessage('SALE_SUM');?></i><br>
                                <span class="h4"><b class="js-item-sum prices__val prices__val_cool"><?=$arItem["SUM"]?></b></span>
                            </div>
                        </div>
                        <div class="col col-xs-4 hidden-md hidden-lg">
                            <div class="quantity-wrap">

                                <div class="loss-menu-right loss-menu-right_last">
                                    <div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}">
                                        <div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button">
                                            <input
                                                type="text"
                                                name="quantity-mobile"
                                                value="<?=$arItem["QUANTITY"]?>"
                                                class="select-input js-select-input js-quantity js-quantity-basket-mobile"
                                                data-ratio="<?=$ratio;?>"
                                            ><span class="select-unit"><?=$arItem["MEASURE_TEXT"]?></span>
                                            <i class="fa fa-angle-down icon-angle-down select-icon"></i>
                                            <i class="fa fa-angle-up icon-angle-up select-icon"></i>
                                        </div>
                                        <ul class="dropdown-menu list-unstyled select-menu" role="menu" aria-labelledby="dLabel">
                                            <?php for($i = 1; $i < 10; $i++): ?>
                                                <li>
                                                    <a class="js-select-label" href="javascript:;"><?=$ratio*$i;?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li><a class="js-select-labelmore" href="javascript:;"><?=$ratio*10;?>+</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>

                            <?php if (!empty($arItem["SKU_DATA"]) && is_array($arItem["SKU_DATA"])): ?>
                            <div class="col col-xs-12 hidden-xs hidden-sm basket-table-sku">
                                <?php
                                $dropdownSkuBuffer = '';
                                foreach ($arItem["SKU_DATA"] as $propId => $arProp):
                                    $isImgProperty = false;
                                    if (!empty($arProp["VALUES"]) && is_array($arProp["VALUES"])) {
                                        foreach ($arProp["VALUES"] as $id => $arVal) {

                                            if (
                                                !empty($arVal["PICT"]) && is_array($arVal["PICT"]) &&
                                                !empty($arVal["PICT"]['SRC'])
                                            ) {
                                                $isImgProperty = true;
                                                break;
                                            }

                                        }
                                    }
                                    $countValues = count($arProp["VALUES"]);
                                    $full = ($countValues > 5) ? "full" : "";

                                    if ($isImgProperty):
                                ?>
                                    <i class="basket-table-sku__prop-name"><?=$arProp['NAME']?>: </i>
                                    <ul
                                      id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
                                      class="basket-table-sku__prop-list js-sku-prop__<?=$arProp["CODE"]?>"
                                      data-sku-prop-type="image"
                                    >
                                        <?php
                                        foreach ($arProp["VALUES"] as $valueId => $arSkuValue):
                                            $selected = "";
                                            foreach ($arItem["PROPS"] as $arItemProp):
                                                if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
                                                {
                                                    if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
                                                        $selected = " active";
                                                }
                                            endforeach;
                                        ?>
                                            <li class="sku_prop<?=$selected?> js-sku_prop"
                                                data-value-id="<?=$arSkuValue["XML_ID"]?>"
                                                data-element="<?=$arItem["ID"]?>"
                                                data-property="<?=$arProp["CODE"]?>"
                                            >
                                                <a href="javascript:void(0)" class="cnt">
                                                    <span class="cnt_item" style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>"></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                        <?php ob_start(); ?>
                                    <div class="basket-table-sku__dropdown js-sku-prop__<?=$arProp["CODE"]?>">
                                        <i class="basket-table-sku__prop-name"><?=$arProp['NAME']?>: </i>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-default dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false" type="button"
                                            >
                                                <span class="js-sku_prop-value">
                                                    <?php
                                                    $isSelected = false;
                                                    foreach ($arProp["VALUES"] as $valueId => $arSkuValue):
                                                        foreach ($arItem["PROPS"] as $arItemProp):
                                                            if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"]) {
                                                                if ($arItemProp["VALUE"] == $arSkuValue["NAME"]) {
                                                                    echo $arSkuValue["NAME"];
                                                                    $isSelected = true;
                                                                }
                                                            }
                                                        endforeach;
                                                    endforeach;
                                                    if(!$isSelected) {
                                                        echo Loc::getMessage('RS.FLYAWAY.SKU_NO_SELECT');
                                                    }
                                                    ?>
                                                </span>
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul
                                                id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
                                                class="dropdown-menu views-box drop-panel sku_prop_list"
                                                role="menu" aria-labelledby="dLabel"
                                            >
                                                <?php foreach ($arProp["VALUES"] as $valueId => $arSkuValue): ?>
                                                <li data-value-id="<?=($arProp['TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory' ? $arSkuValue['XML_ID'] : $arSkuValue['NAME']); ?>"
                                                    data-element="<?=$arItem["ID"]?>"
                                                    data-property="<?=$arProp["CODE"]?>"
                                                    class="views-item sku_prop"
                                                >
                                                    <a href="javascript:void(0)" class="cnt"><?=$arSkuValue["NAME"]?></a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                        <?php $dropdownSkuBuffer .= ob_get_clean(); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                             </div>
                            <div class="col col-xs-12 hidden-xs hidden-sm basket-table-sku">
                                <div class="loss-menu-right">
                                    <?=$dropdownSkuBuffer; $dropdownSkuBuffer = ''?>
                                </div>
                            </div>
                            <?php endif; ?>


                        <div class="col col-md-12 hidden-xs hidden-sm">
                            <span class="basket-table__itemid">
                                <?php
                                $itemArticle = null;
                                if(!empty($arItem['PROPERTY_'.$arParams['RSFLYAWAY_PROP_SKU_ARTICLE'].'_VALUE'])) {
                                    $itemArticle = $arItem['PROPERTY_'.$arParams['RSFLYAWAY_PROP_SKU_ARTICLE'].'_VALUE'];
                                } elseif(!empty($arItem['PROPERTY_'.$arParams['RSFLYAWAY_PROP_ARTICLE'].'_VALUE'])) {
                                    $itemArticle = $arItem['PROPERTY_'.$arParams['RSFLYAWAY_PROP_ARTICLE'].'_VALUE'];
                                }
                                if(!empty($itemArticle)):
                                ?>
                                <i class="small"><?=Loc::getMessage('RS.FLYAWAY.ID')?>:</i> <span class="js-article"><?=$itemArticle?></span>
                                <?php endif; ?>
                            </span>
                            <?php if($arParams["RSFLYAWAY_AVAL_BASKET"] == "Y"):?>
                            <span class="stores">
                                <span class="stores-label"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY')?></span>:
                                <?php if((int)$arItem['AVAILABLE_QUANTITY'] > 0): ?>
                                    <span class="stores-icon  stores-full"></span><span class="genamount"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY_ISSET')?></span>
                                <?php else: ?>
                                    <span class="stores-icon"></span><span class="genamount"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY_EMPTY')?></span>
                                <?php endif; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                </td>

                <?php elseif($arHeader['id'] == "PRICE"): ?>
                <td class="hidden-xs hidden-sm basket-table__price">
                    <i class="small"><?=$arItem["NOTES"]?></i>
                    <div><span class="h4"><b class="js-item-price prices__val prices__val_cool"><?=$arItem["PRICE_FORMATED"]?></b></span></div>
                </td>
                <?php elseif($arHeader['id'] == "SUM"): ?>
                <td class="hidden-xs hidden-sm basket-table__sum">
                    <i class="small"><?=Loc::getMessage('SALE_SUM')?></i>
                    <div><span class="h4"><b class="js-item-sum prices__val prices__val_cool"><?=$arItem[$arHeader["id"]]?></b></span></div>
                </td>
                <?php elseif($arHeader['id'] == 'QUANTITY' ): ?>
                    <td class="hidden-xs hidden-sm basket-table__quantity text-center">
                        <div class="loss-menu-right loss-menu-right_last">
                            <div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}">
                                <div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button">
                                    <input
                                        type="text"
                                        id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
                                        name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
                                        value="<?=$arItem["QUANTITY"]?>"
                                        class="select-input js-select-input js-quantity"
                                        data-ratio="<?=$ratio;?>"
                                        onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)"
                                    ><span class="select-unit"><?=$arItem["MEASURE_TEXT"]?></span>
                                    <i class="fa fa-angle-down hidden-xs icon-angle-down select-icon"></i>
                                    <i class="fa fa-angle-up hidden-xs icon-angle-up select-icon"></i>
                                </div>
                                <ul class="dropdown-menu list-unstyled select-menu" role="menu" aria-labelledby="dLabel">
                                    <?php for($i = 1; $i < 10; $i++): ?>
                                        <li>
                                            <a class="js-select-label" href="javascript:;"><?=$ratio*$i;?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li><a class="js-select-labelmore" href="javascript:;"><?=$ratio*10;?>+</a></li>
                                </ul>
                            </div>
                            <input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>">
                        </div>
                    </td>

                <?php endif; ?>

            <?php endforeach; ?>

                <td class="hidden-xs hidden-sm basket-table__controls">
                    <?php if($arResult['BUTTONS']['DELAY']): ?>
                        <div>
                            <a href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["delay"])?>">
                                <i class="fa fa-clock-o"></i><?=Loc::getMessage('SALE_DELAY')?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if($arResult['BUTTONS']['DELETE']): ?>
                        <div>
                            <a href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["delete"])?>">
                                <i class="fa fa-trash"></i><?=Loc::getMessage('SALE_DELETE')?>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
        </tr>
        <tr class="js-itemedid basket-table__itemedit hidden-lg hidden-md">
            <td colspan="4">
                <div class="collapse" id="itemedit_<?=$arItem['ID']?>">
                    <div class="basket-table__itemedit-icons">
                        <?php if($arResult['BUTTONS']['DELAY']): ?>
                        <a class="favorite-box" href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["delay"])?>">
                            <i class="fa fa-clock-o"></i>
                        </a>
                        <?php endif; ?>
                        <?php if($arResult['BUTTONS']['DELETE']): ?>
                        <a class="remove-box" href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["delete"])?>">
                            <i class="fa fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode(array('QUANTITY', 'DELETE'), ","))?>">
<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>">
<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>">
<input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>">
<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>">
<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>">


<div class="hidden-sm hidden-xs text-right" style="margin-bottom: 15px;">
    <a href="javascript:void(0)" class="personal-basket__clear-link" onclick="clearBasket()">
        <?=Loc::getMessage('SALE_DELETE_ALL')?>
    </a>
</div>

<div class="panel panel-default personal-panel js-personal-panel">
    <div class="panel-body">

        <div class="row">
            <div class="col col-xs-6 visible-xs visible-sm">
                <a href=""><?=Loc::getMessage('SALE_DELETE_ALL');?></a>
            </div>
            <div class="col col-xs-6 col-md-12 personal-panel__allinfo">
                <div class="personal-panel__allinfo-block">
                    <?=Loc::getMessage("SALE_COUNT_ITEMS");?>
                    <span id = ""><?=count($arResult["ITEMS"]["AnDelCanBuy"]);?></span>
                    <?php if(!empty($arResult['allWeight'])): ?>
                        <br>
                        <?=Loc::getMessage("SALE_TOTAL_WEIGHT");?>
                         <span id = "allWeight_FORMATED"><?=$arResult['allWeight_FORMATED']?></span>
                    <?php endif; ?>

                    <br><br>
                    <?=Loc::getMessage("SALE_TOTAL")?>
                    <span ><b id = "allSum_FORMATED"> <?=$arResult['allSum_FORMATED']?> </b></span>
					<?php if(!empty($arResult["allVATSum_FORMATED"])): ?>
					<br>
                    <?=Loc::getMessage('SALE_VAT_INCLUDED'); ?>
                    <span id = "allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></span>
					<?php endif; ?>
                </div>
            </div>
            <div class="col col-xs-12 col-md-5 col-lg-4">
                <?php if($arParams['HIDE_COUPON'] != "Y"): ?>

                    <span><?=Loc::getMessage('STB_COUPON_PROMT')?></span>
                    <div class="input-group form">
                        <input type="text" class="form-item form-control" id="coupon" name="COUPON">
                        <div class="input-group-btn">
                            <input
                                class="btn btn-default btn2"
                                type="button"
                                value="<?=Loc::getMessage('SALE_COUPON_APPLY')?>"
                                onclick="enterCoupon()"
                            >
                        </div>
                    </div>
                    <div class="personal-panel__coupons-block">
                        <table class="table" id="coupons_block">
                            <tbody>
                                <?php if (!empty($arResult['COUPON_LIST'])): ?>
                                <?php foreach ($arResult['COUPON_LIST'] as $oneCoupon): ?>
                                <tr>
                                    <td class="personal-panel__coupons-icon"><i class="fa fa-tags"></i></td>
                                    <td class="personal-panel__coupons-value">
                                        <input
                                            type="hidden"
                                            name="OLD_COUPON[]"
                                            value="<?=htmlspecialcharsbx($oneCoupon['COUPON']);?>"
                                            class="req-input form-item form-control"
                                        >
                                        <?=htmlspecialcharsbx($oneCoupon['COUPON']);?>
                                    </td>
                                    <td class="personal-panel__coupons-text">
                                        <?php
                                        $color = '';
                                        switch ($oneCoupon['STATUS']) {
                                            case DiscountCouponsManager::STATUS_NOT_FOUND:
                                            case DiscountCouponsManager::STATUS_FREEZE:
                                                $color = 'red';
                                                break;
                                            case DiscountCouponsManager::STATUS_APPLYED:
                                                $color = 'gree';
                                                break;
                                        }
                                        ?>
                                        <?php if (isset($oneCoupon['CHECK_CODE_TEXT'])):?>
                                        <span style="color: <?=$color?>">
                                            <?=(is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="personal-panel__coupons-remove">
                                        <a href="javascript:void(0)" data-coupon="<?=htmlspecialcharsbx($oneCoupon['COUPON']); ?>">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col col-xs-12 col-md-7 col-lg-8 personal-panel__btn-wrap">
                <a href="/forms/buy1click/"
                   title="<?=Loc::getMessage('BUY_1_CLICK')?>"
                   class="btn btn-default btn-button JS-Popup-Ajax"
                   data-insertdata='{"RS_EXT_FIELD_0":"<?=$arResult['BUY1CLICK_STRING']?>"}'
                >
                    <?=Loc::getMessage('BUY_1_CLICK')?>
                </a>
                <a class="btn btn-default btn2" href="<?=$arParams['PATH_TO_ORDER']?>"><?=Loc::getMessage('SALE_ORDER')?></a>
            </div>
        </div>

    </div>
</div>
