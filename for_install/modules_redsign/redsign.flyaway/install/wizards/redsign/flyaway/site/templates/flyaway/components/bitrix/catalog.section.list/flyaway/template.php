<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if ($arResult["SECTIONS_COUNT"] > 0) {
	?><div class="row category"><?
		foreach ($arResult['SECTIONS'] as $arSection) {
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

			?><div class="col col-sm-4 col-md-<?=($arParams["SIDEBAR"] == 'Y' ? 4 : 3)?> col-lg-2 category__item" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="category__in"><?
							?><a class="clearfix category__label" href="<?=$arSection['SECTION_PAGE_URL']?>"><?
								?><div class="category__pic"><?
									if ($arSection['PICTURE']['SRC'] != '') {
										?><img border="0" <?
											?>class="category__img" <?
											?>src="<?=$arSection['PICTURE']['SRC']?>" <?
											?>alt="<?=$arSection['PICTURE']['ALT']?>" <?
											?>title="<?=$arSection['PICTURE']['TITLE']?>" <?
										?>/><?
									} else {
										?><img border="0" <?
											?>class="category__img" <?
											?>src="<?=SITE_TEMPLATE_PATH?>/images/img/default-img.png" <?
											?>alt="<?=$arSection['NAME']?>" <?
											?>title="<?=$arSection['NAME']?>" <?
										?>/><?
									}
								?></div><?
								
								?><div class="category__data"><?
									?><div class="category__name"><?=$arSection['NAME']?></div><?
									?><div class="category__description"><?=$arSection['DESCRIPTION']?></div><?
								?></div><?
								
							?></a><?
						?></div><?
					?></div><?
				?></div><?
			?></div><?
		}
	?></div><?
}
