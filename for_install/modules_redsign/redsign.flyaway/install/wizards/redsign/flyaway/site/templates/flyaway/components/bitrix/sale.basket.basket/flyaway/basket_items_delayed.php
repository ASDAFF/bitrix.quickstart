<?php

use \Bitrix\Main\Localization\Loc;
use Bitrix\Sale\DiscountCouponsManager;

$skipCollumns = array('PROPS', 'DELAY', 'DELETE', 'TYPE');
?>

<table class="table basket-table" id="delayed_items">
    <tbody>
        <?php
        foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
        if ($arItem["DELAY"] != "Y" || $arItem["CAN_BUY"] != "Y") {
            continue;
        }
        ?>
        <tr id="<?=$arItem['ID']?>" class="js-element">
            <?php
            foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
                if(in_array($arHeader['id'], $skipCollumns)) {
                    continue;
                }
            ?>

                <?php if($arHeader['id'] == 'NAME'): ?>
                <td class="basket-table__itemphoto">
                    <?php
                    $itemPictureUrl = $arResult['NO_PHOTO']['src'];
                    if(strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0) {
                        $itemPictureUrl = $arItem["PREVIEW_PICTURE_SRC"];;
                    } elseif(strlen($arItem["DETAIL_PICTURE_SRC"]) > 0) {
                        $itemPictureUrl = $arItem["DETAIL_PICTURE_SRC"];
                    }
                    ?>

                    <div class="basket-table__photo" style="background-image: url(<?=$itemPictureUrl?>)"> </div>
                </td>
                <td class="basket-table__item">
                    <div class="row">

                        <div class="basket-table__name col-xs-11 col-md-12">
                            <?php if(strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"] ?>">
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
                                <b class="js-item-price prices__val prices__val_cool"><?=$arItem["PRICE_FORMATED"]?></b>
                            </div>
                        </div>

                        <div class="col col-xs-4 hidden-md hidden-lg text-right">
                            <br><?=$arItem['QUANTITY']?>
                            <?php if(!empty($arItem["MEASURE_TEXT"])): ?>
                                <?=$arItem["MEASURE_TEXT"]?>
                            <?php endif; ?>
                        </div>

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
                            <span class="stores">
                                <span class="stores-label"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY')?></span>:
                                <?php if((int)$arItem['AVAILABLE_QUANTITY'] > 0): ?>
                                    <span class="stores-icon  stores-full"></span><span class="genamount"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY_ISSET')?></span>
                                <?php else: ?>
                                    <span class="stores-icon"></span><span class="genamount"><?=Loc::getMessage('RS.FLYAWAY.QUANTITY_EMPTY')?></span>
                                <?php endif; ?>
                            </span>
                        </div>

                    </div>
                </td>
                 <?php elseif($arHeader['id'] == "PRICE"): ?>
                <td class="hidden-xs hidden-sm basket-table__price">
                    <i class="small"><?=$arItem["NOTES"]?></i>
                    <div><span class="h4"><b class="js-item-price prices__val prices__val_cool"><?=$arItem["PRICE_FORMATED"]?></b></span></div>
                </td>
                <?php elseif($arHeader['id'] == 'QUANTITY'): ?>
                <td class="hidden-xs hidden-sm basket-table__quantity text-center text-nowrap">
                    <?=$arItem['QUANTITY']?>
                    <?php if(!empty($arItem["MEASURE_TEXT"])): ?>
                        <?=$arItem["MEASURE_TEXT"]?>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            <?php endforeach; ?>
                <td class="hidden-xs hidden-sm basket-table__controls">
                    <div>
                        <a href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["add"])?>" class="text-nowrap">
                            <i class="fa fa-cart-plus"></i><?=Loc::getMessage("SALE_ADD_TO_BASKET")?>
                        </a>
                    </div>
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
                        <a class="favorite-box" href="<?=str_replace("#ID#", $arItem["ID"], $arResult['URLS']["add"])?>">
                            <i class="fa fa-cart-plus"></i>
                        </a>
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
