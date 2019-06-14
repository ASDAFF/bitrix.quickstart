<?php

use Bitrix\Main\Localization\Loc;

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

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$this->addExternalCss("/bitrix/css/main/system.auth/flat/style.css");
?>
<div id="<?=$arResult['FORM_ID']?>" class="api-auth-confirm">
	<? if($arResult['MESSAGE']): ?>
		<div class="bx-authform">
			<div class="alert alert-<?=$arResult['MESSAGE']['TYPE']?>"><?=$arResult['MESSAGE']['TEXT']?></div>
		</div>
	<? endif ?>
	<? if(!$USER->IsAuthorized()): ?>
		<? $APPLICATION->IncludeComponent("api:auth.login", "", array()); ?>
	<? endif ?>
</div>