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

?><div class="overflower popupgallery js-gallery"><?
	?><div class="row"><?
		?><div class="col col-md-12"><?

			?><div class="row"><?

				// general picture
				?><div class="col col-sm-9"><?
					?><div class="navigations"><?
						?><div class="around_changeit"><?
							?><div class="changeit"><?
								if(is_array($arImages[0]) && isset($arImages[0]['SRC'])>0) {
									?><img src="<?=$arImages[0]['SRC']?>" alt="" title="" /><?
									if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
										?><span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span><?
									}
								}
							?></div><?
						?></div><?
						?><div class="nav prev js-nav"><span></span></div><?
						?><div class="nav next js-nav"><span></span></div><?
					?></div><?
					if(is_array($arImages) && count($arImages)>0) {
						?><div class="description"><?=$arImages[0]['DESCRIPTION']?></div><?
					}
				?></div><?

				// other pictures
				?><div class="col col-sm-3 fullright"><?
					?><div class="preview"><?=$arResult['PREVIEW_TEXT']?></div><?
					if(is_array($arImages) && count($arImages)>0) {
						?><div class="thumbs style1" data-changeto=".changeit img"><?
							$index = 0;
							foreach($arImages as $arImage) {
								?><div class="pic<?=$arImage["ID"]?><?if($index<1):?> checked<?endif;?> thumb"><?
									?><a <?
										?>href="<?=$arImage['SRC']?>" <?
										?>data-index="<?=$arImage["ID"]?>" <?
										?>data-descr="<?=CUtil::JSEscape($arImage['DESCRIPTION'])?>" <?
										?>style="background-image: url('<?=$arImage['RESIZE']['src']?>');" <?
									?>><?
										?><div class="overlay"></div><?
										?><i class="fa"></i><?
									?></a><?
								?></div><?
								$index++;
							}
						?></div><?
					}
				?></div><?

			?></div><?

		?></div><?
	?></div><?
?></div>