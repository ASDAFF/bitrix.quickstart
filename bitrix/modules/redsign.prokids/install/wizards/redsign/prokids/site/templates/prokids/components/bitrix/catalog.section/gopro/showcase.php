<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$i = 0;
if(is_array($arResult['PRICES']) && count($arResult['PRICES'])>0) {
	foreach($arResult['PRICES'] as $PRICE_CODE => $arPrice) {
		if(!$arPrice['CAN_VIEW']) {
			continue;
		}
		$i++;
	}
}
$multyPrice = ( $i>1 ? true : false );

$this->SetViewTarget('paginator');
if($arParams['IS_AJAXPAGES']!='Y' && $arParams['IS_SORTERCHANGE']!='Y' && $arParams['DISPLAY_TOP_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}
$this->EndViewTarget();

if($arParams['IS_SORTERCHANGE']=='Y') {
	$this->SetViewTarget('paginator');
	echo $arResult['NAV_STRING'];
	$this->EndViewTarget();
	$templateData['paginator'] = $APPLICATION->GetViewContent('paginator');
	$this->SetViewTarget('sorterchange');
}

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
	if($arParams['IS_AJAXPAGES']!='Y') {
		?><!-- showcase --><div class="showcase clearfix<?
			if($multyPrice):?> big<?endif;?><?
			if($arParams['COLUMNS5']=='Y'):?> columns5<?endif;
			?>" id="showcaseview"><?
	}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->SetViewTarget("showcaseview");
	}
	foreach($arResult['ITEMS'] as $key1 => $arItem) {
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
		if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
		?><div class="js-element js-elementid<?=$arItem['ID']?> <?if($HAVE_OFFERS):?>offers<?else:?>simple<?endif;?><?
			if( isset($arItem['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo ' da2'; }
			if( isset($arItem['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }
			?> propvision1" <?
			?>data-elementid="<?=$arItem['ID']?>" <?
			?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"><?
			?><div class="inner"><?
				?><div class="padd"><?
					if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
					} else {
						?><span><?
					}
					?><i class="icon da2qb"></i><?
					if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
						?></a><?
					} else {
						?></span><?
					}
					// -- FIRST PART
					?><div class="name"><?
						if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
							?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?
						} else {
							?><span><?
						}
						?><?=$arItem['NAME']?><?
						if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
							?></a><?
						} else {
							?></span><?
						}
					?></div><?
					?><div class="pic"><?
						// PICTURE
						if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
							?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
						} else {
							?><span><?
						}
							if( isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src'])!='' )
							{
								?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
							} else {
								?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
							}
						if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
							?></a><?
						} else {
							?></span><?
						}
						// TIMERS
						$arTimers = array();
						if( $arItem['HAVE_DA2']=='Y' ) {
							if( isset($arItem['DAYSARTICLE2']) )
							{
								$arTimers[] = $arItem['DAYSARTICLE2'];
							} elseif($HAVE_OFFERS) {
								foreach($arItem['OFFERS'] as $arOffer)
								{
									if( isset($arOffer['DAYSARTICLE2']) )
									{
										$arTimers[] = $arOffer['DAYSARTICLE2'];
									}
								}
							}
						} elseif( $arItem['HAVE_QB']=='Y' ) {
							if( isset($arItem['QUICKBUY']) )
							{
								$arTimers[] = $arItem['QUICKBUY'];
							} elseif($HAVE_OFFERS) {
								foreach($arItem['OFFERS'] as $arOffer)
								{
									if( isset($arOffer['QUICKBUY']) )
									{
										$arTimers[] = $arOffer['QUICKBUY'];
									}
								}
							}
						}
						if( is_array($arTimers) && count($arTimers)>0 )
						{
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
										if( ($arItem['ID']==$arTimer['ELEMENT_ID'] || $PRODUCT['ID']==$arTimer['ELEMENT_ID']) && !$have_vis)
										{
											?>inline-block<?
											$have_vis = true;
										} else {
											?>none<?
										}
										?>;" data-datefrom="<?=$arTimer[$KY]['DATE_FROM']?>"><?
										?><div class="clock"><i class="icon"></i></div><?
										?><div class="intimer clearfix"  data-dateto="<?=$arTimer[$KY]['DATE_TO']?>" data-autoreuse="<?=$arTimer['AUTO_RENEWAL'];?>"><?
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
												?></div><div class="podpis"><?=GetMessage('QB_AND_DA2_MIN')?></div></div><?
											if($arTimer[$KY]['DAYS']<1){
												?><div class="dvoet">:</div><?
												?><div class="val"><div class="value result-second"><?
													echo($arTimer[$KY]['SECOND']>9?$arTimer[$KY]['SECOND']:'0'.$arTimer[$KY]['SECOND'] )
													?></div><div class="podpis"><?=GetMessage('QB_AND_DA2_SEC')?></div></div><?
											}
											if(isset( $arTimer['DINAMICA_EX']) )
											{
												?><div class="val ml"><div class="value"><?
													echo($arTimer[$KY]['PHP_DATA']['persent']>9?$arTimer[$KY]['PHP_DATA']['persent']:'0'.$arTimer[$KY]['PHP_DATA']['persent'] )
													?>%</div><div class="podpis"><?=GetMessage('QB_AND_DA2_PRODANO')?></div></div><?
											} elseif( isset($arTimer['TIMER']) && IntVal($arTimer['QUANTITY'])>0 ) {
												?><div class="val ml"><div class="value"><?
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
					?></div><?
				?></div><?
				// PRICES
				if($multyPrice){
					?><div class="prices scrollp vertical"><?
						$cnt = 0;
						$pricesHTML = '';
						foreach($arResult['PRICES'] as $PRICE_CODE => $arResPrice){
							$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
							if(!$arResult['PRICES'][$PRICE_CODE]['CAN_VIEW'])
								continue;
							$pricesHTML.= '<tr class="';
							if( ($cnt+1)%2==0 ){
								$pricesHTML.= 'even';
							} else {
								$pricesHTML.= 'odd';
							}
							$pricesHTML.= ' scrollitem">';
							$pricesHTML.= '<td class="nowrap">'.$arResPrice['TITLE'].'</td>';
							$pricesHTML.= '<td class="nowrap"><span class="price '. ($arPrice['DISCOUNT_DIFF']>0 ? ' new ' : ' ').'price_pdv_'.$PRICE_CODE.'">'.(isset($arPrice["PRINT_DISCOUNT_VALUE"]) ? $arPrice["PRINT_DISCOUNT_VALUE"] : '&mdash;' ).'</span></td>';
							$pricesHTML.= '</tr>';
							$cnt++;
						}
						?><a rel="nofollow" class="scrollbtn prev" href="#"><i class="icon pngicons"></i></a><?
						?><div class="prices_jscrollpane scroll vertical vertical-only" id="prs_scroll_<?=$arItem['ID']?>" style="height:<?if($cnt>2):?>102<?else:?>68<?endif;?>px;"><?
							?><table class="pricestable scrollinner"><?
								?><tbody><?
									?><?=$pricesHTML?><?
								?></tbody><?
							?></table><?
						?></div><?
						?><a rel="nofollow" class="scrollbtn next" href="#"><i class="icon pngicons"></i></a><?
					?></div><?
				} elseif(is_array($arResult["PRICES"])) {
					?><div class="soloprice"><?
						foreach($arResult['PRICES'] as $PRICE_CODE => $arResPrice){
							if(!$arResult['PRICES'][$PRICE_CODE]['CAN_VIEW'])
								continue;
							$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
							?><span class="price gen price_pdv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_VALUE']?></span><?
							if( $arPrice['DISCOUNT_DIFF']>0 ){
								?><span class="price old price_pv_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_VALUE']?></span><?
								?><span class="discount price_pd_<?=$PRICE_CODE?>"><?=$arPrice['PRINT_DISCOUNT_DIFF']?></span><?
							}
						}
					?></div><?
				}
				// -- SECOND PART
				if($arParams['OFF_SMALLPOPUP']!='Y') {
					?><div class="popup padd"><?
						// PROPERTIES
						if(is_array($arItem['OFFERS_EXT']['PROPERTIES']) && count($arItem['OFFERS_EXT']['PROPERTIES'])>0)
						{
							?><div class="properties"><?
								foreach($arItem['OFFERS_EXT']['PROPERTIES'] as $propCode => $arProperty)
								{
									$isColor = false;
									?><div class="offer_prop prop_<?=$propCode?> closed<?
										if(is_array($arParams['PROPS_ATTRIBUTES_COLOR']) && in_array($propCode,$arParams['PROPS_ATTRIBUTES_COLOR']))
										{
											$isColor = true;
											?> color<?
										}
										?>" data-code="<?=$propCode?>">
										<span class="offer_prop-name"><?=$arItem['OFFERS_EXT']['PROPS'][$propCode]['NAME']?>: </span><?
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
						?><noindex><div class="buy"><?
							?><form class="add2basketform js-buyform<?=$arItem['ID']?> js-synchro<?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?> clearfix" name="add2basketform"><?
								?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET"><?
								?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?
								if($arParams['USE_PRODUCT_QUANTITY'])
								{
									?><span class="quantity"><?
										?><a class="minus js-minus">-</a><?
										?><input type="text" class="js-quantity" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
										if($arParams['OFF_MEASURE_RATION']!='Y') {
											?><span class="js-measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
										}
										?><a class="plus js-plus">+</a><?
									?></span><?
								}
								?><a rel="nofollow" class="submit add2basket" href="#" title="<?=GetMessage('ADD2BASKET')?>"><?=GetMessage('CT_BCE_CATALOG_ADD')?></a><?
								?><a rel="nofollow" class="inbasket" href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage('INBASKET_TITLE')?>"><?=GetMessage('INBASKET')?></a><?
								?><input type="submit" name="submit" class="nonep" value="" /><?
							?></form><?
						?></div></noindex><?
						// COMPARE
						if($arParams['DISPLAY_COMPARE']=='Y')
						{
							?><div class="compare"><?
								?><a rel="nofollow" class="add2compare" href="<?=$arItem['COMPARE_URL']?>"><i class="icon pngicons"></i><?=GetMessage('ADD2COMPARE')?></a><?
							?></div><?
						}
						// DESCRIPTION
						if(isset($arItem['PREVIEW_TEXT']) && $arItem['PREVIEW_TEXT']!='')
						{
							?><div class="description"><div class="text"><?=$arItem['PREVIEW_TEXT']?></div><a class="more" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=GetMessage('GOPRO.MORE')?></a></div><?
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
										?><span class="b-share"><a class="fancyajax fancybox.ajax email2friend b-share__handle b-share__link b-share-btn__vkontakte" href="<?=SITE_DIR?>email2friend/?link=<?=CUtil::JSUrlEscape('http://'.$_SERVER['HTTP_HOST'].$arItem['DETAIL_PAGE_URL'])?>" title="<?=GetMessage('EMAIL2FRIEND')?>"><i class="b-share-icon icon pngicons"></i></a></span><?
										?><span id="detailYaShare_<?=$arItem['ID']?>"></span><?
										?><script type="text/javascript">
										new Ya.share({
											link: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arItem['DETAIL_PAGE_URL']?>',
											title: '<?=CUtil::JSEscape($arItem['NAME'])?>',
											<?if(isset($arItem['PREVIEW_TEXT']) && $arItem['PREVIEW_TEXT']!=''):?>description: '<?=CUtil::JSEscape($arItem['PREVIEW_TEXT'])?>',<?endif;?>
											<?if(isset($arItem['FIRST_PIC'])):?>image: 'http://<?=$_SERVER['HTTP_HOST']?><?=$arItem['FIRST_PIC']['RESIZE']['src']?>',<?endif;?>
											element: 'detailYaShare_<?=$arItem['ID']?>',
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
				}
				// -- /SECOND PART
			?></div><?
		?></div><?
	}
	if($arParams['IS_AJAXPAGES']=='Y')
	{
		?><script>RSGoPro_JSPReinit('.prices_jscrollpane',1)</script><?
		$this->EndViewTarget();
		$templateData['showcaseview'] = $APPLICATION->GetViewContent('showcaseview');
	}
	if($arParams['IS_AJAXPAGES']!='Y')
	{
		?></div><!-- showcase --><?
	}
	if($arParams['IS_AJAXPAGES']=='Y')
	{
		$this->SetViewTarget("catalogajaxpages");
	}
	if(IntVal($arResult['NAV_RESULT']->NavPageNomer)<IntVal($arResult['NAV_RESULT']->NavPageCount))
	{
		?><div class="ajaxpages<?if($arParams['USE_AUTO_AJAXPAGES']=='Y'):?> auto<?endif;?>"><?
			?><a rel="nofollow" href="#" <?
				?>data-ajaxurl="<?=$arResult['AJAXPAGE_URL']?>" <?
				?>data-ajaxpagesid="<?=$arParams['AJAXPAGESID']?>" <?
				?>data-navpagenomer="<?=($arResult['NAV_RESULT']->NavPageNomer)?>" <?
				?>data-navpagecount="<?=($arResult['NAV_RESULT']->NavPageCount)?>" <?
				?>data-navnum="<?=($arResult['NAV_RESULT']->NavNum)?>"<?
			?>><i class="animashka"></i><span><?=GetMessage('AJAXPAGES_LOAD_MORE')?></span></a><?
		?></div><?
	}
	if($arParams['IS_AJAXPAGES']=='Y')
	{
		$this->EndViewTarget();
		$templateData['catalogajaxpages'] = $APPLICATION->GetViewContent('catalogajaxpages');
	}
	if($arParams['IS_SORTERCHANGE']=='Y')
	{
		$this->EndViewTarget();
		$templateData[$arParams['AJAXPAGESID']] = $APPLICATION->GetViewContent('sorterchange');
	}
} elseif($arParams['SHOW_ERROR_EMPTY_ITEMS']=='Y'){
	ShowError(GetMessage('ERROR_EMPTY_ITEMS'));
}