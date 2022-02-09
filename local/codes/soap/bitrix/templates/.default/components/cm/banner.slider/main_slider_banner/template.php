<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



    
<section class="b-content">
 
    <div class="b-tab-head">
        <a href="#b-slide__one" class="b-tab-head__link active" data-slider="Y">Акции и скидки</a>
        <a href="#b-slide__two" class="b-tab-head__link" data-slider="Y">Распродажа</a>
        <a href="#b-slide__three" class="b-tab-head__link" data-slider="Y">Еще пункт</a>
    </div>
    <script type="text/javascript">
        $(function() {
            var slide_last = 0, slide_length = $("#b-slider_1").find(".b-slider").children().length;
            $("#b-slider_1").slides({
                container: "b-slider",
                prev: "m-prev", 
                next: "m-next",
                paginationClass: "b-pager",
                animationStart: function(i) {
                    console.log(i)
                    if(slide_last == slide_length && i == "next") {
                        $(".b-tab-head__link").eq(1).click();
                    }
                },
                animationComplete: function(i) {
                    slide_last = i;
                }
            });
        });
    </script>
    <div id="b-slide__one" class="b-tab__body active">
        <div class="b-tab">
            <div class="b-slider-wrapper" id="b-slider_1">
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider">
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img8.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img8.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img4.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div>
    <div id="b-slide__two" class="b-tab__body">
        <div class="b-tab">
            <div class="b-slider-wrapper" id="b-slider_2">
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider">
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img8.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img9.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><a href="#"><img src="upload/img10.png" alt="" /></a></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img4.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img5.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><a href="#"><img src="upload/img6.png" alt="" /></a></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div>
    <div id="b-slide__three" class="b-tab__body">
        <div class="b-tab">
            <div class="b-slider-wrapper" id="b-slider_3">
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider">
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img9.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><a href="#"><img src="upload/img10.png" alt="" /></a></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img8.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img4.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="upload/img5.png" alt="" /></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                        <div class="b-slider__item">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><a href="#"><img src="upload/img6.png" alt="" /></a></div>
                                <div class="b-slider__link">Фотоаппарат <a href="#">Panasonic Lumix DMC-GF5KEE-K Bla...</a></div>
                                <div class="b-slider__price">5 990.–</div>
                            </div>
                            <div class="b-slider__btn clearfix">
                                <button class="b-button__fast">Быстрый<br>заказ</button>
                                <span class="b-icon" title="подсказка"></span>
                                <a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div>
</section>
    
     



<?return;?>
<div class="b-tab-head">
<?foreach($arResult["PROP"] as $arProp):?>
<a href="#9" class="b-tab-head__link <?if(reset($arResult["PROP"])==$arProp):?>active<?endif;?>"><?=$arProp["NAME"]?></a>
<?endforeach;?>
</div>
<?foreach($arParams[PROPERTY_CODE] as $arProp):?>
				<div id="9" class="b-tab__body active">
					<div class="b-tab">
						<div class="b-slider-wrapper" id="slider_three">
							<a href="#" class="b-slider__control m-prev"  title="назад"></a>
							<div class="b-slider">
<?
$i=0;
foreach($arResult["ITEMS"][$arProp] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$i++;
	?>
	<?if(reset($arResult["ITEMS"])==$arItem OR ($i-1)%3==0):?>
									<div class="clearfix">
	<?endif;?>
									<div class="b-slider__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<div class="b-slider__text">
											<div class="b-slider__image"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="" /></div>
											<div class="b-slider__link">Фотоаппарат <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a></div>
											<div class="b-slider__price">5 990.–</div>
										</div>
										<div class="b-slider__btn clearfix">
											<button class="b-button__fast">Быстрый<br>заказ</button>
											<span class="b-icon" title="подсказка"></span>
											<a href="#" class="b-icon m-icon__compare" title="ссылка"></a>
										</div>
									</div>
	<?if(end($arResult["ITEMS"])==$arItem OR $i%3==0):?>
									</div>
	<?endif;?>
<?endforeach;?>
</div>
							<a href="#" class="b-slider__control m-next" title="вперед"></a>
						</div>
					</div>
				</div>
<?endforeach;?>

