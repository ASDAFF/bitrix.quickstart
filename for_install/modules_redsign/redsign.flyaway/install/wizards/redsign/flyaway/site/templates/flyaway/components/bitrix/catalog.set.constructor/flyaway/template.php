<?php
use \Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(false);
?>

<div class="row constructor-wrapper">
    <div class="section-cart col-md-12">
        <h2 class="product-content__title"><?=Loc::getMessage("SET_CONSTRUCTOR_TITLE")?></h2>
    </div>
    <div class = " col-md-12 set-section">

        <div
          class = "panel panel-constructor js-set-constructor"
          data-iblockid="<?=$arParams['IBLOCK_ID']?>"
          data-elementid="<?=$arResult['ELEMENT']['ID']?>"
          data-ajaxpath="<?=$this->GetFolder();?>/ajax.php"
          data-lid="<?=SITE_ID?>"
          data-setOffersCartProps = "<?=CUtil::PhpToJSObject($arParams["OFFERS_CART_PROPERTIES"])?>"
          data-currency = "<?=$arResult['ELEMENT']['PRICE_CURRENCY']?>"
        >
            <div class="panel panel-body">

                <div class="panel-constructor__top clearfix">

                    <div class="panel-constructor__selected-items js-sets-selected-items ">
                    <?php
                    for($i = -1; $i < 3; $i++):
                        if($i === -1) {
                            $arItem = $arResult['ELEMENT'];
                        } elseif(!empty($arResult["SET_ITEMS"]["DEFAULT"][$i])) {
                            $arItem = $arResult["SET_ITEMS"]["DEFAULT"][$i];
                        } else {
                            $arItem = null;
                        }
                    ?>

                        <?php if($arItem): ?>
                            <div
                                class="set-item js-set-item"
                                data-elementid="<?=$arItem['ID']?>"
                                data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
                                data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
                                data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>"
                                data-index = "<?=$i?>"
                            >

                                <div class="set-item__pic">
                                    <a href = "<?=$arItem['DETAIL_PAGE_URL']?>">
                                        <?php
                                        $img = $arResult['NO_PHOTO'];
                                        if(!empty($arItem["DETAIL_PICTURE"]) && strlen($arItem["DETAIL_PICTURE"]['src'])) {
                                            $img = $arItem["DETAIL_PICTURE"];
                                        }
                                        ?>
                                        <img src="<?=$img['src']?>" alt="<?=$strAlt?>">
                                    </a>
                                </div>
                                <div class="set-item__data">
                                    <div class="set-item__name">
                                        <a class="element" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
                                    </div>
                                    <div class="separator"></div>
                                    <div class="set-item__prices">
                                        <?php if($arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] > 0): ?>
                                        <div class="hidden-xs prices__val prices__val_old">
                                            <?=$arItem['PRICE_PRINT_VALUE']?>
                                        </div>
                                        <div class="prices__val prices__val_cool prices__val_new">
                                            <?=$arItem['PRICE_PRINT_DISCOUNT_VALUE']?>
                                        </div>
                                        <?php else: ?>
                                        <div class="prices__val prices__val_cool"><?=$arItem['PRICE_PRINT_VALUE']?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="set-item__icons loss-menu-right">
                                    <?php if($i !== -1): ?>
                                        <a class="set-item__icon js-remove" href="javascript:;">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>

                        <?php else: ?>
                            <div class="set-item js-set-item hidden-xs"><div class="set-item__cart"></div></div>
                        <?php endif; ?>

                    <?php endfor;?>
                    </div>

                    <div class="sets-buyblock">
                        <div class="sets-buyblock__contract-price">
                            <?=Loc::getMessage('CONTRACT_PRICE');?>:
                        </div>
                        <div class="prices js-buyblock-prices">
                        <?php if($arResult['SET_ITEMS']['OLD_PRICE'] != 0): ?>
                            <div class="prices__val prices__val_cool prices__val_new big">
                               <?=$arResult['SET_ITEMS']['PRICE']; ?>
                            </div>
                            <div class="prices__val prices__val_old">
                                <?=$arResult['SET_ITEMS']['OLD_PRICE']; ?>
                            </div>
                            <div class="set-buyblock__profit">
                                <i><?=Loc::getMessage('YOUR_PROFIT');?>:</i>
                            </div>
                            <div class="prices__val prices__val_cool prices__val_new">
                                <?=$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE']?>
                            </div>
                        <?php else: ?>
                            <div class="prices__val prices__val_cool"><?=$arResult['SET_ITEMS']['PRICE']; ?></div>
                        <?php endif; ?>
                        </div>
                        <div style="margin-top: 15px;">

                            <a class = "btn btn-primary  btn2 js-set-add2basket"><?=Loc::getMessage('IN_BASKET')?></a>
                            <a class = "btn btn-default JS-Popup-Ajax  js-set-buy1click btn-button"
                               href = "<?=SITE_DIR?>forms/buy1click/"
                               title="<?=Loc::getMessage('BUY_1CLICK')?>">
                                    <?=Loc::getMessage('BUY_1CLICK')?>
                            </a>
                        </div>
                        <div class="sets-buyblock__myset-link">
                            <a href = "javascript:;" class="js-myset"><?=Loc::getMessage('MY_SET')?></a>
                            <a href = "javascript:;" class="js-myset" style="display: none;"><?=Loc::getMessage('MY_SET_TURN')?></a>
                        </div>
                    </div>
                </div>


                <div class="panel-constructor__bottom">
                    <div class="panel-constructor__all-items js-sets-all-items" style="display: none;">
                        <?php foreach (array("DEFAULT", "OTHER") as $type): ?>
                            <?php foreach($arResult["SET_ITEMS"][$type] as $i => $arItem): ?>
                                <div
                                    class="set-item js-set-item"
                                    data-elementid="<?=$arItem['ID']?>"
                                    data-price = "<?=$arItem['PRICE_DISCOUNT_VALUE']?>"
                                    data-oldprice = "<?=$arItem['PRICE_VALUE']?>"
                                    data-discount = "<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE']?>"
                                    data-index = "<?=$i?>"
                                >

                                    <div class="set-item__pic">
                                        <a href = "<?=$arItem['DETAIL_PAGE_URL']?>">
                                            <?php
                                            $img = $arResult['NO_PHOTO'];
                                            if(!empty($arItem["DETAIL_PICTURE"]) && strlen($arItem["DETAIL_PICTURE"]['src'])) {
                                                $img = $arItem["DETAIL_PICTURE"];
                                            }
                                            ?>
                                            <img src="<?=$img['src']?>" alt="<?=$arItem["NAME"]?>">
                                        </a>
                                    </div>
                                    <div class="set-item__data">
                                        <div class="set-item__name">
                                            <a class="element" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
                                        </div>
                                        <div class="separator"></div>
                                        <div class="set-item__prices">
                                            <?php if($arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] > 0): ?>
                                            <div class="hidden-xs prices__val prices__val_old">
                                                <?=$arItem['PRICE_PRINT_VALUE']?>
                                            </div>
                                            <div class="prices__val prices__val_cool prices__val_new">
                                                <?=$arItem['PRICE_PRINT_DISCOUNT_VALUE']?>
                                            </div>
                                            <?php else: ?>
                                            <div class="prices__val prices__val_cool"><?=$arItem['PRICE_PRINT_VALUE']?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="set-item__icons loss-menu-right">
                                        <div class="set-item__icon gui-box">
                                            <label class="gui-checkbox">
                                                <input  class="gui-checkbox-input js-set-toggle" type="checkbox" <?php if($type == "DEFAULT") echo 'checked';?> value=""> <span class="gui-checkbox-icon"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>
<script>
    BX.message({
        YOUR_PROFIT: '<?=Loc::getMessage("YOUR_PROFIT")?>'
    });
</script>
