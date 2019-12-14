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
	if($arParams['IS_AJAXPAGES']!="Y") {
			?><!-- artables --><div class="artables table clearfix"><?
				?><table class="names" border="0" cellpadding="0" cellspacing="0"><?
					?><thead><?
						?><th class="free"></th><?
						?><th class="nowrap name"><div class="fix"><?=GetMessage('GOPRO_TH_PRODUCT')?></div></th><?
					?></thead><?
					?><tbody><?
	}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->SetViewTarget("catalognames");
	}
						$inc = 0;
						foreach($arResult['ITEMS'] as $key1 => $arItem) {
							$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
							if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
							?><tr class="js-name<?=$arItem['ID']?><?if( ($inc+1)%2==0 ):?> even<?endif;?>" data-elementid="<?=$arItem['ID']?>"><?
								?><td class="free"><?
									?><span<?
										if( 
											isset($arItem['DAYSARTICLE2']) ||
											isset($PRODUCT['DAYSARTICLE2']) ||
											isset($arItem['QUICKBUY']) ||
											isset($PRODUCT['QUICKBUY'])
										){
											echo ' class="';
											if( isset($arItem['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo 'da2'; }
											if( isset($arItem['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }
											echo '"';
										}
									?>></span><?
								?></td><?
								?><td class="name"><?
									?><div class="js-position"><?
										if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
											?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
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
								?></td><?
							?></tr><?
							$inc++;
						}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->EndViewTarget();
		$templateData['catalognames'] = $APPLICATION->GetViewContent('catalognames');
	}
	if($arParams['IS_AJAXPAGES']!="Y") {
					?></tbody><?
				?></table><?
				?><!-- arproducts --><div class="arproducts"><?
					?><table class="products" border="0" cellpadding="0" cellspacing="0"><?
						?><thead><?
							?><tr><?
								?><th class="free"></th><?
								?><th class="nowrap name"><div class="name"><?=GetMessage("GOPRO_TH_PRODUCT")?></div></th><?
								if(isset($arParams['PROP_SKU_ARTICLE']) || isset($arParams['PROP_ARTICLE'])) {
									?><th class="nowrap"><?=GetMessage('ARTICLE')?></th><?
								}
								if($arParams['USE_STORE']=='Y') {
									?><th class="nowrap"><?=GetMessage('GOPRO_TH_QUANTITY')?></th><?
								}
								if(is_array($arResult['PRICES']) && count($arResult['PRICES'])>0) {
									foreach($arResult['PRICES'] as $PRICE_CODE => $arPrice) {
										if(!$arPrice['CAN_VIEW']) {
											continue;
										}
										?><th class="nowrap"><?=$arPrice['TITLE']?></th><?
									}
								}
								?><th class="nowrap"><?=GetMessage('GOPRO_TH_ZAKAZ')?></th><?
							?></tr><?
						?></thead><?
						?><tbody><?
	}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->SetViewTarget("catalogproducts");
	}
		$inc = 0;
		foreach($arResult['ITEMS'] as $key1 => $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
			if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
			?><tr class="js-element js-elementid<?=$arItem['ID']?> <?if($HAVE_OFFERS):?>offers<?else:?>simple<?endif;?><?if( ($inc+1)%2==0 ):?> even<?endif;?>" data-elementid="<?=$arItem['ID']?>" id="<?=$this->GetEditAreaId($arItem["ID"]);?>"><?
				?><td class="free"><?
					?><span<?
						if( 
							isset($arItem['DAYSARTICLE2']) ||
							isset($PRODUCT['DAYSARTICLE2']) ||
							isset($arItem['QUICKBUY']) ||
							isset($PRODUCT['QUICKBUY'])
						){
							echo ' class="';
							if( isset($arItem['DAYSARTICLE2']) || isset($PRODUCT['DAYSARTICLE2']) ) { echo ' da2'; }
							if( isset($arItem['QUICKBUY']) || isset($PRODUCT['QUICKBUY']) ) { echo ' qb'; }
							echo '"';
						}
					?>></span><?
				?></td><?
				?><td class="name"><?
					?><div class="name js-position"><?
						if( $arParams['DONT_SHOW_LINKS']!='Y' ) {
							?><a class="js-detaillink" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
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
				?></td><?
				if($HAVE_OFFERS) {
					if(isset($arParams['PROP_SKU_ARTICLE']) || isset($arParams['PROP_ARTICLE'])) {
						?><td class="nowrap"><?
						if(isset($arParams['PROP_SKU_ARTICLE']) && isset($arItem['OFFERS'][0]['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]) && $arItem['OFFERS'][0]['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE']!='')
							echo $arItem['OFFERS'][0]['PROPERTIES'][$arParams['PROP_SKU_ARTICLE']]['VALUE'];
						elseif(isset($arParams['PROP_ARTICLE']) && $arItem['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE']!='')
							echo $arItem['PROPERTIES'][$arParams['PROP_ARTICLE']]['VALUE'];
						?></td><?
					}
					$BUY_ID = 0;//$arItem['OFFERS'][0]['ID'];
				} else {
					if(isset($arParams['PROP_SKU_ARTICLE']) || isset($arParams['PROP_ARTICLE'])) {
						?><td class="nowrap"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['PROP_ARTICLE']]['DISPLAY_VALUE']?></td><?
					}
					$BUY_ID = $arItem['ID'];
				}
				if($arParams['USE_STORE']=='Y') {
					?><td class="nowrap"><?
					if($arParams['USE_MIN_AMOUNT']=='Y') {
						if( $arItem['FULL_CATALOG_QUANTITY']<1 ) {
							echo GetMessage('GOPRO_QUANTITY_EMPTY');
						} elseif( $arItem['FULL_CATALOG_QUANTITY']<$arParams['MIN_AMOUNT'] ) {
							echo GetMessage('GOPRO_QUANTITY_LOW');
						} else {
							echo GetMessage('GOPRO_QUANTITY_ISSET');
						}
					} else {
						echo $arItem['FULL_CATALOG_QUANTITY'];
					}
					?></td><?
				}
				if(is_array($arResult["PRICES"]) && count($arResult["PRICES"])>0) {
					foreach($arResult['PRICES'] as $PRICE_CODE => $arResPrice) {
						$arPrice = $PRODUCT['PRICES'][$PRICE_CODE];
						if(!$arResult['PRICES'][$PRICE_CODE]['CAN_VIEW'])
							continue;
						?><td class="nowrap"><?=(isset($arPrice["PRINT_DISCOUNT_VALUE"]) ? $arPrice["PRINT_DISCOUNT_VALUE"] : '&mdash;' )?></td><?
					}
				}
				?><td class="nowrap"><?
					?><noindex><?
						?><form class="add2basketform js-add2basketform<?=$arItem['ID']?><?if(!$PRODUCT['CAN_BUY'] && $BUY_ID>0):?> cantbuy<?endif;?> js-synchro" name="add2basketform"><?
							?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET" /><?
							if($arParams['USE_PRODUCT_QUANTITY']) {
								?><span class="quantity"><?
									?><a class="minus js-minus">-</a><?
									?><input type="text" class="js-quantity" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
									if($arParams['OFF_MEASURE_RATION']!='Y') {
										?><span class="js-measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
									}
									?><a class="plus js-plus">+</a><?
								?></span><?
							}
							?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$BUY_ID?>" /><?
							?><a rel="nofollow" class="submit js-add2basketlink" href="#" title="<?=GetMessage("ADD2BASKET")?>"><i class="icon pngicons"></i></a><?
							?><a class="submit inbasket" href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage("INBASKET")?>"><i class="icon pngicons"></i></a><?
							?><i class="tick icon pngicons"></i><?
							?><input type="submit" name="submit" class="nonep" value="" /><?
						?></form><?
					?></noindex><?
				?></td><?
			?></tr><?
			$inc++;
		}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->EndViewTarget();
		$templateData['catalogproducts'] = $APPLICATION->GetViewContent('catalogproducts');
	}
	if($arParams['IS_AJAXPAGES']!="Y") {
						?></tbody><?
					?></table><?
				?></div><!-- /arproducts --><?
			?></div><!-- /artables --><?
			?><script>RSGoPro_DetectTable();</script><?
	}
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->SetViewTarget("catalogajaxpages");
	}
	if(IntVal($arResult['NAV_RESULT']->NavPageNomer)<IntVal($arResult['NAV_RESULT']->NavPageCount)) {
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
	if($arParams['IS_AJAXPAGES']=="Y") {
		$this->EndViewTarget();
		$templateData['catalogajaxpages'] = $APPLICATION->GetViewContent('catalogajaxpages');
	}
	if($arParams['IS_SORTERCHANGE']=='Y') {
		$this->EndViewTarget();
		$templateData[$arParams['AJAXPAGESID']] = $APPLICATION->GetViewContent('sorterchange');
	}
} elseif($arParams['SHOW_ERROR_EMPTY_ITEMS']=='Y'){
	ShowError(GetMessage('ERROR_EMPTY_ITEMS'));
}