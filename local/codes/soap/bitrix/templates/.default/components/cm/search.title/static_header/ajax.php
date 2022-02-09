<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?

    $secttr = array();
CModule::IncludeModule("catalog");
?>
<script src="/js/js.js"></script>
<div class="b-popup__wrapper">
    <?
        foreach($arResult["SECT"] as $section => $arSectioncnt):
            if ($arSectioncnt <= 2 ):?>
            <div class="b-popup-search__line clearfix">
                <div class="b-popup-search__title"><?=$arResult["SECT_NAME"][$section]?><br><b><?=$arResult["SECT"][$section]?> товара</b></div>
                <div class="b-popup-search-slider">
                    <div class="b-slider">
                        <div class="clearfix">
                            <?
                                foreach($arResult["CATEGORIES"][0]["ITEMS"] as $j => $arItem):?>
                                <?if ($arItem["SECT_ID"] == $section):?>
<?$arPrice = CPrice::GetBasePrice($arItem["ITEM_ID"]);?>
                                    <div class="b-slider__item">
                                        <div class="b-popup-slider__image"><a href="<?echo $arItem["URL"]?>"><img src="<?=($arItem["PREV"]["SRC"]?$arItem["PREV"]["SRC"]:"/images/img-element__image.png");?>"></a></div>
                                        <div class="b-popup-slider__title"><?echo CutString($arItem["NAME"],28);?><br><b><?=CurrencyFormat($arPrice["PRICE"], "RUB");?></b></div>
                                    </div>  
                                    <?endif;?>
                                <?endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
            <?else:?>
            <?$i = 0;?>
            <div class="b-popup-search__line clearfix">
                <div class="b-popup-search__title"><?=$arResult["SECT_NAME"][$section]?><br><b><?=$arResult["SECT"][$section]?> товара</b></div>
                <div class="b-popup-search-slider" id="b-popup__slider">
                    <a href="#" class="b-slider__control m-prev"></a>
                    <div class="b-slider">
                        <?$it = 0;?>
						<?$z=0;?>
						<?foreach($arResult["CATEGORIES"][0]["ITEMS"] as $i => $arItem):?>
                            <?if ($arItem["SECT_ID"] == $section):
							$z++;
							endif;?> 
                        <?endforeach;?>	
                        <?foreach($arResult["CATEGORIES"][0]["ITEMS"] as $i => $arItem):?>
                            <?if ($arItem["SECT_ID"] == $section):
							$z1++;
                            $arPrice = CPrice::GetBasePrice($arItem["ITEM_ID"]);?>
                                <?if($it % 2 == 0):?><div class="clearfix"><?endif;?>

                                <div class="b-slider__item">
                                    <div class="b-popup-slider__image"><a href="<?echo $arItem["URL"]?>"><img src="<?echo $arItem["PREV"]["SRC"]?>" ></a></div>
                                    <div class="b-popup-slider__title"><?echo CutString($arItem["NAME"],28);?><br><b><?=CurrencyFormat($arPrice["PRICE"], "RUB");?></b></div>
                                </div>
								<?if($it == 1 OR $z1 == $z):?></div><?$it = 0; else:$it++;endif;?>

                            <?endif;?> 
                        <?endforeach;?>
					</div>
					<a href="#" class="b-slider__control m-next"></a>
                </div>
            </div>
    <?endif;?>
    <?endforeach;?>

<div class="b-popup-search__footer clearfix">
    <div class="b-popup-footer__title">А так же:</div>
    <div class="b-popup-footer__link">
        <?
            foreach($arResult["SECT_NAME"] as $i => $arItem):
                if(!in_array($i, $secttr)):?>
                <a href="<?=$arResult["SECT_URL"][$i]?>"><span><?echo $arItem?></span> <b>(<?=$arResult["SECT"][$i]?>)</b></a>
                <? endif;endforeach;?>
    </div>
    </div>
    <div class="b-popup__show_all"><a href="<?=$arResult["CATEGORIES_all"];?>">Показать все найденные результаты</a> <b>(<?=$arResult["COUNT_ITEM"]?>)</b></div>
 </div>

    <!--<div class="title-search-fader"></div>  -->