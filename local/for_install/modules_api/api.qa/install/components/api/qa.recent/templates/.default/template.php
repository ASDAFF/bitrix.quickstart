<?php
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

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

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
?>
<? if($arResult['ITEMS']): ?>
	<div class="api-qa-recent aqarecent-color-<?=$arParams['COLOR']?>">
		<? if($arParams['HEADER_ON'] == 'Y' && isset($arParams['HEADER_TITLE'])): ?>
			<div class="api-header"><?=$arParams['HEADER_TITLE']?></div>
		<? endif ?>
		<div class="api-items">
			<? foreach($arResult['ITEMS'] as $arItem): ?>
				<div class="api-item">
					<div class="api-meta">
						<? if($arItem['PICTURE']): ?>
							<div class="api-avatar">
								<img src="<?=$arItem['PICTURE']?>" alt="" title="<?=$arItem['GUEST_NAME']?>">
							</div>
						<? endif ?>
						<div class="api-user-info">
							<div class="api-user">
								<span class="api-user-name" <?=($arItem['USER_ID'] ? '' : 'data-edit="GUEST_NAME"')?>><?=$arItem['GUEST_NAME']?></span>
								<?=($arItem['TYPE'] == 'A' ? '<span class="api-expert">' . $arParams['LIST_QUESTION_MESS_EXPERT'] . '</span>' : '')?>
							</div>
							<? if($arItem['DISPLAY_ACTIVE_FROM']): ?>
								<div class="api-date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></div>
							<? endif ?>
						</div>
					</div>
					<? if($arItem['TEXT']): ?>
						<div class="api-text"><?=$arItem['TEXT']?></div>
					<? endif ?>
					<? if($arItem['NAME']): ?>
						<div class="api-name">
							<? if($arItem['PAGE_URL']): ?>
								<a href="<?=$arItem['PAGE_URL']?>"><?=$arItem['NAME']?></a>
							<? else: ?>
								<?=$arItem['NAME']?>
							<? endif ?>
						</div>
					<? endif ?>
				</div>
			<? endforeach; ?>
		</div>
	</div>
<? endif ?>
