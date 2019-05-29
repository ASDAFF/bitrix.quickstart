<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( $arParams['RSMONOPOLY_PROP_FILE']!='' && is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
	if( $arParams['RSMONOPOLY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSMONOPOLY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=$arResult["NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="<?if($arParams['RSMONOPOLY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> docs" <?
		?>data-changespeed="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>data-margin="24" <?
		?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_PHONE'])>0?$arParams['RSMONOPOLY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_TABLET'])>0?$arParams['RSMONOPOLY_OWL_TABLET']:1)?>"},"991":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_PC'])>0?$arParams['RSMONOPOLY_OWL_PC']:1)?>"}}'<?
		?>><?
		foreach($arResult["ITEMS"] as $arItem) {

			if( !is_array($arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_FILE']]['VALUE']) ) {
				continue;
			}
			$arFields = current($arItem['PROPERTIES'][$arParams['RSMONOPOLY_PROP_FILE']]['VALUE']);

			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			?><div class="item<?if($arParams['RSMONOPOLY_USE_OWL']!='Y'):?> col col-md-<?=$arParams['RSMONOPOLY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="image"><?
							if($arFields) {
								?><a href="<?=$arFields['FULL_PATH']?>"><?
							}
							if( $arItem['PREVIEW_PICTURE']['SRC']!='' ) {
								?><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" /><?
							} else {
								?><img src="<?=$templateFolder?>/img/pic.jpg" alt="<?=$arFields['DESCRIPTION']?>" title="<?=$arFields['DESCRIPTION']?>" /><?
								if($arFields) {
									?><span><?=$arFields['EXTENSION']?></span<?
								}
							}
							if($arFields) {
								?></a><?
							}
						?></div><?
						?><div class="data"><?
							?><div class="info<?if($arFields):?> smaller<?endif;?>"><?
								?><div class="name"><?
									if($arFields) {
										?><a class="aprimary robotolight" href="<?=$arFields['FULL_PATH']?>"><?=$arItem["NAME"]?></a><?
									} else {
										?><span class="aprimary"><?=$arItem["NAME"]?></span><?
									}
								?></div><?
								if( $arItem['PREVIEW_TEXT']!='' ) {
									?><div class="descr"><?=$arItem["PREVIEW_TEXT"]?></div><?
								}
							?></div><?
							if($arFields) {
								?><div class="dl"><?
									?><a href="<?=$arFields['FULL_PATH']?>"><?=GetMessage("RSMONOPOLY_DS_DL")?>: <?=strtoupper($arFields['EXTENSION'])?>, <?=$arFields['SIZE']?></a><?
								?></div><?
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