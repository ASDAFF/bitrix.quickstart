<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($arParams['DISPLAY_TOP_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}

ob_start();

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
	?><div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?>"><?
		foreach($arResult['ITEMS'] as $key1 => $arItem) {
 			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			
			?><div class="item <?
				?>col col-sm-4 col-md-<?if($arParams["SIDEBAR"] == 'Y'):?>6<?else:?>3<?endif;?> col-lg-<?if($arParams["SIDEBAR"] == 'Y'):?>4<?else:?>2<?endif;?> <?
				?>js-element <?
				?>js-elementid<?=$arItem['ID']?> <?
				?>JS-Compare <?
				?>" <?
				?>data-elementid="<?=$arItem['ID']?>" <?
				?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"<?
				?>><?

				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="in"><?
							// PICTURE
							?><div class="pic"><?
								?><a class="JS-Compare-Label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
									if (isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src']) != '') {
										?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
									} else {
										?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
									}
								?></a><?
								?><div class="marks"><?
									if ($arItem['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_action"><?=GetMessage('RS_ACTION_ITEM');?></span><?
									}
									
									if ($arItem['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_hit"><?=GetMessage('RS_BESTSELLER_ITEM');?></span><?
									}
									
									if ($arItem['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_new"><?=GetMessage('RS_NEW_ITEM');?></span><?
									}
								?></div><?
							?></div><?
							?><div class="data"><?
								// NAME
								?><div class="name"><?
									?><a class="aprimary" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br /><?
								?></div><?
								
								if ('Y' == $arParams['SHOW_SECTION_URL'] && isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])) {
									?><div class="category separator"><?
										?><a class="category__label" href="<? echo $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>"><?
											echo $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME'];
										?></a><?
									?></div><?
								}
								
								if (isset($arItem['PREVIEW_TEXT']) && '' != $arItem['PREVIEW_TEXT']) {
									?><div class="description"><?
										echo $arItem['PREVIEW_TEXT'];
									?></div><?
								}
								
								// PRICES
								?><div class="prices-wrapper"><?
										?><div class="prices"><?
											?><div class="title"></div><?
											?><div class="value"><?
												if (IntVal($arItem['RS_PRICE']['DISCOUNT_DIFF']) > 0) {
													?><div class="price old"><?=$arItem['RS_PRICE']['PRINT_VALUE']?></div><?
													?><div class="price cool new"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
												} else {
													?><div class="price cool"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
												}
											?></div><?
										?></div><?
								?></div><?
								
								?><div class="detail"><?
									?><div class="row clearfix buttons-section"><?
										// ADD2BASKET
										?><div class="col col-xs-6 col-xs-offset-6 col-right text-right"><?
												?><a class="btn btn-default" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('RS.FLYAWAY.BTN_MORE')?></a><?
										?></div><?
									?></div><?
								
									?><div class="row clearfix quantity-wrapper"><?
										?><div class="col col-xs-6 col-left"><?
											?><span class="identifer"><?
												if ($arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'] != '') {
													echo GetMessage('RS.FLYAWAY.ARTICLE').': '.$arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'];
												}
											?></span><?
										?></div><?
															
										if ($arParams['RSFLYAWAY_PROP_QUANTITY'] != '') {
											?><div class="col col-xs-6 col-right text-right pull-right"><?
												?><span class="stores"><?
													?><span class="stores-label"><?=GetMessage('RS.FLYAWAY.QUANTITY')?></span>: <?
													
													if (IntVal($arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_QUANTITY']]['VALUE']) < 1) {
														?><span class="stores-icon"></span><span class="genamount empty"><?=GetMessage('RS.FLYAWAY.QUANTITY_EMPTY')?></span><?
													} else {
														?><span class="stores-icon stores-full"></span><span class="genamount isset"><?=GetMessage('RS.FLYAWAY.QUANTITY_ISSET')?></span><?
													}
												?></span><?
											?></div><?
										}
									?></div><?
									
									?><div class="row clearfix compare-wrapper"><?
										// COMPARE
										?><div class="col col-xs-6 col-left compare"><?
											if ($arParams['DISPLAY_COMPARE'] == 'Y') {
												?><span class="icon-east JS-Compare-Box"><?
													?><a class="JS-Compare-Switcher" href="javascript:;"><?
														?><i class="fa fa-align-left"></i><?
														?><span><?=GetMessage('RS.FLYAWAY.COMPARE')?></span><?
														?><span class="icon-east__label"><?=GetMessage('RS.FLYAWAY.IN_COMPARE')?></span><?
													?></a><?
													?><span class="tooltip"><?=GetMessage('RS.FLYAWAY.ADD_COMPARE')?></span><?
													?><span class="tooltip tooltip_hidden"><?=GetMessage('RS.FLYAWAY.DEL_COMPARE')?></span><?
												?></span><?
											}
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
	?><div class="alert alert-info" role="alert"><?=GetMessage('RS.FLYAWAY.NO_PRODUCTS')?></div><?
}

if ($arParams['DISPLAY_BOTTOM_PAGER'] == 'Y') {
	echo $arResult['NAV_STRING'];
}

$templateData = ob_get_flush();
