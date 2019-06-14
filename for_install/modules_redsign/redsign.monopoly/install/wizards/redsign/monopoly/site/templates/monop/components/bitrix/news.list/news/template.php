<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

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
	?><div class="row news"><?
		if($arParams["DISPLAY_TOP_PAGER"]) {
			echo $arResult["NAV_STRING"];
		}
		foreach($arResult["ITEMS"] as $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			
			?><div class="item col col-md-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-sm-3 col-md-2 image"><?
						if( $arItem['PREVIEW_PICTURE']['SRC']!='' ) {
							?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
								?><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
									?> alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
									?> title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
									?>/><?
							?></a><?
						}
					?></div><?
					?><div class="col col-md-10 data"><?
						if( $arParams['DISPLAY_DATE']=='Y' && $arItem['DISPLAY_ACTIVE_FROM']!='' ) {
							?><div class="date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div><?
						}
						?><div class="name aprimary"><a class="aprimary" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div><?
						?><div class="descr"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['PREVIEW_TEXT']?></a></div><?
					?></div><?
				?></div><?
			?></div><?
		}
		if($arParams["DISPLAY_BOTTOM_PAGER"]) {
			echo $arResult["NAV_STRING"];
		}
	?></div><?
}