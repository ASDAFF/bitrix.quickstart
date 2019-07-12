<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):
	if ($arParams['RSFLYAWAY_SHOW_BLOCK_NAME'] == 'Y'):
		?><h2><?
			if ($arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK'] == 'Y' && $arResult['LIST_PAGE_URL'] != ''):
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			else:
				?><?=$arResult["NAME"]?><?
			endif;
		?></h2><?
	endif;

	?><div class="partners clearfix"><?
		?><div class="partners-list <?if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):?>owl<?else:?>row<?endif;?>" <?
			?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]) < 1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
			?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]) < 1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
			?>data-margin="20" <?
			?>data-responsive='{<?
				?>"0": {"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE']) > 0 ? $arParams['RSFLYAWAY_OWL_PHONE'] : 1)?>"},<?
				?>"740": {"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET']) > 0 ? $arParams['RSFLYAWAY_OWL_TABLET'] : 1)?>"},<?
				?>"1080": {"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_MID']) > 0 ? $arParams['RSFLYAWAY_OWL_MID'] : 1)?>"},<?
				?>"1620": {"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC']) > 0 ? $arParams['RSFLYAWAY_OWL_PC'] : 1)?>"}<?
			?>}'<?
			?>><?
			foreach ($arResult["ITEMS"] as $arItem):
				if (empty($arItem['PREVIEW_PICTURE']) || $arItem['PREVIEW_PICTURE']['SRC'] == ''):
					continue;
				endif;
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				?><div class="item<?if ($arParams['RSFLYAWAY_USE_OWL'] != 'Y'):?> col col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
					?><div class="row"><?
						?><div class="col col-md-12"><?
							?><a class="partners__label clearfix" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
								?><div class="row"><?
									?><div class="col col-md-12"><?
										if ($arItem['PREVIEW_PICTURE']['SRC'] != ''):
											?><img class="partners__img" u="image" border="0" <?
												?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
												?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
												?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
											?>/><?
										endif;
									?></div><?
								?></div><?
							?></a><?
						?></div><?
					?></div><?
				?></div><?
			endforeach;
		?></div><?

		if(!empty($arResult['NAV_RESULT']->NavPageCount) && $arResult['NAV_RESULT']->NavPageCount > 1) {
			?><a href="<?=$arResult['LIST_PAGE_URL']?>"><?=Loc::getMessage('SHOW_ALL')?></a><?
		}
	?></div><?

	if ($arParams["DISPLAY_BOTTOM_PAGER"]):
		echo $arResult["NAV_STRING"];
	endif;
endif;
