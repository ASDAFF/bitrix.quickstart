<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
ob_start();
?>
Вы заказали:<br>
<b><?=$arResult["NUM_PRODUCTS"];?> <?=formatByCount($arResult["NUM_PRODUCTS"], 'товар', 'товара', 'товаров');?> на сумму <?=$arResult['SUMM'];?></b>


<?php
$b = ob_get_clean();
$_SESSION['last_basket_contents'] = $b;
echo $b; 