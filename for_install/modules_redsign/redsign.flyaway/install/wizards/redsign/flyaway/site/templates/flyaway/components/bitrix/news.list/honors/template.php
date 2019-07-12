<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
	if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=$arResult["NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="<?if($arParams['RSFLYAWAY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> honors" <?
		?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>data-margin="24" <?
		?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},"991":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}}'<?
		?>><?
		foreach($arResult["ITEMS"] as $arItem) {

			if( !is_array($arItem['PREVIEW_PICTURE']) || !is_array($arItem['DETAIL_PICTURE']) ) {
				continue;
			}

			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			?><div class="honors-item<?if($arParams['RSFLYAWAY_USE_OWL']!='Y'):?> col col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="honors-image"><?
							?><a href="<?=$arItem['DETAIL_PICTURE']['SRC']?>" target="_blank"><?
								?><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" /><?
							?></a><?
						?></div><?
						?><div class="honors-data"><?
							?><div class="honors-info<?if( $arParams['RSFLYAWAY_SHOW_DATE']=='Y' && $arItem['DISPLAY_ACTIVE_FROM']!='' ):?> smaller<?endif;?>"><?
								?><div class="honors-name"><a class="aprimary robotolight" href="<?=$arItem['DETAIL_PICTURE']['SRC']?>" target="_blank"><?=$arItem["NAME"]?></a></div><?
								?><div class="honors-descr"><?=$arItem["PREVIEW_TEXT"]?></div><?
							?></div><?
							if( $arParams['RSFLYAWAY_SHOW_DATE']=='Y' && $arItem['DISPLAY_ACTIVE_FROM']!='' ) {
								?><div class="honors-date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div><?
							}
						?></div><?
					?></div><?
				?></div><?
			?></div><?
		}
	?></div><?
	if($arParams["DISPLAY_BOTTOM_PAGER"]) {
		echo $arResult["NAV_STRING"];
	}
}