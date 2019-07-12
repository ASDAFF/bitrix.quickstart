<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="row">
	<div class="col col-md-9"><?
		if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
			if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' ) {
				?><h2 class="coolHeading"><span class="secondLine"><?
					if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
						?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
					} else {
						?><?=$arResult["NAME"]?><?
					}
				?></span></h2><?
			}
			?><div class="<?if($arParams['RSFLYAWAY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> services" <?
				?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
				?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
				?>data-margin="13" <?
				?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},"991":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}}'<?
				?>><?
				foreach($arResult["ITEMS"] as $arItem) {
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

					?><div class="services-item<?if($arParams['RSFLYAWAY_USE_OWL']!='Y'):?> col col-sm-6 col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><div class="row"><?
							?><div class="col col-md-12"><?
								?><a class="clearfix" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
									?><div class="row services-image"><?
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
									?><div class="row services-data"><?
										?><div class="col col-md-12"><div class="services-name"><?=$arItem['NAME']?></div></div><?
										?><div class="col col-md-12"><div class="services-description"><?=$arItem['PREVIEW_TEXT']?></div></div><?
									?></div><?
								?></a><?
							?></div><?
						?></div><?
					?></div><?
				}
			?></div><?
			if($arParams["DISPLAY_BOTTOM_PAGER"]) {
				echo $arResult["NAV_STRING"];
			}
		}
	?></div>
</div>
