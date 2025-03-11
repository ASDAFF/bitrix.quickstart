<?
if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use MHT\Product;
?>

    <div class="stock_main">
        <div class="dsc_wrap">
            <p class="dsc">Товары со скидкой по таймеру</p>
            <div class="chrono" data-end="<?=$arResult['DISCOUNT']['ACTIVE_TO_FORMAT']?>">
                <div class="_counter_">
                    <div id="clockdiv">
                        <div class="clock_div">
                            <span class="days"></span>
                            <span class="smalltext sm_days">д</span>
                        </div>
                        <div class="clock_div">
                            <span class="hours"></span>
                            <span class="smalltext sm_hours">ч</span>
                        </div>
                        <div class="clock_div">
                            <span class="minutes"></span>
                            <span class="smalltext sm_minutes">м</span>
                        </div>
                        <div class="clock_div">
                            <span class="seconds"></span>
                            <span class="smalltext sm_seconds">с</span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="/aktsii/#stock-2" class="dsc_all">Все товары</a>
        </div>




        <ul class="dsc_slider">

            <?
            foreach ($arResult['ITEMS'] as $item) {
                $product = Product::byID($item['ID']);
            ?>

            <li class="dsc_product">
                <a href="<?=$product->get('link')?>" class="dsc_item">
                    <p class="dsc_count">Скидки от <span><?=round($item['DISCOUNT']['VALUE'],0)?>%</span></p>
                    <div class="dsc_item-left-block">

                        <div class="dsc_image"> <img src="<?=$product->get('small-image', 'src')?>">
                        </div>

                    </div>
                    <div class="dsc_item-right-block">

                        <div class="item-title"> <?=$item['NAME']?> </div>

                        <div class="item-block-price">
                            <div class="item-price"> <span class="item-price-value"><?=$product->get('price')?></span> ₽</div> <a class="item-actions-buy" href="<?=$product->get('link')?>/index.php?action=BUY&amp;id=<?=$item['ID']?>" data-id="<?=$item['ID']?>" onmousedown="try { rrApi.addToBasket(<?=$item['ID']?>) } catch(e) {}"></a>


                        </div>
                    </div>
                </a>
            </li>

            <?

            }

            ?>

        </ul>



    </div>
