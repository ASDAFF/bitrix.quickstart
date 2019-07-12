<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?><div class="row"><?
	?><div class="col-md-12"><? 
		if( $arParams['RSFLYAWAY_PROP_FILE']!='' && is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ) {
			if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' ) {
				?><h2 class="coolHeading"><span class="secondLine"><?
					if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
						?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
					} else {
						?><?=$arResult["NAME"]?><?
					}
				?></span></h2><?
			}
			?><div class="<?if($arParams['RSFLYAWAY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> docs" <?
				?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
				?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
				?>data-margin="24" <?
				?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},"991":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}}'<?
				?>><?
				foreach($arResult["ITEMS"] as $arItem) {

					if( !is_array($arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_FILE']]['VALUE']) ) {
						continue;
					}
					$arFields = current($arItem['PROPERTIES'][$arParams['RSFLYAWAY_PROP_FILE']]['VALUE']);

					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

					?><div class="docs-item<?if($arParams['RSFLYAWAY_USE_OWL']!='Y'):?> col col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><div class="row"><?
							?><div class="col col-md-12"><?
								?><div class="docs-image"><?
									if($arFields) {
										?><a href="<?=$arFields['FULL_PATH']?>"><?
									}
									if( $arItem['PREVIEW_PICTURE']['SRC']!='' ) {
										?><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" /><?
									} else {
										?><img src="<?=$templateFolder?>/img/pic.jpg" alt="<?=$arFields['DESCRIPTION']?>" title="<?=$arFields['DESCRIPTION']?>" /><?
									}
									if($arFields) {
										?></a><?
									}
								?></div><?
								?><div class="docs-data"><?
									?><div class="docs-info<?if($arFields):?> smaller<?endif;?>"><?
										?><div class="docs-name"><?
											if($arFields) {
												?><a class="docs-aprimary robotolight" href="<?=$arFields['FULL_PATH']?>"><?=$arItem["NAME"]?></a><?
											} else {
												?><span class="docs-aprimary"><?=$arItem["NAME"]?></span><?
											}
										?></div><?
										if( $arItem['PREVIEW_TEXT']!='' ) {
											?><div class="docs-descr"><?=$arItem["PREVIEW_TEXT"]?></div><?
										}
									?></div><?
									if($arFields) {
										?><div class="docs-dl"><?
											?><a href="<?=$arFields['FULL_PATH']?>"><?=GetMessage("RSFLYAWAY_DS_DL")?>: <?=strtoupper($arFields['EXTENSION'])?>, <?=$arFields['SIZE']?></a><?
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
	?></div><?
?></div><?