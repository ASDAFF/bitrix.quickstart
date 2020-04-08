<?
$item = $result['item'];
$brands = $result['brands'];
$colors = $result['colors'];
$brandname = $result['brandname'];
$class = $result['class'];
$country = $result['country'];
$countryname = $result['countryname'];
?>
<div class="<?=$class ? $class : 'col-12 col-sm-6 col-lg-4 js-page-tile'?>">
<?//\Indi\Main\Util::debug($item);?>
	<div class="tile__item">
		<div class="tile__container">
			<div class="tile__content tile__content_hover">
				<div class="tile__img-wrap">
					<img class="tile__img" src="<?=strlen($item['UF_IMAGES_SMALL'][0]['src']) > 0 ? $item['UF_IMAGES_SMALL'][0]['src'] : '/local/templates/main/images/teaser_for_img@2x.jpg'?>" alt="">
					<a class="tile__item-link" href="<?=$item["SECTION_PAGE_URL"]?>"></a>
				</div>
				<div class="<? if($item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Plitka) {?> tile__alt <? } elseif($item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Oboi){?>tile__alt tile__alt_round <? }?>">
					<ul class="tile-alt__list">
						<?foreach ($item['UF_COLOR'] as $key=>$color)
							{
								?>
                                <?if(strlen($item["SECTION_PAGE_URL"]) > 0 && strlen($item['UF_IMAGES_SMALL'][$key]['src']) > 0 && strlen($item['UF_IMAGES_SMALL'][$key]['src']) > 0 && strlen($colors[$color]['UF_NAME']) > 0){?>
                                <li class="tile-alt__item">
                                    <a class="tile-alt__link js-tile-alt-img" href="<?=$item["SECTION_PAGE_URL"]?>"
                                       data-src="<?=$item['UF_IMAGES_SMALL'][$key]['src']?>">
                                        <img class="tile-alt__icon" src="<?=$colors[$color]['UF_FILE']['src']?>"
                                             alt="<?=$colors[$color]['UF_NAME']?>">
                                    </a>
                                    <span class="tile-alt__tooltip"><?=$colors[$color]['UF_NAME']?></span>
                                </li>
                                <? }?>
								<?
							}
						?>
					</ul>
				</div>
				<? if ($item['UF_NEW'] || $item['UF_STOCK'] || $item['UF_HIT']|| $item['UF_ECONOM']): ?>
					<div class="tile__advers">
						<div class="advers">
							<ul class="advers__list">
								<? if ($item['UF_NEW']): ?>
									<li class="advers__item advers__item_new"><span class="advers__title">Новинка</span>
									</li>
								<? endif ?>
								<? if ($item['UF_STOCK'] && $item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Plitka): ?>
									<li class="advers__item advers__item_stock"><span class="advers__title">Акция</span>
									</li>
								<? endif ?>
                                <? if ($item['UF_STOCK'] && $item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Oboi): ?>
									<li class="advers__item advers__item_stock"><span class="advers__title">Распродажа</span>
									</li>
								<? endif ?>
								<? if ($item['UF_HIT']): ?>
									<li class="advers__item advers__item_hit"><span
												class="advers__title">Хит продаж</span></li>
								<? endif ?>
							</ul>
						</div>
					</div>
				<? endif ?>
			</div>
			<div class="tile__desc text-right">
				<div class="tile__bg-icon">
                    <?if($item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Plitka){?>
                        <svg class="svg-catalog_romb ">
                            <use xlink:href="#catalog_romb" />
                        </svg>
                    <? } elseif($item["IBLOCK_ID"] == Indi\Main\Iblock\ID_Product_Oboi){?>
                        <svg class="svg-catalog_round ">
                            <use xlink:href="#catalog_round"/>
                        </svg>
                    <? }?>
				</div>
				<a class="tile__title fz_xl" href="<?=$item["SECTION_PAGE_URL"]?>"><?=$item['NAME']?></a>
				<div class="tile__text">
					<? if ($item['UF_BRAND']): ?>
						<?=$brandname?><?=strlen($countryname) > 0 ? ', '.$countryname : ''?>
<!--                        --><?//foreach ($country as $arCountry){?>
<!--                            --><?//if($item['UF_COUNTRY'] === $arCountry['ID']){?>
<!--                                --><?//=$arCountry['UF_NAME']?>
<!--                            --><?//}?>
<!--                        --><?//}?>
						<?else: ?>
						&nbsp;
					<? endif ?>
				</div>
			</div>
		</div>
	</div>
</div>