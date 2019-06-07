<?php
/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var ApiAuthLoginComponent    $component
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

use Bitrix\Main\Web\Json,
	 Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$formId = $arResult['FORM_ID'];

$dataCss = $templateFolder . '/styles.css';
$dataJs  = $templateFolder . '/scripts.js';

$this->addExternalCss($dataCss);
$this->addExternalJs($dataJs);
?>
	<div id="<?=$formId?>" class="api-auth-login" data-css="<?=$dataCss?>" data-js="<?=$dataJs?>">
		<form id="<?=$formId?>_form"
		      name="<?=$formId?>_form"
		      action=""
		      method="post"
		      class="api_form">
			<div class="api_error"></div>
			<div class="api_row">
				<input type="text" name="LOGIN" value="" maxlength="50" class="api_field" placeholder="<?=$arResult['LOGIN_PLACEHOLDER']?>">
			</div>
			<div class="api_row">
				<input type="password" name="PASSWORD" value="" maxlength="50" autocomplete="off" class="api_field" placeholder="<?=$arResult['PASSWORD_PLACEHOLDER']?>">
				<? if($arResult["SECURE_AUTH"]): ?>
					<div class="api-password-protected">
						<div class="api-password-protected-desc"><span></span><?=Loc::getMessage('API_AUTH_LOGIN_SECURE_NOTE')?>
						</div>
					</div>
				<? endif ?>
			</div>

			<?//if($arResult["CAPTCHA_CODE"]):?>
			<div class="api_row api-captcha">
				<div class="api_row api-captcha_sid">
					<div class="api_controls">
						<input type="hidden" class="api_captcha_sid" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>">
						<img class="api_captcha_src" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
						     width="180" height="40" alt="<?=Loc::getMessage('API_AUTH_LOGIN_TPL_CAPTCHA_LOADING')?>">
						<span class="api-captcha-refresh api-icon-refresh" title="<?=Loc::getMessage('API_AUTH_LOGIN_TPL_CAPTCHA_REFRESH')?>"></span>
					</div>
				</div>
				<div class="api_row api_row_required api-row-captcha_word">
					<div class="api_label"><?=Loc::getMessage('API_AUTH_LOGIN_TPL_CAPTCHA_WORD')?><span class="api_required">*</span></div>
					<div class="api_controls">
						<div class="api_control">
							<input type="text" class="api_captcha_word" name="captcha_word"  maxlength="50" value="" autocomplete="off">
						</div>
					</div>
				</div>
			</div>
			<?//endif;?>

			<div class="api_row">
				<button type="button" class="api_button api_button_primary api_button_large api_button_wait api_width_1_1"><?=Loc::getMessage('API_AUTH_LOGIN_BUTTON')?></button>
			</div>
			<div class="api_row api_grid api_grid_width_1_2">
				<div>
					<a class="api_link api_auth_restore_url"
					   href="<?=$arParams['RESTORE_URL']?>"
					   data-header="<?=CUtil::JSEscape($arParams['RESTORE_MESS_HEADER'])?>"><?=Loc::getMessage('API_AUTH_LOGIN_RESTORE_URL')?></a>
				</div>
				<?if($arParams['ALLOW_NEW_USER_REGISTRATION'] == 'Y'):?>
					<div class="api_text_right">
						<a class="api_link api_auth_register_url"
						   href="<?=$arParams['REGISTER_URL']?>"
						   data-header="<?=CUtil::JSEscape($arParams['REGISTER_MESS_HEADER'])?>"><?=Loc::getMessage('API_AUTH_LOGIN_REGISTER_URL')?></a>
					</div>
				<?endif?>
			</div>
		</form>
		<? if($arResult['AUTH_SERVICES']): ?>
			<div class="api_soc_auth api_text_center">
				<div class="api_soc_auth_title"><?=Loc::getMessage('API_SOC_AUTH_TITLE')?></div>
				<?
				$APPLICATION->IncludeComponent(
					 'bitrix:socserv.auth.form',
					 'flat',
					 array(
							'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
							'AUTH_URL'      => $arResult['AUTH_URL'],
							'POST'          => $arResult['POST'],
							'POPUP'         => 'Y',
					 ),
					 false,
					 array('HIDE_ICONS' => 'Y')
				);
				?>
			</div>
		<? endif ?>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$.fn.apiAuthLogin({
				wrapperId: '#<?=$formId?>',
				formId: '#<?=$formId?>_form',
				siteId: '<?=SITE_ID?>',
				sessid: '<?=bitrix_sessid()?>',
				secureAuth: <?=($arResult["SECURE_AUTH"] ? 'true' : 'false')?>,
				secureData: <?=Json::encode($arResult['SECURE_DATA'])?>,
				messLogin: '<?=$arResult['LOGIN_PLACEHOLDER']?>',
				messSuccess: '<?=CUtil::JSEscape($arParams['~LOGIN_MESS_SUCCESS'])?>',
				useCaptcha: '<?=$arResult['CAPTCHA_CODE'] != false?>',
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
								title:'<?=Loc::getMessage('API_AUTH_LOGIN_USER_CONSENT_TITLE')?>',
								width: 600,
								content: '<textarea rows="50" readonly>'+ response.text +'</textarea>',
								labels: {
									ok:'<?=Loc::getMessage('API_AUTH_LOGIN_USER_CONSENT_BTN_ACCEPT')?>',
									cancel:'<?=Loc::getMessage('API_AUTH_LOGIN_USER_CONSENT_BTN_REJECT')?>',
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