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

if (!empty($arResult['ITEMS'])):
?>
	<div class="carousel js-carousel_brands owl-shift">
		<?php
		foreach($arResult['ITEMS'] as $arItem):
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<a class="carousel__item"  id="<?=$this->GetEditAreaId($arItem['ID']);?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
			<?php if('N' != $arParams['DISPLAY_PICTURE'] && is_array($arItem['PREVIEW_PICTURE'])): ?>
				<img class="carousel__img" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
			<?php else: ?>
				<div class="carousel__name"><?=$arItem['NAME'];?></div>
			<?php endif; ?>
		</a>
		<?php endforeach; ?>
</div>
<?php
endif;

$this->SetViewTarget('main_brands_tab');

global $bRSHomeTabShow;
$tabId = $this->getEditAreaId('tab');

if (!empty($arResult['ITEMS'])): ?>

	<li<?php if(!$bRSHomeTabShow): ?> class="active"<?php endif; ?>>
		<a href="#<?=$tabId?>" data-toggle="tab"><?echo $arResult['NAME'];?></a>
	</li>
	<?php
endif;

$this->EndViewTarget();

$this->SetViewTarget('main_brands_start_div');
?>
	<div id="<?=$tabId?>" class="tab-pane<?php if (!$bRSHomeTabShow && !empty($arResult['ITEMS'])): ?> active<?php endif; ?>">
<?
$this->EndViewTarget();