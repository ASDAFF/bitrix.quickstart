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
	?><div class="row vacancies"><?
		?><div class="col col-md-12"><?
			// filter
			if( is_array($arResult['FILTER']['VALUES']) && count($arResult['FILTER']['VALUES'])>0 ) {
				?><div class="row"><?
					?><div class="col col-md-12 filter"><?
						foreach( $arResult['FILTER']['VALUES'] as $arValue ) {
							?><button class="btn btn-default" type="button" data-filter="<?=htmlspecialcharsbx($arValue['XML_ID'])?>"><?=$arValue['VALUE']?></button><?
						}
						?><button class="btn btn-primary" type="button" data-filter=""><?=GetMessage('RS.MONOPOLY.FILTER_ALL')?></button><?
					?></div><?
				?></div><?
			}
			// /filter
			if($arParams["DISPLAY_TOP_PAGER"]) {
				echo $arResult["NAV_STRING"];
			}
			?><div class="panel-group" role="tablist" aria-multiselectable="true"><?
				$index = 0;
				foreach($arResult["ITEMS"] as $arItem) {
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					
					?><div <?
						?>class="item panel panel-default filter<?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_VACANCY_TYPE']]['VALUE_XML_ID']?>" <?
						?>id="<?=$this->GetEditAreaId($arItem['ID']);?>" <?
						?>><?
						?><div class="panel-heading" role="tab" id="heading<?=$index?>"><?
							?><div class="panel-title roboto"><?
								?><a <?
									?>class="<?if($index>0):?>collapsed<?endif;?>" <?
									?>data-toggle="collapse" <?
									?>data-parent="#accordion<?=$index?>" <?
									?>href="#collapseOne<?=$index?>" <?
									?>aria-expanded="true" <?
									?>aria-controls="collapseOne<?=$index?>" <?
								?>><?
									?><?=$arItem['NAME']?><?
									if( $arParams['RSMONOPOLY_PROP_SIGNATURE']!='' && $arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_SIGNATURE']]['DISPLAY_VALUE']!='' ) {
										?><span class="right"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_SIGNATURE']]['DISPLAY_VALUE']?></span><?
									}
								?></a><?
							?></div><?
						?></div><?
						?><div id="collapseOne<?=$index?>" class="panel-collapse collapse<?if($index==0):?> in<?endif;?>" role="tabpanel" aria-labelledby="heading<?=$index?>"><?
							?><div class="panel-body"><?
								?><?=$arItem['PREVIEW_TEXT']?><br /><br /><?
								?><div class="row"><?
									?><div class="col col-md-6"><?
										?><a class="btn-respond btn btn-primary" href="#vacancyForm" data-vacancy="<?=CUtil::JSEscape($arItem['NAME'])?>"><?=GetMessage('RS.MONOPOLY.BTN_RESPOND_VACANCY')?></a><?
									?></div><?
									?><div class="col col-md-6 yashare"><?
										?><span><?=GetMessage("RS.MONOPOLY.YASHARE")?>:</span><?
										?><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook,twitter,gplus"></div><?
									?></div><?
								?></div><?
							?></div><?
						?></div><?
					?></div><?
					$index++;
				}
			?></div><?
			if($arParams["DISPLAY_BOTTOM_PAGER"]) {
				echo $arResult["NAV_STRING"];
			}
		?></div><?
	?></div><?
}