<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (!empty($arResult['SECTIONS'])): ?>
<div class="carousel js-carousel_section owl-shift">
	<?php foreach($arResult['SECTIONS'] as $arSection): ?>
		<a class="carousel__item" href="<?=$arSection['SECTION_PAGE_URL']?>">
			<img class="carousel__img" src="<?=$arSection['PICTURE']['SRC']?>" alt="<?=$arSection['ALT']?>" title="<?=$arSection['TITLE']?>">
			<div class="carousel__name text_fade"><?=$arSection['NAME']?></div>
		</a>
	<?php endforeach; ?>
</div>
<?
endif;

$this->SetViewTarget('main_section_tab');

global $bRSHomeTabShow;
$tabId = $this->getEditAreaId('tab');

if (!empty($arResult['SECTIONS'])): ?>
	<li<?php if(!$bRSHomeTabShow): ?> class="active"<?php endif; ?>>
		<a href="#<?=$tabId?>" data-toggle="tab">
			<?=(0 < strlen($arParams['BLOCK_TITLE']) ? $arParams['BLOCK_TITLE'] : Loc::getMessage('RS_SLINE.UFSL_AL.BLOCK_TITLE_SAMPLE')); ?>
		</a>
	</li>
<?
endif;

$this->EndViewTarget();

$this->SetViewTarget('main_section_start_div');
?>
	<div id="<?=$tabId?>" class="tab-pane<?php if (!$bRSHomeTabShow && !empty($arResult['SECTIONS'])): ?> active<?php endif; ?>">
<?
$this->EndViewTarget();	