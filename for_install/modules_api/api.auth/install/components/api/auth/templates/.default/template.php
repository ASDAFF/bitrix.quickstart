<?php
use Bitrix\Main\Application,
	 Bitrix\Main\Localization\Loc;

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

$this->addExternalCss($templateFolder . '/styles.css');
$request = Application::getInstance()->getContext()->getRequest();
?>
<div id="<?=$arResult['FORM_ID']?>" class="api-auth">
	<? if($USER->IsAuthorized()): ?>
		<div class="api_user_authorized">
			<?=$arParams['~MESS_AUTHORIZED']?>
		</div>
	<? else: ?>
		<? if($arParams['ALLOW_NEW_USER_REGISTRATION'] == 'Y' && ($request->get('reg') || $request->get('register'))): ?>
			<? $APPLICATION->IncludeComponent('api:auth.register', '', $arParams); ?>
		<? elseif($request->get('restore') || $request->get('forgot_password')): ?>
			<? $APPLICATION->IncludeComponent('api:auth.restore', '', $arParams); ?>
		<? elseif($request->get('change') || $request->get('change_password')): ?>
			<? $APPLICATION->IncludeComponent('api:auth.change', '', $arParams); ?>
		<? elseif($arParams['ALLOW_NEW_USER_REGISTRATION'] == 'Y' && ($request->get('confirm') || $request->get('confirm_registration'))): ?>
			<? $APPLICATION->IncludeComponent('api:auth.confirm', '', $arParams); ?>
		<? else: ?>
			<? $APPLICATION->IncludeComponent('api:auth.login', '', $arParams); ?>
		<? endif ?>
	<? endif ?>
</div>