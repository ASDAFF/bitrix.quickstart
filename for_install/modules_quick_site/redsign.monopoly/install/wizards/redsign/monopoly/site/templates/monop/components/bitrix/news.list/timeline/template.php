<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
	if( $arParams['RSMONOPOLY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSMONOPOLY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=$arResult["NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="row timeline"><?
		foreach($arResult["ITEMS"] as $key => $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			?><div class="item col col-sm-6" id="<?=$this->GetEditAreaId($arItem['ID']);?>" data-key="<?=$key?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><a class="body clearfix" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
							?><div class="row image"><?
								?><div class="col col-md-12"><?
									if( $arItem['PREVIEW_PICTURE']['SRC']!='' ) {
										?><img u="image" border="0" <?
											?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
											?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
											?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
										?>/><?
									} else {
										?><img u="image" border="0" <?
											?>src="<?=$templateFolder."/img/nopic.jpg"?>" <?
											?>alt="<?=$arItem['NAME']?>" <?
											?>title="<?=$arItem['NAME']?>" <?
										?>/><?
									}
								?></div><?
							?></div><?
							?><div class="row data"><?
								?><div class="col col-md-12 name aprimary"><?=$arItem['NAME']?></div><?
								if( $arItem['DISPLAY_ACTIVE_FROM']!='' ) {
									?><div class="col col-md-12 date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div><?
								}
								?><div class="col col-md-12"><?=$arItem['PREVIEW_TEXT']?></div><?
							?></div><?
						?></a><?
					?></div><?
				?></div><?
				?><div class="pointer <?if(($key+1)%2==false):?>left<?else:?>right<?endif;?>"><div></div><span></span></div><?
			?></div><?
		}
	?></div><?
	if($arParams["DISPLAY_BOTTOM_PAGER"]) {
		echo $arResult["NAV_STRING"];
	}
}