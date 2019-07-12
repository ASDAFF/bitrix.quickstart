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
$this->setFrameMode(true);
?>
 <ul id="filters" class="clearfix">
 	<li><span class="filter active" data-filter="<?foreach($arResult['SECTIONS'] as $arSection):?><?=$arSection["CODE"]?> <?endforeach;?>"><?=GetMessage("BD_ALL_GALLERY")?></span></li>
<?foreach($arResult['SECTIONS'] as $arSection):?>
	<?
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	?>
	<li><span class="filter" data-filter="<?=$arSection["CODE"]?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?=$arSection["NAME"]?></span></li>
    <?endforeach;?>
</ul>