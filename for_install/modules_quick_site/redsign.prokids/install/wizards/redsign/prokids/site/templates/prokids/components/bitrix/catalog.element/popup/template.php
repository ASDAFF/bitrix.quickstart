<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$HAVE_OFFERS = (is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0) ? true : false;
if($HAVE_OFFERS) { $PRODUCT = &$arResult['OFFERS'][0]; } else { $PRODUCT = &$arResult; }
?><div class="js-element js-elementid<?=$arResult['ID']?> <?if($HAVE_OFFERS):?>offers<?else:?>simple<?endif;?> elementpopup<?
	if( isset($arResult['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo ' da2'; }
	if( isset($arResult['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }
	?> propvision1" data-elementid="<?=$arResult['ID']?>"<?
	?> data-elementname="<?=CUtil::JSEscape($arResult['NAME'])?>"><?
	?><i class="icon da2qb"></i><?
	?><div class="elementpopupinner clearfix"><?
		?><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><i class="icon da2qb"></i></a><?
		// -- LEFT BLOCK
		?><div class="block left"><?
			?><div class="ppadding"><?
				?><div class="name"><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=$arResult['NAME']?></a></div><?
				?><div class="pic"><?
					// PICTURE
					?><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?
						if(isset($arResult['FIRST_PIC']))
						{
							?><img src="<?=$arResult['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arResult['FIRST_PIC']['ALT']?>" title="<?=$arResult['FIRST_PIC']['TITLE']?>" /><?
						} else {
							?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arResult['NAME']?>" alt="<?=$arResult['NAME']?>" /><?
						}
					?></a><?
					// TIMERS
					// TODO: timers
				?></div><?
			?></div><?
			// PRICES
			if(is_array($arResult['CAT_PRICES']) && count($arResult['CAT_PRICES'])>1)
			{
				?><div class="prices scrollp vertical"><?
					$cnt = 0;
					$pricesHTML = '';
					foreach($arResult['CAT_PRICES'] as $PRICE_CODE => $arResPrice)
					{
						$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
						if(!$arResult['CAT_PRICES'][$PRICE_CODE]['CAN_VIEW'])
							continue;
						$pricesHTML.= '<tr class="';
						if( ($cnt+1)%2==0 )
						{
							$pricesHTML.= 'even';
						} else {
							$pricesHTML.= 'odd';
						}
						$pricesHTML.= ' scrollitem">';
						$pricesHTML.= '<td class="nowrap">'.$arResPrice['TITLE'].'</td>';
						$pricesHTML.= '<td class="nowrap"><span class="price price_pdv_'.$PRICE_CODE.'">'.(isset($arPrice["PRINT_DISCOUNT_VALUE"]) ? $arPrice["PRINT_DISCOUNT_VALUE"] : '&mdash;' ).'</span></td>';
						$pricesHTML.= '</tr>';
						$cnt++;
					}
					?><a rel="nofollow" class="scrollbtn prev" href="#"><i class="icon pngicons"></i></a><?
					?><div class="prs_jscrollpane scroll vertical vertical-only" id="prs_scroll_<?=$arResult['ID']?>" style="height:<?if($cnt>2):?>102<?else:?>68<?endif;?>px;"><?
						?><table class="pricestable scrollinner"><?
							?><tbody><?
								?><?=$pricesHTML?><?
							?></tbody><?
						?></table><?
					?></div><?
					?><a rel="nofollow" class="scrollbtn next" href="#"><i class="icon pngicons"></i></a><?
				?></div><?
			} elseif(is_array($arResult["CAT_PRICES"]) && count($arResult["CAT_PRICES"])==1) {
				?><div class="ppadding soloprice"><?
					foreach($arResult['CAT_PRICES'] as $PRICE_CODE => $arResPrice)
					{
						if(!$arResult['CAT_PRICES'][$PRICE_CODE]['CAN_VIEW'])
							continue;
						$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
						?><span class="price<?if( $arPrice['DISCOUNT_DIFF']>0 ):?> new<?endif;?> gen price_pdv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_VALUE']?></span><?
						if( $arPrice['DISCOUNT_DIFF']>0 )
						{
							?><span class="price old price_pv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_VALUE']?></span><?
							?><span class="discount price_pd_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_DIFF']?></span><?
						}
					}
				?></div><?
			}
		?></div><?
		// -- RIGHT BLOCK
		?><div class="block right"><?
			?><div class="ppadding"><?
				?><div class="propanddesc"><?
					// ARTICLE
					if( isset($PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']) || isset($arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']) )
					{
						?><div class="article"><?=GetMessage('ARTICLE')?>: <span class="offer_article" <?
							?>data-prodarticle="<?=( isset($arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']) ? $arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE'] : '' )?>"><?
							?><?=( isset($PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']) ? $PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE'] : $arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE'] )?><?
						?></span></div><?
					} else {
						?><div class="article" style="display:none;"><?=GetMessage('ARTICLE')?>: <span class="offer_article"></span></div><?
					}
					// PROPERTIES
					if(is_array($arResult['OFFERS_EXT']['PROPERTIES']) && count($arResult['OFFERS_EXT']['PROPERTIES'])>0)
					{
						?><div class="properties"><?
							foreach($arResult['OFFERS_EXT']['PROPERTIES'] as $propCode => $arProperty)
							{
								$isColor = false;
								?><div class="offer_prop prop_<?=$propCode?> closed<?
									if(is_array($arParams['PROPS_ATTRIBUTES_COLOR']) && in_array($propCode,$arParams['PROPS_ATTRIBUTES_COLOR']))
									{
										$isColor = true;
										?> color<?
									}
									?>" data-code="<?=$propCode?>">
									<span class="offer_prop-name"><?=$arResult['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </span><?
									?><div class="div_select"><?
										?><div class="div_options"><?
										$firstVal = false;
										foreach($arProperty as $value => $arValue)
										{
											?><div class="div_option<?
												if($arValue['FIRST_OFFER'] == 'Y'):?> selected<?
												elseif($arValue['DISABLED_FOR_FIRST'] == 'Y'):?> disabled<?
												endif;?>" data-value="<?=htmlspecialcharsbx($arValue['VALUE'])?>"><?
												if($isColor)
												{
													?><span style="background-image:url('<?=$arValue['PICT']['SRC']?>');" title="<?=$arValue['VALUE']?>"></span> &nbsp; <?=$arValue['VALUE']?><?
												} else {
													?><span><?=$arValue['VALUE']?></span><?
												}
											?></div><?
											if($arValue['FIRST_OFFER'] == 'Y')
											{
												$firstVal = $arValue;
											}
										}
										?></div><?
										if(is_array($firstVal))
										{
											?><div class="div_selected"><?
												if($isColor)
												{
													?><span style="background-image:url('<?=$firstVal['PICT']['SRC']?>');" title="<?=$firstVal['VALUE']?>"></span><?
												} else {
													?><span><?=$firstVal['VALUE']?></span><?
												}
												?><i class="icon pngicons"></i><?
											?></div><?
										}
									?></div><?
								?></div><?
							}
						?></div><?
					}
					// DESCRIPTION
					if(isset($arResult['PREVIEW_TEXT']) && $arResult['PREVIEW_TEXT']!='')
					{
						?><div class="description"><div class="text"><?=$arResult['PREVIEW_TEXT']?></div><a class="more" href="<?=$arResult['DETAIL_PAGE_URL']?>" title="<?=$arResult['NAME']?>"><?=GetMessage('GOPRO.MORE')?></a></div><?
					}
				?></div><?
				// ADD2BASKET
				?><noindex><div class="buy"><?
					?><form class="add2basketform js-buyform<?=$arResult['ID']?> js-synchro<?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?> clearfix" name="add2basketform"><?
						?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET"><?
						?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?
						if($arParams['USE_PRODUCT_QUANTITY'])
						{
							?><span class="quantity"><?
								?><a class="minus js-minus">-</a><?
								?><input type="text" class="js-quantity" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
								?><span class="js-measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
								?><a class="plus js-plus">+</a><?
							?></span><?
						}
						?><a rel="nofollow" class="submit add2basket" href="#" title="<?=GetMessage('ADD2BASKET')?>"><?=GetMessage('CT_BCE_CATALOG_ADD')?></a><?
						?><a rel="nofollow" class="inbasket" href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage('INBASKET_TITLE')?>"><?=GetMessage('INBASKET')?></a><?
						?><input type="submit" name="submit" class="none2" value="" /><?
					?></form><?
				?></div></noindex><?
				// COMPARE
				if($arParams['USE_COMPARE']=='Y')
				{
					?><div class="compare"><?
						?><a rel="nofollow" class="add2compare" href="<?=$arResult['COMPARE_URL']?>"><i class="icon pngicons"></i><?=GetMessage('ADD2COMPARE')?></a><?
					?></div><?
				}
				// FAVORITE & SHARE
				?><div class="favorishare clearfix"><?
					if($arParams['USE_FAVORITE']=='Y' || $arParams['USE_SHARE']=='Y')
					{
						// FAVORITE
						if($arParams['USE_FAVORITE']=='Y')
						{
							?><div class="favorite"><?
								?><a rel="nofollow" class="add2favorite" href="#favorite"><i class="icon pngicons"></i><?=GetMessage('FAVORITE')?></a><?
							?></div><?
						}
						// SHARE
						if($arParams['USE_SHARE']=='Y')
						{
							?><div class="share"><?
								?><span class="b-share"><a class="fancyajax fancybox.ajax email2friend b-share__handle b-share__link b-share-btn__vkontakte" href="<?=SITE_DIR?>email2friend/?link=<?=CUtil::JSUrlEscape('http://'.$_SERVER['HTTP_HOST'].$arResult['DETAIL_PAGE_URL'])?>" title="<?=GetMessage('EMAIL2FRIEND')?>"><i class="b-share-icon icon pngicons"></i></a></span><?
								?><span id="detailYaShare_<?=$arResult['ID']?>"></span><?
								?><script type="text/javascript">
								new Ya.share({
									link: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arResult['DETAIL_PAGE_URL']?>',
									title: '<?=CUtil::JSEscape($arResult['TITLE'])?>',
									<?if(isset($arResult['PREVIEW_TEXT']) && $arResult['PREVIEW_TEXT']!=''):?>description: '<?=CUtil::JSEscape($arResult['PREVIEW_TEXT'])?>',<?endif;?>
									<?if(isset($arResult['FIRST_PIC'])):?>image: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arResult['FIRST_PIC']['RESIZE']['src']?>',<?endif;?>
									element: 'detailYaShare_<?=$arResult['ID']?>',
									elementStyle: {
										'type': 'none',
										'border': false,
										'text': '<?=GetMessage('YSHARE')?>',
										'quickServices': ['vkontakte','facebook','twitter']
									}
								});
								</script><?
							?></div><?
						}
					}
				?></div><?
			?></div><?
		?></div><?
	?></div><?
?></div>