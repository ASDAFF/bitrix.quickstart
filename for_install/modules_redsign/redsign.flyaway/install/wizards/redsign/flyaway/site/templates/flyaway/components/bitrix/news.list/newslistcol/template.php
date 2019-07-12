<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0) {
	if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arResult['LIST_PAGE_URL']!='' ) {
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			} else {
				?><?=empty($arParams['RS_BLOCK_NAME']) ? $arResult["NAME"] : $arParams['RS_BLOCK_NAME']?><?
			}
		?></span></h2><?
	}

	?><div class="newslistcol clearfix"><?
		?><div class="newslistcol-list owl" <?
			?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
			?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
			?>data-margin="24" <?
			?>data-responsive='{<?
				?>"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},<?
				?>"740":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},<?
				?>"1080":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_MID'])>0?$arParams['RSFLYAWAY_OWL_MID']:1)?>"},<?
				?>"1620":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}<?
			?>}'<?
			?>><?
			foreach ($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				?><div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
					?><div class="row"><?
						?><div class="col col-md-12"><?
							if( !empty($arItem['PREVIEW_PICTURE']['SRC'])) {
								?><div class="newslistcol-image"><?
									?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
										?><img u="image" border="0" <?
											?>src="<?=$arItem['PREVIEW_PICTURE']['RESIZE']['src']?>" <?
											?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
											?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
										?>/><?
									?></a><?
								?></div><?
							}
							?><div class="newslistcol-data"><?
								if( $arParams['RFLYAWAY_SHOW_DATE']=='Y' && $arItem['DISPLAY_ACTIVE_FROM']!='' ) {
									?><div class="date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div><?
								}
								?><div class="newslistcol-name"><a class="aprimary roboto" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div><?
								if (strlen($arItem['PREVIEW_TEXT'])> 60):
									$rest = substr($arItem['PREVIEW_TEXT'], 0, 60);
									?><div class="newslistcol-description"><?=$rest;?>...</div><?
								else:
									?><div class="newslistcol-description"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['PREVIEW_TEXT']?></a></div><?
								endif;
							?></div><?
						?></div><?
					?></div><?
				?></div><?
			}
		?></div><?

		if(!empty($arResult['NAV_RESULT']->NavPageCount) && $arResult['NAV_RESULT']->NavPageCount > 1) {
			?><a href="<?=$arResult['LIST_PAGE_URL']?>"><?=Loc::getMessage('SHOW_ALL')?></a><?
		}
	?></div><?

	if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
		echo $arResult["NAV_STRING"];
	}
}
