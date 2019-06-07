<?php

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var ApiAuthAjaxComponent     $component
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

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$formId = $arResult['FORM_ID'];

$this->addExternalCss($templateFolder . '/styles.css');
$this->addExternalJs($templateFolder . '/scripts.js');

if($arParams) {
	foreach($arParams as $key => $val) {
		if($arParams[ '~' . $key ]) {
			$arParams[ $key ] = $arParams[ '~' . $key ];
		}
		unset($arParams[ '~' . $key ]);
	}
}
?>
	<div id="<?=$formId?>" class="api_auth_ajax">
		<? if($USER->IsAuthorized()): ?>
			<div class="api_profile">
				<a href="<?=$arParams['PROFILE_URL']?>"> <span><?=$USER->GetFormattedName()?></span> </a>
				<a href="<?=$arParams['LOGOUT_URL']?>"> <span><?=Loc::getMessage('AAAP_AJAX_LOGOUT')?></span> </a>
			</div>
		<? else: ?>
			<? if($arParams['LOGIN_MESS_LINK']): ?>
				<a class="api_link <?=$arParams['LOGIN_BTN_CLASS']?>" href="#api_auth_login"
				   data-header="<?=CUtil::JSEscape($arParams['LOGIN_MESS_HEADER'])?>"><?=$arParams['LOGIN_MESS_LINK']?></a>
			<? endif ?>
			<? if($arParams['REGISTER_MESS_LINK'] && $arParams['ALLOW_NEW_USER_REGISTRATION'] == 'Y'): ?>
				<a class="api_link <?=$arParams['REGISTER_BTN_CLASS']?>" href="#api_auth_register"
				   data-header="<?=CUtil::JSEscape($arParams['REGISTER_MESS_HEADER'])?>"><?=$arParams['REGISTER_MESS_LINK']?></a>
			<? endif ?>
			<div id="api_auth_ajax_modal" class="api_modal">
				<div class="api_modal_dialog">
					<div class="api_modal_close"></div>
					<div class="api_modal_header"></div>
					<div class="api_modal_content">
						<div id="api_auth_login">
							<? $APPLICATION->IncludeComponent('api:auth.login', '', $arParams); ?>
						</div>
						<?if($arParams['ALLOW_NEW_USER_REGISTRATION'] == 'Y'):?>
							<div id="api_auth_register">
								<? $APPLICATION->IncludeComponent('api:auth.register', '', $arParams); ?>
							</div>
						<?endif?>
						<div id="api_auth_restore">
							<? $APPLICATION->IncludeComponent('api:auth.restore', '', $arParams); ?>
						</div>
					</div>
				</div>
			</div>
		<? endif ?>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiAuthAjax({
				modalId: '#api_auth_ajax_modal',
				authId: '.api_auth_ajax',
			});
		});
	</script>
<?
$script = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($script, true, AssetLocation::AFTER_JS);
?>