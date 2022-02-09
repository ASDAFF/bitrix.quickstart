<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
  
                         
<?
foreach ($arResult["ITEMS"] as $item) {
    $n++;
    ?> 
        <? if ($n == 1) { ?>
  <div class="clearfix"> <!--<?=$n?>   sdsdsds-->
       <? } ?>
                    <div class="b-slider__item">  
                        <div class="b-slider__text">
                            <div class="b-slider__image"><img src="<?= $item["DETAIL_PICTURE"]['SRC'] ?>" alt="" /></div>
                            <div class="b-slider__link"><a href="<?= $item["DETAIL_PAGE_URL"] ?>"><?= CutString($item['NAME'],28); ?></a></div>
                            <div class="b-slider__price"><?= $item["PRICES"]["price"]["PRINT_VALUE"] ?></div>
                        </div>
                    </div>


        <? if ($n == 4) {
            $n = 0;
            ?>
    </div>
    <? }
}
?>
<?if ($n != 0) { ?></div><? }
 