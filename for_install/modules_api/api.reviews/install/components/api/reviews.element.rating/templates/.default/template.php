<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent         $component
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $parentTemplateFolder
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

//$this - объект шаблона
//$component - объект компонента

//$this->GetFolder()
//$tplId = $this->GetEditAreaId($arResult['ID']);

//Объект родительского компонента
//$parent = $component->getParent();
//$parentPath = $parent->getPath();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}

$reviewsId = "api_reviews_element_rating_" . $component->randString();
$border = ($arParams['HIDE_BORDER'] == 'Y' ? 'api-hide-border' : '');
?>
<div id="<?=$reviewsId?>">
	<?
	$dynamicArea = new \Bitrix\Main\Page\FrameStatic(ToLower($reviewsId));
	$dynamicArea->setAnimation(true);
	$dynamicArea->setStub('');
	$dynamicArea->setContainerID($reviewsId);
	$dynamicArea->startDynamicArea();
	?>
	<div class="api-reviews-element-rating arelrating-color-<?=$arParams['COLOR']?> <?=$border?>">
		<div class="api-row api-rating">
			<div class="api-stars-empty">
				<div class="api-stars-full" style="width: <?=$arResult['FULL_RATING']?>%"></div>
			</div>
			<div class="api-average">
				<? if($arParams['REVIEWS_LINK']): ?>
					<a href="<?=$arParams['REVIEWS_LINK']?>"><?=$arResult['MESS_FULL_RATING']?></a>
				<? else: ?>
					<?=$arResult['MESS_FULL_RATING']?>
				<? endif ?>
			</div>
		</div>
		<? if($arParams['SHOW_PROGRESS_BAR'] == 'Y'): ?>
			<div class="api-row">
				<div class="api-info">
					<? for($i = 5; $i >= 1; $i--): ?>
						<div class="api-info-row">
							<div class="api-info-title">
								<div class="api-icon-star api-icon-star<?=$i?>"></div>
							</div>
							<div class="api-info-progress">
								<div style="width:<?=$arResult['COUNT_PROGRESS'][ $i ]?>%" class="api-info-bar api-info-bar<?=$i?>"></div>
							</div>
							<div class="api-info-qty" title="<?=$arResult['COUNT_REVIEWS'][ $i ]?>"><?=$arResult['COUNT_PROGRESS'][ $i ]?>%</div>
						</div>
					<? endfor ?>
				</div>
			</div>
		<? endif ?>
	</div>
	<?
	$dynamicArea->finishDynamicArea();
	?>
</div>
