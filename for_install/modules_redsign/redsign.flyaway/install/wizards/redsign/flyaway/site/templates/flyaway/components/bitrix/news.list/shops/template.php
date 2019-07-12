<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( empty($arParams['RSFLYAWAY_PROP_CITY']) || empty($arParams['RSFLYAWAY_PROP_TYPE']) || empty($arParams['RSFLYAWAY_PROP_COORDINATES']) )
	return;

if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0) {
	if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=$arResult["NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="row shops js-shops"><?
		?><div class="col col-md-12"><?
			?><div class="row"><?

				?><div class="col col-md-3 search_city js-search_city"><?
					?><div class="input-group form"><?
						?><input class="form-control form-item shops-input" type="text" name="city" value="" placeholder="<?=GetMessage('RS.FLYAWAY.SEARCH')?>" /><?
						?><a class="fa fa-times shops-clear_input_btn js-clear-shops-input"></a><?
						?><span class="input-group-addon shops-btn"><i class="fa fa-search"></i></span><?
					?></div><?
				?></div><?

				// filter
				if( is_array($arResult['FILTER']['VALUES']) && count($arResult['FILTER']['VALUES'])>0 ) {
					?><div class="col col-md-9 tabs shops-filter"><?
						?><ul class="nav nav-tabs js-filter"><?
							foreach( $arResult['FILTER']['VALUES'] as $arValue ) {
								?><li class="tabs-item js-btn" data-filter="<?=htmlspecialcharsbx($arValue['XML_ID'])?>"><a class="tabs-item__label" href="javascript:;"><?=$arValue['VALUE']?></a></li><?
							}
							?><li class="tabs-item js-btn" data-filter=""><a class="tabs-item__label" href="javascript:;"><?=GetMessage('RS.FLYAWAY.FILTER_ALL')?></a></li><?
						?></ul><?
					?></div><?
				}
				// /filter
			?></div><?
			?><div class="row"><?
				?><div class="col col-md-3"><?
					?><div class="row"><?
						?><div class="col col-sm-12 col-xs-12"><?
							?><div class="shops_list js-shops_list"><?
								?><ul class="list-unstyled"><?
									foreach($arResult["ITEMS"] as $arItem) {
										?><li class="shops-item js-item" <?
											?>data-coords="<?=$arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_COORDINATES']]['VALUE']?>" <?
											?>data-id="<?=$arItem['ID']?>" <?
											?>data-city="<?=$arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_CITY']]['VALUE_XML_ID']?>" <?
											?>data-type="<?=$arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_TYPE']]['VALUE_XML_ID']?>" <?
										?>><?
											?><div class="shops-name"><?=$arItem['NAME']?></div><?
											?><div class="shops-descr"><?=$arItem['PREVIEW_TEXT']?></div><?
										?></li><?
									}
									?><li class="js-not-found" style="display: none;"><?=GetMessage('RS.FLYAWAY.NOT_FOUND'); ?></li><?
								?></ul><?
							?></div><?
						?></div><?
					?></div><?
				?></div><?
				?><div class="col col-md-9"><?
					?><div class="map"><?
						?><div id="rsYMapShops" style="width:100%;height:350px;"></div><?
					?></div><?
				?></div><?
			?></div><?
		?></div><?
	?></div><?
}

//echo"<pre>";print_r($arResult);echo"</pre>";
