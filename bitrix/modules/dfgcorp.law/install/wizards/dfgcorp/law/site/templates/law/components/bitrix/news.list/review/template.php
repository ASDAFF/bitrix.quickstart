<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h3><?=(strlen($arParams["BLOCK_TITLE"])>0 ? $arParams["BLOCK_TITLE"] : GetMessage("CT_IBLOCK_TITLE_DEFAULT"))?></h3>
<div class="review-list">
<?
	$i=0;
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<?
	$i++;
?>
	<div class="review-item<?=($i%3==0?" last":"")?>">
		<p class="author-text"><?=$arItem["PREVIEW_TEXT"]?></p>
		<p class="author-name"><?=$arItem["NAME"]?></p>
	</div>
<?endforeach;?>
<div class="clear"></div>
</div>
