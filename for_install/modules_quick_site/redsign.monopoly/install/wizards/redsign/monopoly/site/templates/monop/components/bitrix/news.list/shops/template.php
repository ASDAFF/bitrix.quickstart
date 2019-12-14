<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( empty($arParams['RSMONOPOLY_PROP_CITY']) || empty($arParams['RSMONOPOLY_PROP_TYPE']) || empty($arParams['RSMONOPOLY_PROP_COORDINATES']) )
	return;

if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0) {
	if( $arParams['RSMONOPOLY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSMONOPOLY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=$arResult["NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="row shops"><?
		?><div class="col col-md-12"><?
			?><div class="row"><?
				// cities
				if( is_array($arResult['CITIES']['VALUES']) && count($arResult['CITIES']['VALUES'])>0 ) {
					?><div class="col col-md-3 search_city"><?
						?><div class="input-group"><?
							?><input class="form-control" type="text" name="city" value="" placeholder="<?=GetMessage('RS.MONOPOLY.CITY')?>" /><?
							?><span class="input-group-addon"><i class="fa"></i></span><?
						?></div><?
						?><ul class="dropdown-menu list-unstyled cities_list"><?
							foreach( $arResult['CITIES']['VALUES'] as $arValue ) {
								?><li class="item"><?
									?><a href="#" data-filter="<?=htmlspecialcharsbx($arValue['XML_ID'])?>"><?=$arValue['VALUE']?></a><?
								?></li><?
							}
						?></ul><?
					?></div><?
				}
				// /cities
				// filter
				if( is_array($arResult['FILTER']['VALUES']) && count($arResult['FILTER']['VALUES'])>0 ) {
					?><div class="col col-md-9 filter"><?
						foreach( $arResult['FILTER']['VALUES'] as $arValue ) {
							?><button class="btn btn-default" type="button" data-filter="<?=htmlspecialcharsbx($arValue['XML_ID'])?>"><?=$arValue['VALUE']?></button><?
						}
						?><button class="btn btn-primary" type="button" data-filter=""><?=GetMessage('RS.MONOPOLY.FILTER_ALL')?></button><?
					?></div><?
				}
				// /filter
			?></div><?
			?><div class="row"><?
				?><div class="col col-md-3"><?
					?><div class="row"><?
						?><div class="col col-sm-12 col-xs-9"><?
							?><div class="shops_list"><?
								?><ul class="list-unstyled"><?
									foreach($arResult["ITEMS"] as $arItem) {
										?><li class="item" <?
											?>data-coords="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_COORDINATES']]['VALUE']?>" <?
											?>data-id="<?=$arItem['ID']?>" <?
											?>data-city="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_CITY']]['VALUE_XML_ID']?>" <?
											?>data-type="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_TYPE']]['VALUE_XML_ID']?>" <?
										?>><?
											?><div class="name"><?=$arItem['NAME']?></div><?
											?><div class="descr"><?=$arItem['PREVIEW_TEXT']?></div><?
										?></li><?
									}
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