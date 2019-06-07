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
<div class="api-search-page tpl-default" id="<?=$arResult['COMPONENT_ID']?>">
	<div class="theme-<?=$arParams['THEME'];?>">
		<form action="<?=POST_FORM_ACTION_URI?>" autocomplete="off">
			<div class="api-search-fields">
				<div class="api-query">
					<input class="api-search-input"
					       placeholder="<?=$arParams['INPUT_PLACEHOLDER']?>"
					       name="q"
					       maxlength="300"
					       value="<?=htmlspecialcharsEx($arResult['q'])?>"
					       type="text">
					<span class="api-ajax-icon"></span>
					<span class="api-clear-icon"></span>
				</div>
				<div class="api-search-button">
					<button type="submit"><?=($arParams['BUTTON_TEXT'] ? $arParams['BUTTON_TEXT'] : '<i class="api-search-icon"></i>')?></button>
				</div>
			</div>
		</form>
		<div class="api-search-result"><?include 'ajax.php'; ?></div>
		<div class="api-preload"></div>
	</div>
</div>
<script>
	jQuery(function ($) {
		$.fn.apiSearchPage({
			container_id: '#<?=$arResult['COMPONENT_ID']?>',
			input_id: '.api-search-input',
			result_id: '.api-search-result',
			ajax_icon_id: '', //.api-ajax-icon
			clear_icon_id: '.api-clear-icon',
			ajax_preload_id: '.api-preload',
			pagination_id: '.api-pagination',
			wait_time: 500,
			mess: {}
		});
	});
</script>