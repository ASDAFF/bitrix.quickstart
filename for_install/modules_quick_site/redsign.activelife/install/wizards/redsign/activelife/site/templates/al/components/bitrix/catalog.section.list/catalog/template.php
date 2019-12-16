<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$TOP_DEPTH = $arResult['SECTION']['DEPTH_LEVEL'];
$CURRENT_DEPTH = $TOP_DEPTH;

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

?>
<?php foreach ($arResult['SECTIONS'] as $key => $arSection): ?>
    <?php
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	?>
	<?php if ($CURRENT_DEPTH < $arSection['DEPTH_LEVEL'] || !$CURRENT_DEPTH): ?>
		<ul class="<?if($CURRENT_DEPTH == $TOP_DEPTH):?>menu_rtl<?else:?>lvl<?=$CURRENT_DEPTH - $TOP_DEPTH;?>_submenu none2<?endif;?>">
	<?php elseif($CURRENT_DEPTH == $arSection['DEPTH_LEVEL']): ?>
		</li>
	<?php else: ?>
		<?php while($CURRENT_DEPTH > $arSection['DEPTH_LEVEL']): ?>
			</li></ul>
			<?php $CURRENT_DEPTH--; ?>
		<?php endwhile; ?>
		</li>
	<?php endif; ?>
	<li id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="menu_rtl-menu lvl<?echo $arSection['DEPTH_LEVEL'] - $TOP_DEPTH;?>"><?
		?><a class="sla<?if($_REQUEST['SECTION_ID']==$arSection['ID']):?> selected<?endif;?> a_lvl<?=$arSection['DEPTH_LEVEL'] - $TOP_DEPTH?> clearfix" href="<?=$arSection['SECTION_PAGE_URL']?>"><?
			?><span class="imya"><?=$arSection['NAME'];?></span><?
			if($arParams['COUNT_ELEMENTS']):
				?> <span class="count">(<?echo $arSection['ELEMENT_CNT']?>)</span><?
			endif;
			if($arSection['HAVE_SUBSECTIONS']):
				?><span class="icon multimage_icons"></span><?
			endif;
		?></a><?
	$CURRENT_DEPTH = $arSection['DEPTH_LEVEL'];
endforeach;

while($CURRENT_DEPTH > $TOP_DEPTH){
	?></li></ul><?
	$CURRENT_DEPTH--;
}
if($arResult['SECTION']['DESCRIPTION'] != ''){
	$this->SetViewTarget('catalog_section_description');
		?><div class="descr"><?=$arResult['SECTION']['DESCRIPTION']?></div><?
	$this->EndViewTarget();
}
if('Y' == $arParams['SHOW_SECTION_PICTURE'] && isset($arResult['SECTION']['PICTURE']['RESIZE'])){
	$this->SetViewTarget('catalog_section_pic');
		?><img class="catalog_section-pic" src="<?=$arResult['SECTION']['PICTURE']['RESIZE']['src']?>" alt="<?=$arResult['SECTION']['PICTURE']['ALT']?>" title="<?=$arResult['SECTION']['PICTURE']['TITLE']?>" /><?
	$this->EndViewTarget();
}