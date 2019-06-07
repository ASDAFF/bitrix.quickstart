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
 * @var array                    $result
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

use Bitrix\Main\Localization\Loc;
?>
<div class="api_checkout_empty">
	<div class="api_image"></div>
	<div class="api_text"><?=Loc::getMessage("EMPTY_BASKET_TITLE")?></div>
	<div class="api_link">
		<a href="/"><?=Loc::getMessage("EMPTY_BASKET_HINT")?></a>
	</div>
</div>