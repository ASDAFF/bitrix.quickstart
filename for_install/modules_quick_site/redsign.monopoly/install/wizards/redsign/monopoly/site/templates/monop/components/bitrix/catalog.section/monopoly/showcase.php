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
			
			?><div class="item js-element js-elementid<?=$arItem['ID']?> col col-sm-6 col-md-<?if($arParams["SIDEBAR"]=='Y'):?>6<?else:?>4<?endif;?> col-lg-<?if($arParams["SIDEBAR"]=='Y'):?>4<?else:?>3<?endif;?>" <?
				?>data-elementid="<?=$arItem['ID']?>" <?
				?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"<?
				?>><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="in"><?
							?><div class="pic"><?
								?><a class="js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
									if( isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src'])!='' ) {
										?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
									} else {
										?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
									}
								?></a><?
							?></div><?
							?><div class="data"><?
								?><div class="name"><?
									?><a class="aprimary" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br /><?
								?></div><?
								?><div class="row buy"><?
									?><div class="col col-xs-6 prices"><?
										if( IntVal($arItem['RS_PRICE']['DISCOUNT_DIFF'])>0 ) {
											?><div class="price old"><?=$arItem['RS_PRICE']['PRINT_VALUE']?></div><?
											?><div class="price cool new"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
										} else {
											?><div class="price cool"><?=$arItem['RS_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
										}
									?></div><?
									?><div class="col col-xs-6 text-right buybtn"><?
										?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-primary"><?=GetMessage('RS.MONOPOLY.BTN_MORE')?></a><?
									?></div><?
								?></div><?
								?><div class="row bot"><?
									?><div class="col col-xs-6 compare"><?
										if($arParams['DISPLAY_COMPARE']=='Y'){
											?><a class="js-compare" href="<?=$arItem['COMPARE_URL']?>"><span><?=GetMessage('RS.MONOPOLY.COMPARE')?></span><span class="count"></span></a><?
										}
									?></div><?
									if( $arParams['RSMONOPOLY_PROP_QUANTITY']!='' ) {
										?><div class="col col-xs-6 text-right stores"><?
											?><?=GetMessage('RS.MONOPOLY.QUANTITY')?>:<?
											if( IntVal($arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_QUANTITY']]['VALUE'])<1 ) {
												?><span class="empty"> <?=GetMessage('RS.MONOPOLY.QUANTITY_EMPTY')?></span><?
											} else {
												?><span class="isset"> <?=GetMessage('RS.MONOPOLY.QUANTITY_ISSET')?></span><?
											}
										?></div><?
									}
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

// echo"<pre>";print_r($arResult['ITEMS'][0]);echo"</pre>";

if($arParams['DISPLAY_BOTTOM_PAGER']=='Y') {
	echo $arResult['NAV_STRING'];
}

$templateData = ob_get_flush();