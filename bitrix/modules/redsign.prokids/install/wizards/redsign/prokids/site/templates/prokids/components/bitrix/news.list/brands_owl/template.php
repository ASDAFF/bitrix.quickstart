<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0)
{
	?><div class="brandslistimgowl1<?if($arParams['ADD_STYLES_FOR_MAIN']=='Y'):?> mainstyles<?endif;?>"><?

		if($arParams['ADD_STYLES_FOR_MAIN']=='Y') {
			?><div class="title"><h1><a href="<?=$arParams['BRAND_PAGE']?>"><?=GetMessage('BRAND_TITLE')?></a></h1></div><?
		}

		?><div id="owl_brandslist1" <?if(count($arResult['ITEMS'])==1):?> class="hidecontrols"<?endif;?>><?
			foreach($arResult['ITEMS'] as $arItem)
			{
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				if($arParams['DISPLAY_PICTURE']!='N' && is_array($arItem['PREVIEW_PICTURE'])) {
					?><div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><div class="in"><?
							?><div class="pic"><?
								?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
									?><img <?
										?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
										?>border="0" <?
										?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
										?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
									?>/><?
								?></a><?
							?></div><?
						?></div><?
					?></div><?
				}
			}
		?></div><?
	?></div><?
	?><script>
		var RSGOPRO_change_speed_brands = <?if(IntVal($arParams["RSGOPRO_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSGOPRO_CHANGE_SPEED"]?><?endif;?>;
		var RSGOPRO_change_delay_brands = <?if(IntVal($arParams["RSGOPRO_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSGOPRO_CHANGE_DELAY"]?><?endif;?>;
	</script><?
}