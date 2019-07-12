<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


	

<?
if (IntVal($arResult["NUM_PRODUCTS"])>0)
{
?>	
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_TITLE')?>:</A><br /> <?echo $arResult["TOTAL_FORMATTED_STRING"]?>
	<div class="checkout">
		<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('CHECKOUT')?></a>  
	</div>
<?
}
else
{
?>
	<div class="cart_empty"><a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_TITLE')?></a>: <?echo GetMessage('YOUR_CART_EMPTY')?></div>
<?
}
?>
