<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var ApiQaList                $component
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

use Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

//plugins
//$this->addExternalCss($templateFolder . '/plugins/modal/api.modal.css');
$this->addExternalCss($templateFolder . '/plugins/form/api.form.css');
$this->addExternalCss($templateFolder . '/plugins/button/api.button.css');
//$this->addExternalJs($templateFolder . '/plugins/modal/api.modal.js');

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}
?>
<div class="api-qa api-qa-theme-<?=$arParams['THEME']?> api-qa-color-<?=$arParams['COLOR']?>">

	<!--api-qa-form-->
	<div class="api-qa-form">
		<div class="api-title"><?=$arParams['FORM_QUESTION_MESS_TITLE']?></div>
		<div class="api-form">
			<input class="api-field" type="hidden" name="TYPE" value="Q">
			<? if(!$USER->IsAuthorized()): ?>
				<div class="api-row api-guest">
					<div class="api-controls">
						<div class="api-control">
							<input type="text" name="GUEST_NAME" class="api-field" value="" placeholder="<?=$arParams['FORM_QUESTION_MESS_NAME']?>">
						</div>
						<div class="api-control">
							<input type="text" name="GUEST_EMAIL" class="api-field" value="" placeholder="<?=$arParams['FORM_QUESTION_MESS_EMAIL']?>">
						</div>
					</div>
				</div>
			<? endif ?>
			<div class="api-row">
				<div class="api-controls">
					<div class="api-control">
						<textarea name="TEXT" class="api-field" data-autoresize="" placeholder="<?=$arParams['FORM_QUESTION_MESS_TEXT']?>"></textarea>
					</div>
				</div>
			</div>
			<div class="api-row api-buttons">
				<div class="api-controls">
					<div class="api-control">
						<? if(!$USER->IsAuthorized()): ?>
							<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
								<div class="api-privacy">
									<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
									<? if($arParams['MESS_PRIVACY_LINK']): ?>
										<a rel="nofollow" href="<?=$arParams['MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['MESS_PRIVACY']?></a>
									<? else: ?>
										<?=$arParams['MESS_PRIVACY']?>
									<? endif ?>
								</div>
							<? endif ?>
						<? endif ?>
						<button type="button" class="api-button api-form-submit"
						        onclick="$.fn.apiQaList('question',this)"><?=$arParams['FORM_QUESTION_MESS_SUBMIT']?></button>
						<input type="checkbox" name="NOTIFY" class="api-field" checked=""> <?=$arParams['FORM_QUESTION_MESS_REPLY']?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--api-qa-list-->
	<div class="api-qa-list">
		<div class="api-items">
			<? if($arResult['ITEMS']): ?>
				<? foreach($arResult['ITEMS'] as $arItem): ?>
					<?
					include 'ajax_tpl.php';
					?>
				<? endforeach; ?>
			<? endif ?>
		</div>
	</div>

	<!--api-qa-unswer-->
	<?if($arParams['IS_ALLOW']):?>
	<div class="api-qa-form-unswer">
		<div class="api-form">
			<input class="api-field" type="hidden" name="TYPE" value="<?=($arParams['IS_EDITOR'] ? 'A' : 'C')?>">
			<input class="api-field" type="hidden" name="LEVEL" value="">
			<input class="api-field" type="hidden" name="PARENT_ID" value="">
			<? if(!$USER->IsAuthorized()): ?>
				<div class="api-row api-guest">
					<div class="api-controls">
						<div class="api-control">
							<input type="text" name="GUEST_NAME" class="api-field" value="" placeholder="<?=$arParams['FORM_ANSWER_MESS_NAME']?>">
						</div>
						<div class="api-control">
							<input type="text" name="GUEST_EMAIL" class="api-field" value="" placeholder="<?=$arParams['FORM_ANSWER_MESS_EMAIL']?>">
						</div>
					</div>
				</div>
			<? endif ?>
			<div class="api-row">
				<div class="api-controls">
					<div class="api-control">
						<textarea name="TEXT" class="api-field" data-autoresize="" placeholder="<?=$arParams['FORM_ANSWER_MESS_TEXT']?>"></textarea>
					</div>
				</div>
			</div>
			<div class="api-row api-buttons">
				<div class="api-controls">
					<? if(!$USER->IsAuthorized()): ?>
						<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
							<div class="api-control api-privacy">
								<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
								<? if($arParams['MESS_PRIVACY_LINK']): ?>
									<a rel="nofollow" href="<?=$arParams['MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['MESS_PRIVACY']?></a>
								<? else: ?>
									<?=$arParams['MESS_PRIVACY']?>
								<? endif ?>
							</div>
						<? endif ?>
					<? endif ?>
					<div class="api-control">
						<button type="button"
						        class="api-button api-form-submit"
						        onclick="$.fn.apiQaList('answer',this)"><?=$arParams['FORM_ANSWER_MESS_SUBMIT']?></button>
						<input type="checkbox" name="NOTIFY" class="api-field" checked=""> <?=$arParams['FORM_ANSWER_MESS_REPLY']?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<? endif ?>

</div>
<script>
	jQuery(document).ready(function ($) {
		$.fn.apiQaList(<?=Json::encode($component->getAjaxParams($this))?>);
		<?if($arResult['SCROLL_TO']):?>
		$(window).on('load',function(){
			var api_qa_item = '#api_qa_item_<?=$arResult['SCROLL_TO']?>';
			$('html, body').animate({
				scrollTop: $(api_qa_item).offset().top
			}, 400, function () {
				$(api_qa_item).addClass('api-active');
			});
		});
		<?endif?>
	});
</script>
