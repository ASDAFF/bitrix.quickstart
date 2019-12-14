<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

// pictures
$arImages = array();
if( is_array($arResult["DETAIL_PICTURE"]) ) {
	$arImages[] = $arResult['DETAIL_PICTURE'];
}
if(is_array($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE']) && count($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'])>0) {
	foreach($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'] as $arImage) {
		$arImages[] = $arImage;
	}
}

$tabDescription = ($arResult['DETAIL_TEXT']!='') ? true : false ;
$tabProperties = (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES'])>0) ? true : false;
$tabDocs = false;


?><div class="row"><?
	?><div class="js-detail js-element js-elementid<?=$arResult['ID']?> col col-md-12" <?
		?>data-elementid="<?=$arResult['ID']?>" <?
		?>data-elementname="<?=CUtil::JSEscape($arResult['NAME'])?>" <?
		?>><?
		?><a class="js-detail_page_url" href="<?=$arResult['DETAIL_PAGE_URL']?>"></a><?

		?><div class="row"><?
			?><div class="col col-md-9"><?

				?><div class="row"><?
					?><div class="col col-md-<?if($arParams['HEAD_TYPE']=='type3'):?>6<?else:?>5<?endif;?>"><?

						?><div class="row"><?
							// general picture
							?><div class="col col-md-12 pic"><?
							
								if(is_array($arImages) && count($arImages) > 0) {
									
									?><div class="owlslider js-general_images"><?
									
										foreach($arImages as $arImage) {
											?><div class="changeFromSlider 
														fancybox.ajax
														fancyajax"
														href="<?= $arResult["DETAIL_PAGE_URL"] ?>"
														title="<?=$arResult["NAME"]?>"><?
												?><img src="<?=$arImage['SRC']?>" alt="<?=$arImage['ALT']?>" title="<?=$arImage['TITLE']?>" data-index="<?=$arImage['ID']?>"><?
											?></div><?
										}
									
									?></div><?
								}
							
								if(is_array($arImages) && count($arImages)>0) {
									?></a><?
								}
							?></div><?
							// slider
							if(is_array($arImages) && count($arImages)>0) {
								?><div class="col col-md-12"><?
									?><div class="thumbs"><?
										?><div class="owlslider js-slider_images"><?
											$index = 0;
											foreach($arImages as $arImage) {
												?><div class="pic<?=$arImage['ID']?><?if($index<1):?> checked<?endif;?> thumb"><?
													?><a href="<?=$arImage['SRC']?>" data-index="<?=$arImage['ID']?>" style="background-image: url('<?=$arImage['RESIZE']['src']?>');"><?
														?><div class="overlay"></div><?
														?><i class="fa"></i><?
													?></a><?
												?></div><?
												$index++;
											}
										?></div><?
									?></div><?
								?></div><?
							}
						?></div><?

					?></div><?
					?><div class="col col-md-<?if($arParams['HEAD_TYPE']=='type3'):?>6<?else:?>7<?endif;?>"><?
						?><div class="row"><?
							// breaadcrumb and title
							?><div class="col col-md-12 brcrtitle"><?
								?><div class="brcr"></div><?
								if( $arParams['RSMONOPOLY_PROP_ARTICLE']!='' && $arResult['PROPERTIES'][$arParams['RSMONOPOLY_PROP_ARTICLE']]['VALUE']!='' ) {
									?><div class="row"><div class="col col-md-12 text-right article"><?
										?><?=GetMessage('RS.MONOPOLY.ARTICLE')?>: <?=$arResult['PROPERTIES'][$arParams['RSMONOPOLY_PROP_ARTICLE']]['VALUE']?><?
									?></div></div><?
								}
								?><div class="ttl"></div><?
							?></div><?
							// prices
							if( isset($arResult['RS_PRICE']) ) {
								?><div class="col col-md-12 prices"><?
									?><div><?
										?><?=GetMessage('RS.MONOPOLY.PRICE')?>: <?if( IntVal($arResult['RS_PRICE']['DISCOUNT_DIFF'])>0 ) {
											?><span class="price old"><?=$arResult['RS_PRICE']['PRINT_VALUE']?></span><?
											?><div><span class="price cool new"><?=$arResult['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></span><?
												?><span class="discount"><?=GetMessage('RS.MONOPOLY.DISCOUNT')?>: <?=$arResult['RS_PRICE']['PRINT_DISCOUNT_DIFF']?></span></div><?
										} else {
											?><div class="price cool"><?=$arResult['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
										}
									?></div><?

								?></div><?
							}
							// preview text
							if( $arResult['PREVIEW_TEXT']!='' ) {
								?><div class="col col-md-12 previewtext hidden-xs hidden-sm"><?
									?><?=$arResult['PREVIEW_TEXT']?><?
									if($tabDescription){
										?><br /><a class="moretext" href="#tabs"><?=GetMessage('RS.MONOPOLY.MORE')?></a><?
									}
								?></div><?
							}
							// compare
							?><div class="col col-md-12 compare"><?
								if($arParams['DISPLAY_COMPARE']=='Y'){
									?><a class="js-compare" href="<?=$arResult['COMPARE_URL']?>"><span><?=GetMessage('RS.MONOPOLY.COMPARE')?></span><span class="count"></span></a><?
								}
							?></div><?
							// properties
							if( is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES'])>0 ) {
								?><div class="col col-md-12 proptable hidden-xs hidden-sm"><?
									?><table><?
										?><tbody><?
											$cnt = 0;
											foreach($arResult['DISPLAY_PROPERTIES'] as $code => $arProp) {
												$cnt++;
												?><tr class="prop_<?=$code?>"><?
													?><td class="name"><span><?=$arProp['NAME']?></span></td><?
													?><td class="val"><span><?
													if(is_array($arProp['DISPLAY_VALUE']))
														echo implode(' / ',$arProp['DISPLAY_VALUE']);
													else
														echo $arProp['DISPLAY_VALUE'];
													?></span></td><?
												?></tr><?
												if($cnt>4) { break; }
											}
										?></tbody><?
									?></table><?
									?><br /><a class="moreprops" href="#tabs"><?=GetMessage('RS.MONOPOLY.MORE_PROPS')?></a><?
								?></div><?
							}
						?></div><?
					?></div><?
				?></div><?

			?></div><?
			?><div class="col col-md-3"><?
				?><div class="buyblock"><?
					?><div class="row"><?
						if( $arParams['RSMONOPOLY_PROP_QUANTITY']=='Y' ) {
							?><div class="col col-md-12 stores"><?
								?><?=GetMessage('RS.MONOPOLY.QUANTITY')?>:<?
								if( IntVal($arResult['PROPERTIES'][$arParams['RSMONOPOLY_PROP_QUANTITY']]['VALUE'])<1 ) {
									?><span class="empty"> <?=GetMessage('RS.MONOPOLY.QUANTITY_EMPTY')?></span><?
								} else {
									?><span class="isset"> <?=GetMessage('RS.MONOPOLY.QUANTITY_ISSET')?></span><?
								}
							?></div><?
						}
						?><div class="col col-md-12 buybtns"><?
							$name = '['.$arResult['ID'].'] '.$arResult['NAME'];
							?><a class="fancyajax fancybox.ajax btn btn-primary" <?
								?>href="<?=SITE_DIR?>forms/buy/" <?
								?>data-insertdata='{"RS_EXT_FIELD_0":"<?=CUtil::JSEscape($name)?>"}' <?
								?>title="<?=GetMessage('RS.MONOPOLY.BUY_BTN_TITLE')?>" <?
								?>><?=GetMessage('RS.MONOPOLY.BUY_BTN')?></a><?
							?><a class="fancyajax fancybox.ajax btn btn-default" <?
								?>href="<?=SITE_DIR?>forms/ask_us/" <?
								?>data-insertdata='{"RS_EXT_FIELD_0":"<?=CUtil::JSEscape($name)?>"}' <?
								?>title="<?=GetMessage('RS.MONOPOLY.ASK_US_TITLE')?>" <?
								?>><?=GetMessage('RS.MONOPOLY.ASK_US')?></a><?
						?></div><?
						?><div class="col col-md-12 delivery"><?
							?><?$APPLICATION->IncludeFile(SITE_DIR."include_areas/catalog_delivery.php",array(),array("MODE"=>"html","HIDE_ICONS"=>"Y"));?><?
						?></div><?
						?><div class="col col-md-12 yashare"><?
							?><span><?=GetMessage("RS.MONOPOLY.YASHARE")?>:</span><?
							?><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook,twitter,gplus"></div><?
						?></div><?
					?></div><?
				?></div><?
			?></div><?
		?></div><?
		?><div class="row part2"><?
			?><div class="col col-md-12"><?
				?><a name="tabs"></a><?
				?><div class="tabs"><?
					?><ul class="nav nav-tabs"><?
						if($tabDescription) {
							?><li><a class="detailtext" href="#description" data-toggle="tab"><?=GetMessage('RS.MONOPOLY.DESCRIPTION')?></a></li><?
						}
						if($tabProperties) {
							?><li><a class="properties" href="#properties" data-toggle="tab"><?=GetMessage('RS.MONOPOLY.PROPERTIES')?></a></li><?
						}
					?></ul><?
					?><div class="tab-content"><?
						if($tabDescription) {
							?><div class="tab-pane fade" id="description"><?=$arResult['DETAIL_TEXT']?></div><?
						}
						if($tabProperties) {
							?><div class="tab-pane fade" id="properties"><?
								?><div class="row proptable"><?
									?><div class="col col-md-7"><?
										?><table><?
											?><tbody><?
												foreach($arResult['DISPLAY_PROPERTIES'] as $code => $arProp) {
													?><tr class="prop_<?=$code?>"><?
														?><td class="name"><span><?=$arProp['NAME']?></span></td><?
														?><td class="val"><span><?
															if(is_array($arProp['DISPLAY_VALUE']))
																echo implode(' / ',$arProp['DISPLAY_VALUE']);
															else
																echo $arProp['DISPLAY_VALUE'];
														?></span></td><?
													?></tr><?
												}
											?></tbody><?
										?></table><?
									?></div><?
								?></div><?
							?></div><?
						}
					?></div><?
				?></div><?
			?></div><?
		?></div><?

	?></div><?
?></div><?
?><script>
if($('.js-brcrtitle').length>0 && $('.js-detail').find('.brcrtitle').length>0) {
	$('.js-detail').find('.brcrtitle').find('.brcr').html( $('.js-brcrtitle').html() );
	$('.js-detail').find('.brcrtitle').find('.ttl').html( $('.js-ttl').html() );
	$('html').addClass('detailprodpage');
}
</script><?