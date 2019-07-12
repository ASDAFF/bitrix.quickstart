<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<span class="bx_cart_top_inline_icon"></span>
<?if (IntVal($arResult["NUM_PRODUCTS"])>0):?>
	<a class="bx_cart_top_inline_link" href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo str_replace('#NUM#', intval($arResult["NUM_PRODUCTS"]), GetMessage('YOUR_CART'))?></a>
<?else:?>
	<a class="bx_cart_top_inline_link" href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_EMPTY')?><span id="bx_cart_num"></span></a>
<?endif?>