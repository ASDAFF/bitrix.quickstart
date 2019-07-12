<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (IntVal($arResult["NUM_PRODUCTS"])>0)
{
	?>
	<p id="tp-info"><i></i><?= GetMessage("SBL_IN_CART");?> <a href="<?=$arParams["PATH_TO_BASKET"]?>" id="priceCount" ccount="<?= $arResult["NUM_PRODUCTS"]; ?>"><?= $arResult["NUM_PRODUCTS"] . ' ' . _emisc::ruscomp($arResult["NUM_PRODUCTS"], GetMessage("SBL_GOODS_IN_CART")); ?></a></p>
	<p id="def-cart-mess" style="display: none"><i></i><?= GetMessage("SBL_IN_CART_EMPTY");?></p>
	<?
}
else
{
	?>
	<p id="tp-info" style="display: none"><i></i><?= GetMessage("SBL_IN_CART");?> <a href="<?=$arParams["PATH_TO_BASKET"]?>" id="priceCount" ccount="0">0</a></p>
	<p id="def-cart-mess"><i></i><?= GetMessage("SBL_IN_CART_EMPTY");?></p>
	<?
}
?>