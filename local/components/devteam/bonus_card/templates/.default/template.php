<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="b-bonus-add clearfix">
  <form action="" method="POST">
<div class="b-bonus-add__text"><input type="text" class="b-text" name="card"></div>
<div class="b-bonus-add__btn"><button class="b-button">Добавить карту</button></div>
</form>
</div>  
<section class="b-detail">
<div class="b-detail-content">
<?if($arResult['ADDED']){?>     
 <p>Ваша карта отправлена на модерацию</p>
<?} elseif($arResult['CARD']) {?>
<div class="b-bonus clearfix">
<div class="b-bonus__card">
<div class="b-bonus__percent">-<?=$arResult['CARD']["procent"]?>%</div>
<div class="b-bonus__number"><?=$arResult['CARD']["nomer"]?></div>
</div>  
<div class="b-bonus-info">
<table class="b-bonus__table">
<tbody><tr>
<td class="b-bonus__table-title">Ваша скидка:</td>
<td class="b-bonus__table-value"><b>-<?=$arResult['CARD']["procent"]?>%</b></td>
</tr>
<tr>
<td class="b-bonus__table-title">Общая сумма покупок:</td>
<td class="b-bonus__table-value"><span class="b-price"><?=$arResult['CARD']["summa"]?></span></td>
</tr>
<tr> 
<td class="b-bonus__table-title">Бонусных баллов:</td>
<td class="b-bonus__table-value"><b><?=$arResult['CARD']["ostatok"]?></b></td>
</tr>
</tbody></table>
</div>
</div>
<?} else {?><h2>У вас нет бонусных карт</h2><?}?></div></section>