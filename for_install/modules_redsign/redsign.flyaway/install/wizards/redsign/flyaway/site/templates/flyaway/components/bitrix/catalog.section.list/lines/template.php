<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$TOP_DEPTH = $arResult['SECTION']['DEPTH_LEVEL'];
$CURRENT_DEPTH = $TOP_DEPTH;

$strSectionEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');
$strSectionDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE');
$arSectionDeleteParams = array('CONFIRM' => GetMessage('RS.ONEAIR.ELEMENT_DELETE_CONFIRM'));

foreach ($arResult['SECTIONS'] as $key => $arSection):
	$this->AddEditAction($arResult['SECTION']['ID'], $arResult['SECTION']['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	
	if ($CURRENT_DEPTH < $arSection['DEPTH_LEVEL'] || !$CURRENT_DEPTH):
		?><ul class="<?=($CURRENT_DEPTH == $TOP_DEPTH ? 'nav-side nav nav-list' : '')?>"><?
	elseif ($CURRENT_DEPTH == $arSection['DEPTH_LEVEL']):
		?></li><?
	else:
		while ($CURRENT_DEPTH > $arSection['DEPTH_LEVEL']):
			?></li></ul><?
			$CURRENT_DEPTH--;
		endwhile;
		?></li><?
	endif;
	
	?><li id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="nav-side__item"><?
		?><a class="nav-side__label element" href="<?=$arSection['SECTION_PAGE_URL']?>"><?
			?><?=$arSection['NAME'];?><?
			if ($arParams['COUNT_ELEMENTS']):
				?> <span class="count">(<?echo $arSection['ELEMENT_CNT']?>)</span><?
			endif;
		?></a><?
	$CURRENT_DEPTH = $arSection['DEPTH_LEVEL'];
endforeach;

while ($CURRENT_DEPTH > $TOP_DEPTH):
	?></li></ul><?
	$CURRENT_DEPTH--;
endwhile;

$this->SetViewTarget('catalog_section_descr');
	if ($arResult['SECTION']['DESCRIPTION'] != '') {
		?><div class="col col-md-12"><?
			?><div class="row"><?
				if (isset($arResult['SECTION']['PICTURE']['SRC'])){
					?><div class="col col-sm-4 col-md-3 col-lg-2 hidden-xs sections-cover"><?
						?><img class="sections-cover__img" src="<?=$arResult['SECTION']['PICTURE']['SRC']?>" alt="<?=$arResult['SECTION']['PICTURE']['ALT']?>" title="<?=$arResult['SECTION']['PICTURE']['TITLE']?>" /><?
					?></div><?
				} 
				
				if (isset($arResult['SECTION']['DESCRIPTION'])) {
					?><div class="sections-description"><?
						?><div class="sections-detail"><?=$arResult['SECTION']['DESCRIPTION']?></div><?
					?></div><?
				}
			?></div><?
		?></div><?
	}
$this->EndViewTarget();
