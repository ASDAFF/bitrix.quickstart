<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( $arResult["SECTIONS_COUNT"]>0 ) {
	?><div class="row gallery"><?
		foreach ($arResult['SECTIONS'] as $arSection) {
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

			?><div class="item col col-sm-6 col-md-<?if($arParams["SIDEBAR"]=='Y'):?>6<?else:?>4<?endif;?> col-lg-<?if($arParams["SIDEBAR"]=='Y'):?>4<?else:?>3<?endif;?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?
				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><a class="clearfix" href="<?=$arSection['SECTION_PAGE_URL']?>"><?
							?><div class="row image"><?
								?><div class="col col-md-12"><?
									if( $arSection['PICTURE']['SRC']!='' ) {
										?><img u="image" border="0" <?
											?>src="<?=$arSection['PICTURE']['SRC']?>" <?
											?>alt="<?=$arSection['PICTURE']['ALT']?>" <?
											?>title="<?=$arSection['PICTURE']['TITLE']?>" <?
										?>/><?
									} else {
										?><img u="image" border="0" <?
											?>src="<?=$templateFolder."/img/nopic.jpg"?>" <?
											?>alt="<?=$arSection['NAME']?>" <?
											?>title="<?=$arSection['NAME']?>" <?
										?>/><?
									}
								?></div><?
							?></div><?
							?><div class="row"><?
								?><div class="col col-md-12 info"><?
									?><div class="data"><?
										?><div class="name aprimary"><?=$arSection['NAME']?></div><?
										if($arParams['RSMONOPOLY_SHOW_DESC_IN_SECTION'] == "Y") {
                                            ?><div class="descr"><?=strip_tags($arSection['DESCRIPTION'], '<p><br><br/><span>');?></div><?
                                        }
									?></div><?
								?></div><?
							?></div><?
						?></a><?
					?></div><?
				?></div><?
			?></div><?
		}
	?></div><?
}