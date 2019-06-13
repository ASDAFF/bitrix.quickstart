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
	     class="api-auth-restore"
	     data-css="<?=$dataCss?>"
	     data-js="<?=$dataJs?>">
		<form id="<?=$formId?>_form"
		      action="<?=$arParams["RESTORE_URL"]?>"
		      name="<?=$formId?>_form"
		      method="post"
		      class="api_form">
			<? if($arParams['BACK_URL'] != ''): ?>
				<input type="hidden" name="backurl" value="<?=$arParams['BACK_URL']?>"/>
			<? endif ?>
			<div class="api_error"></div>
			<div class="api_row">
				<input type="text" name="LOGIN" maxlength="255" value="<?=$arParams['LAST_LOGIN']?>" placeholder="<?=Loc::getMessage('API_AUTH_RESTORE_LOGIN_PLACEHOLDER')?>">
			</div>

			<div class="api_row">
				<button type="button" class="api_button api_button_primary api_button_large api_button_wait api_width_1_1"><?=Loc::getMessage('API_AUTH_RESTORE_BUTTON')?></button>
			</div>
			<div class="api_row">
				<a class="api_link api_auth_login"
				   href="<?=$arParams['LOGIN_URL']?>"
				   data-header="<?=CUtil::JSEscape($arParams['~LOGIN_MESS_HEADER'])?>"><?=Loc::getMessage('API_AUTH_RESTORE_LOGIN_URL')?></a>
			</div>
		</form>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$.fn.apiAuthRestore({
				wrapperId: '#<?=$formId?>',
				formId: '#<?=$formId?>_form',
			});

			//---------- User consent ----------//
			<?if($arResult['DISPLAY_USER_CONSENT']):?>
			var obUserConset = <?=Json::encode($arResult['DISPLAY_USER_CONSENT'])?>;

			$('#<?=$formId?>').on('click', '.api-row-user-consent .api-accept-label', function (e) {
				e.preventDefault();

				var checkbox = $(this).find('input');
				var agreementId = $(this).data('id');

				var config = obUserConset[agreementId].CONFIG;
				var data = {
					'action': 'getText',
					'sessid': BX.bitrix_sessid(),
				};

				$.ajax({
					type: 'POST',
					url: '/bitrix/components/bitrix/main.userconsent.request/ajax.php',
					data: $.extend({},data,config),
					error: function (jqXHR, textStatus, errorThrown) {
						console.error('textStatus: ' + textStatus);
						console.error('errorThrown: ' + errorThrown);
					},
					success: function (response) {
						if(!!response.text){
							$.fn.apiAlert({
								type: 'confirm',
								title:'<?=Loc::getMessage('API_AUTH_RESTORE_USER_CONSENT_TITLE')?>',
								width: 600,
								content: '<textarea rows="50" readonly>'+ response.text +'</textarea>',
								labels: {
									ok:'<?=Loc::getMessage('API_AUTH_RESTORE_USER_CONSENT_BTN_ACCEPT')?>',
									cancel:'<?=Loc::getMessage('API_AUTH_RESTORE_USER_CONSENT_BTN_REJECT')?>',
								},
								callback: {
									onConfirm: function (isConfirm) {
										if (isConfirm) {
											checkbox.prop('checked',true).change();
											checkbox.parents('.api_control').find('.api-error').slideUp(200);
										}
										else {
											checkbox.prop('checked',false).change();
											checkbox.parents('.api_control').find('.api-error').slideDown(200);
										}
									}
								}
							});
						}
					}
				});
			});
			<?endif?>

		});
	</script>
<?
$script = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($script, true, AssetLocation::AFTER_JS);
?>