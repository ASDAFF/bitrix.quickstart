<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Loader;

$this->IncludeLangFile('template.php');

if(Loader::includeModule('redsign.devfunc')) {
    $basketEndWord = RSDevFunc::BasketEndWord($arResult['NUM_PRODUCTS']);
}

?>

<div class="loss-menu-right loss-menu-right_count basketinhead">

	<a class="selected dropdown-toggle views" href="<?=$arParams['PATH_TO_BASKET']?>"  id="dropdown_basket_link">
		<i class="fa fa-shopping-cart"></i>
        <span class="count"><?=$arResult['NUM_PRODUCTS']?></span>
    </a>

    <div class="dropdown-basket hidden-xs"  id="dropdown_basket">

        <?php $isItemsExists = count($arResult["CATEGORIES"]["READY"]) > 0 ? true : false;  ?>

        <div class="js-smbasket "<?php if(!$isItemsExists) echo ' style="display: none"';?>>
            <div class="heighter" style="padding-bottom: 35px;">

                <table class="table basket-table basket-table--small">
                    <tbody>
                        <?php 
                        foreach ($arResult["CATEGORIES"]["READY"] as $k => $arItem):

                            $ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 1;
                            $useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
                            $useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");
                        ?>

                        <tr data-id="<?=$arItem['ID']?>" class="js-element">
                            <td class="basket-table__itemphoto">
                                 <?php
                                $itemPictureUrl = $arResult['NO_PHOTO']['src'];
                                if(strlen($arItem["PICTURE_SRC"]) > 0) {
                                    $itemPictureUrl = $arItem["PICTURE_SRC"];;
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

                                    <div class="col col-md-12 hidden-xs hidden-sm">
                                        <span class="basket-table__itemid">
                                            <i class="small"><?=Loc::getMessage('RS.FLYAWAY.ID')?>:</i> <?=$arItem['ID']?>
                                        </span>
                                    </div>

                                </div>
                            </td>

                            <td class="basket-table__price">
                                <i class="small"><?=Loc::getMessage('PRICE')?></i>
                                <div><span class="h4"><b class="js-item-price prices__val prices__val_cool"><?=$arItem["PRICE_FMT"]?></b></span></div>
                            </td>

                            <td class="basket-table__quantity text-center">

                                <div class="loss-menu-right loss-menu-right_last">
                                    <div class="dropdown dropdown_digit select js-select js-toggle-switcher" data-select="{'classUndisabled':'select-btn_undisabled'}">
                                        <div class="btn btn-default dropdown-toggle select-btn js-select-field" data-toggle="dropdown" aria-expanded="true" type="button">
                                            <input
                                                type="text"
                                                name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
                                                value="<?=$arItem["QUANTITY"]?>"
                                                class="select-input js-select-input js-quantity js-outbasket-quantity"
                                                data-ratio="<?=$ratio;?>"
                                            ><span class="select-unit"><?=$arItem["MEASURE_NAME"]?></span>
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
                                </div>

                            </td>

                            <td class="basket-table__sum">
                                <i class="small"><?=Loc::getMessage('SALE_SUM')?></i>
                                <div><span class="h4"><b class="js-item-sum prices__val prices__val_cool"><?=$arItem['SUM']?></b></span></div>
                            </td>

                            <td class="basket-table__controls">
                                <a href="javascript:;" onclick="<?=$arParams['cartId']?>.removeItemFromCart(<?=$arItem['ID']?>)" class="js-smbasket__delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>

                        </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col col-xs-12">
                    <div class="pull-right">
                        <div class="" style="margin-top: 15px">
                            <span style="padding: 6px 12px">
                                 <?php
                                $totalResultString = Loc::getMessage('TOTAL_RESULT');
                                $totalResultString = str_replace("#NUM_PRODUCTS#", $arResult['NUM_PRODUCTS'], $totalResultString);
                                $totalResultString = str_replace("#PRODUCTS_NAME#", Loc::getMessage('PRODUCTS_NAME').$basketEndWord, $totalResultString);
                                $totalResultString = str_replace("#TOTAL_PRICE#", $arResult['TOTAL_PRICE'], $totalResultString);
                                ?>
                                <?=$totalResultString?>
                            </span>
                            <a href="<?=$arParams['PATH_TO_BASKET']?>" class="btn btn-default btn2" style="margin-left: 15px;"><?=Loc::getMessage('GO2BASKET');?></a>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="alert alert-info js-error"<?php if($isItemsExists) echo ' style="display: none"'; ?>>
             <?=Loc::getMessage('RS.FLYAWAY.EMPTY_BASKET'); ?>
        </div>

    </div>

</div>
