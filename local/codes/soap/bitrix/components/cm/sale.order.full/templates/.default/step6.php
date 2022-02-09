<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
?>
<div class="b-cart__list clearfix">
           <div class="b-cart-field__info">
<?echo $_SESSION['last_basket_contents'];
?>  
<br><br>
Информация о покупателе:<br>
<b><?=$arResult['USER']['PERSON_TYPE']['NAME']?></b><br>
<a href="mailto:<?=$arResult['USER']["EMAIL"];?>"><?=$arResult['USER']["EMAIL"];?></a><br>
<b><?=$arResult['USER']["NAME"];?></b><br>
<b><?=$arResult['USER']["PERSONAL_PHONE"];?></b> 
<br><br> 
<?if($arResult['PUNKT']) { 
    ?>
Пункт Самовывоза:<br>
<b><?=$arResult['PUNKT'];?></b><br>
<?
}elseif($arResult['ADDR']){?>
Адрес доставки:<br> 
<b><?=$arResult['ADDR'];?></b><br>     
<?} ?>
           
        </div>
<h2 class="b-thanks__h2">Ваш заказ № <?=$arResult["ORDER"]['ID'];?> принят,<br>
    в ближайшее время с Вами свяжется<br>
    оператор по указанному телефону</h2> 
                </div>  