
<?
$item = $result['item'];
$brands = $result['brands'];
$app = $result['app'];
$basis = $result['basis'];
$width = $result['width'];
$length = $result['length'];
?>

<div class="tile__item tile__item_sm">
    <div class="tile__container">
        <div class="tile__content tile__content_jump">
            <div class="tile__img-wrap">
                <?if($item['DETAIL_PICTURE']['src']):?>
                    <img class="tile__img tile__img_sm" src="<?=$item['DETAIL_PICTURE']['src']?>" alt="<?=$item['NAME']?>">
                <?else:?>
                    <img class="tile__img tile__img_sm" src='/kaluga.kuzov-auto.ru/local/templates/main/images/teaser_for_img@2x.jpg' alt="<?=$item['NAME']?>">
                <?endif?>
                <a class="tile__item-link" href="<?=$item['DETAIL_PAGE_URL']?>"></a>
            </div>
            <div class="tile__advers">
                <div class="advers">
                    <ul class="advers__list">
                        <? if ($item['PROPERTY_NEW_VALUE'] == 'Y'): ?>
                            <li class="advers__item advers__item_new"><span class="advers__title">Новинка</span>
                            </li>
                        <? endif ?>
                        <? if ($item['PROPERTY_STOCK_VALUE'] == 'Y'): ?>
                            <li class="advers__item advers__item_sale"><span class="advers__title">Акция</span>
                            </li>
                        <? endif ?>
                        <? if ($item['PROPERTY_HIT_VALUE'] == 'Y'): ?>
                            <li class="advers__item advers__item_hit"><span class="advers__title">Хит продаж</span></li>
                        <? endif ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tile__desc"><a class="tile__title" href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
            <div class="tile__text"><?=$basis['UF_NAME']?>, <?=$width['UF_NAME'].' x '.$length['UF_NAME'].' м'?></div>
            <div class="tile__text"><?=$brands[$item['PROPERTY_BRAND_VALUE']]?></div>
        </div>
    </div>
</div>