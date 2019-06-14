<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);
?>
<div class="api-yashare" style="margin:15px 0">
	<script src="https://yastatic.net/share2/share.js" defer="defer" charset="utf-8"></script>
	<div id="<?=$arResult['element']?>"
		<? if($arParams['QUICKSERVICES']): ?>
			data-services="<?=implode(',', $arParams['QUICKSERVICES'])?>"
		<? endif ?>
		<? if($arParams['SIZE']): ?>
			data-size="<?=$arParams['SIZE']?>"
		<? endif ?>
		<? if($arParams['TYPE'] == 'counter'): ?>
			data-counter
		<? endif ?>
		<? if($arParams['TYPE'] == 'limit' && $arParams['LIMIT']): ?>
			data-limit="<?=$arParams['LIMIT']?>"
			<? if($arParams['COPY']): ?>
				data-copy="<?=$arParams['COPY']?>"
			<? endif ?>
			<? if($arParams['POPUP_DIRECTION']): ?>
				data-popup-direction="<?=$arParams['POPUP_DIRECTION']?>"
			<? endif ?>
			<? if($arParams['POPUP_POSITION']): ?>
				data-popup-position="<?=$arParams['POPUP_POSITION']?>"
			<? endif ?>
		<? endif ?>
		<? if($arParams['LANG']): ?>
			data-lang="<?=$arParams['LANG']?>"
		<? endif ?>
		<? if($arParams['UNUSED_CSS']): ?>
			data-bare
		<? endif ?>

		<? if($arParams['DATA_TITLE']): ?>
			data-title="<?=$arParams['DATA_TITLE']?>"
		<? endif ?>
		<? if($arParams['DATA_URL']): ?>
			data-url="<?=$arParams['DATA_URL']?>"
		<? endif ?>
		<? if($arParams['DATA_IMAGE']): ?>
			data-image="<?=$arParams['DATA_IMAGE']?>"
		<? endif ?>
		<? if($arParams['DATA_DESCRIPTION']): ?>
			data-description="<?=$arParams['DATA_DESCRIPTION']?>"
		<? endif ?>
		 <? if($arParams['twitter_hashtags']): ?>
			 hashtags:twitter="<?=$arParams['twitter_hashtags']?>"
		 <? endif ?>

		 <? if($arParams['SHARE_SERVICES']): ?>
			<?foreach($arParams['SHARE_SERVICES'] as $service):?>
				<?if($arParams[$service.'_title']):?>
					data-title:<?=$service?>="<?=$arParams[$service.'_title']?>"
				<?endif?>
				<?if($arParams[$service.'_url']):?>
					data-url:<?=$service?>="<?=$arParams[$service.'_url']?>"
				<?endif?>
				<?if($arParams[$service.'_description']):?>
					data-description:<?=$service?>="<?=$arParams[$service.'_description']?>"
				<?endif?>
				<?if($arParams[$service.'_image']):?>
					data-image:<?=$service?>="<?=$arParams[$service.'_image']?>"
				<?endif?>
			<?endforeach;?>
		<? endif ?>
		  class="ya-share2"></div>
</div>