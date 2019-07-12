<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="basket" id="cart_line_12">
    <a href="<?=$arParams["PATH_TO_BASKET"]?>"><i class="icon-arrow-basket"></i>
        <?=GetMessage('YOUR_CART_EMPTY')?>
        <span class="number-basket">(<?=$arResult["NUM_PRODUCTS"]?>)</span>
        <span class="result-basket"><?=$arResult["SUM"]?> <?=$arResult["CURRENCY"]?>.</span>
    </a>
</div>
