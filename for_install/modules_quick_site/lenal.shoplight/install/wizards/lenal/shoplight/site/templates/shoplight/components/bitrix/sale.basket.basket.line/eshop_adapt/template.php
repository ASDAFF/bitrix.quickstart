<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<a class="b-main-menu__item-link" href="<?= $arParams["PATH_TO_BASKET"] ?>">
    <img class="b-icon i-menu__cart" src="<?= SITE_TEMPLATE_PATH ?>/images/1px.gif">
    <? if (IntVal($arResult["NUM_PRODUCTS"]) > 0): ?>
        <div class="b-yellow-roundlabel"><?=intval($arResult["NUM_PRODUCTS"])?></div>
    <? endif ?>
</a>
<div class="b-dialog b-dialog-checkout">
    <div class="b-dialog__close"></div>

    <div class="b-dialog-checkout__inner">
        <span class="b-dialog-checkout__text"><?=GetMessage('YOUR_CART_DIALOG_TEXT')?></span>
        <a href="" onclick="$('.b-dialog-checkout').trigger('hide');
                return false;" class="b-dialog-checkout__continue b-button b-button_grey"><?=GetMessage('YOUR_CART_CLOSE')?></a>
        <a href="<?= $arParams["PATH_TO_BASKET"] ?>" class="b-dialog-checkout__checkout b-button"><?=GetMessage('YOUR_CART_CHECKOUT')?></a>
    </div>

</div>