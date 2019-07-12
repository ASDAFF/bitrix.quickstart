<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?><form name="<?=$arResult['FILTER_NAME'].'_form'?>" action="<?=$arResult['FORM_ACTION']?>" method="get" class="smartfilter" onsubmit="return RSGoPro_FilterOnSubmitForm();"><?
	foreach($arResult['HIDDEN'] as $arItem)
	{
		?><input <?
			?>type="hidden" <?
			?>name="<?=$arItem['CONTROL_NAME']?>" <?
			?>id="<?=$arItem['CONTROL_ID']?>" <?
			?>value="<?=$arItem['HTML_VALUE']?>" <?
		?>/> <?
	}
	?><div class="around_filtren"><?
		?><div class="filtren clearfix<?if($arParams['FILTER_FIXED']=='Y'):?> filterfixed<?endif;?><?if($arParams['FILTER_USE_AJAX']=='Y'):?> ajaxfilter<?endif;?>"><?
			?><div class="title"><?
				?><a class="shhi" href="#"><span class="show"><?=GetMessage('CT_BCSF_FILTER_TITLE_SHOW')?></span><span class="hide"><?=GetMessage('CT_BCSF_FILTER_TITLE_HIDE')?></span></a><?
				if($arParams['USE_COMPARE']=='Y') {
					?><span class="filtercompare"><?=GetMessage('FILTER_COMPARE')?>: <a href="#"></a></span><?
				}
			?></div><?
			?><div class="body"><?
				?><ul class="clearfix"><?
				foreach($arResult['ITEMS'] as $arItem)
				{
					// color
					if(in_array($arItem['CODE'],$arParams['PROPS_FILTER_COLORS']) || in_array($arItem['CODE'],$arParams['PROPS_SKU_FILTER_COLORS']))
					{
						$IS_COLOR = true;
					} else {
						$IS_COLOR = false;
					}
					// scroll
					if(in_array($arItem['CODE'],$arParams['FILTER_PROP_SCROLL']) || in_array($arItem['CODE'],$arParams['FILTER_SKU_PROP_SCROLL']))
					{
						$IS_SCROLABLE = true;
					} else {
						$IS_SCROLABLE = false;
					}
					// search
					if(in_array($arItem['CODE'],$arParams['FILTER_PROP_SEARCH']) || in_array($arItem['CODE'],$arParams['FILTER_SKU_PROP_SEARCH']))
					{
						$IS_SEARCHABLE = true;
					} else {
						$IS_SEARCHABLE = false;
					}
					if($arItem['PROPERTY_TYPE'] == 'N' || isset($arItem['PRICE']))
					{
						if( IntVal($arItem['VALUES']['MIN']['VALUE'])<1 && IntVal($arItem['VALUES']['MAX']['VALUE'])<1 )
						{
							continue;
						}
						if(!in_array($arItem['CODE'],$arParams['FILTER_PRICE_GROUPED']))
						{
							?><li class="lvl1 number<?if(in_array($arItem['CODE'],$arParams['PRICE_CODE'])):?> price<?endif;?>" data-propid="<?=$arItem['ID']?>" data-propcode="<?=$arItem['CODE']?>"><?
								?><a href="#" class="showchild"><i class="icon pngicons"></i><?=$arItem['NAME']?></a><?
								?><ul><?
									?><li class="lvl2"><?
										?><div class="inputs"><?
											?><span class="from"><?=GetMessage('CT_BCSF_FILTER_FROM')?></span><?
											?><input <?
												?>class="min" <?
												?>type="text" <?
												?>name="<?=$arItem['VALUES']['MIN']['CONTROL_NAME']?>" <?
												?>id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>" <?
												?>value="<?=($arItem['VALUES']['MIN']['HTML_VALUE']!='')?$arItem['VALUES']['MIN']['HTML_VALUE']:(float) $arItem['VALUES']['MIN']['VALUE']?>" <?
												?>data-startvalue="<?=(float)$arItem['VALUES']['MIN']['VALUE']?>" <?
												?>size="9" <?
											?>/><?
											?><span class="separator"></span><?
											?><span class="to"><?=GetMessage("CT_BCSF_FILTER_TO")?></span><?
											?><input <?
												?>class="max" <?
												?>type="text" <?
												?>name="<?=$arItem['VALUES']['MAX']['CONTROL_NAME']?>" <?
												?>id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>" <?
												?>value="<?=($arItem['VALUES']['MAX']['HTML_VALUE']!='')?$arItem['VALUES']['MAX']['HTML_VALUE']:(float)$arItem['VALUES']['MAX']['VALUE']?>" <?
												?>data-startvalue="<?=(float)$arItem['VALUES']['MAX']['VALUE']?>" <?
												?>size="9" <?
											?>/><?
										?></div><?
										?><div class="aroundslider"><?
											?><div id="slider-<?=$arItem['ID']?>" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><?
												?><a class="pngicons ui-slider-handle ui-state-default ui-corner-all ui-cursor-left" href="#"></a><?
												?><a class="pngicons ui-slider-handle ui-state-default ui-corner-all ui-cursor-right" href="#"></a><?
												?><div class="ui-slider-range ui-widget-header"></div><?
											?></div><?
										?></div><?
									?></li><?
								?></ul><?
							?></li><?
							$arValueMax = explode('.', (float) $arItem['VALUES']['MAX']['VALUE']);
							$arValueMin = explode('.', (float) $arItem['VALUES']['MIN']['VALUE']);
							$step = pow(0.1, strlen(max($arValueMax[1], $arValueMin[1])));
							?><script>
							$(document).ready(function(){
								if(RSDevFunc_PHONETABLET)
								{
									$('#slider-<?=$arItem['ID']?>').parent().remove();
								} else {
									$('#slider-<?=$arItem['ID']?>').slider({
										range:true,
										min: <?=($arItem['VALUES']['MIN']['VALUE']) ? $arItem['VALUES']['MIN']['VALUE'] : 0;?>,
										max: <?=($arItem['VALUES']['MAX']['VALUE']) ? $arItem['VALUES']['MAX']['VALUE'] : 0;?>,
										step: <?=$step?>,
										values: [<?=($arItem['VALUES']['MIN']['HTML_VALUE']) ? $arItem['VALUES']['MIN']['HTML_VALUE'] : $arItem['VALUES']['MIN']['VALUE']?>,<?=($arItem['VALUES']['MAX']['HTML_VALUE']) ? $arItem['VALUES']['MAX']['HTML_VALUE'] : $arItem['VALUES']['MAX']['VALUE']?>],
										slide: function(event, ui){
											var slider = $(this);
											setTimeout(function(){
												var parent = slider.parents('.lvl2');
												parent.find('.min').val(slider.slider('values',0));
												parent.find('.max').val(slider.slider('values',1));
												smartFilter.keyup(BX('<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>'));
												parent.find('.min, .max').each(function(i){
													$(this).val( RSDevFunc_NumberFormat( $(this).val() ) );
												});
											},50);
										}
									});
								}
							});
							</script><?
						} else {
							?><li class="lvl1 number pricegroup" data-propid="<?=$arItem['ID']?>" data-propcode="<?=$arItem['CODE']?>"><?
								?><a href="#" class="showchild"><i class="icon pngicons"></i><?=$arItem['NAME']?></a><?
								?><ul class="clearfix"><?
									foreach($arItem['GROUP_VALUES']['FOR_TEMPLATE'] as $keyD => $groupValue)
									{
										?><li class="f_li lvl2<?if($groupValue['SELECTED'] == 'Y'):?> f_li_checked<?endif;?>"><?
											?><input <?
												?>class="lvl2_checkbox" <?
												?>type="checkbox" <?
												?>value="Y" <?
												?>name="<?=$arItem['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'][$keyD]['CONTROL_NAME']?>" <?
												?>id="<?=$arItem['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'][$keyD]['CONTROL_ID']?>" <?
												if($groupValue['SELECTED'] == 'Y'):?> checked="checked"<?endif;
												?>onclick="RSGoPro_priceGoupClick()" <?
												?>/><?
											?><label for="<?=$arItem['GROUP_VALUES']['PRICE_GROUP_DIAPAZONS'][$keyD]['CONTROL_ID']?>"<?if($groupValue['SELECTED'] == 'Y'):?> class="label_checked"<?endif;?>><span><?=$groupValue['NAME1']?></span></label><?
										?></li><?
									}
								?></ul><?
							?></li><?
						}
					} elseif(!empty($arItem['VALUES'])) {
						if($IS_SCROLABLE && count($arItem['VALUES'])>$arParams['FILTER_PROP_SCROLL_CNT'])
						{
							$IS_SCROLABLE = true;
						} else {
							$IS_SCROLABLE = false;
						}
						?><li class="lvl1<?if($IS_COLOR):?> color<?endif;?><?if($IS_SCROLABLE && !$IS_COLOR):?> scrolable<?endif;?><?if($IS_SEARCHABLE && !$IS_COLOR):?> searcheble<?endif;?>" data-propid="<?=$arItem['ID']?>" data-propcode="<?=$arItem['CODE']?>"><?
							?><a href="#" class="showchild"><i class="icon pngicons"></i><?=$arItem['NAME']?></a><?
							?><ul><?
								// search
								if($IS_SEARCHABLE && !$IS_COLOR)
								{
									?><div class="around_f_search"><input type="text" class="f_search" name="f_search" id="f_search" value="" placeholder="<?=GetMessage('FILTR_SEARHC')?>"></div><?
								}
								// scroll
								if($IS_SCROLABLE && !$IS_COLOR)
								{
									?><div class="f_jscrollpane" id="f_scroll_<?=$arItem['ID']?>"><?
								}
								foreach($arItem['VALUES'] as $val => $ar)
								{
									?><li class="lvl2<? if($ar['DISABLED']){?> lvl2_disabled<?}?>"><?if($IS_COLOR):?><span><?endif;?><input <?
										?>type="checkbox" <?
										?>value="<?=$ar['HTML_VALUE']?>" <?
										?>name="<?=$ar['CONTROL_NAME']?>" <?
										?>id="<?=$ar['CONTROL_ID']?>" <?
										?><?=$ar['CHECKED']? 'checked="checked"': ''?> <?
										?>onclick="smartFilter.click(this)" <?
									?>/><?if($IS_COLOR):?></span><?endif;?><label for="<?=$ar['CONTROL_ID']?>"><?
									if($IS_COLOR)
									{
										?><span style="background-image:url('<?=$ar['PICT']['SRC']?>');" title="<?=$ar['VALUE'];?>"></span><?
									} else {
										?><?=$ar['VALUE'];?><?
									}
									?></label></li><?
								}
								// scroll
								if($IS_SCROLABLE && !$IS_COLOR)
								{
									?></div><?
								}
							?></ul><?
						?></li><?
					}
				}
				?></ul><?
				
				?><div class="buttons"><?
					?><a rel="nofollow" class="btn1 set_filter" href="#"><?=GetMessage('CT_BCSF_SET_FILTER')?></a><?
					?><span class="separator"></span><?
					?><a rel="nofollow" class="btn3 del_filter" href="#"><?=GetMessage('CT_BCSF_DEL_FILTER')?></a><?
					?><input class="nonep" type="submit" id="set_filter" name="set_filter" value="<?=GetMessage('CT_BCSF_SET_FILTER')?>" /><?
					?><input class="nonep" type="submit" id="del_filter" name="del_filter" value="<?=GetMessage('CT_BCSF_DEL_FILTER')?>" /><?
				?></div><?
			?></div><?
			
			?><div class="modef" id="modef" <?if(!isset($arResult['ELEMENT_COUNT'])) echo 'style="display:none"';?>><?
				?><span class="arrow">&nbsp;</span><?
				?><span class="data"><?
					?><?=GetMessage('CT_BCSF_FILTER_COUNT', array('#ELEMENT_COUNT#' => '<span id="modef_num">'.intval($arResult['ELEMENT_COUNT']).'</span>'));?><?
					?> <a href="<?=$arResult['FILTER_URL']?>"><?=GetMessage('CT_BCSF_FILTER_SHOW')?></a><?
				?></span><?
			?></div><?
		?></div><?
	?></div><?
?></form><?
?><script>
	var smartFilter = new JCSmartFilter('<?=CUtil::JSEscape($arResult['FORM_ACTION'])?>');
</script>