<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if ($arParams['DISPLAY_TOP_PAGER'] == 'Y') {
	echo $arResult['NAV_STRING'];
}

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
	?><div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?>"><?
		foreach ($arResult['ITEMS'] as $key1 => $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			
			if (empty($arItem['OFFERS'])) {
				$HAVE_OFFERS = false;
				$PRODUCT = &$arItem;
			} else { 
				$HAVE_OFFERS = true;
				$PRODUCT = &$arItem['OFFERS'][0]; 
			}
			
			?><div class="item <?
				?>js-element <?
				?>js-elementid<?=$arItem['ID']?> <?
				?>col col-md-12 <?
				?>" <?
				?>data-elementid="<?=$arItem['ID']?>" <?
                ?>data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>" <?
				?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"<?
				?>><?
				?><div class="da2_icon hidden-xs"><span><?=Loc::getMessage('DA2_ICON_TITLE')?></span></div><?
				?><div class="qb_icon hidden-xs"><span><?=Loc::getMessage('QB_ICON_TITLE')?></span></div><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="in"><?
							?><div class="row"><?
								// picture
								?><div class="col col-xs-4 col-sm-3 col-md-2 part part1"><?
									?><div class="pic text-center"><?
										?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
											if ($arParams['RSFLYAWAY_USE_FAVORITE'] == "Y") {
												?><div 
													class="favorite favorite-heart"
													data-elementid = "<?=$arItem['ID']?>"
													data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>"
												>
												</div><?
											}
											
											if (isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src']) != '') {
												?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
											} else {
												?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
											}
										?></a><?
									?></div><?
								?></div><?

								// other
								?><div class="col col-xs-8 col-sm-9 col-md-10 part part2"><?
									?><div class="data"><?
										?><div class="row"><?
											?><div class="col col-md-<?if($arParams["SIDEBAR"]=='Y'):?>6<?else:?>8<?endif;?> col-sm-6"><?
												?><div class="limiter"><?
													?><div class="name"><?
														?><a class="aprimary" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br /><?
													?></div><?
													
													if ($arItem['PREVIEW_TEXT'] != '') {
														?><div class="descr hidden-xs"><?=$arItem['PREVIEW_TEXT']?></div><?
													}
													
												?></div><?
												?><div class="bot"><?
													?><div class="row"><?
														?><div class="col col-xs-12 artstorcompare"><?
															if ($arParams['RSFLYAWAY_PROP_ARTICLE'] != '' && $arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']]['VALUE'] != '') {
																?><span class="article text-nowrap"><?
																	?><?=Loc::getMessage('RS.FLYAWAY.ARTICLE')?>: <?=$arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_ARTICLE']]['VALUE']?><?
																?></span><?
															}
															
															if ($arParams['USE_STORE'] != '') {
																?><span><?
																$APPLICATION->IncludeComponent(
																	'bitrix:catalog.store.amount',
																	'flyaway',
																	array(
																		"ELEMENT_ID" => $arItem["ID"],
																		"STORE_PATH" => $arParams["STORE_PATH"],
																		"CACHE_TYPE" => "A",
																		"CACHE_TIME" => "36000",
																		"MAIN_TITLE" => $arParams["MAIN_TITLE"],
																		"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
																		"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
																		"USE_MIN_AMOUNT" => "N",
																		"FLYAWAY_USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
																		"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
																		"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
																		"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
																		"USER_FIELDS" => $arParams['USER_FIELDS'],
																		"FIELDS" => $arParams['STORES_FIELDS'],
																		// flyaway
																		'DATA_QUANTITY' => $arItem['DATA_QUANTITY'],
																		'FIRST_ELEMENT_ID' => $PRODUCT['ID'],
																	),
																	$component,
																	array('HIDE_ICONS'=>'Y')
																);
																?></span><?
															}
															
															if ($arParams['DISPLAY_COMPARE'] == 'Y') {
																?><span class="compare text-nowrap"><?
																	?><a class="js-compare link" href="<?=$arItem['COMPARE_URL']?>"><span><?=Loc::getMessage('RS.FLYAWAY.COMPARE')?></span><span class="count"></span></a><?
																?></span><?
															}
														?></div><?
													?></div><?
												?></div><?
											?></div><?
											?><div class="col col-md-<?if($arParams["SIDEBAR"]=='Y'):?>3<?else:?>2<?endif;?> col-sm-3"><?
												?><div class="prices"><div><?
													if (IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0) {
														?><div class="price old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div><?
														?><div class="price cool new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													} else {
														?><div class="price cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													}
												?></div></div><?
											?></div><?
											?><div class="col col-md-<?if($arParams["SIDEBAR"]=='Y'):?>3<?else:?>2<?endif;?> col-sm-3 hidden-xs"><?
												?><div class="buybtn"><?
													?><div><?
														if($HAVE_OFFERS){
															?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-primary"><?=Loc::getMessage('FLYAWAY.BTN_MORE')?></a><?
														} else {
															?><noindex><?
															?><form class="add2basketform js-buyform<?=$arItem['ID']?><?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?><?if($arParams['USE_PRODUCT_QUANTITY']):?> usequantity<?endif;?> text-center" name="add2basketform"><?
															?><input type="hidden" name="action" value="ADD2BASKET"><?
															?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$PRODUCT['ID']?>"><?
															/*if($arParams['USE_PRODUCT_QUANTITY']){
																?><span class="quantity"><?
																	?><a class="minus js-minus"><i class="fa"></i></a><?
																	?><input type="text" class="js-quantity form-control text-center" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
																	?><a class="plus js-plus"><i class="fa"></i></a><?
																	?><span class="js-measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
																?></span><?
															}*/
															?><button type="submit" rel="nofollow" class="btn btn-primary submit js-add2basketlink" value=""><?=Loc::getMessage('RS.FLYAWAY.BTN_BUY')?></button><?
															?><a class="btn btn-primary inbasket" href="<?=$arParams['BASKET_URL']?>"><?=Loc::getMessage('RS.FLYAWAY.BTN_GO2BASKET')?></a><?
															?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-primary js-morebtn buybtn"><?=Loc::getMessage('RS.FLYAWAY.BTN_MORE')?></a><?
															?></form><?
															?></noindex><?
														}
													?></div><?
												?></div><?
											?></div><?
										?></div><?
									?></div><?
								?></div><?
							?></div><?

						?></div><?
					?></div><?
				?></div><?
			?></div><?

		}

	?></div><?

} else {
	?><div class="alert alert-info" role="alert"><?=Loc::getMessage('RS.FLYAWAY.NO_PRODUCTS')?></div><?
}

if($arParams['DISPLAY_BOTTOM_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}