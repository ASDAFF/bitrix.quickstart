<?php
use Bitrix\Main\Web\Json,
	 Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
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

$formId = $arResult['FORM_ID'];

$dataCss = $templateFolder . '/styles.css';
$dataJs  = $templateFolder . '/scripts.js';

$this->addExternalCss($dataCss);
$this->addExternalJs($dataJs);
?>
	<div id="<?=$formId?>"
	     class="api-auth-change"
	     data-css="<?=$dataCss?>"
	     data-js="<?=$dataJs?>">

		<form id="<?=$formId?>_form"
		      method="post"
		      action="<?=$arResult["AUTH_FORM"]?>"
		      name="<?=$formId?>_form"
		      class="api_form">

			<? if(strlen($arResult["BACKURL"]) > 0): ?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>"/>
			<? endif ?>
			<div class="api_error"></div>

			<div class="api_row">
				<div class="api_label"><?=GetMessage("AUTH_LOGIN")?></div>
				<div class="api_controls">
					<input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" class="api_field">
				</div>
			</div>

			<div class="api_row">
				<div class="api_label"><?=GetMessage("AUTH_CHECKWORD")?></div>
				<div class="api_controls">
					<input type="text" name="USER_CHECKWORD" maxlength="255" value="<?=$arResult["USER_CHECKWORD"]?>" class="api_field">
				</div>
			</div>

			<div class="api_row">
				<div class="api_label"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?></div>
				<div class="api_controls">
					<input type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult["USER_PASSWORD"]?>" autocomplete="off" class="api_field">
					<? if($arResult["SECURE_AUTH"]): ?>
						<div class="api_password_protected">
							<div class="api_password_protected_desc"><span></span><?=Loc::getMessage('API_AUTH_CHANGE_SECURE_NOTE')?>
							</div>
						</div>
					<? endif ?>
				</div>
			</div>

			<div class="api_row">
				<div class="api_label"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?></div>
				<div class="api_controls">
					<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" autocomplete="off" class="api_field">
					<? if($arResult["SECURE_AUTH"]): ?>
						<div class="api_password_protected">
							<div class="api_password_protected_desc"><span></span><?=Loc::getMessage('API_AUTH_CHANGE_SECURE_NOTE')?>
							</div>
						</div>
					<? endif ?>
				</div>
			</div>
			<div class="api_row">
				<button type="button"
				        class="api_button api_button_primary api_button_large api_button_wait api_width_1_1"
				        name="change_pwd"
				        value="<?=GetMessage("AUTH_CHANGE")?>"><?=GetMessage("AUTH_CHANGE")?></button>
			</div>
			<div class="api_row">
				<?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?>
			</div>
			<div class="api_row">
				<a href="<?=$arResult["AUTH_AUTH_URL"]?>" class="api_link"><?=GetMessage("AUTH_AUTH")?></a>
			</div>
		</form>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiAuthChange({
				wrapperId: '#<?=$formId?>',
				formId: '#<?=$formId?>_form',
				secureAuth: <?=($arResult["SECURE_AUTH"] ? 'true' : 'false')?>,
				secureData: <?=Json::encode($arResult['SECURE_DATA'])?>,
			});
		});
	</script>
<?
$script = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($script, true, AssetLocation::AFTER_JS);
?>