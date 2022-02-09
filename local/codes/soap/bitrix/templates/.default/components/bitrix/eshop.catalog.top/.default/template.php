<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
  
<div class="b-popup m-popup__orange" id="b-compare__add">
		<div class="b-popup__wrapper">
			<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
			<a class="b-button__fast_n" href="/catalogue/compare.php">Сравнить товары</a>
		</div>
</div>
<div class="slider-content-wrap">
<!-- PHP GENERATE SLIDES START -->                         
<?
foreach ($arResult["ITEMS"] as $item) {
    $n++;
    ?> 
 
        <? if ($n == 1) { ?>
  <div class="slider-content clearfix"> <!--<?=$n?>   sdsdsds-->
       <? } ?>
                    <div class="b-slider__item">  
                        <div class="b-slider__text">
                            <div class="b-slider__image"><img src="<?= $item["DETAIL_PICTURE"]['SRC'] ?>" alt="" id="<?=$item['ID']?>"/></div>
                            <div class="b-slider__link"><a href="<?= $item["DETAIL_PAGE_URL"] ?>"><?=CutString($item['NAME'],28); ?></a></div>
                            <div class="b-slider__price"><?= $item["PRICES"]["price"]["PRINT_VALUE"] ?></div>
                        </div>
                        <div class="b-slider__btn clearfix"> 
                            <button  class="b-button__fast" el="<?=$item['ID']?>">Быстрый<br>заказ</button>
                            <a el='<?=$item['ID']?>' class="b-icon m-wishlist__add" href="#b-wishlist__add" title="Добавить в вишлист"></a>
                            <a href="#b-compare__add" id="<?=$item['ID']?>" class="b-icon m-icon__compare m-compare__add" title="Сравнить" rel="/catalogue/?action=ADD_TO_COMPARE_LIST&id=<?=$item['ID']?>"  ></a>
                        </div>
                    </div>


        <? if ($n == 3) {
            $n = 0;
            ?>
    </div>
    <? }
}
?>
<?if ($n != 0) { ?></div><?}?>
<!-- PHP GENERATE SLIDES END -->
</div>