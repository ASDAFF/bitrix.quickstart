<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeModuleLangFile(__FILE__);

    $detail = $arParams['FASHION_ROOT_PATH'].$val['CODE']."/";

    $arButtons = CIBlock::GetPanelButtons(
        $arParams['FASHION_IBLOCK_ID'],
        $arResult["ID"],
        0,
        array("SECTION_BUTTONS"=>false, "SESSID"=>false)
    );
    $val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

    $this->AddEditAction($arResult["ID"], $val["EDIT_LINK"], CIBlock::GetArrayByID($arParams['FASHION_IBLOCK_ID'], "ELEMENT_EDIT"));

?>
<div class="col-left" id="<?=$this->GetEditAreaId($arResult['ID'])?>">
    <div class="col-left">
        <div class="detalet-cart">
            <div class="img-photos-demo">
                <div class="big-demo">
                    <?
                    $PicPath = NOVAGR_JSWSHOP_TEMLATE_DIR."images/nophoto.png";
                    if ( isset($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE']) )
                    {
                        $i = 0;
                        foreach($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'] as $val)
                        {
                            $i++;
                            if( $i <= 1) $firsPic = $val;
                            ?>
                            <a id="fLinkPicCollection" href="#myModalCollection" role="button"
                               href="<?=$val;?>" class="detailLink"> <img
                                    <? if( $i <= 1)echo'id="detailImg"';?> width="450" alt="<?=$arResult['ELEMENT']['NAME']?>"
                                                                           src="<?=$val;?>">
                            </a>
                            <?
                            break;
                        }
                    } else {
                        ?>
                        <a href="<?=$PicPath;?>" rel="gallery" id="detailImg" class="detailLink" style="<? if($i++ > 1)echo'display:none;';?>">
                            <img width="450" alt="<?=$arResult['ELEMENT']['NAME']?>" src="<?=$PicPath;?>">
                        </a>
                    <?
                    }
                    ?>
                </div>
            </div>
            <div id="thumbsCollection" class="thumbs">
                <?
                if ( isset($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE']) )
                    foreach($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'] as $val)
                    {
                        ?>
                        <img width="90" height="130" href="<?=$val;?>"
                             alt="" src="<?=$val;?>" class="previewImg">
                    <?
                    }
                ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>

    </div>
	<div class="clear"></div>
    <?
    Novagroup_Classes_General_Main::getView('catalog.element','yashare');
    ?>
    <script type="text/javascript">$('div.info-bar').removeClass('soc')</script>
</div>
<div class="col-right">
	<div class="composite-pr">
		<div class="header-title-demo">
			<div class="more-pr tooltip-demo" data-toggle="buttons-radio">
                <?php /*
                    <span><a href="/" rel="tooltip" data-placement="top" data-original-title="назад по образам"><img width="10" height="12" src="/bitrix/templates/novagr_jwshop/images/left-d.gif" alt=""></a></span>
                */?>
                <span><h1><?=$arResult['ELEMENT']['NAME'];?></h1></span>
                <?php /*<span><a href="/" rel="tooltip" data-placement="top" data-original-title="вперед по образам"><img width="10" height="12" src="/bitrix/templates/novagr_jwshop/images/right-d.gif" alt=""></a></span>
                */?>
            </div>
		</div>
        <?php 
            //deb($val); 
            // если пользователь входит в группу Администраторы интернет-магазина [5]
            // то показываем карандашик для редакрирования

            if ($arResult['SHOW_EDIT_BUTTON'] == "Y") {
            ?>
            <div class="tooltip-demo">
			<div class="bs-docs-tooltip-examples admin">
				<a target="_blank"
					href="/bitrix/admin/iblock_element_edit.php?ID=<?=$arResult['ELEMENT']['ID']?>&type=catalog&IBLOCK_ID=<?=$arParams["FASHION_IBLOCK_ID"]?>"
					data-original-title="<?=GetMessage("EDIT_LABEL")?>" rel="tooltip"
					data-placement="top"><i class="icon-admin"></i></a>
			</div>
		</div>
            <?php 
            }
        ?>

        <hr class="composite-hr">
		<div class="">
		<?php $ITEMS_IN_IMAGERY = (count($arResult['ITEMS'])>0) ? GetMessage("ITEMS_IN_IMAGERY") : GetMessage("ITEMS_IN_IMAGERY_NOT_EXISTS"); ?>
			<p class="title"><?=$ITEMS_IN_IMAGERY; ?></p>
            <?php 
                $SUM_FULL_PRICE = array();  $SUM_OLD_PRICE = array(); $SUM_CURRENCY = null;
                foreach ($arResult['ITEMS'] as $item) {
                   // deb($item);
                ?>
                <div class="pic-image">
                    <div class="block-pr">
                        <span class="depiction">
                        	<div class="card-lider-m fk">
								<?
			                    if ($item['PROPERTY_SPECIALOFFER_ENUM_ID'] == "1")
			                        echo '<div class="card-spec-min f">%</div>';
			                    if ($item['PROPERTY_NEWPRODUCT_ENUM_ID'] == "3")
			                        echo '<div class="card-new-min f">new</div>';
			                    if ($item['PROPERTY_SALELEADER_ENUM_ID'] == "5")
			                        echo '<div class="card-lider-min f">'.GetMessage('SALELEADER').'</div>';
			                    ?>
								
							</div>
                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>">
                                <img src="<?$APPLICATION->IncludeComponent(
                                    "novagroup:catalog.element.photo",
                                    "common",
                                    Array(
                                        "CATALOG_IBLOCK_ID" => $item['IBLOCK_ID'],
                                        "CATALOG_ELEMENT_ID" => $item['ID'],
                                        "PHOTO_ID" => "",
                                        "PHOTO_WIDTH" => "100",
                                        "PHOTO_HEIGHT" => "120"
                                    ),
                                    false,
                                    Array(
                                        'ACTIVE_COMPONENT' => 'Y',
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>" />
                            </a>
                            <div class="wrapped-depiction">
                                <span style="cursor:pointer;" class="link-popover-card catalog-preview-color-element-498 catalog-preview-color-element-498-14">
                                    <a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="quickViewLink" data-toggle="modal"><?=GetMessage("NOVAGR_JWSHOP_PROSMOTR")?>
                                        <img width="21" height="18" alt="" src="<?=NOVAGR_JSWSHOP_TEMLATE_DIR?>images/lupa.png">
                                    </a>
                                </span>
                            </div>
                        </span>
                    </div>
				<div class="block-pr">

					<p><?=$item['NAME']?></p>
					<div class="wrapper-l">


                            <?php
                                if ($item['PRICE'] < $item['OLD_PRICE']) {
                                ?>
                                <span class="discount"><?=CurrencyFormat($item["PRICE"], $item["CURRENCY"]);?></span>
						<span class="old-price"><?=CurrencyFormat($item['OLD_PRICE'], $item["CURRENCY"]);$SUM_OLD_PRICE[]=$item["OLD_PRICE"]; $SUM_CURRENCY = $item["CURRENCY"];?></span>
                                <?php 

                                } else {
                                ?><span class="default-value"><?=CurrencyFormat($item["PRICE"], $item["CURRENCY"]); $SUM_FULL_PRICE[]=$item['PRICE']; $SUM_CURRENCY = $item["CURRENCY"];?></span><?php 
                                }
                            ?>
                            <hr class="composite-hr">
						<div class="wrapper-l">
							<span class="brand-l"><?=GetMessage("BRAND_LABEL")?>:</span> <span><?=$item["PROPERTY_VENDOR_NAME"]?></span>
						</div>
					</div>
				</div>
			</div>
                <?php 

                }

            ?>

            <div class="pic-image"></div>
			<div class="total-price">
                <?php if($arResult['ELEMENT']['TOTAL']>0):?>
                <p>
                    <?=GetMessage("TOTAL_COST_IMAGERY")?>:
                    <?if(count($SUM_OLD_PRICE)):?>
                        <span class="discount"><?=CurrencyFormat($arResult['ELEMENT']['TOTAL'], $SUM_CURRENCY)?></span>
					<span class="old-price"><? $SUM_PRICE =(array_sum($SUM_FULL_PRICE)+array_sum($SUM_OLD_PRICE)); echo CurrencyFormat($SUM_PRICE, $SUM_CURRENCY); ?></span>
                        <? else:?>
                        <span><?=CurrencyFormat($arResult['ELEMENT']['TOTAL'], $SUM_CURRENCY)?></span>
                        <?endif;?>
                </p>
                <?php endif;?>
            </div>
		</div>
	</div>
</div>
<div class="clear"></div>

<script>
    $(document).ready(function() {
        $('.previewImg').mouseenter(function(){
            $('#detailImg').attr('src', $(this).attr('href'));
        });

        // when you click on a small picture pops up large	
        $("#thumbsCollection img").live('click', function(){

            $("#fLinkPicCollection").trigger('click');
            return false;
        });

        // тултипы
        $('.tooltip-demo').tooltip({
            selector: "button,li,a[rel=tooltip]"
        });

        // клик по картинке - всплывает мод. окно с каруселью
        $("#fLinkPicCollection").live('click',function() {

            showAjaxLoader();
            var picHTML = '';
            var picArr = [];
            var curPic = $(this).find("img").attr("src");

            var total = $('#thumbsCollection img').length;

            var title = $('.header-title-demo h1').html();
            $('#thumbsCollection img').each(function(i, val) {
                picArr[i] = $(this).attr("href");			
            });

            var j = 1;
            var curImageIndex = 1;

            for (var i in picArr) {
                var active = '';
                if (picArr[i] == curPic) {
                    active = 'active ';	
                    curImageIndex = j;
                }

                picHTML += '<div class="'+active+'item">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h3>'+title+' </h3>' +
                '</div>' +
                '<div class="modal-body">' +
                '<img alt="" src="'+picArr[i]+'">' +
                '</div>' +
                '<div class="modal-footer"><?=GetMessage("CAROUSEL_LABEL1")?> <span class="curImg">'+j+'</span> <?=GetMessage("CAROUSEL_LABEL2")?> <span class="totalImg">'+total+'</span></div>' +
                '</div>';
                ++j;		
            }

            // показываем стрелки в зависимости от текущей страницы
            showHideArrows(curImageIndex, total);

            $("#carousel-inner-collection").html(picHTML);

            var $myCarouselCollection = $('#myCarouselCollection').carousel({'interval': false});
            // скрываем стрелки если послед. картинка
            $myCarouselCollection.on('slid', function() {

                var curImageIndex = $("#carousel-inner-collection .active .curImg").html();
                showHideArrows(curImageIndex, total);
                showAjaxLoader();

                var preloadImage = new Image();
                preloadImage.onload = function(){
                    hideAjaxLoader();
                    var marginLeft = ((preloadImage.width+30)/2);
                    $("#myModalCollection").css('marginLeft', "-"+marginLeft+"px");
                }
                preloadImage.src = $("#myModalCollection .carousel-inner .active .modal-body img").attr("src");

            });

            var preloadImage = new Image();
            preloadImage.onload = function(){
                hideAjaxLoader();
                var marginLeft = ((preloadImage.width+30)/2);
                $("#myModalCollection").modal({'marginLeft': marginLeft});
            }
            preloadImage.src = curPic;
            return false;
        });

    });
</script>
<script type="text/javascript">
    $('.quickViewLink').click(function(){
        return loadPreviewElementModalWindow($(this).attr('href'));
    });
    /*конец всплывающий быстрый просмотр*/
</script>

<div id="myModalCollection" class="modal fade card-img modal-overflow"
	tabindex="-1" aria-hidden="false" style="display: none;">
	<div id="myCarouselCollection" class="carousel">
		<div class="carousel-inner" id="carousel-inner-collection"></div>
		<!-- Carousel nav -->
		<a id="left-arr" class="carousel-control left" href="#myCarouselCollection"
			data-slide="prev"></a> <a id="right-arr"
			class="carousel-control right" href="#myCarouselCollection" data-slide="next"></a>
	</div>
</div>
