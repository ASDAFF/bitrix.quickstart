<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<?
	$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arResult['ID'], $arResult['DELETE_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"));
?>
<div class="fotorama" data-nav="thumbs" data-width="<?=$arParams['WIDTH']?>" data-height="<?=$arParams['HEIGHT']?>" data-thumbwidth="<?=$arParams['THUMB_WIDTH']?>" data-thumbheight="<?=$arParams['THUMB_HEIGHT'];?>" id="<?=$this->GetEditAreaId($arResult['ID']);?>">
	<? foreach($arResult['PROPERTY'] as $arImage){?>
		<img src="<?=$arImage['SRC']?>" alt="<?=$arImage['DESCRIPTION']?>">
	<? }?>
</div>