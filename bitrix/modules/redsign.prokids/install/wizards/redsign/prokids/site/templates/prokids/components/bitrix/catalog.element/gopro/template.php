<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$HAVE_OFFERS = (is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0) ? true : false;
if($HAVE_OFFERS) { $PRODUCT = &$arResult['OFFERS'][0]; } else { $PRODUCT = &$arResult; }
?><div class="elementdetail js-element js-elementid<?=$arResult['ID']?> <?if($HAVE_OFFERS):?>offers<?else:?>simple<?endif;?><?
	if( isset($arResult['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo ' da2'; }
	if( isset($arResult['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }
	?> propvision1 clearfix" data-elementid="<?=$arResult['ID']?>" <?
	?> data-elementname="<?=CUtil::JSEscape($arResult['NAME'])?>"<?
	?>><i class="icon da2qb"></i><?
	// PICTURES
	?><div class="pictures changegenimage"><?
		?><div class="pic"><?
			if(isset($arResult['FIRST_PIC_DETAIL']['SRC']))
			{
				?><div class="glass"><?
					?><img class="js_picture_glass genimage" src="<?=$arResult['FIRST_PIC_DETAIL']['SRC']?>" alt="<?=$arResult['FIRST_PIC_DETAIL']['RESIZE']['ALT']?>" title="<?=$arResult['FIRST_PIC_DETAIL']['RESIZE']['TITLE']?>" /><?
					?><div class="glass_lupa"></div><?
				?></div><?
			} else {
				?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arResult['NAME']?>" alt="<?=$arResult['NAME']?>" /><?
			}
			// TIMERS
			$arTimers = array();
			if( $arResult['HAVE_DA2']=='Y' ) {
				if( isset($arResult['DAYSARTICLE2']) ) {
					$arTimers[] = $arResult['DAYSARTICLE2'];
				} elseif($HAVE_OFFERS) {
					foreach($arResult['OFFERS'] as $arOffer) {
						if( isset($arOffer['DAYSARTICLE2']) ) {
							$arTimers[] = $arOffer['DAYSARTICLE2'];
						}
					}
				}
			} elseif( $arResult['HAVE_QB']=='Y' ) {
				if( isset($arResult['QUICKBUY']) )
				{
					$arTimers[] = $arResult['QUICKBUY'];
				} elseif($HAVE_OFFERS) {
					foreach($arResult['OFFERS'] as $arOffer)
					{
						if( isset($arOffer['QUICKBUY']) )
						{
							$arTimers[] = $arOffer['QUICKBUY'];
						}
					}
				}
			}
			if( is_array($arTimers) && count($arTimers)>0 ) {
				?><div class="timers"><?
					$have_vis = false;
					foreach($arTimers as $arTimer)
					{
						$KY = 'TIMER';
						if(isset($arTimer['DINAMICA_EX']))
						{
							$KY = 'DINAMICA_EX';
						}
						?><div class="timer <?if(isset($arTimer['DINAMICA_EX'])):?>da2<?else:?>qb<?endif;?> js-timer_id<?=$arTimer['ELEMENT_ID']?> clearfix" style="display:<?
							if( ($arResult['ID']==$arTimer['ELEMENT_ID'] || $PRODUCT['ID']==$arTimer['ELEMENT_ID']) && !$have_vis)
							{
								?>inline-block<?
								$have_vis = true;
							} else {
								?>none<?
							}
							?>;" data-datefrom="<?=$arTimer[$KY]['DATE_FROM']?>"><?
							?><div class="clock"><i class="icon"></i></div><?
							?><div class="intimer clearfix" data-dateto="<?=$arTimer[$KY]['DATE_TO']?>"><?
								if($arTimer[$KY]['DAYS']>0){
									?><div class="val"><div class="value result-day"><?
										echo($arTimer[$KY]['DAYS']>9?$arTimer[$KY]['DAYS']:'0'.$arTimer[$KY]['DAYS'] )
										?></div><div class="podpis"><?=GetMessage('QB_AND_DA2_DAY')?></div></div><?
									?><div class="dvoet">:</div><?
								}
								?><div class="val"><div class="value result-hour"><?
									echo($arTimer[$KY]['HOUR']>9?$arTimer[$KY]['HOUR']:'0'.$arTimer[$KY]['HOUR'] )
									?></div><div class="podpis"><?=GetMessage('QB_AND_DA2_HOUR')?></div></div><?
									?><div class="dvoet">:</div><?
								?><div class="val"><div class="value result-minute"><?
									echo($arTimer[$KY]['MINUTE']>9?$arTimer[$KY]['MINUTE']:'0'.$arTimer[$KY]['MINUTE'] )
									?></div><div class="podpis "><?=GetMessage('QB_AND_DA2_MIN')?></div></div><?
								if($arTimer[$KY]['DAYS']<1){
									?><div class="dvoet">:</div><?
									?><div class="val"><div class="value result-second"><?
										echo($arTimer[$KY]['SECOND']>9?$arTimer[$KY]['SECOND']:'0'.$arTimer[$KY]['SECOND'] )
										?></div><div class="podpis "><?=GetMessage('QB_AND_DA2_SEC')?></div></div><?
								}
								if(isset( $arTimer['DINAMICA_EX']) )
								{
									?><div class="val ml da2"><div class="value"><?
										echo($arTimer[$KY]['PHP_DATA']['persent']>9?$arTimer[$KY]['PHP_DATA']['persent']:'0'.$arTimer[$KY]['PHP_DATA']['persent'] )
										?>%</div><div class="podpis"><?=GetMessage('QB_AND_DA2_PRODANO')?></div></div><?
								} elseif( isset($arTimer['TIMER']) && IntVal($arTimer['QUANTITY'])>0 ) {
									?><div class="val ml qb"><div class="value"><?
										echo($arTimer['QUANTITY']);
										?><?=GetMessage('QB_AND_DA2_SHT')?></div><div class="podpis"><?=GetMessage('QB_AND_DA2_SHT')?></div></div><?
								}
							?></div><?
							if(isset( $arTimer['DINAMICA_EX']) )
							{
								?><div class="clear"></div><div class="progressbar"><div class="progress" style="width:<?=$arTimer[$KY]['PHP_DATA']['persent']?>%;"></div></div><?
							}
						?></div><?
					}
				?></div><?
			}
			// /TIMERS
		?></div><?
		if(isset($arResult['FIRST_PIC_DETAIL']['SRC']))
		{
			?><div class="zoom"><?
				?><i class="icon pngicons"></i><?=GetMessage('ZOOM')?><?
			?></div><?
			?><div class="picslider horizontal scrollp"><?
				?><a rel="nofollow" class="scrollbtn prev page" href="#"><i class="icon pngicons"></i></a><?
				?><a rel="nofollow" class="scrollbtn next page" href="#"><i class="icon pngicons"></i></a><?
				?><div class="d_jscrollpane scroll horizontal-only" id="d_scroll_<?=$arResult['ID']?>"><?
					$imagesCnt = 0;
					$imagesHTML = '';
					$first = false;
					if($HAVE_OFFERS)
					{
						foreach($arResult['OFFERS'] as $arOffer)
						{
							if( is_array($arOffer['DETAIL_PICTURE']['RESIZE']) )
							{
								$imagesHTML.= '<a rel="nofollow" class="changeimage';
								if($arOffer['ID']==$PRODUCT['ID'])
								{
									$imagesHTML.= ' scrollitem';
								}
								$imagesHTML.= ' imgoffer imgofferid'.$arOffer['ID'].'"';
								if($arOffer['ID']==$PRODUCT['ID'])
								{
									$imagesCnt++;
								} else {
									$imagesHTML.= ' style="display:none;"';
								}
								$imagesHTML.= ' href="#">';
									$imagesHTML.= '<img src="'.$arOffer['DETAIL_PICTURE']['RESIZE']['src'].'" ';
										$imagesHTML.= 'alt="'.$arOffer['DETAIL_PICTURE']['ALT'].'" ';
										$imagesHTML.= 'title="'.$arOffer['DETAIL_PICTURE']['TITLE'].'" ';
										$imagesHTML.= 'data-bigimage="'.$arOffer['DETAIL_PICTURE']['SRC'].'" ';
									$imagesHTML.= '/>';
								$imagesHTML.= '</a>';
							}
							if( is_array($arOffer['PROPERTIES'][$arParams['PROP_SKU_MORE_PHOTO']]['VALUE'][0]['RESIZE']) )
							{
								foreach($arOffer['PROPERTIES'][$arParams['PROP_SKU_MORE_PHOTO']]['VALUE'] as $arImage)
								{
									$imagesHTML.= '<a rel="nofollow" class="changeimage ';
									if($arOffer['ID']==$PRODUCT['ID'])
									{
										$imagesHTML.= ' scrollitem';
									}
									$imagesHTML.= ' imgoffer imgofferid'.$arOffer['ID'].'"';
									if($arOffer['ID']==$PRODUCT['ID'])
									{
										$imagesCnt++;
									} else {
										$imagesHTML.= ' style="display:none;"';
									}
									$imagesHTML.= ' href="#">';
										$imagesHTML.= '<img src="'.$arImage['RESIZE']['src'].'" ';
											$imagesHTML.= 'alt="'.$arOffer['NAME'].'" ';
											$imagesHTML.= 'title="'.$arOffer['NAME'].'" ';
											$imagesHTML.= 'data-bigimage="'.$arImage['SRC'].'" ';
										$imagesHTML.= '/>';
									$imagesHTML.= '</a>';
								}
							}
						}
					}
					if( is_array($arResult['DETAIL_PICTURE']['RESIZE']) )
					{
						$imagesHTML.= '<a rel="nofollow" class="changeimage scrollitem" href="#">';
							$imagesHTML.= '<img src="'.$arResult['DETAIL_PICTURE']['RESIZE']['src'].'" ';
								$imagesHTML.= 'alt="'.$arResult['DETAIL_PICTURE']['ALT'].'" ';
								$imagesHTML.= 'title="'.$arResult['DETAIL_PICTURE']['TITLE'].'" ';
								$imagesHTML.= 'data-bigimage="'.$arResult['DETAIL_PICTURE']['SRC'].'" ';
							$imagesHTML.= '/>';
						$imagesHTML.= '</a>';
						$imagesCnt++;
					}
					if( is_array($arResult['PROPERTIES'][$arParams['PROP_MORE_PHOTO']]['VALUE'][0]['RESIZE']) )
					{
						foreach($arResult['PROPERTIES'][$arParams['PROP_MORE_PHOTO']]['VALUE'] as $arImage)
						{
							$imagesHTML.= '<a rel="nofollow" class="changeimage scrollitem" href="#">';
								$imagesHTML.= '<img src="'.$arImage['RESIZE']['src'].'" ';
									$imagesHTML.= 'alt="'.$arResult['NAME'].'" ';
									$imagesHTML.= 'title="'.$arResult['NAME'].'" ';
									$imagesHTML.= 'data-bigimage="'.$arImage['SRC'].'" ';
								$imagesHTML.= '/>';
							$imagesHTML.= '</a>';
							$imagesCnt++;
						}
					}
					?><div class="sliderin scrollinner" style="width:<?=($imagesCnt*112)?>px;"><?=$imagesHTML?></div><?
				?></div><?
			?></div><?
			?><div class="fancyimages noned" title="<?=$arResult['NAME']?>"><?
				?><div class="fancygallery"><?
					?><table class="changegenimage"><?
						?><tbody><?
							?><tr><?
								?><td class="image"><img class="max genimage" src="<?=$arResult['FIRST_PIC']['SRC']?>" alt="" title="" /></td><?
								?><td class="slider"><?
									?><div class="picslider scrollp vertical"><?
										?><a rel="nofollow" class="scrollbtn prev pop" href="#"><i class="icon pngicons"></i></a><?
										?><div class="popd_jscrollpane scroll vertical-only max" id="d_scroll_popup_<?=$arResult['ID']?>"><?
											?><div class="scrollinner"><?
												?><?=$imagesHTML?><?
											?></div><?
										?></div><?
										?><a rel="nofollow" class="scrollbtn next pop" href="#"><i class="icon pngicons"></i></a><?
									?></div><?
								?></td><?
							?></tr><?
						?></tbody><?
					?></table><?
				?></div><?
			?></div><?
		}
	?></div><?
	// INFO
	?><div class="info"><?
		// ARTICLE && STORES
		?><div class="articleandstores clearfix"><?
			// ARTICLE
			if( isset($PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']) || isset($arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']) ) {
				?><div class="article"><?
					if( $PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']!='' || $arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']!='' ) {
						?><?=GetMessage('ARTICLE')?>: <span class="offer_article" <?
							?>data-prodarticle="<?=( isset($arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']) ? $arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE'] : '' )?>"><?
							?><?=( isset($PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']) ? $PRODUCT['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE'] : $arResult['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE'] )?><?
						?></span><?
					}
				?></div><?
			} else {
				?><div class="article" style="display:none;"><?=GetMessage('ARTICLE')?>: <span class="offer_article"></span></div><?
			}
			// STORES
			if($arParams['USE_STORE']=='Y') {
				?><?$APPLICATION->IncludeComponent(
					'bitrix:catalog.store.amount',
					( $arParams['STORES_TEMPLATE']!='' ? $arParams['STORES_TEMPLATE'] : 'gopro' ),
					array(
						"ELEMENT_ID" => $arResult["ID"],
						"STORE_PATH" => $arParams["STORE_PATH"],
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000",
						"MAIN_TITLE" => $arParams["MAIN_TITLE"],
						"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
						"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
						"USE_MIN_AMOUNT" => "N",
						"GOPRO_USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
						"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
						"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
						"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
						"USER_FIELDS" => $arParams['USER_FIELDS'],
						"FIELDS" => $arParams['FIELDS'],
						// gopro
						'DATA_QUANTITY' => $arResult['DATA_QUANTITY'],
						'FIRST_ELEMENT_ID' => $PRODUCT['ID'],
					),
					$component,
					array('HIDE_ICONS'=>'Y')
				);?><?
			}
		?></div><?
		// PRICES
		if(is_array($arResult["CAT_PRICES"]) && count($arResult["CAT_PRICES"])>1)
		{
			?><div class="prices horizontal scrollp"><?
				$cnt = 0;
				$pricesHTML_head = '';
				$pricesHTML_old_price = '';
				$pricesHTML_price = '';
				foreach($arResult['CAT_PRICES'] as $PRICE_CODE => $arResPrice)
				{
					if(!$arResult['CAT_PRICES'][$PRICE_CODE]['CAN_VIEW'])
						continue;
					$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
					// header
					$pricesHTML_head.= '<th class="nowrap">'.$arResPrice['TITLE'].'</th>';
					// old price
					$pricesHTML_old_price.= '<td class="nowrap"><span class="price old price_pv_'.$PRICE_CODE.'">';
					if( $arPrice['DISCOUNT_DIFF']>0 )
					{
						$pricesHTML_old_price.= $arPrice['PRINT_VALUE'];
					} else {
						$pricesHTML_old_price.= '';
					}
					$pricesHTML_old_price.= '</span></td>';
					// price
					$pricesHTML_price.= '<td class="nowrap"><span class="price';
					if( $arPrice['DISCOUNT_DIFF']>0 )
					{
						$pricesHTML_price.= ' new';
					}
					$pricesHTML_price.= ' price_pdv_'.$PRICE_CODE.'">'.$arPrice["PRINT_DISCOUNT_VALUE"].'</span></td>';
					$cnt++;
				}
				?><a rel="nofollow" class="scrollbtn prev" href="#"><span></span><i class="icon pngicons"></i></a><?
				?><a rel="nofollow" class="scrollbtn next" href="#"><span></span><i class="icon pngicons"></i></a><?
				?><div class="prs_jscrollpane scroll horizontal-only" id="prs_scroll_<?=$arResult['ID']?>"><?
					?><div class="scrollinner" style="width:<?=($cnt*160)?>px;"><?
						?><table class="pricestable scrollitem"><?
							?><thead><?
								?><tr><?
									?><?=$pricesHTML_head?><?
								?></tr><?
							?></thead><?
							?><tbody><?
								?><tr><?
									?><?=$pricesHTML_old_price?><?
								?></tr><?
								?><tr><?
									?><?=$pricesHTML_price?><?
								?></tr><?
							?></tbody><?
						?></table><?
					?></div><?
				?></div><?
			?></div><?
		} elseif(is_array($arResult["CAT_PRICES"]) && count($arResult["CAT_PRICES"])==1) {
			?><div class="soloprice"><?
				foreach($arResult['CAT_PRICES'] as $PRICE_CODE => $arResPrice)
				{
					if(!$arResult['CAT_PRICES'][$PRICE_CODE]['CAN_VIEW'])
						continue;
					$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
					?><table><?
						?><tr><?
							?><td><div class="line"><span class="name"><?=GetMessage('SOLOPRICE_PRICE')?><span></div></td><td class="nowrap"><span class="price<?if( $arPrice['DISCOUNT_DIFF']>0 ):?> new<?endif;?> gen price_pdv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_VALUE']?></span></td><?
						?></tr><?
						if( $arPrice['DISCOUNT_DIFF']>0 )
						{
							?><tr class="hideifzero"><?
								?><td><div class="line"><span class="name"><?=GetMessage('SOLOPRICE_PRICE_OLD')?><span></div></td><td class="nowrap"><span class="price old price_pv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_VALUE']?></span></td><?
							?></tr><?
							?><tr class="hideifzero"><?
								?><td><div class="line"><span class="name"><?=GetMessage('SOLOPRICE_DISCOUNT')?><span></div></td><td class="nowrap"><span class="discount price_pd_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_DIFF']?></span></td><?
							?></tr><?
						}
					?></table><?
				}
			?></div><?
		}
		// PROPERTIES
		if(is_array($arResult['OFFERS_EXT']['PROPERTIES']) && count($arResult['OFFERS_EXT']['PROPERTIES'])>0)
		{
			?><div class="properties clearfix"><?
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
		// ADD2BASKET
		?><noindex><div class="buy clearfix"><?
			?><form class="add2basketform js-buyform<?=$arResult['ID']?> js-synchro<?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?> clearfix" name="add2basketform"><?
				?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET"><?
				?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?
				if($arParams['USE_PRODUCT_QUANTITY'])
				{
					?><span class="quantitytitle"><?=GetMessage('CT_BCE_QUANTITY')?>&nbsp; &nbsp;</span><?
					?><span class="quantity"><?
						?><a class="minus js-minus">-</a><?
						?><input type="text" class="js-quantity" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
						if($arParams['OFF_MEASURE_RATION']!='Y') {
							?><span class="js-measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
						}
						?><a class="plus js-plus">+</a><?
					?></span><?
				}
				?><a rel="nofollow" class="submit add2basket" href="#" title="<?=GetMessage('ADD2BASKET')?>"><i class="icon pngicons"></i><?=GetMessage('CT_BCE_CATALOG_ADD')?></a><?
				?><a rel="nofollow" class="inbasket" href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage('INBASKET_TITLE')?>"><i class="icon pngicons"></i><?=GetMessage('INBASKET')?></a><?
				?><a rel="nofollow" class="go2basket" href="<?=$arParams['BASKET_URL']?>"><?=GetMessage('INBASKET_TITLE')?></a><?
				?><a rel="nofollow" class="buy1click detail fancyajax fancybox.ajax" href="<?=SITE_DIR?>buy1click/" title="<?=GetMessage('BUY1CLICK')?>"><?=GetMessage('BUY1CLICK')?></a><?
				/*
				if($PRODUCT['CATALOG_SUBSCRIPTION']=='Y')
				{
					?><a rel="nofollow" class="btn btn1 product2subscribe" href="#" title="<?=GetMessage('SUBSCRIBE_PROD_TITILE')?>"><?=GetMessage('SUBSCRIBE_PROD')?></a><?
				}
				*/
				?><input type="submit" name="submit" class="noned" value="" /><?
			?></form><?
		?></div></noindex><?
		// COMPARE & FAVORITE & CHEAPER
		?><div class="threeblock clearfix"><?
			// COMPARE
			if($arParams['USE_COMPARE']=='Y')
			{
				?><div class="compare"><?
					?><a rel="nofollow" class="add2compare" href="<?=$arResult['COMPARE_URL']?>"><i class="icon pngicons"></i><?=GetMessage('ADD2COMPARE')?></a><?
				?></div><?
			}
			// FAVORITE & CHEAPER
			if($arParams['USE_FAVORITE']=='Y' || $arParams['USE_CHEAPER']=='Y')
			{
				?><div class="favoriteandcheaper"><?
					// FAVORITE
					if($arParams['USE_FAVORITE']=='Y')
					{
						?><div class="favorite"><?
							?><a rel="nofollow" class="add2favorite" href="#favorite"><i class="icon pngicons"></i><?=GetMessage('FAVORITE')?></a><?
						?></div><?
					}
					// CHEAPER
					if($arParams['USE_CHEAPER']=='Y')
					{
						?><div class="cheaper"><?
							?><a rel="nofollow" class="cheaper detail fancyajax fancybox.ajax" href="<?=SITE_DIR?>cheaper/" title="<?=GetMessage('CHEAPER')?>"><i class="icon pngicons"></i><?=GetMessage('CHEAPER')?></a><?
						?></div><?
					}
				?></div><?
			}
		?></div><?
		// SHARE
		if($arParams['USE_SHARE']=='Y')
		{
			?><div class="share"><?
				/*?><span class="b-share"><a class="email2friend b-share__handle b-share__link b-share-btn__vkontakte" href="#email2friend" title="<?=GetMessage('EMAIL2FRIEND')?>"><i class="b-share-icon icon pngicons"></i></a></span><?*/
				?><span id="detailYaShare_<?=$arResult['ID']?>"></span><?
				?><script type="text/javascript">
				new Ya.share({
					link: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arResult['DETAIL_PAGE_URL']?>',
					title: '<?=CUtil::JSEscape($arResult['TITLE'])?>',
					<?if(isset($arResult['PREVIEW_TEXT']) && $arResult['PREVIEW_TEXT']!=''):?>description: '<?=CUtil::JSEscape($arResult['PREVIEW_TEXT'])?>',<?endif;?>
					<?if(isset($arResult['FIRST_PIC'])):?>image: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arResult['FIRST_PIC']['RESIZE']['src']?>',<?endif;?>
					element: 'detailYaShare_<?=$arResult['ID']?>',
					elementStyle: {
						'type': 'button',
						'border': false,
						'text': '<?=GetMessage('YSHARE')?>',
						'quickServices': ['yaru','vkontakte','facebook','twitter','odnoklassniki']
					},
					popupStyle: {
						blocks: {
							'<?=GetMessage('YSHARE2')?>': ['yaru','vkontakte','facebook','twitter','odnoklassniki','gplus','liveinternet','lj','moikrug','moimir','myspace']
						},
						copyPasteField: false
					}
				});
				</script><?
			?></div><?
		}
		// PREVIEW TEXT
		if($arParams['SHOW_PREVIEW_TEXT']=='Y' && $arResult['PREVIEW_TEXT']!='')
		{
			?><div class="previewtext"><?
				?><?=$arResult['PREVIEW_TEXT']?><?
				if( $arResult['TABS']['DETAIL_TEXT'] )
				{
					?>&nbsp;<a class="go2detailfrompreview" href="#detailtext"><?=GetMessage('GO2DETAILFROMPREVIEW')?></a><?
				}
			?></div><?
		}
	?></div><?
?></div><?
?><script>
	BX.message({
		RSGoPro_DETAIL_PROD_ID: '<?=GetMessageJS('RSGOPRO.DETAIL_PROD_ID')?>',
		RSGoPro_DETAIL_PROD_NAME: '<?=GetMessageJS('RSGOPRO.DETAIL_PROD_NAME')?>',
		RSGoPro_DETAIL_PROD_LINK: '<?=GetMessageJS('RSGOPRO.DETAIL_PROD_LINK')?>',
		
		RSGoPro_DETAIL_CHEAPER_TITLE: '<?=GetMessageJS('RSGOPRO.DETAIL_CHEAPER_TITLE')?>',
	});
	$(document).ready(function() {
		if ($(document).width()<670) {
			$(".add2review").css("margin-top", "10px");
			$(".add2review").css("margin-left", "0px");
		}
	});
</script><?

// tabs
// tabs -> HEADERS
$this->SetViewTarget('TABS_HTML_HEADERS');
if( $arResult['TABS']['DETAIL_TEXT'] )
{
	?><a class="switcher" href="#detailtext"><?=GetMessage('TABS_DETAIL_TEXT')?></a><?
}
if( $arResult['TABS']['DISPLAY_PROPERTIES'] )
{
	?><a class="switcher" href="#properties"><?=GetMessage('TABS_PROPERTIES')?></a><?
}
if( $arResult['TABS']['SET'] )
{
	?><a class="switcher" href="#set"><?=GetMessage('TABS_SET')?></a><?
}
if( $arResult['TABS']['PROPS_TABS'] )
{
	foreach($arParams['PROPS_TABS'] as $sPropCode)
	{
		if(
			$sPropCode!='' &&
			$arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='E' &&
			isset($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			is_array($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			count($arResult['PROPERTIES'][$sPropCode]['VALUE'])>0
		)
		{
			?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['PROPERTIES'][$sPropCode]['NAME']?></a><?
		} elseif(
			$sPropCode!='' &&
			$arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='F' &&
			isset($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			is_array($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			count($arResult['PROPERTIES'][$sPropCode]['VALUE'])>0
		) { // files
			?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['PROPERTIES'][$sPropCode]['NAME']?></a><?
		} elseif( $sPropCode!='' && isset($arResult['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']) ) { // else
			?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['DISPLAY_PROPERTIES'][$sPropCode]['NAME']?></a><?
		}
	}
}
$this->EndViewTarget();
// tabs -> CONTENTS
$this->SetViewTarget('TABS_HTML_CONTENTS');
if( $arResult['TABS']['DETAIL_TEXT'] )
{
	?><div class="content selected" id="detailtext"><?
		?><a class="switcher" href="#detailtext"><?=GetMessage('TABS_DETAIL_TEXT')?></a><?
		?><div class="contentbody clearfix"><?
			?><div class="contentinner"><?
				?><?=$arResult['DETAIL_TEXT']?><?
			?></div><?
		?></div><?
	?></div><?
}
if( $arResult['TABS']['DISPLAY_PROPERTIES'] )
{
	?><div class="content properties selected" id="properties"><?
		?><a class="switcher" href="#properties"><?=GetMessage('TABS_PROPERTIES')?></a><?
		?><div class="contentbody clearfix"><?
			?><div class="contentinner"><?
				$arTemp = array();
				if(is_array($arParams['PROPS_TABS']) && count($arParams['PROPS_TABS'])>0)
				{
					foreach($arParams['PROPS_TABS'] as $sPropCode)
					{
						$arTemp[$sPropCode] = $sPropCode;
					}
				}
				$APPLICATION->IncludeComponent('redsign:grupper.list',
					'gopro',
					array(
						'DISPLAY_PROPERTIES' => array_diff_key($arResult['DISPLAY_PROPERTIES'], $arTemp),
						'CACHE_TIME' => 36000,
					),
					$component,
					array('HIDE_ICONS'=>'Y')
				);
			?></div><?
		?></div><?
	?></div><?
}
if( $arResult['TABS']['SET'] )
{
	?><div class="content set selected" id="set"><?
		?><a class="switcher" href="#set"><?=GetMessage('TABS_SET')?></a><?
		?><div class="contentbody clearfix"><?
			if($HAVE_OFFERS && $arResult['OFFERS_IBLOCK']>0)
			{
				foreach($arResult['OFFERS'] as $arOffer)
				{
					if(!$arOffer['HAVE_SET'])
						continue;
					?><div class="aroundset offer offerid<?=$arOffer['ID']?><?if($PRODUCT['ID']!=$arOffer['ID']):?> noned<?endif;?>"><?
						?><?$APPLICATION->IncludeComponent('bitrix:catalog.set.constructor',
							'gopro',
							array(
								'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
								'ELEMENT_ID' => $arOffer['ID'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);?><?
					?></div><?
				}
			}
			if($arResult['HAVE_SET'])
			{
				?><div class="aroundset simple"><?
					?><?$APPLICATION->IncludeComponent('bitrix:catalog.set.constructor',
						'gopro',
						array(
							'IBLOCK_ID' => $arParams['IBLOCK_ID'],
							'ELEMENT_ID' => $arResult['ID'],
							'PRICE_CODE' => $arParams['PRICE_CODE'],
							'BASKET_URL' => $arParams['BASKET_URL'],
							'CACHE_TYPE' => $arParams['CACHE_TYPE'],
							'CACHE_TIME' => $arParams['CACHE_TIME'],
							'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						),
						$component,
						array('HIDE_ICONS' => 'Y')
					);?><?
				?></div><?
			}
		?></div><?
	?></div><?
}
if( $arResult['TABS']['PROPS_TABS'] )
{
	global $lightFilter;
	foreach($arParams['PROPS_TABS'] as $sPropCode)
	{
		if(
			$sPropCode!='' &&
			$arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='E' &&
			isset($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			is_array($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			count($arResult['PROPERTIES'][$sPropCode]['VALUE'])>0
		)
		{ // binds to elements
			?><div class="content selected" id="prop<?=$sPropCode?>"><?
				?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['PROPERTIES'][$sPropCode]['NAME']?></a><?
				?><div class="contentbody clearfix"><?
					?><div class="contentinner"><?
						$lightFilter = array(
							'ID' => $arResult['PROPERTIES'][$sPropCode]['VALUE'],
						);
						?><?$intSectionID = $APPLICATION->IncludeComponent(
							'bitrix:catalog.section',
							'light',
							array(
								'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],
								'ELEMENT_SORT_FIELD' => $arParams['ELEMENT_SORT_FIELD'],
								'ELEMENT_SORT_ORDER' => $arParams['ELEMENT_SORT_ORDER'],
								'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
								'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
								'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
								'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
								'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
								'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
								'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
								'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
								'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
								'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
								'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
								'FILTER_NAME' => 'lightFilter',
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],
								'CACHE_FILTER' => $arParams['CACHE_FILTER'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'SET_TITLE' => $arParams['SET_TITLE'],
								'SET_STATUS_404' => $arParams['SET_STATUS_404'],
								'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
								'PAGE_ELEMENT_COUNT' => $arParams['PAGE_ELEMENT_COUNT'],
								'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
								'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

								'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
								'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
								'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
								'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
								'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],

								'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
								'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
								'PAGER_TITLE' => $arParams['PAGER_TITLE'],
								'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
								'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
								'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
								'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
								'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],

								'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
								'OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
								'OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
								'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
								'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
								'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
								'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
								'OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'],

								'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
								'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
								'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
								'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
								'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
								'CURRENCY_ID' => $arParams['CURRENCY_ID'],
								'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
								// goPro params
								'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
								'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
								'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
								'USE_FAVORITE' => $arParams['USE_FAVORITE'],
								'USE_SHARE' => $arParams['USE_SHARE'],
								'SHOW_ERROR_EMPTY_ITEMS' => $arParams['SHOW_ERROR_EMPTY_ITEMS'],
								'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
								'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
								'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
								// store
								'USE_STORE' => $arParams['USE_STORE'],
								'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
								'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
								'MAIN_TITLE' => $arParams['MAIN_TITLE'],
								// some...
								'BY_LINK' => 'Y',
								// seo
								"ADD_SECTIONS_CHAIN" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"ADD_ELEMENT_CHAIN" => "N",
							),
							$component,
							array('HIDE_ICONS'=>'Y')
						);?><?
					?></div><?
				?></div><?
			?></div><?
		} elseif(
			$sPropCode!='' &&
			$arResult['PROPERTIES'][$sPropCode]['PROPERTY_TYPE']=='F' &&
			isset($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			is_array($arResult['PROPERTIES'][$sPropCode]['VALUE']) &&
			count($arResult['PROPERTIES'][$sPropCode]['VALUE'])>0
		) { // files
			?><div class="content files selected" id="prop<?=$sPropCode?>"><?
				?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['PROPERTIES'][$sPropCode]['NAME']?></a><?
				?><div class="contentbody clearfix"><?
					?><div class="contentinner"><?
						$index = 1;
						foreach($arResult['PROPERTIES'][$sPropCode]['VALUE'] as $arFile)
						{
							?><a class="docs" href="<?=$arFile['FULL_PATH']?>"><?
								?><i class="icon pngicons <?=$arFile['TYPE']?>"></i><?
								?><span class="name"><?=$arFile['ORIGINAL_NAME']?></span><?
								if( isset($arFile['DESCRIPTION']) ) { ?><span class="description"><?=$arFile['DESCRIPTION']?></span><? }
								?><span class="size">(<?=$arFile['TYPE']?>, <?=$arFile['SIZE']?>)</span><?
							?></a><?
							if($index>3) { $index==0; }
							?><span class="separator x<?=$index?>"></span><?
							$index++;
						}
					?></div><?
				?></div><?
			?></div><?
		} elseif( $sPropCode!='' && isset($arResult['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']) ) { // else
			?><div class="content selected" id="prop<?=$sPropCode?>"><?
				?><a class="switcher" href="#prop<?=$sPropCode?>"><?=$arResult['DISPLAY_PROPERTIES'][$sPropCode]['NAME']?></a><?
				?><div class="contentbody clearfix"><?
					?><div class="contentinner"><?
						?><?=$arResult['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']?><?
					?></div><?
				?></div><?
			?></div><?
		}
	}
}
$this->EndViewTarget();