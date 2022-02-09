<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
if (IntVal($arResult["NUM_PRODUCTS"]) > 0) {
    ?>

    <a href="<?= $arParams["PATH_TO_BASKET"] ?>" class="b-minicart__link">Корзина (<?= $arResult["NUM_PRODUCTS"]; ?>)</a>
 
    <?
} else {
    ?>

    <a href="<?= $arParams["PATH_TO_BASKET"] ?>" class="b-minicart__link">Корзина (0)</a>
    <?
} 