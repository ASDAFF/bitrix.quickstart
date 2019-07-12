<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( $arResult["SECTIONS_COUNT"]>0 ) {
	if( $arParams['RSMONOPOLY_SHOW_BLOCK_NAME']=='Y' && $arParams["RSMONOPOLY_BLOCK_NAME"]!='' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSMONOPOLY_BLOCK_NAME_IS_LINK']=='Y' && $arParams['RSMONOPOLY_BLOCK_LINK']!='' ) {
				?><a href="<?=$arResult['RSMONOPOLY_BLOCK_LINK']?>"><?=$arParams["RSMONOPOLY_BLOCK_NAME"]?></a><?
			} else {
				?><?=$arParams["RSMONOPOLY_BLOCK_NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="<?if($arParams['RSMONOPOLY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> gallery" <?
		?>data-changespeed="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if(IntVal($arParams["RSMONOPOLY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSMONOPOLY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>data-margin="34" <?
		?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_PHONE'])>0?$arParams['RSMONOPOLY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_TABLET'])>0?$arParams['RSMONOPOLY_OWL_TABLET']:1)?>"},"1200":{"items":"<?=(IntVal($arParams['RSMONOPOLY_OWL_PC'])>0?$arParams['RSMONOPOLY_OWL_PC']:1)?>"}}'<?
		?>><?
		foreach ($arResult['SECTIONS'] as $arSection) {
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

			?><div class="item<?if($arParams['RSMONOPOLY_USE_OWL']!='Y'):?> col col-sm-6 col-md-<?=$arParams['RSMONOPOLY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?
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