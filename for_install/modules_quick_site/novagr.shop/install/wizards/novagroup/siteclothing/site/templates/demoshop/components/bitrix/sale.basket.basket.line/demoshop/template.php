<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>
<div class="basket" id="cart_line">
<?
//if (IntVal($arResult["NUM_PRODUCTS"])>0)
//{
?>
	<p><a href="<?=$arParams["PATH_TO_BASKET"]?>"><i class="icon-arrow-basket"></i> <?=GetMessage('YOUR_CART_EMPTY')?> <span class="number-basket">(<?=$arResult["NUM_PRODUCTS"]?>)</span> <span class="result-basket"><?=$arResult["SUM"]?> <?=$arResult["CURRENCY"]?>.</span></a></p>
	
<?php 
	/*?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo str_replace('#NUM#', intval($arResult["NUM_PRODUCTS"]), GetMessage('YOUR_CART'))?></a>
<? */
//}
//else
//{

	/*?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_EMPTY')?></a>
<? */
//}
?>
</div>