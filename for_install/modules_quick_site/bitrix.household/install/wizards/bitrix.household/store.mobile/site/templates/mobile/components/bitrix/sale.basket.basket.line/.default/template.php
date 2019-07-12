<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (IntVal($arResult["NUM_PRODUCTS"])>0)
{
?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>" data-theme="a" data-ajax="false"><?echo str_replace('#NUM#', intval($arResult["NUM_PRODUCTS"]), GetMessage('YOUR_CART'))?></a>
<?
}
else
{
?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>" data-theme="a" data-ajax="false"><?echo GetMessage('YOUR_CART_EMPTY')?></a>
<?
}
?>