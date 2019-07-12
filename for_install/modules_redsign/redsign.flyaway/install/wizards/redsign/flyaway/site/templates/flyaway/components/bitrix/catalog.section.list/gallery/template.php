<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');
$strSectionDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE');
$arSectionDeleteParams = array('CONFIRM' => GetMessage('RS.ONEAIR.ELEMENT_DELETE_CONFIRM'));

?>


<?php if ($arResult["SECTIONS_COUNT"] > 0 ): ?>
	<?php if ($arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' && $arParams["RSFLYAWAY_BLOCK_NAME"]!=''): ?>
		<h2 class="coolHeading"><span class="secondLine">
			<?php if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arParams['RSFLYAWAY_BLOCK_LINK']!='' ): ?>
				<a href="<?=$arResult['RSFLYAWAY_BLOCK_LINK']?>"><?=$arParams["RSFLYAWAY_BLOCK_NAME"]?></a>
			<?php else: ?>
				<?=$arParams["RSFLYAWAY_BLOCK_NAME"]?>
			<?php endif; ?>
		</span></h2>
	<?php endif; ?>
<?php endif; ?>
	<div
		class="<?if ($arParams['RSFLYAWAY_USE_OWL'] == 'Y'):?>owl<?else:?>row<?endif;?> category clearfix"
		data-changespeed="<?if(intval($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]) < 1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>"
		data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>"
		data-margin="20"
		data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},"1200":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}}'
	>
	<?php
	foreach ($arResult['SECTIONS'] as $arSection):
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	?>
		<div class="item category__item<?if ($arParams['RSFLYAWAY_USE_OWL']!='Y'):?> col col-sm-6 col-md-4 col-lg-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
			<div class="row">
				<div class="col col-md-12">
					<div class="category__in">
						<a class="clearfix category__label" href="<?=$arItem['DETAIL_PAGE_URL']?>">
							<div class="category__pic">
								<?php if( $arSection['PICTURE']['SRC']!='' ): ?>
									<img u="image" border="0"
										src="<?=$arSection['PICTURE']['SRC']?>"
										alt="<?=$arSection['PICTURE']['ALT']?>"
										title="<?=$arSection['PICTURE']['TITLE']?>"
									>
								<?php else: ?>
									<img u="image" border="0"
										src="<?=$templateFolder."/img/nopic.jpg"?>"
										alt="<?=$arSection['NAME']?>"
										title="<?=$arSection['NAME']?>"
									>
								<?php endif; ?>
							</div>
							<div class="category__data">
								<div class="category__name"><?=$arSection['NAME']?></div>
								<div class="descr"><?=strip_tags($arSection['DESCRIPTION'], '<p><br><br/><span>');?></div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>

	<?php endforeach; ?>

	</div>

<?php
/**<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( $arResult["SECTIONS_COUNT"]>0 ) {
	if( $arParams['RSFLYAWAY_SHOW_BLOCK_NAME']=='Y' && $arParams["RSFLYAWAY_BLOCK_NAME"]!='' ) {
		?><h2 class="coolHeading"><span class="secondLine"><?
			if( $arParams['RSFLYAWAY_BLOCK_NAME_IS_LINK']=='Y' && $arParams['RSFLYAWAY_BLOCK_LINK']!='' ) {
				?><a href="<?=$arResult['RSFLYAWAY_BLOCK_LINK']?>"><?=$arParams["RSFLYAWAY_BLOCK_NAME"]?></a><?
			} else {
				?><?=$arParams["RSFLYAWAY_BLOCK_NAME"]?><?
			}
		?></span></h2><?
	}
	?><div class="<?if($arParams['RSFLYAWAY_USE_OWL']=='Y'):?>owl<?else:?>row<?endif;?> gallery" <?
		?>data-changespeed="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_SPEED"]?><?endif;?>" <?
		?>data-changedelay="<?if(IntVal($arParams["RSFLYAWAY_OWL_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSFLYAWAY_OWL_CHANGE_DELAY"]?><?endif;?>" <?
		?>data-margin="34" <?
		?>data-responsive='{"0":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PHONE'])>0?$arParams['RSFLYAWAY_OWL_PHONE']:1)?>"},"768":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_TABLET'])>0?$arParams['RSFLYAWAY_OWL_TABLET']:1)?>"},"1200":{"items":"<?=(IntVal($arParams['RSFLYAWAY_OWL_PC'])>0?$arParams['RSFLYAWAY_OWL_PC']:1)?>"}}'<?
		?>><?
		foreach ($arResult['SECTIONS'] as $arSection) {
			$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
			$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

			?><div class="item<?if($arParams['RSFLYAWAY_USE_OWL']!='Y'):?> col col-sm-6 col-md-<?=$arParams['RSFLYAWAY_COLS_IN_ROW']?><?endif;?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?
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
										?><div class="gallery-name aprimary"><?=$arSection['NAME']?></div><?
										?><div class="gallery-descr"><?=$arSection['DESCRIPTION']?></div><?
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
**/
