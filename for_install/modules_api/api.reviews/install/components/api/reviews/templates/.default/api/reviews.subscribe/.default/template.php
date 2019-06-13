<?php
use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc;

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

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}
?>
	<div class="api-reviews-subscribe arsubscribe-color-<?=$arParams['COLOR']?>">
		<div class="api-link"><?=$arParams['MESS_LINK']?></div>
		<div class="api-subscribe-form">
			<div class="api-form-edge"></div>
			<div class="api-form-row">
				<input type="text" class="api-field api-field-email" placeholder="<?=$arParams['MESS_FIELD_PLACEHOLDER']?>">
			</div>
			<div class="api-form-row">
				<div class="api_button api_button_small api_button_yellow api_width_1_1"><?=$arParams['MESS_BUTTON_TEXT']?></div>
			</div>
		</div>
	</div>
<?
if($arParams) {
	foreach($arParams as $key => $val)
		unset($arParams[ '~' . $key ]);
}

ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsSubscribe(<?=Json::encode($arParams)?>);
		});
	</script>
<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);
?>