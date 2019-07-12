<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
    <img class="b-icon i-menu__cart" src="<?= SITE_TEMPLATE_PATH ?>/images/1px.gif">
    <? if (IntVal($arResult["NUM_PRODUCTS"]) > 0): ?>
        <div class="b-yellow-roundlabel"><?=intval($arResult["NUM_PRODUCTS"])?></div>
    <? endif ?>