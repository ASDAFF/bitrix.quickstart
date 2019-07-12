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
	
	?><div class="reviews clearfix"><?
		?><div class="reviews-list<?if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):?> owl<?else:?> row<?endif;?>" <?
			?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
			?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
			?>data-margin="0" <?
			?>data-responsive='{<?
				?>"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE']) > 0 ? $arParams['RSFLYAWAY_OWL_PHONE'] : 1)?>"},<?
				?>"740":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET']) > 0 ? $arParams['RSFLYAWAY_OWL_TABLET'] : 1)?>"},<?
				?>"1080":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC']) > 0 ? $arParams['RSFLYAWAY_OWL_PC'] : 1)?>"}<?
			?>}'<?
		?>><?
			foreach ($arResult["ITEMS"] as $arItem):

				if ($arItem['PREVIEW_TEXT'] == ''):
					continue;
				endif;

				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				?><div class="item reviews__item<?if ($arParams['RSFLYAWAY_USE_OWL'] != 'Y'):?> col col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
					?><div class="row"><?
						?><div class="<?if ($arParams['RSFLYAWAY_USE_OWL'] != 'Y'):?>col col-xs-12<?endif;?>"><?
							?><div class="col col-xs-12 col-sm-4 col-md-4<?if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):?> col-lg-4<?else:?> col-lg-3<?endif;?> reviews__user"><?
								?><div class="reviews__image"><?
									?><span class="reviews__image-avatar"><?
										if ($arItem['PREVIEW_PICTURE']['SRC'] != ''):
											?><img u="image" border="0" <?
												?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
												?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
												?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
											?>/><?
										else:
											?><img u="image" border="0" <?
												?>src="<?=$templateFolder."/img/nopic.jpg"?>" <?
												?>alt="<?=$arItem['NAME']?>" <?
												?>title="<?=$arItem['NAME']?>" <?
											?>/><?
										endif;
									?></span><?
								?></div><?
								
								?><div class="reviews__info"><?
									if ($arItem['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_AUTHOR_NAME']]['DISPLAY_VALUE'] != ''):
										?><div class="reviews__user-name"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_AUTHOR_NAME']]['DISPLAY_VALUE']?></div><?
									endif;
									
									if ($arItem['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_AUTHOR_JOB']]['DISPLAY_VALUE'] != ''):
										?><div class="reviews__mail"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSFLYAWAY_AUTHOR_JOB']]['DISPLAY_VALUE']?></div><?
									endif;
								?></div><?
							?></div><?
							
							?><div class="col col-xs-12 col-sm-8 col-md-8<?if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):?> col-lg-8<?else:?> col-lg-9<?endif;?> reviews__rating"><?
								?><div class="in"><?=$arItem['PREVIEW_TEXT']?></div><?
							?></div><?
						?></div><?
					?></div><?
				?></div><?
			endforeach;
		?></div><?
	
		if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):
			?><a href="<?=SITE_DIR.'about/reviews/'?>"><?=Loc::getMessage('ALL_REVIEWS')?></a><?
		endif;
	?></div><?
	
	if ($arParams["DISPLAY_BOTTOM_PAGER"]):
		echo $arResult["NAV_STRING"];
	endif;
endif;
