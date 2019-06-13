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

if($arParams['INCLUDE_CSS'] == 'Y')
	$this->addExternalCss($this->GetFolder() . '/styles.css');
?>
<div class="api-search-catalog tpl-default" id="<?=$arResult['COMPONENT_ID']?>">
	<? if($arParams['USE_SEARCH'] == 'Y'): ?>
		<form action="<?=POST_FORM_ACTION_URI?>" autocomplete="off">
			<div class="api-search-fields">
				<div class="api-query">
					<input class="api-search-input"
					       placeholder="<?=$arParams['INPUT_PLACEHOLDER']?>"
					       name="q"
					       maxlength="300"
					       value="<?=$arResult['q']?>"
					       type="text">
				</div>
				<div class="api-search-button">
					<button type="submit"><?=($arParams['BUTTON_TEXT'] ? $arParams['BUTTON_TEXT'] : '<i class="api-search-icon"></i>')?></button>
				</div>
			</div>
		</form>
	<? endif ?>
	<?
	if($arParams['IBLOCK_ID'])
	{
		if(strlen($arResult['q']) >= API_SEARCH_CHAR_LENGTH)
		{
			$APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				$arParams['CATALOG_TEMPLATE'],
				$arParams,
				$arResult['THEME_COMPONENT'],
				array('HIDE_ICONS' => 'Y')
			);
		}
	}
	?>
</div>