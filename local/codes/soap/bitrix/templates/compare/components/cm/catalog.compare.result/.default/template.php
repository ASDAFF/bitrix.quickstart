<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
$(function(){
    $('button.b-button__fast').live('click', function(){
 
       $('.b-fast_order').remove();
 
       $('body').append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Вам перезвонит оператор и оформит заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','154px') 
                         .css('left','70px')
                         .css('z-index','5'); 
 
       return false;
    });
	
});  
$(document).ready(function() {
        $("a[href='#b-wishlist__add']").click(function(){
                $("#wishlist_add_el").attr('el',$(this).attr('el'))
        })
        function wishlist_element_add(object){
            var ID = object.attr('el')
            var category = $('#cat_list').val()
            var name = $('.b-cart-field__input[placeholder="Новый вишлист"]').val()
            var button = object
            $.ajax({
                    type: "POST",
                    url: "/includes/ajax/wishlist/add_element.php",
                    data: ({
                            element : ID,
                            cat : category,
                            name: name
                    }),
                    success: function(html){
						
						var opt = "<option value="+html+">"+name+"</option>"
						$("#cat_list").append(opt)
                        $.gritter.add({
                                title: 'Добавление товара',
                                text: 'Товар был успешно добавлен в вишлист!',
                                sticky: false,
                                time: 2500
                        });
                    }
            })
        }
        $("#wishlist_add_el").click(function(){
                var button = $(this)
                if(button.attr('el')){
                    wishlist_element_add(button)
                }

        }) 
		});
</script>
<div class="b-popup m-popup__orange" id="b-compare__add">
		<div class="b-popup__wrapper">
			<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
			<a class="b-button__fast" href="/catalogue/compare.php">Сравнить товары</a>
		</div>
	</div>
	
<div class="catalog-compare-result">
    <a name="compare_table"></a>
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

<?$sect_id = array();
    $ball = array();
    $array = array();
    $z = 0;?>
<div class="b-container m-wishlist m-compare-list clearfix">
<div class="b-popup m-popup__orange" id="b-wishlist__add">
		<div class="b-popup__wrapper">
			<div class="b-wishlist__select">
				<select name="cat" id="cat_list">
<?global $USER;?>
				<?	if($USER->GetID()){
						$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
						$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
						while($ar_result = $db_list->GetNext())
						{?>
							<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
						<?}
					}
				
				?>
				</select>
			</div>
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='' class="b-button__fast">OK</a></div>
		</div>
	</div>
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
            $z++; 
        ?>
        <?endforeach;?>
    <div class="b-compare__title">К сравнению добавлено <b><?=count($arResult["ITEMS"]);?></b> товаров в <b><?=count($sect_id);?></b> категориях:</div>
    <aside class="b-sidebar">
        <div class="b-sidebar-filter m-sidebar"> 
            <?foreach($sect_id as $arSec):
                    //echo "<pre>", print_r($arSec,1), "</pre>";?>
                <div class="b-sidebar-wishlist__section">
                    <div class="b-sidebar-wishlist__title">
                        <!--<h2 class="b-sidebar-wishlist__h2"><?//=$arSec["NAME"]?>&nbsp;<span class="b-sidebar-wishlist__count"><?//=$arSec["NUM"]?></span></h2>-->
                        <a href="<?=$arSec["URL"]?>" class="b-sidebar-wishlist__h2"><?=$arSec["NAME"]?>&nbsp;<span class="b-sidebar-wishlist__count"><?=$arSec["NUM"]?></span></a>
                    </div>


                    <form action="compare.php" method="get">
                        <?foreach($arResult["ITEMS"] as $arElement):
                                if($arElement["IBLOCK_SECTION_ID"] == $arSec['ID']):
                                ?>
                                <input type="hidden" name="ID[]" value="<?=$arElement["ID"]?>" />
                                <?
                                    endif;
                                endforeach;?>
                        <input type="submit" name="submit" class="b-button__delete m-cart__delete m-wishlist__delete" value="">
                        <input type="hidden" name="action" value="DELETE_FROM_COMPARE_RESULT" />
                        <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
                    </form>


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
            <form action="compare.php" method="get">
                <div class="b-slider-wrapper" id="compare">
                    <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                    <div class="b-slider clearfix">

                        <?
                            $i = 0;
                            $z1 = 0;
                            foreach($arResult["ITEMS"] as $arElement):
                                $z1++;?>
                            <?if ($i % 3 == 0):?><div class="clearfix"><?endif;?>
                                <div class="b-slider__item">
                                    <div class="b-slider__text">
<?if(is_array($arElement["FIELDS"]["PREVIEW_PICTURE"])):?>
                                        <div class="b-slider__image"><img border="0" src="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["ALT"]?>" /></div>
<?else:?>
<div class="b-slider__image"><img border="0" src="/images/img-element__image.png"  alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></div>
<?endif?>             
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
                                        <a el='<?=$arElement['ID']?>' href="#b-wishlist__add" class="b-icon m-wishlist__add" title="<?echo GetMessage("WISHLIST")?>"></a>
                                        <?
                                            if($arElement["CAN_BUY"]):
                                            ?>
                                            <a id="<?=$arElement['ID']?>" href="<?echo $arElement["ADD_URL"]?>" class="b-icon m-icon__buy"></a>
                                            <?endif;?>
                                    </div>


                                    <button name="ID_el[]" class="b-button__delete m-cart__delete m-wishlist-item__delete" value="<?=$arElement["ID"]?>"></button>

                                    <!--<input type="submit" value="<?=GetMessage("CATALOG_REMOVE_PRODUCTS")?>" />-->

                                </div>

                            <?if ($i == 2 OR $z1 == $z):?></div><?$i = 0; else: $i++; endif;?>
                            <?endforeach?>


                        <!--  </div>-->
                    </div>
                    <a href="#" class="b-slider__control m-next" title="вперед"></a>
                </div>

                <input type="hidden" name="action" value="DELETE_FROM_COMPARE_RESULT" />
                <input type="hidden" name="IBLOCK_ID" value="<?=$arParams["IBLOCK_ID"]?>" />
            </form>

        </article>
    </section>
    <div class="b-winner clearfix">
        <div class="b-winner__checkbox"><label class="b-checkbox" id="m-changes__show"><input type="checkbox" name="checkbox_gp_1" value="1" checked />Только различия</label></div>
        <div class="b-winner__wrapper">
            <div class="b-slider-wrapper" id="b-winner__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>


                <div class="b-slider clearfix">
                    <?
                        $i = 0;
                        $z1 = 0;
                        foreach($arResult["ITEMS"] as $arElement):
                            $z1++;?>
                        <?if ($i % 3 == 0):?><div class="clearfix"><?endif;?>
                            <?if ($arElement["ID"] == $arResult["mostFre"]):?>

                                <div class="b-winner__item m-winner">Победитель</div>
                                <?else:?>
                                <div class="b-winner__item"></div>
                                <?endif;?>

                        <?if ($i == 2 OR $z1 == $z):?></div><?$i = 0; else: $i++; endif;?>
                        <?endforeach?>
                </div>
                <!--</div>-->

                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>
        </div>
    </div>
</div>
<div class="b-compare-wrapper">
    <h2 class="b-compare__h2">Общая информация</h2>
    <div class="b-compare__section clearfix">
        <div class="b-compare-name">


            <?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
                    $arCompare = Array();

                ?>
                <div class="b-compare-name__title"><?=$arProperty["NAME"]?></div>
                <?endforeach;?>
        </div>
        <div class="b-compare-prop">
            <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
                <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                <div class="b-slider clearfix">
                    <?
                        $i = 0;
                        $z1 = 0;
                        foreach($arResult["ITEMS"] as $arElement):
                            $z1++;?>
                        <?if ($i % 3 == 0):?><div class="clearfix"><?endif;?>
                            <div class="b-winner__item">
                                <?if($diff || !$arResult["DIFFERENT"]):?>
                                    <?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
                                            if ($arProperty["PROPERTY_TYPE"] != 'L'):?>
                                            <div class="b-compare-name__title <?if ($arResult["MAX_BAL"][$code]["id"] == $arElement["ID"]): $ball[$arElement["ID"]]++;?>m-compare__changes<?endif;?>"><?if($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"] != ""):?><?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?><?else: echo " - "; endif;?></div>
                                            <?else:?>
                                            <div class="b-compare-name__title <?if ($arResult["MAX_BAL_LIST"][$code]["id"] == $arElement["ID"]): $ball[$arElement["ID"]]++;?>m-compare__changes<?endif;?>"><?if($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"] != ""):?><?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?><?else: echo " - "; endif;?></div>
                                        <?endif;?>
                                        <?endforeach;
                                        else:?>

                                    <?foreach($arResult["SHOW_PROPERTIES"] as $code=>$arProperty):
                                            if ($arProperty["PROPERTY_TYPE"] != 'L'):?>
                                            <div class="b-compare-name__title <?if ($arResult["MAX_BAL"][$code]["id"] == $arElement["ID"]): $ball[$arElement["ID"]]++;?>m-compare__changes<?endif;?>"><b><?if($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"] != ""):?><?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?><?else: echo " - "; endif;?></b></div>
                                        <?else:?>
                                        <div class="b-compare-name__title <?if ($arResult["MAX_BAL_LIST"][$code]["id"] == $arElement["ID"]): $ball[$arElement["ID"]]++;?>m-compare__changes<?endif;?>"><b><?if($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"] != ""):?><?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?><?else: echo " - "; endif;?></b></div>
                                    <?endif;?>
                                    <?endforeach;?>

                                <?endif;?>
                            </div>

                        <?if ($i == 2 OR $z1 == $z):?></div><?$i = 0; else: $i++; endif;?>
                        <?endforeach?>
                    <!--</div>-->
                </div>
                <a href="#" class="b-slider__control m-next" title="вперед"></a>
            </div>

        </div>
    </div>
</div>
<? //echo "<pre>", print_r($ball,1), "</pre>";?>
<div class="b-compare__section m-compare__score clearfix">
    <div class="b-compare-name">
        <div class="b-compare-name__title">Баллы</div>
    </div>
    <div class="b-compare-prop">
        <div class="b-slider-wrapper" id="b-compare-prop__slider"><!-- сюда вешаем ID или CLASS для слайдера -->
            <a href="#" class="b-slider__control m-prev" title="вперед"></a>
            <div class="b-slider clearfix">
                <?
                    $i = 0;
                    $z1 = 0;
                    foreach($arResult["ITEMS"] as $arElement):
                        $z1++;?>
                    <?if ($i % 3 == 0):?><div class="clearfix"><?endif;?>
                        <div class="b-winner__item">
                            <?if (isset($ball[$arElement["ID"]])):?>
                                <div class="b-compare-name__title"><?=$ball[$arElement["ID"]];?></div>
                                <?else:?>
                                <div class="b-compare-name__title">0</div>
                                <?endif;?>
                        </div>
                    <?if ($i == 2 OR $z1 == $z):?></div><?$i = 0; else: $i++; endif;?>
                    <?endforeach?>
                <!--</div>-->
            </div>
            <a href="#" class="b-slider__control m-next" title="вперед"></a>
        </div>
    </div>
    </div>
 </div> 
                         
