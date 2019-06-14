<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
	?><div class="owl_banners banners owl_banners_colors" <?
		?>data-changespeed="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>style="overflow: hidden; max-height: 1px;" <?
		?>><?
		foreach($arResult["ITEMS"] as $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			
			$bannerType = 'banner';
			if($arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_TYPE']]['VALUE_XML_ID']=='text') {
				$bannerType = 'text';
			} elseif(
				$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_TYPE']]['VALUE_XML_ID']=='product' &&
				$arItem['PROPERTIES'][$arParams['RSMONOPOLY_LINK']]['VALUE'] != ''
			) {
				$bannerType = 'product';
			} elseif(
				$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_TYPE']]['VALUE_XML_ID']=='video' &&
				$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_MP4']]['FILE_PATH_MP4']!='' &&
				$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM']!=''
			) {
				$bannerType = 'video';
			}

			?><div class="item <?=$bannerType?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><a href="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_LINK']]['VALUE']?>"<?if($arItem['PROPERTIES'][$arParams['RSMONOPOLY_BLANK']]['VALUE']!=''):?> target="_blank"<?endif;?>><?
					if( $bannerType=='text' ) {
						?><img u="image" border="0" <?
							?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
							?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
							?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
						?>/><?
						?><div class="container abs"><?
							?><div class="info"><?
								if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_1']]['VALUE'])) {
									?><div class="name robotolight"><p><span class="aprimary"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_1']]['DISPLAY_VALUE']?></span></p></div><br /><?
								}
								if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_2']]['VALUE'])) {
									?><div class="descr robotolight"><p><span><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_2']]['DISPLAY_VALUE']?></p></div><?
								}
							?></div><?
						?></div><?
					} elseif( $bannerType=='product' ) {
						?><div class="container"><?
							?><div class="info"><?
								if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_1']]['VALUE'])) {
									?><div class="name robotolight"><p><span class="aprimary"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_TEXT_1']]['DISPLAY_VALUE']?></p></span></div><?
								}
								if($arItem['PREVIEW_TEXT']!='') {
									?><div class="descr robotolight"><p><span><?=$arItem['PREVIEW_TEXT']?></p></span></div><?
								}
								if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PRICE']]['VALUE'])) {
									?><div class="buy"><?
										?><span class="price"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PRICE']]['DISPLAY_VALUE']?></span><?
										?><button type="button" class="btn btn-default"><?=GetMessage('RSMONOPOLY_BTN_BUY')?></button><?
									?></div><?
								}
							?></div><?
							?><div class="pic"><?
								?><img u="image" border="0" <?
									?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
									?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
									?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
								?>/><?
							?></div><?
						?></div><?
					} elseif( $bannerType=='video' ) {
						?><video id="video<?=$arItem['ID']?>" autoplay="autoplay" muted="muted" loop="loop" poster="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"><?
							?><source src="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_MP4']]['FILE_PATH_MP4']?>" type="video/mp4"><?
							?><source src="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM']?>" type="video/webm"><?
						?></video><?
					} else {
						if($arParams['RSMONOPOLY_FIX_HEIGHT']!='Y') {
							?><img u="image" border="0" <?
								?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
								?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
								?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
							?>/><?
						}
					}
				?></a><?
			?></div><?
		}
		
	?></div><?
}