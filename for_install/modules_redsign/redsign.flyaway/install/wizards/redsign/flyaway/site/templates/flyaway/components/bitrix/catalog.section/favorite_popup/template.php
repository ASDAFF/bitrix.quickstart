<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$isAjax = false;
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_REQUEST['action'] == 'favoriteinhead_update') {
    $APPLICATION->RestartBuffer();
    $isAjax = true;
} else {
    $this->createFrame()->begin();
}
?>

<?php if(is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0): ?>
    <div class="heighter">
        <table class="table basket-table basket-table--small">

            <tbody>
                <?php
                foreach($arResult['ITEMS'] as $arItem):
                    $arItemShow = !empty($arItem['OFFERS']) && count($arItem['OFFERS']) > 0 ? $arItem['OFFERS'][0] : $arItem;
                ?>
                <tr>

                    <td class="basket-table__itemphoto">
                        <?php
                        $picture = $arResult['NO_PHOTO']['src'];
                        if(!empty($arItem['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']) > 0) {
                            $picture = $arItem['PREVIEW_PICTURE']['SRC'];
                        } elseif(!empty($arItem['DETAIL_PICTURE']) && strlen($arItem['DETAIL_PICTURE']['SRC']) > 0) {
                            $picture = $arItem['DETAIL_PICTURE']['SRC'];
                        }
                        ?>
                        <div class="basket-table__photo js-item_picture" style="background-image: url(<?=$picture?>)"> </div>
                    </td>

                    <td class="item">

                        <div class="row">
                            <div class="basket-table__name col-xs-11 col-md-12">
                                <?php if(strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?>
                                    <a href="<?=$arItem["DETAIL_PAGE_URL"] ?>" class="js-item_name">
                                        <?=$arItem["NAME"]?>
                                    </a>
                                <?php else: ?>
                                    <?=$arItem['NAME']?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="hidden-xs hidden-sm">
                            <span class="basket-table__itemid">
                                <i class="small"><?=Loc::getMessage('RS.FLYAWAY.ID')?>:</i> <?=$arItem['ID']?>
                            </span>
                        </div>

                    </td>

                    <td class="basket-table__price">
                        <?php if(count($arItemShow['PRICES']) > 0): ?>
                            <?php foreach($arResult['PRICES'] as $key1 => $titlePrices): ?>
                                <?php if(!empty($arItemShow['PRICES'][$key1])): ?>
                                    <i class="small"><?=$titlePrices['TITLE']?></i>
                                    <div><span class="h4"><b class="js-item-price prices__val prices__val_cool"><?=$arItemShow['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></b></span></div>
                                <?php break; endif; ?>
                            <?php  endforeach; ?>
                        <?php endif; ?>
                    </td>

                     <td class="basket-table__controls">
                        <a href="javascript:;"  class="js-favorite js-favorite-heart" data-elementid="<?=$arItem['ID']?>" data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

    <div class="row" style="margin-top: 15px">
        <div class="col col-xs-12">
            <div class="pull-right">
                <div class="" style="margin-top: 15px">
                    <a href="<?=$arParams['PATH_TO_FAVORITE']?>" class="btn btn-default btn2" style="margin-left: 15px;"><?=Loc::getMessage('RS.FLYAWAY.GO2FAVORITE');?></a>
                </div>

            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col col-md-12">
            <div class="alert alert-info">
                <?=Loc::getMessage('RS.FLYAWAY.NO_FAVORITE_ITEMS');?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
if($isAjax) {
    die();
}
