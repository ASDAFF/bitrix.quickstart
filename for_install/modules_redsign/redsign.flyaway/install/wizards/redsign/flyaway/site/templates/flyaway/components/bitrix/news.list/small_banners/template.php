<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):

	if ($arParams['RS_SHOW_BLOCK_NAME'] == 'Y'):
		?><h2 class="coolHeading"><span class="secondLine"><?
			if ( $arParams['RS_BLOCK_NAME_IS_LINK'] == 'Y' && $arResult['LIST_PAGE_URL'] != ''):
				?><a href="<?=( str_replace('//','/', str_replace('#SITE_DIR#',SITE_DIR,$arResult['LIST_PAGE_URL']) ) )?>"><?=$arResult["NAME"]?></a><?
			else:
				?><?=$arResult["NAME"]?><?
			endif;
		?></span></h2><?
	endif;
	?><div class="row"><?
		?><div class="smallbanners clearfix<?php if(!empty($arParams['RS_IS_HOVER_SCALE']) && $arParams['RS_IS_HOVER_SCALE'] == 'Y') echo ' is-hover-scale';?>"><?
			foreach ($arResult["ITEMS"] as $key => $arItem):

				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				if (!empty($arItem['PREVIEW_PICTURE']) && !empty($arItem['PREVIEW_PICTURE']['SRC'])):
					?><div class="smallbanners__item col col-xs-6  col-sm-4<?
						if($arItem["PROPERTIES"][$arParams["RS_TYPE_BANNER"]]["VALUE"] == "Y") {
							echo ' col-md-4 smallbanners__item_wide';
						} else {
							echo ' col-md-2';
						}
					?>" <?
					?>id="<?=$this->GetEditAreaId($arItem['ID']);?>"<?
					?>><?
						?><div class="row"><?
							?><div class="col col-xs-12"><?
								?><a class="smallbanners__label"<?
									?>href="<?=isset($arItem['DISPLAY_PROPERTIES'][$arParams['RS_LINK']]['DISPLAY_VALUE'])
												? htmlspecialcharsbx($arItem['PROPERTIES'][$arParams['RS_LINK']]['VALUE']) : '/' ?>"<?
								?>><?
									?><img class="smallbanners__img" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>"><?
									if (isset($arItem['DISPLAY_PROPERTIES'][$arParams['RS_TEXT']]['DISPLAY_VALUE']) || isset($arItem['DISPLAY_PROPERTIES'][$arParams['RS_TEXT_PRICE']]['DISPLAY_VALUE'])):
										?><div class="smallbanners__info"><?
											?><div class="smallbanners__decor"></div><?
											?><div class="smallbanners__name"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RS_TEXT']]['DISPLAY_VALUE']?></div><?
											?><div class="smallbanners__price"><?
												?><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RS_TEXT_PRICE']]['DISPLAY_VALUE']?><?
											?></div><?
										?></div><?
									endif;
								?></a><?
							?></div><?
						?></div><?
					?></div><?
				endif;
			endforeach;
		?></div><?
	?></div><?
endif;
