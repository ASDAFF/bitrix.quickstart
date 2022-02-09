<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-compare-result">
    <a name="compare_table"></a>
    <noindex><p>
            <?if($arResult["DIFFERENT"]):
                ?><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a><?
                    else:
                ?><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?><?
                    endif
            ?>&nbsp;|&nbsp;<?
                if(!$arResult["DIFFERENT"]):
                ?><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a><?
                    else:
                ?><?=GetMessage("CATALOG_ONLY_DIFFERENT")?><?
                    endif?>
        </p></noindex>
    <?if(!empty($arResult["DELETED_PROPERTIES"]) || !empty($arResult["DELETED_OFFER_FIELDS"]) || !empty($arResult["DELETED_OFFER_PROPS"])):?>
        <noindex><p>
                <?=GetMessage("CATALOG_REMOVED_FEATURES")?>:
                <?foreach($arResult["DELETED_PROPERTIES"] as $arProperty):?>
                    <a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&pr_code=".$arProperty["CODE"],array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=$arProperty["NAME"]?></a>
                    <?endforeach?>
                <?foreach($arResult["DELETED_OFFER_FIELDS"] as $code):?>
                    <a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&of_code=".$code,array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=GetMessage("IBLOCK_FIELD_".$code)?></a>
                    <?endforeach?>
                <?foreach($arResult["DELETED_OFFER_PROPERTIES"] as $arProperty):?>
                    <a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&op_code=".$arProperty["CODE"],array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=$arProperty["NAME"]?></a>
                    <?endforeach?>
            </p></noindex>
        <?endif?>







    <br />
    <form action="<?=$APPLICATION->GetCurPage()?>" method="get">
        <table class="data-table" cellspacing="0" cellpadding="0" border="0">
            <thead>
            <tr>
                <td valign="top">&nbsp;</td>
                <?foreach($arResult["ITEMS"] as $arElement):?>
                    <td valign="top" width="<?=round(100/count($arResult["ITEMS"]))?>%">
                        <input type="checkbox" name="ID[]" value="<?=$arElement["ID"]?>" />
                    </td>
                    <?endforeach?>
            </tr>






        </table>
        <br />
        <input type="submit" value="<?=GetMessage("CATALOG_REMOVE_PRODUCTS")?>" />
        <input type="hidden" name="action" value="DELETE_FROM_COMPARE_RESULT" />
        <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
    </form>
    <br />
    <?if(count($arResult["ITEMS_TO_ADD"])>0):?>
        <p>
            <form action="<?=$APPLICATION->GetCurPage()?>" method="get">
                <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
                <input type="hidden" name="action" value="ADD_TO_COMPARE_RESULT" />
                <select name="id">
                    <?foreach($arResult["ITEMS_TO_ADD"] as $ID=>$NAME):?>
                        <option value="<?=$ID?>"><?=$NAME?></option>
                        <?endforeach?>
                </select>
                <input type="submit" value="<?=GetMessage("CATALOG_ADD_TO_COMPARE_LIST")?>" />
            </form>
        </p>
        <?endif?>
</div>

<?$sect_id = array();?>
<div class="b-container m-wishlist m-compare-list clearfix">
    <?foreach($arResult["ITEMS"] as $arElement):
            $arFilter = Array('ID' => $arElement["IBLOCK_SECTION_ID"]);
            $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);

            if($ar_result = $db_list->GetNext())
            {
                if (!array_key_exists($ar_result['ID'], $sect_id)):
                    //echo '<a href=?section='.$ar_result['ID'].'>'.$ar_result['NAME'].'</a>';
                    $sect_id[$ar_result['ID']]["ID"] = $ar_result['ID'];
                    $sect_id[$ar_result['ID']]["NAME"] = $ar_result['NAME'];
                    $sect_id[$ar_result['ID']]["URL"] = '?section='.$ar_result['ID'];
                    $sect_id[$ar_result['ID']]["NUM"] = 1;
                    else: 
                    $sect_id[$ar_result['ID']]["NUM"] += 1;
                    endif;
            } 
        ?>
        <?endforeach;?>
    <div class="b-compare__title">К сравнению добавлено <b><?=count($arResult["ITEMS"]);?></b> товаров в <b><?=count($sect_id);?></b> категориях:</div>
    <aside class="b-sidebar">
        <div class="b-sidebar-filter m-sidebar"> 
            <?foreach($sect_id as $arSec):?>
                <div class="b-sidebar-wishlist__section">
                    <div class="b-sidebar-wishlist__title">
                        <!--<h2 class="b-sidebar-wishlist__h2"><?//=$arSec["NAME"]?>&nbsp;<span class="b-sidebar-wishlist__count"><?//=$arSec["NUM"]?></span></h2>-->
                        <a href="<?=$arSec["URL"]?>" class="b-sidebar-wishlist__h2"><?=$arSec["NAME"]?>&nbsp;<span class="b-sidebar-wishlist__count"><?=$arSec["NUM"]?></span></a>
                    </div>
                    <button class="b-button__delete m-cart__delete m-wishlist__delete"></button>
                </div>
                <?endforeach;?>
        </div>
    </aside><!--/.b-sidebar-->
    <section class="b-content m-compare">
    <article class="">
    <script>
        $(document).ready(function() {
                $("#compare").slides({
                        container: "b-slider",
                        prev: "m-prev",
                        next: "m-next",
                        paginationClass: "b-pager",
                        autoHeight: true,
                        animationStart: function(current) {
                            switch(current) {
                                case "next": 
                                    $("#b-winner__slider .m-next").click(); 
                                    $("#b-compare-prop__slider .m-next").click(); 
                                    break;
                                case "prev": 
                                    $("#b-winner__slider .m-prev").click(); 
                                    $("#b-compare-prop__slider .m-prev").click(); 
                                    break;
                            }
                        }
                });
                $("#b-winner__slider, #b-compare-prop__slider").slides({
                        container: "b-slider",
                        prev: "m-prev",
                        next: "m-next",
                        paginationClass: "b-pager",
                        autoHeight: true
                });
        });
    </script>
    <div class="b-slider-wrapper" id="compare">
        <a href="#" class="b-slider__control m-prev"  title="назад"></a>
        <div class="b-slider clearfix">
            <?
                $i = 0;
                foreach($arResult["ITEMS"] as $arElement):?>
                <?if ($i == 0):?><div class="clearfix"><?endif;?>
                    <div class="b-slider__item">
                        <div class="b-slider__text">
                            <div class="b-slider__image"><img border="0" src="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["ALT"]?>" /></div>
                            <div class="b-slider__link"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></div>


                            <div class="b-slider__price">
                                <?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):?>
                                    <?if($arPrice["CAN_ACCESS"]):?>
                                        <?if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
                                            <b><?=$arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"]?></b>
                                            <?endif;?>
                                        <?endif;?>
                                    <?endforeach;?>
                            </div>
                        </div>
                        <div class="b-slider__btn m-btn__center">
                            <span class="b-icon" title="подсказка"></span>
                            <?
                                if($arElement["CAN_BUY"]):
                                ?>
                                <a href="<?=$arElement["BUY_URL"]?>" class="b-icon m-icon__buy"></a>
                                <?endif;?>
                        </div>
                        <button class="b-button__delete m-cart__delete m-wishlist-item__delete"></button>
                    </div>
                    <?$i++;?>
                <?if ($i == 3):?></div><?$i = 0; endif;?>
                <?endforeach?>
        </div>
    </div>
    <a href="#" class="b-slider__control m-next" title="вперед"></a>
</div>
</article>
</section>
<div class="b-winner clearfix">
    <div class="b-winner__checkbox"><label class="b-checkbox" id="m-changes__show"><input type="checkbox" name="checkbox_gp_1" value="1" checked />Только различия</label></div>
    <div class="b-winner__wrapper">
        <div class="b-slider-wrapper" id="b-winner__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
            <a href="#" class="b-slider__control m-prev"  title="назад"></a>
            <div class="b-slider clearfix">
                <div class="clearfix">
                    <div class="b-winner__item"></div>
                    <div class="b-winner__item m-winner">Победитель</div>
                    <div class="b-winner__item"></div>
                </div>
                <div class="clearfix">
                    <div class="b-winner__item"></div>
                    <div class="b-winner__item"></div>
                    <div class="b-winner__item"></div>
                </div>
            </div>
            <a href="#" class="b-slider__control m-next" title="вперед"></a>
        </div>
    </div>
</div>







<div class="b-compare-wrapper">
    <h2 class="b-compare__h2">Общая информация</h2>
    <div class="b-compare__section clearfix">
        <div class="b-compare-name">


            <?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
                ?>
                <div class="b-compare-name__title"><?=$arProperty["NAME"]?></div>
                <?endforeach;?>
<!--
            <div class="b-compare-name__title">Цена</div>
            <div class="b-compare-name__title">Дата выхода на рынок</div>
            <div class="b-compare-name__title">Ценовой диапазон</div>
            <div class="b-compare-name__title">Назначение</div>
            <div class="b-compare-name__title">Дизайн</div>
            <div class="b-compare-name__title m-two__lines">Особенности конструкции</div>
            <div class="b-compare-name__title">Ноутбук-трансформер</div>-->
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider clearfix">
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title m-compare__changes">высший уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title">нет</div>
                            <div class="b-compare-name__title">да</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title m-compare__changes">конечный уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title m-compare__changes">нет</div>
                            <div class="b-compare-name__title m-compare__changes">да</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title">конечный уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title">нет</div>
                            <div class="b-compare-name__title">да</div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title">высший уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title">нет</div>
                            <div class="b-compare-name__title">да</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title">конечный уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title">нет</div>
                            <div class="b-compare-name__title">да</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">23200р</div>
                            <div class="b-compare-name__title">2012 г.</div>
                            <div class="b-compare-name__title">конечный уровень</div>
                            <div class="b-compare-name__title">домашний (мультимедийный) </div>
                            <div class="b-compare-name__title">"глянцевый"</div>
                            <div class="b-compare-name__title">нет</div>
                            <div class="b-compare-name__title">да</div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div>
    <h2 class="b-compare__h2">Процессор и чипсет</h2>
    <div class="b-compare__section clearfix">
        <div class="b-compare-name">
            <div class="b-compare-name__title m-two__lines">Платформа (кодовое название)</div>
            <div class="b-compare-name__title">Процессор </div>
            <div class="b-compare-name__title">Модель процессора</div>
            <div class="b-compare-name__title">Количество ядер</div>
            <div class="b-compare-name__title">Тактовая частота </div>
            <div class="b-compare-name__title">Кэш (общий, L2 или L3)</div>
            <div class="b-compare-name__title m-two__lines">Энергопотребление процессора</div>
            <div class="b-compare-name__title">Чипсет</div>
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider clearfix">
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title">2 </div>
                            <div class="b-compare-name__title">2 100 МГц </div>
                            <div class="b-compare-name__title">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title m-compare__changes">2 </div>
                            <div class="b-compare-name__title">2 100 МГц </div>
                            <div class="b-compare-name__title">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title">2 </div>
                            <div class="b-compare-name__title m-compare__changes">2 100 МГц </div>
                            <div class="b-compare-name__title m-compare__changes">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title">2 </div>
                            <div class="b-compare-name__title">2 100 МГц </div>
                            <div class="b-compare-name__title">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title">2 </div>
                            <div class="b-compare-name__title">2 100 МГц </div>
                            <div class="b-compare-name__title m-compare__changes">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">Intel Chief River (2012)</div>
                            <div class="b-compare-name__title">Intel Pentium </div>
                            <div class="b-compare-name__title">B950 </div>
                            <div class="b-compare-name__title">2 </div>
                            <div class="b-compare-name__title">2 100 МГц </div>
                            <div class="b-compare-name__title">2 Мб </div>
                            <div class="b-compare-name__title">35 Вт </div>
                            <div class="b-compare-name__title">Intel HM76 Express</div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div><!--/.b-compare__section-->
    <h2 class="b-compare__h2">Размеры и вес</h2>
    <div class="b-compare__section clearfix">
        <div class="b-compare-name">
            <div class="b-compare-name__title">Ширина </div>
            <div class="b-compare-name__title">Глубина</div>
            <div class="b-compare-name__title">Толщина </div>
            <div class="b-compare-name__title">Вес </div>
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider clearfix">
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title m-compare__changes">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">412 мм</div>
                            <div class="b-compare-name__title">267 мм</div>
                            <div class="b-compare-name__title">36.8 мм</div>
                            <div class="b-compare-name__title">2980 г </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div><!--/.b-compare__section-->
    <h2 class="b-compare__h2">Конструкция</h2>
    <div class="b-compare__section clearfix">
        <div class="b-compare-name">
            <div class="b-compare-name__title">Материал корпуса</div>
            <div class="b-compare-name__title m-two__lines">Фактура поверхности корпуса</div>
            <div class="b-compare-name__title">Цвет панелей корпуса</div>
            <div class="b-compare-name__title">Материал крышки</div>
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider clearfix">
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">пластик</div>
                            <div class="b-compare-name__title">глянцевая гладкая</div>
                            <div class="b-compare-name__title">чёрный</div>
                            <div class="b-compare-name__title">пластик</div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div><!--/.b-compare__section-->
    <div class="b-compare__section m-compare__score clearfix">
        <div class="b-compare-name">
            <div class="b-compare-name__title">Баллы</div>
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev" title="вперед"></a>
                <div class="b-slider clearfix">
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">5</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">20</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">2</div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">6</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">11</div>
                        </div>
                        <div class="b-winner__item">
                            <div class="b-compare-name__title">7</div>
                        </div>
                    </div>
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div><!--/.b-compare__section-->
    </div>
        </div><!--/content-->