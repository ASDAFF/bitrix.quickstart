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

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}
?>
<? if($arResult['ITEMS']): ?>
	<div class="api-reviews-recent arrecent-color-<?=$arParams['COLOR']?>">
		<? if($arParams['~HEADER_TITLE']): ?>
			<div class="api-header"><?=$arParams['~HEADER_TITLE']?></div>
		<? endif ?>
		<div class="api-items">
			<? foreach($arResult['ITEMS'] as $arItem): ?>
				<div class="api-item">
					<? if($arItem['RATING']): ?>
						<div class="api-star-rating">
							<? for($i = 1; $i <= 5; $i++): ?>
								<? $active = ($arItem['RATING'] >= $i) ? '-active' : ''; ?>
								<i class="api-icon-star api-icon<?=$active?>"></i>
							<? endfor ?>
						</div>
					<? endif ?>
					<? if($arItem['TITLE']): ?>
						<div class="api-field-title">
							<? if($arItem['PAGE_URL']): ?>
								<a href="<?=$arItem['PAGE_URL']?>"><?=$arItem['TITLE']?></a>
							<? else: ?>
								<?=$arItem['TITLE']?>
							<? endif ?>
						</div>
					<? endif ?>
					<? if($arItem['ADVANTAGE']): ?>
						<div class="api-field-advantage"><?=$arItem['ADVANTAGE']?></div>
					<? endif ?>
					<? if($arItem['DISADVANTAGE']): ?>
						<div class="api-field-disadvantage"><?=$arItem['DISADVANTAGE']?></div>
					<? endif ?>
					<? if($arItem['ANNOTATION']): ?>
						<div class="api-field-annotation"><?=$arItem['ANNOTATION']?></div>
					<? endif ?>
					<? if($arItem['DISPLAY_ACTIVE_FROM']): ?>
						<div class="api-field-date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div>
					<? endif ?>
				</div>
			<? endforeach; ?>
		</div>
		<? if($arParams['~FOOTER_TITLE'] && $arParams['~FOOTER_URL']): ?>
			<div class="api-footer">
				<a href="<?=$arParams['~FOOTER_URL']?>"><?=$arParams['~FOOTER_TITLE']?></a>
			</div>
		<? endif ?>
	</div>
	<?
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsRecent();
		});
	</script>
	<?
	$html = ob_get_contents();
	ob_end_clean();

	Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);
	?>
<? endif ?>
