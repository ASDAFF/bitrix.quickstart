<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<i id="cart-status">
<?
if (IntVal($arResult["NUM_PRODUCTS"])>0)
{
?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo str_replace('#NUM#', intval($arResult["NUM_PRODUCTS"]), GetMessage('YOUR_CART'))?></a>
<?
}
else
{
?>
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_EMPTY')?></a>
<?
}
?>
</i>

											<p class="money">� ����� ������� ������� �� �����: <strong>4 220 ���.</strong></p>
											<p class="order"><a href="#" class="icon">�������</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#">�������� �����</a></p>