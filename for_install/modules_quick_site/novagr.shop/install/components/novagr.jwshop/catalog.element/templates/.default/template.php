<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$ajaxUrl = $templateFolder . '/ajax.php';

if (!empty($arResult['ELEMENT']["ID"])) {
    $val = $arResult['ELEMENT'];
//if (count($arResult['ELEMENTS'])) {
//    $val = array_pop($arResult['ELEMENTS']);
    $noPhotoPath =  NOVAGR_JSWSHOP_TEMLATE_DIR."images/nophoto.png";

    if (is_array($arResult["CURRENT_ELEMENT"]["COLORS"]) and is_array($arResult["ELEMENT_COLORS_PHOTOS"])) {
        $hasPhoto = false;
        foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
            foreach ($arResult["ELEMENT_COLORS_PHOTOS"] as $colorId => $photos) {
                if (isset($arResult["ELEMENT_COLORS_PHOTOS"][$color['ID']])) {
                    $hasPhoto = true;
                }
            }
        }
        if ($hasPhoto === false) {
            foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
                $has = array_key_exists($color['ID'], $arResult["ELEMENT_COLORS_PHOTOS"]);
                if ($has === false) {
                    foreach ($arResult["ELEMENT_COLORS_PHOTOS"] as $colorId => $photos) {
                        if (is_array($photos)) {
                            foreach ($photos as $photoId) {
                                if (isset($arResult["ELEMENT_PHOTO"][$photoId]["SRC"])) {
                                    $noPhotoPath = $arResult["ELEMENT_PHOTO"][$photoId]["SRC"];
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
?>
    <script>

        product.setProductName("<?=$val['NAME']?>");
    </script>
<div class="col-left">
	<div class="col-left">
		<div class="detalet-cart">
			<div class="img-photos-demo">
				<div class="big-demo" id="photos">
				<?php 
				// fotos prints by js
				
				?>
				</div>
                <div class="label-card">
                    <?
                    if ($val['PROPERTIES']['SPECIALOFFER']['VALUE_XML_ID'] == "1")
                        echo '<div class="card-spec">%</div>';
                    if ($val['PROPERTIES']['NEWPRODUCT']['VALUE_XML_ID'] == "1")
                        echo '<div class="card-new">new</div>';
                    if ($val['PROPERTIES']['SALELEADER']['VALUE_XML_ID'] == "1")
                        echo '<div class="card-lider">'.GetMessage('SALELEADER').'</div>';
                    ?>
                </div>
			</div>
			<div class="thumbs" id="thumbs">
			<?php // fotos prints by js
				?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>

	</div>
	<div class="col-right">
		<div class="card-tab-det">
			<div class="bs-tab">
				<?php

				if ($arParams['SHOW_EDIT_BUTTON'] == "Y") {
					?>
				<div class="tooltip-demo card-tool">
			
					<div class="bs-docs-tooltip-examples admin"><a target="_blank" data-placement="top" rel="tooltip" data-original-title="<?=GetMessage("EDIT_LABEL")?>" href="/bitrix/admin/iblock_element_edit.php?ID=<?=$val['ID']?>&type=catalog&IBLOCK_ID=<?=$arParams["CATALOG_IBLOCK_ID"]?>"><i class="icon-admin"></i></a>
					</div>

				</div>
				<?php 
				}
				?>
            	<ul class="nav nav-tabs" id="myTab1">
              	<li class="active"><a data-toggle="tab" href="#details"><?=GetMessage("DETAILS_LABEL")?></a></li>
              	<?php 
              if (!empty($val['DETAIL_TEXT'])) {
              	?>
              	<li><a data-toggle="tab" href="#description"><?=GetMessage("DESCR_LABEL")?></a></li>
              	<?
              }              
              ?>             	
              	<li><a data-toggle="tab" href="#delivery"><?=GetMessage("DELIVERY_LABEL")?></a></li>
				<?php
				if ($arResult["COMMENTS_ON"] == 1) {
					?>
				<li><a data-toggle="tab" href="#comment"><?=GetMessage("COMMENTS_LABEL")?></a></li>
					<?
				}
				?>
				</ul>
				
				<div class="tab-content" id="myTabContent1">
					<div id="details" class="tab-pane fade in active">
						<div class="header-title-demo">
							<div data-toggle="buttons-radio" class="more-pr tooltip-demo">

								<span><h1><?=$val['NAME']?></h1></span>

							</div>
						</div>
						<div class="clear"></div>
						<?php 
						if (!empty($arResult['mixData'][$val["PROPERTIES"]['VENDOR']["VALUE"] ]['NAME'])) {
							?>
					
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("BRAND_LABEL")?>:</span>
								<span><?=$arResult['mixData'][$val["PROPERTIES"]['VENDOR']["VALUE"] ]['NAME']?></span>
							</div>
						<?
						}
						if (!empty($val["PROPERTIES"]['CML2_ARTICLE']["VALUE"])) {
							?>
											
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("ARTICUL_LABEL")?>:</span>
								<span><?=$val["PROPERTIES"]['CML2_ARTICLE']["VALUE"]?></span>
							</div>
						<?
						}
						if (!empty($val["PROPERTIES"]['STYLE']["VALUE"])) {
							?>
																	
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("STYLE_LABEL")?>:</span>
								<span><?=$arResult['mixData'][$val["PROPERTIES"]['STYLE']["VALUE"] ]['NAME']?></span>
							</div>
						<?
						}
						if (!empty($arResult["COLLECTION_NAME"])) {
							?>
																							
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("COLLECTION_LABEL")?>:</span>
								<span><?=$arResult["COLLECTION_NAME"]?></span>
							</div>
						<?
						}
						if (!empty($val["PROPERTIES"]['STONE']["VALUE"])) {
							?>
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("INSERTION_LABEL")?>:</span>
								<span><?=$arResult['mixData'][$val["PROPERTIES"]['STONE']["VALUE"] ]['NAME']?></span>
							</div>
						<?
						}
						if (is_array($val["PROPERTIES"]['METAL']["VALUE"]) &&
								!empty($val["PROPERTIES"]['METAL']["VALUE"][0])) {
							
							$k =0;
							$metalStr = '';
							foreach ($val["PROPERTIES"]['METAL']["VALUE"] as $metal) {
								if ($k>0) $metalStr .=", "; 
								$metalStr .= $arResult['mixData'][$metal ]['NAME'];
								$k++;
							}
							?>
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("METAL_LABEL")?>:</span>
								<span><?=$metalStr?></span>
							</div>
						<?
						}
						if (is_array($val["PROPERTIES"]['METAL_COLOR']["VALUE"]) &&
								!empty($val["PROPERTIES"]['METAL_COLOR']["VALUE"][0])) {
								
							$k =0;
							$metalColorStr = '';
							foreach ($val["PROPERTIES"]['METAL_COLOR']["VALUE"] as $metalColor) {
								if ($k>0) $metalColorStr .=", ";
								$metalColorStr .= $arResult['mixData'][$metalColor ]['NAME'];
								$k++;
							}
							?>
							<div class="wrapper-l">
								<span class="brand-l"><?=GetMessage("METAL_COLOR_LABEL")?>:</span>
								<span><?=$metalColorStr?></span>
							</div>
						<?
						}

			?>			
			<hr class="composite-hr">
			<?
		if (count($arResult["CURRENT_ELEMENT"]["COLORS"])) {		
			
			?>
			<div class="choice-color"><?=GetMessage("CHOOSE_COLOR_LABEL")?>:</div>
			<div class="btn-group color-ch " data-toggle="buttons-radio">
				<div id="color-ch" class="bs-docs-tooltip-examples">
				<?

				foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
					?>
					<button data-original-title="<?=$color['NAME']?>" id="color-<?=$color['ID']?>-<?=$val['ID']?>-set-by-hash" class="btn" data-placement="top" rel="tooltip" data-color="<?=$color['ID']?>" type="button">
						<span class="<?php print($color['PROPERTY_CLASS_STONE_COLOR_VALUE']);?>"><i class="icon-diamond"></i></span>
                    </button>
					<?
				}
				?><div class="clear"></div>			
				</div>
			</div>
			<?php
		}

		$countSizes = count($arResult["CURRENT_ELEMENT"]["STD_SIZE"]);

            if ($countSizes == 1 and isset($arResult["CURRENT_ELEMENT"]["STD_SIZE"][-1])) {
                $countSizes = 0;
            }

		if ($countSizes>0) {

			// сортируем массив с размерами от меньшего к большему
			$sortArray = array();
			foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
				unset($arResult["CURRENT_ELEMENT"]["STD_SIZE"][$key]);
				// получаем ключ для сортировки массива размеров
				// если сортировка совпала - прибавляем + 1
				$keyForSort = $arResult['mixData'][$key]['SORT'];
					
				while (in_array($keyForSort, $sortArray)) {
		
					$keyForSort = $keyForSort+1;
				}
				$sortArray[] = $keyForSort;
					
				$arResult["CURRENT_ELEMENT"]["STD_SIZE"][$keyForSort] = $size;
			}
			ksort($arResult["CURRENT_ELEMENT"]["STD_SIZE"]);
		}
		?>
                        <?php
                        if ($countSizes>0) {
                            ?>
		<div class="choice-size "><?=GetMessage("CHOOSE_SIZE_LABEL")?>:</div>

			<div id="size-table" >
				&nbsp;<a href="#myModal8" ><?=GetMessage("SIZE_TABLE")?></a>
			</div>
            <?php
            }
            ?>
			<div class="tab-choice tooltip-demo">
			<?php 
			
			if ($countSizes>0) {

				?>
				<div class="bs-docs-tooltip-examples ">
					<div class="btn-group size-ch " data-toggle="buttons-radio">
						<div id="sizesButtons" class="bs-docs-tooltip-examples">
						<ul>
					<?php 
					$i = 0;
					foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
						
						?>
						<li id="li_<?=$arResult['mixData'][$size["SIZE"]]['ID']?>" data-size-name="<?=$arResult['mixData'][$size["SIZE"]]['NAME']?>">
						<a data-size="<?=$arResult['mixData'][$size["SIZE"]]['ID']?>" data-original-title="" class="btn" data-placement="top" rel="tooltip" type="button">
						<span class="border-ch"><span class="bg-ch"><?=$arResult['mixData'][$size["SIZE"]]['NAME']?></span></span>
						</a>
						</li>
						<?php
						$i++;
					}                         
             		?>
             			</ul>
						<div class="clear"></div>
						</div>
					</div>
              		
				</div>
				<?php 			
			}		
			
			?>			
			
				<script>
					$(document).ready(function(){

						// click handler on the size
                        $("#sizesButtons a").unbind('click');
						$("#sizesButtons a").live("click", function(){
							var sizeId = $(this).data("size");
							product.changeSize(sizeId);
		
							return false;
						});
						// click handler in color
                        $("#color-ch button").unbind('click');
						$("#color-ch button").live("click", function(){
							var colorId = $(this).data("color");
							product.changeColor(colorId);
						});
						
						// handler click the button to buy
                        $("#btnsel").unbind('click');
						$("#btnsel").bind('click', function(){
							product.buyClick(this);					   		
							return false;
						});

                        $("#size-table a").unbind('click');
						$("#size-table a").bind('click', function(){
							
							// рассчитываем и задаем высоту и ширину окна редактирования
							$("#myModal8").modal('show');
							return false;
						});
					
						<?php
						$i = 0;
						foreach ($arResult["OFFERS"] as $item) {

							$quantity = intval($item["CATALOG_QUANTITY"]);
							$colorId = $item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];
							if ($i == 0) {
								
								?>
								var curPhotosSmall = [];
								var curPhotosBig = [];
								var curPhotosBigHeight = [];
								<?php 
								$j = 0 ;
								if (!empty($arResult["DETAIL_PICTURE"]) && $arResult["USE_MORE_PHOTO"] == true) {
									?>
									curPhotosSmall.push('<?=$arResult["DETAIL_PICTURE_MIN_SRC"]?>');
									curPhotosBig.push('<?=$arResult["DETAIL_PICTURE_ARR"]["SRC"]?>');
									curPhotosBigHeight.push('<?=$arResult["DETAIL_PICTURE_ARR"]["HEIGHT"]?>');
									<?php 
									$firsPic = $photo["SRC"];
									$j++;
								}									
								
								if ($arResult["USE_MORE_PHOTO"] == true) {
										
									foreach ($arResult["ELEMENT_MORE_PHOTO"] as $photoId => $photo) {
								
										?>
										curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
										curPhotosBig.push('<?=$photo["SRC"]?>');
										curPhotosBigHeight.push('<?=$photo["HEIGHT"]?>');
										<?php
										if ($j<1) {
											$firsPic = $photo["SRC"];
										}
										$j++; 
									}
									
								} elseif (count($arResult["ELEMENT_COLORS_PHOTOS"][$colorId]) == 0) {
									// если фото нет то выводим фотку-заглушку
									?>
									curPhotosSmall.push('<?=$noPhotoPath?>');
									curPhotosBig.push('<?=$noPhotoPath?>');
									curPhotosBigHeight.push('0');
									<?
									
								} else {
								
									foreach ($arResult["ELEMENT_COLORS_PHOTOS"][$colorId] as $photoId) {

										?>
										curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
										curPhotosBig.push('<?=$arResult["ELEMENT_PHOTO"][$photoId]["SRC"]?>');
										curPhotosBigHeight.push('<?=$arResult["ELEMENT_PHOTO"][$photoId]["HEIGHT"]?>');
										<?php
										if ($j<1) {
											$firsPic = $arResult["ELEMENT_PHOTO"][$photoId]["SRC"];
												
										}
										$j++; 
									}		
								}	
								// show the first offer with min price
						
								?>
								var messages = {
										"ALERT_NAME" : "<?=GetMessage("ALERT_NAME")?>",
										"NO_IN_STOCK" : "<?=GetMessage("NO_IN_STOCK")?>",
										"NO_SIZE_LABEL" : "<?=GetMessage("NO_SIZE_LABEL")?>",
										"PRODUCT_ADDED_TO_CART" : "<?=GetMessage("PRODUCT_ADDED_TO_CART")?>",
										"PRODUCT_ALREADY_IN_CARD" : "<?=GetMessage("PRODUCT_ALREADY_IN_CARD")?>",
										"ALERT_MESSAGE" : "<?=GetMessage("ALERT_MESSAGE")?>"
								}
								product.init(
									<?=$item["ID"]?>,
									'<?=$item["PRICES"][$arResult["BASE_PRICE_CODE"]]["PRINT_DISCOUNT_VALUE_VAT"]?>','<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>',
									'<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>',
									'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>',
									'<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>',
									curPhotosSmall,
									curPhotosBig,
									curPhotosBigHeight,
									'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>',
									'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['SORT']?>',
									<?=$quantity?>,
									'<?=$item["PRICES"][$arResult["BASE_PRICE_CODE"]]["PRINT_VALUE_VAT"]?>',
									'<?=$ajaxUrl?>',
									<?=$val["ID"]?>,
									messages,
									<?=$arResult["COMMENTS_ON"]?>,
                                    <?=($arParams["CATALOG_SUBSCRIBE_ENABLE"] == "Y" ? 'true' : 'false' )?>
								);
                                product.productUrl = '<?=$arResult["DETAIL_PAGE_URL"]?>';

                        <?
								
							} else {
								
								?>
								var curPhotosSmall = [];
								var curPhotosBig = [];
								var curPhotosBigHeight = [];
								<?php
								if (!empty($arResult["DETAIL_PICTURE"]) && $arResult["USE_MORE_PHOTO"] == true) {
									?>
									curPhotosSmall.push('<?=$arResult["DETAIL_PICTURE_MIN_SRC"]?>');
									curPhotosBig.push('<?=$arResult["DETAIL_PICTURE_ARR"]["SRC"]?>');
									curPhotosBigHeight.push('<?=$arResult["DETAIL_PICTURE_ARR"]["HEIGHT"]?>');
									<?php 									
									
								}	
								if ($arResult["USE_MORE_PHOTO"] == true) {
										
									foreach ($arResult["ELEMENT_MORE_PHOTO"] as $photoId => $photo) {
										?>
										curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
										curPhotosBig.push('<?=$photo["SRC"]?>');
										curPhotosBigHeight.push('<?=$photo["HEIGHT"]?>');
										<?
									}
									
								} elseif (count($arResult["ELEMENT_COLORS_PHOTOS"][$colorId]) == 0) {
									// если фоток нет то выводим фотку-заглушку
									?>
									
									curPhotosSmall.push('<?=$noPhotoPath?>');
									curPhotosBig.push('<?=$noPhotoPath?>');
									curPhotosBigHeight.push('0');
									<?
									
								} else {									
									
									foreach ($arResult["ELEMENT_COLORS_PHOTOS"][$colorId] as $photoId) {
										?>
										curPhotosSmall.push('<?=$arResult['PREVIEW_PICTURE'][$photoId]?>');
										curPhotosBig.push('<?=$arResult["ELEMENT_PHOTO"][$photoId]["SRC"]?>');
										curPhotosBigHeight.push('<?=$arResult["ELEMENT_PHOTO"][$photoId]["HEIGHT"]?>');
										<?php 
									}
								}	
							?>
							
							product.addToSet(
								<?=$item["ID"]?>,
								'<?=$item["PRICES"][$arResult["BASE_PRICE_CODE"]]["PRINT_DISCOUNT_VALUE_VAT"]?>',
								'<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>',
								'<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>',
								'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>',
								'<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>',
								curPhotosSmall,
								curPhotosBig,
								curPhotosBigHeight,
								'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>',
								'<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['SORT']?>',
								<?=$quantity?>,
								'<?=$item["PRICES"][$arResult["BASE_PRICE_CODE"]]["PRINT_VALUE_VAT"]?>'
							);
							<?php
							}
							$i++;
						}	
						?>
						
						// handler hover on the preview gallery
						$("#thumbs img").live("mouseenter" ,function() { 
							document.getElementById('detailImg1').src = $(this).attr('href');
						});
										
						// when you click on a small picture pops up large
                        $("#thumbs img").unbind('click');
						$("#thumbs img").live('click', function(){
                            $("#detailImg1").unbind('click');
							$("#detailImg1").trigger('click');
							return false;
						});
						
						// клик по картинке - всплывает мод. окно с каруселью
                        $("#fLinkPic").unbind('click');
						$("#fLinkPic").live('click',function() {

                            showAjaxLoader();
							var picHTML = '';
							var picArr = [];
							var curPic = $(this).find("img").attr("src");

							var total = $('#thumbs img').length;
							
							var title = $('.header-title-demo h1').html();
							$('#thumbs img').each(function(i, val) {
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
										'<img alt="<?=$val["NAME"]?>" src="'+picArr[i]+'">' +
										'</div>' +
										'<div class="modal-footer"><?=GetMessage("CAROUSEL_LABEL1")?> <span class="curImg">'+j+'</span> <?=GetMessage("CAROUSEL_LABEL2")?> <span class="totalImg">'+total+'</span></div>' +
										'</div>';
								++j;		
							}

							// показываем стрелки в зависимости от текущей страницы
							showHideArrows(curImageIndex, total);
							
							$("#carousel-inner").html(picHTML);

							var $myCarousel = $('#myCarousel').carousel({'interval': false});
							// скрываем стрелки если послед. картинка
							$myCarousel.on('slid', function() {
								
								var curImageIndex = $("#carousel-inner .active .curImg").html();
								showHideArrows(curImageIndex, total);
                                showAjaxLoader();

                                var preloadImage = new Image();
                                preloadImage.onload = function(){
                                    hideAjaxLoader();
                                    var marginLeft = ((preloadImage.width+30)/2);
                                    $("#myModal").css('marginLeft', "-"+marginLeft+"px");
                                }

                                preloadImage.src = $("#myModal .carousel-inner .active .modal-body img").attr("src");
							});

                            var preloadImage = new Image();
                            preloadImage.onload = function(){
                                hideAjaxLoader();
                                var marginLeft = ((preloadImage.width+30)/2);
                                $("#myModal").modal({'marginLeft': marginLeft});
                            }
                            preloadImage.src = curPic;
							return false;
						});

						var optionsComments = { 	        
								dataType:  'json',
						        beforeSubmit:  product.checkCommentForm,  
						        success: function(json) { 
						        	// убираем лоадер
									hideAjaxLoader();
						        	if (json.result == "ERROR") {
								
										$("#alert").attr("class", "alert alert-error").html(json.message);
											
									} else if (json.result == "OK") {


										product.getComments(1);
										$("#alert").attr("class", "alert alert-success").html(json.message);
										$("#commenForm" )[0].reset();
										$("#controlGroupName").attr("class", "control-group");
                                        $("#controlGroupText").attr("class", "control-group");

									} 					
						        }
							}
						
						$('#commenForm').ajaxForm(optionsComments); 
						// обновление комментариев
                        $('.refreshComments').unbind('click');
						$('.refreshComments').live('click', function() {

							product.getComments(1);
							return false;
						});
                        $('.pagination ul li a').unbind('click');
						$('.pagination ul li a').live('click', function(){

							product.getComments($(this).attr('inumpage'));

							return false;
						});

                        //set color by anchor
                        var getAnchor = location.hash;
                        if (getAnchor != "")
                        {

                            $(getAnchor + '-set-by-hash').unbind('click');
                            $(getAnchor + '-set-by-hash').click();
                            $('#sizesButtons a:first').unbind('click');
                            $('#sizesButtons a:first').click();
                        } else {

                            $('.color-ch button:first').unbind('click');
                            $('#sizesButtons a:first').unbind('click');
                            $('.color-ch button:first').click();
                            $('#sizesButtons a:first').click();

                        }
                        var sizeIds = [];
                        <?
                        if (isset($arParams['CATALOG_ELEM_CS']))
                        {
                            $cs = explode("-",$arParams['CATALOG_ELEM_CS']);
                            if (is_array($cs))
                            {
                                foreach($cs as $item)
                                {
                                    ?>
                                    sizeIds[sizeIds.length] = '<?=$item?>';
                                    <?
                                }
                            }
                        }
                        ?>
                        if ($.isArray(sizeIds)) {
                            for (var i = 0; i < sizeIds.length; i++) {
                                if (product.checkSize(sizeIds[i])) {
                                    //product.changeSize(sizeIds[i]);

                                    $("#li_"+sizeIds[i]+" a").trigger('click');
                                    break;
                                }
                            }
                        }

                        <?php
                        if ($arResult["MAX_COUNT_SIZE"] > 0 && $arResult["MAX_COUNT_COLOR"] > 0) {
                            // choose size and color like smart site
                            ?>

                            product.currentSizeId = <?=$arResult["MAX_COUNT_SIZE"]?>;
                            $( "button[data-color='<?=$arResult["MAX_COUNT_COLOR"]?>']" ).click();
                            <?
                        }
                        ?>
            });
        </script>
    <?
    Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/actual-price.php');
    Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/quick-buy.php');
    ?>
            <div class="adbasket new">
                <div id="buy-popup" style="display: none;">
                    <div class="message-demo" id="message-demo"></div>
                </div>
                <a href="<?//echo $arResult["ADD_URL"]?>" class="addToBasket">
                    <span class="icon-arrow-down"></span>
                </a>
                <a href="<?//echo $arResult["ADD_URL"]?>" class="btn btn-primary addToBasket" id="btnsel"><?=GetMessage("ADD_TO_CART")?></a>

            </div>
            <div class="clear"></div>
            <div class="last-ie">
            <?
            Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/basket.php');
            ?>
            </div>
         <div class="clear"></div>	
		</div>
              </div>
              <?php 
              if (!empty($val['DETAIL_TEXT'])) {
              	?>
              	<div id="description" class="tab-pane fade">
                <h2><?=GetMessage("ABOUT_PRODUCT")?>:</h2>
			 	<?=$val['DETAIL_TEXT']?>
              </div>
              	<?
              }              
              ?>
              <div id="delivery" class="tab-pane fade">
                <?=$arResult["delivery"]?>
              </div>
              <?php
				if ($arResult["COMMENTS_ON"] == 1) {
					?>
				<div id="comment" class="tab-pane fade">
		
					<div class="coment ">
							<div class="wrap-tab">
								<h4><?=GetMessage('COMMENTS_LABEL')?> <a class="refresh refreshComments" href="#" title="<?=GetMessage('COMMENTS_LABEL2')?>"><span class="icon-refresh"></span></a></h4>
								
								<div class="comments-list" id="comments-list"></div>
								<form id="commenForm" method="post" action="<?=$ajaxUrl?>">
								
									<div class="comment-refresh" id="comment-refresh" style="display:none">
									<a class="refresh refreshComments" href="#" title="<?=GetMessage('COMMENTS_LABEL2')?>"><span class="icon-refresh"></span> <?=GetMessage('COMMENTS_LABEL2')?></a>
									</div>
									
									<div id="accordion2" class="accordion smiles-accordeon">
					                    <div class="accordion-group">
					                        <div class="accordion-heading">
					                            <a href="#collapseOne" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle"><span class="icon-addcomment"></span> <?=GetMessage('COMMENTS_LABEL3')?></a>
					                        </div>
					                        <div class="accordion-body collapse" id="collapseOne">
					                            <div class="accordion-inner">
					
					                                <div id="alert"></div>
					                                <input type="hidden" name="action" value="comment">
					                                <input type="hidden" name="productId" value="<?=$arResult["ID"]?>">
					                                <input type="hidden" name="productCode" value="<?=$arResult['ELEMENT']["CODE"]?>">
					
					
					                                <div id="controlGroupName" class="control-group">
					                                </div>
					                                <div id="controlGroupEmail" class="control-group">
					                                </div>
					
					                                <div id="controlGroupText" class="control-group">
					                                    <textarea id="REVIEW_TEXT" tabindex="5"  rows="8" cols="35" name="REVIEW_TEXT" class="comments-form-comment"></textarea>
					                                </div>
					
					                                <div><input id="sendComment" type="submit" class="btn" value="<?=GetMessage('COMMENTS_LABEL5')?>"></div>
					
					                            </div>
					                        </div>
					                    </div>
									</div>
								</form>
							</div>
						</div>
						<div class="clear"></div>
				</div>
					<?
				}
				?>	
            </div>
          </div>
		<div class="clear"></div>
        <?php
            if($_REQUEST['CAJAX']!=="1"){
                Novagroup_Classes_General_Main::getView('catalog.element','yashare');
            }
        ?> 
		</div>   
	</div>
</div>
	
	<?php
    if($arParams['CAJAX']!=="1"){
    $APPLICATION->IncludeComponent("novagroup:catalog.element.recommend", "", array(
            "ELEMENT_ID" => $arResult["ID"],
            "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
            "OFFERS_IBLOCK_ID" => $arParams['CATALOG_OFFERS_IBLOCK_ID'],
            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
            "CACHE_TIME" => $arParams['CACHE_TIME'],
        ), false,
        Array(
            'ACTIVE_COMPONENT' => 'Y',
            "HIDE_ICONS" => "Y"
        ));
    }
	?>

	<div class="clear"></div>

	<div id="myModal8" class="modal hide fade size-tab-my mod-size" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="false">
	<div class="modal-header" id="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h3 id="myModalLabel8"><?=GetMessage("SIZE_TABLE2")?></h3>
	</div>
	<div class="modal-body" id="modal-body">
	<?=$arResult["tablitsa-razmerov"]?>           
		</div>
	</div>
<?php 
}
?>

<div id="myModal" class="modal fade card-img modal-overflow" tabindex="-1" aria-hidden="false"  style="display: none;">
    <div id="myCarousel" class="carousel">
        <div class="carousel-inner" id="carousel-inner"></div>
       <!-- Carousel nav -->
        <a id="left-arr" class="carousel-control left" href="#myCarousel" data-slide="prev"></a>
        <a id="right-arr" class="carousel-control right" href="#myCarousel" data-slide="next"></a>
    </div>
</div>