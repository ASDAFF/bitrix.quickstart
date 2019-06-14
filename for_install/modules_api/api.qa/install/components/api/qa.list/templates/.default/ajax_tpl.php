<?php
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $templateFile
 * @var string           $templateFolder
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 *
 * @var array            $arItem
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
$margin = $arItem['LEVEL'] * 20;
?>
<div id="api_qa_item_<?=$arItem['ID']?>"
     class="api-item api-level-<?=$arItem['LEVEL']?> api-type-<?=ToLower($arItem['TYPE'])?>"
     style="margin-left: <?=$margin?>px"
     data-level="<?=$arItem['LEVEL']?>"
     data-id="<?=$arItem['ID']?>"
     data-parent-id="<?=$arItem['PARENT_ID']?>">
	<div class="api-question" id="api_qa_question_<?=$arItem['ID']?>">
		<div class="api-header">
			<div class="api-hash">
				<a onclick="$.fn.apiQaList('getLink',this,<?=$arItem['ID']?>,'<?=$arItem['URL']?>');">#<?=$arItem['ID']?></a>
			</div>
			<div class="api-avatar">
				<img src="<?=$arItem['PICTURE']?>" alt="" title="<?=$arItem['GUEST_NAME']?>">
			</div>
			<div class="api-user-info">
				<div class="api-user">
					<span class="api-user-name" <?=($arItem['USER_ID'] ? '' : 'data-edit="GUEST_NAME"')?>><?=$arItem['GUEST_NAME']?></span>
					<?=($arItem['TYPE'] == 'A' ? '<span class="api-expert">' . $arParams['LIST_QUESTION_MESS_EXPERT'] . '</span>' : '')?>
				</div>
				<div class="api-date"><?=$arItem['DATE_CREATE']?></div>
			</div>
		</div>
		<div class="api-content">
			<div class="api-text <?=($arItem['TEXT'] ? '' : 'api-text-empty')?>"
			     data-edit="TEXT"><?=($arItem['TEXT'] ? $arItem['TEXT'] : $arParams['LIST_QUESTION_MESS_TEXT_ERASE'])?></div>
		</div>
		<div class="api-footer">
			<span class="api-link api-answer"
			      onclick="$.fn.apiQaList('answerForm',this,<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_ANSWER']?></span>
			<? if($arParams['IS_EDITOR']): ?>
				<span
					 class="api-link api-edit"
					 onclick="$.fn.apiQaList('edit',<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_EDIT']?></span>
				<span
					 class="api-link api-save api-hidden"
					 onclick="$.fn.apiQaList('save',<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_SAVE']?></span>
				<span
					 class="api-link api-cancel api-hidden"
					 onclick="$.fn.apiQaList('cancel',<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_CANCEL']?></span>
				<span
					 class="api-link api-erase"
					 onclick="$.fn.apiQaList('erase',<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_ERASE']?></span>
				<span
					 class="api-link api-delete"
					 onclick="$.fn.apiQaList('delete',<?=$arItem['ID']?>)"><?=$arParams['LIST_QUESTION_MESS_BUTTON_DELETE']?></span>
			<? endif ?>
		</div>
	</div>
	<div class="api-form-answer" id="api_qa_form_answer_<?=$arItem['ID']?>"></div>
</div>