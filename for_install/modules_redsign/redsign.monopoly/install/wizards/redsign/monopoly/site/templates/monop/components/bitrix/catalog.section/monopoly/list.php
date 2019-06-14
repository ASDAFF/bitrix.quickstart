<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams['DISPLAY_TOP_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}

ob_start();

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {

	?><div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?>"><?

		foreach($arResult['ITEMS'] as $key1 => $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			
			?><div class="item js-element js-elementid<?=$arItem['ID']?> col col-md-12" <?
				?>data-elementid="<?=$arItem['ID']?>" <?
				?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"<?
				?>><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="in"><?
							?><div class="row"><?

								// picture
								?><div class="col col-xs-4 col-sm-3 col-md-2 part part1"><?
									?><div class="pic text-center"><?
										?><a class="js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
											if( isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src'])!='' ) {
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
													if($arItem['PREVIEW_TEXT']!='') {
														?><div class="descr hidden-xs"><?=$arItem['PREVIEW_TEXT']?></div><?
													}
												?></div><?
												?><div class="bot"><?
													?><div class="row"><?
														?><div class="col col-xs-12"><?
															if( $arParams['RSMONOPOLY_PROP_ARTICLE']!='' && $arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_ARTICLE']]['VALUE']!='' ) {
																?><span class="article text-nowrap"><?
																	?><?=GetMessage('RS.MONOPOLY.ARTICLE')?>: <?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_ARTICLE']]['VALUE']?><?
																?></span><?
															}
															if( $arParams['RSMONOPOLY_PROP_QUANTITY']!='' ) {
																?><span class="stores text-nowrap"><?
																	?><?=GetMessage('RS.MONOPOLY.QUANTITY')?>:<?
																	if( IntVal($arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_QUANTITY']]['VALUE'])<1 ) {
																		?><span class="empty"> <?=GetMessage('RS.MONOPOLY.QUANTITY_EMPTY')?></span><?
																	} else {
																		?><span class="isset"> <?=GetMessage('RS.MONOPOLY.QUANTITY_ISSET')?></span><?
																	}
																?></span><?
															}
															if($arParams['DISPLAY_COMPARE']=='Y'){
																?><span class="compare text-nowrap"><?
																	?><a class="js-compare" href="<?=$arItem['COMPARE_URL']?>"><span><?=GetMessage('RS.MONOPOLY.COMPARE')?></span><span class="count"></span></a><?
																?></span><?
															}
														?></div><?
													?></div><?
												?></div><?
											?></div><?
											?><div class="col col-md-<?if($arParams["SIDEBAR"]=='Y'):?>3<?else:?>2<?endif;?> col-sm-3"><?
												?><div class="prices"><div><?
													if( IntVal($arItem['RS_PRICE']['DISCOUNT_DIFF'])>0 ) {
														?><div class="price old"><?=$arItem['RS_PRICE']['PRINT_VALUE']?></div><?
														?><div class="price cool new"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													} else {
														?><div class="price cool"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													}
												?></div></div><?
											?></div><?
											?><div class="col col-md-<?if($arParams["SIDEBAR"]=='Y'):?>3<?else:?>2<?endif;?> col-sm-3 hidden-xs"><?
												?><div class="buybtn"><?
													?><div><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-primary"><?=GetMessage('RS.MONOPOLY.BTN_MORE')?></a></div><?
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
	?><div class="alert alert-info" role="alert"><?=GetMessage('RS.MONOPOLY.NO_PRODUCTS')?></div><?
}

if($arParams['DISPLAY_BOTTOM_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}

$templateData = ob_get_flush();