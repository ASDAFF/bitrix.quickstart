<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):
	if ($arParams['RS_SHOW_BLOCK_NAME'] == 'Y'):
		?><h2 class="coolHeading"><span class="secondLine"><?
			if ($arParams['RS_BLOCK_NAME_IS_LINK'] == 'Y' && $arResult['LIST_PAGE_URL'] != ''):
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			else:
				?><?=$arResult["NAME"]?><?
			endif;
		?></span></h2><?
	endif;

	?><div class="<?if ($arParams['RS_USE_OWL'] == 'Y'):?>owl<?else:?>row<?endif;?> features" <?
		?>data-changespeed="<?if (IntVal($arParams["RS_OWL_CHANGE_SPEED"]) < 1) : ?>2000<?else:?><?=$arParams["RS_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if (IntVal($arParams["RS_OWL_CHANGE_DELAY"]) < 1) : ?>8000<?else:?><?=$arParams["RS_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>data-margin="20" <?
		?>data-responsive='{<?
			?>"0": {"items":"<?=(IntVal($arParams['RS_OWL_PHONE']) > 0 ? $arParams['RS_OWL_PHONE'] : 1)?>"},<?
			?>"740": {"items":"<?=(IntVal($arParams['RS_OWL_TABLET']) > 0 ? $arParams['RS_OWL_TABLET'] : 1)?>"},<?
			?>"1080": {"items":"<?=(IntVal($arParams['RS_OWL_PC']) > 0 ? $arParams['RS_OWL_PC'] : 1)?>"},<?
			?>"1440": {"items": "4" }<?
		?>}'<?
		?>><?
		foreach ($arResult["ITEMS"] as $arItem):
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			?><div class="features__item item<?if ($arParams['RS_USE_OWL']!='Y'):?> col col-md-<?=$arParams['RS_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><a class="features__label clearfix" href="<?=$arItem['PROPERTIES'][$arParams['RS_LINK']]['VALUE']?>"<?if ($arItem['PROPERTIES'][$arParams['RS_BLANK']]['VALUE']!=''):?> target="_blank"<?endif;?>><?
							?><div class="features__pic"><?
								if ($arItem['PREVIEW_PICTURE']['SRC'] != ''):
									?><img class="features__img" u="image" border="0" <?
										?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
										?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
										?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
									?>/><?
								else:
									?><img class="features__img" u="image" border="0" <?
										?>src="<?=$templateFolder."/img/nopic.jpg"?>" <?
										?>alt="<?=$arItem['NAME']?>" <?
										?>title="<?=$arItem['NAME']?>" <?
									?>/><?
								endif;
							?></div><?
							?><div class="features__data"><?
								?><div class="features__name"><?=$arItem['NAME']?></div><?
								?><div class="features__description"><?=$arItem['PREVIEW_TEXT']?></div><?
							?></div><?
						?></a><?
					?></div><?
				?></div><?
			?></div><?
		endforeach;
	?></div><?
	if ($arParams["DISPLAY_BOTTOM_PAGER"]):
		echo $arResult["NAV_STRING"];
	endif;
endif;
