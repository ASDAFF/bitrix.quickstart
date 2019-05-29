<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

// pictures
$arImages = array();
if( is_array($arResult["DETAIL_PICTURE"]) ) {
	$arImages[] = $arResult['DETAIL_PICTURE'];
}
if(is_array($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE']) && count($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'])>0) {
	foreach($arResult["PROPERTIES"][$arParams['RSMONOPOLY_PROP_MORE_PHOTO']]['VALUE'] as $arImage) {
		$arImages[] = $arImage;
	}
}

?><div class="detailGallery"><?
	
	?><div class="row"><?
		if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
			?><div class="col col-md-12 activefrom"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div><?
		}
		// general picture
		?><div class="col col-md-12 pic"><?
			if(is_array($arImages) && count($arImages)>0) {
				?><a class="fancyajax changeFromSlider fancybox.ajax" href="<?=$arResult["DETAIL_PAGE_URL"]?>" title="<?=$arResult["NAME"]?>"><?
			}
			if(is_array($arImages[0]) && isset($arImages[0]['SRC'])>0) {
				?><img <?
					?>src="<?=$arImages[0]['SRC']?>" <?
					?>alt="<?=($arImages[0]['ALT']!='' ? $arImages[0]['ALT'] : $arResult['NAME'])?>" <?
					?>title="<?=($arImages[0]['TITLE']!='' ? $arImages[0]['TITLE'] : $arResult['NAME'])?>" <?
				?>/><?
			}
			if(is_array($arImages) && count($arImages)>0) {
				?></a><?
			}
		?></div><?
		// slider
		if(is_array($arImages) && count($arImages)>0) {
			?><div class="col col-md-12"><?
				?><div class="thumbs" data-changeto=".changeFromSlider img"><?
					?><div class="owlslider"><?
						$index = 0;
						foreach($arImages as $arImage) {
							?><div class="pic<?=$index?><?if($index<1):?> checked<?endif;?> thumb"><?
								?><a href="<?=$arImage['SRC']?>" data-index="<?=$index?>" style="background-image: url('<?=$arImage['RESIZE']['src']?>');"><?
									?><div class="overlay"></div><?
									?><i class="fa"></i><?
								?></a><?
							?></div><?
							$index++;
						}
					?></div><?
				?></div><?
			?></div><?
		}

		?><div class="col col-md-12 text"><?=$arResult["DETAIL_TEXT"]?></div><?

	?></div><?

?></div>