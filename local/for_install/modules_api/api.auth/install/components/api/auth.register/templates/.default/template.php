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
	<div id="<?=$formId?>_wrap" class="api-auth-register" data-css="<?=$dataCss?>" data-js="<?=$dataJs?>">
		<form id="<?=$formId?>"
		      name="<?=$formId?>"
		      action=""
		      method="post"
		      class="api_form">
			<div class="api_error"></div>

			<? if($arResult['GROUP_ID']): ?>
				<div class="api_row">
					<div class="api_label"><?=Loc::getMessage('REGISTER_FIELD_GROUP_ID')?></div>
					<div class="api_controls">
						<? foreach($arResult['GROUP_ID'] as $groupId => $groupName): ?>
							<label class="api_label_inline api_radio">
								<input type="radio" name="FIELDS[GROUP_ID][]" value="<?=$groupId?>"> <span><?=$groupName?></span>
							</label>
						<? endforeach ?>
					</div>
				</div>
			<? endif ?>

			<? if($arParams['SHOW_FIELDS']): ?>
				<? foreach($arParams["SHOW_FIELDS"] as $FIELD): ?>
					<? $placeholder = Loc::getMessage('REGISTER_FIELD_' . $FIELD) . (in_array($FIELD, $arParams["REQUIRED_FIELDS"]) ? ' *' : '') ?>
					<? if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true): ?>
						<tr>
							<td><?=Loc::getMessage("main_profile_time_zones_auto")?><? if($arResult["REQUIRED_FIELDS_FLAGS"][ $FIELD ] == "Y"): ?>
									<span class="starrequired">*</span><? endif ?></td>
							<td>
								<select name="FIELDS[AUTO_TIME_ZONE]" onchange="this.form.elements['FIELDS[TIME_ZONE]'].disabled=(this.value != 'N')">
									<option value=""><? echo Loc::getMessage("main_profile_time_zones_auto_def") ?></option>
									<option value="Y"<?=$arResult["VALUES"][ $FIELD ] == "Y" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("main_profile_time_zones_auto_yes")?></option>
									<option value="N"<?=$arResult["VALUES"][ $FIELD ] == "N" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("main_profile_time_zones_auto_no")?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><? echo Loc::getMessage("main_profile_time_zones_zones") ?></td>
							<td>
								<select name="FIELDS[TIME_ZONE]"<? if(!isset($_REQUEST["FIELDS"]["TIME_ZONE"]))
									echo 'disabled="disabled"' ?>>
									<? foreach($arResult["TIME_ZONE_LIST"] as $tz => $tz_name): ?>
										<option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
									<? endforeach ?>
								</select>
							</td>
						</tr>
					<? else: ?>
						<div class="api_row">
							<div class="api_controls">
								<?
								switch($FIELD) {
									case "PASSWORD":
										?>
										<input type="password" name="FIELDS[<?=$FIELD?>]" value="<?=$arResult["VALUES"][ $FIELD ]?>" placeholder="<?=$placeholder?>" autocomplete="off" class="api_field">
										<? if($arResult["SECURE_AUTH"]): ?>
										<div class="api-password-protected">
											<div class="api-password-protected-desc"><span></span><?=Loc::getMessage('AUTH_SECURE_NOTE')?>
											</div>
										</div>
									<? endif ?>
										<?
										break;
									case "CONFIRM_PASSWORD":
										?>
										<input type="password" name="FIELDS[<?=$FIELD?>]" value="<?=$arResult["VALUES"][ $FIELD ]?>" autocomplete="off" placeholder="<?=$placeholder?>" class="api_field">
										<? if($arResult["SECURE_AUTH"]): ?>
										<div class="api-password-protected">
											<div class="api-password-protected-desc"><span></span><?=Loc::getMessage('AUTH_SECURE_NOTE')?>
											</div>
										</div>
									<? endif ?>
										<?
										break;

									case "PERSONAL_GENDER":
										?>
										<div class="api_label"><?=$placeholder?></div>
										<label class="api_label_inline">
											<input type="radio" class="api_radio" name="FIELDS[<?=$FIELD?>]" value="M"> <?=Loc::getMessage("REGISTER_USER_MALE")?>
										</label>
										<label class="api_label_inline">
											<input type="radio" class="api_radio" name="FIELDS[<?=$FIELD?>]" value="F"> <?=Loc::getMessage("REGISTER_USER_FEMALE")?>
										</label>
										<?
										break;

									case "PERSONAL_COUNTRY":
									case "WORK_COUNTRY":
										?>
										<select name="FIELDS[<?=$FIELD?>]" placeholder="<?=$placeholder?>" class="api_field">
											<? foreach($arResult["COUNTRY_LIST"] as $key => $value): ?>
												<option value="<?=$key?>"<? if($key == $arResult["VALUES"][ $FIELD ]): ?> selected="selected"<? endif ?>><?=$value?></option>
											<? endforeach; ?>
										</select>
										<?/*
										if(\Bitrix\Main\Loader::includeModule('sale')):?>
											<?
											CSaleLocation::proxySaleAjaxLocationsComponent(
												 array(
														"LOCATION_VALUE"  => "",
														"CITY_INPUT_NAME" => "FIELDS[$FIELD]",
														//"CODE"  => "",
														//"INPUT_NAME"  => "FIELDS[$FIELD]",
														"SITE_ID"         => SITE_ID,
												 ),
												 array(),
												 '',
												 true,
												 'api_location'
											);
											?>
										<? else: ?>
										<? endif */
										?>
										<?
										break;

									case "PERSONAL_PHOTO":
									case "WORK_LOGO":
										?>
										<div class="api_label"><?=$placeholder?></div>
										<input size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" placeholder="<?=$placeholder?>" class="api_field"><?
										break;

									case "PERSONAL_NOTES":
									case "WORK_NOTES":
										?>
										<textarea cols="30" rows="5" name="FIELDS[<?=$FIELD?>]" placeholder="<?=$placeholder?>" class="api_field"><?=$arResult["VALUES"][ $FIELD ]?></textarea><?
										break;

									case "PERSONAL_BIRTHDAY":
										?>
										<?/*if($FIELD == "PERSONAL_BIRTHDAY"):?>
										<small><?=$arResult["DATE_FORMAT"]?></small><br/>
										<?endif;*/
										?>
										<input type="text" name="FIELDS[<?=$FIELD?>]" value="<?=$arResult["VALUES"][ $FIELD ]?>" placeholder="<?=$placeholder?>" class="api_field">
										<?
										$APPLICATION->IncludeComponent(
											 'bitrix:main.calendar',
											 '',
											 array(
													'SHOW_INPUT' => 'N',
													'FORM_NAME'  => 'regform',
													'INPUT_NAME' => 'FIELDS[PERSONAL_BIRTHDAY]',
													'SHOW_TIME'  => 'N',
											 ),
											 null,
											 array("HIDE_ICONS" => "Y")
										);
										?>
										<?
										break;

									default:
										?>
										<input type="text" name="FIELDS[<?=$FIELD?>]" value="<?=$arResult["VALUES"][ $FIELD ]?>" placeholder="<?=$placeholder?>" class="api_field"><?
								} ?>
							</div>
						</div>
					<? endif ?>
				<? endforeach ?>
			<? endif ?>

			<? if($arParams['USER_FIELDS']): ?>
				<? foreach($arResult['USER_FIELDS'] as $key => $arUserField): ?>
					<?
					$name = $arUserField['EDIT_FORM_LABEL'] ? $arUserField['EDIT_FORM_LABEL'] : $arUserField['FIELD_NAME'];
					$req  = ($arUserField['MANDATORY'] == 'Y' || ($arParams['REQUIRED_FIELDS'] && in_array($key, $arParams['REQUIRED_FIELDS'])));
					?>
					<div class="api_row api-custom-field">
						<div class="api_label"><?=$name?><?=($req ? '<span class="api_required">*</span>' : '')?></div>
						<div class="api_controls">
							<? $APPLICATION->IncludeComponent(
								 'bitrix:system.field.edit',
								 $arUserField['USER_TYPE']['USER_TYPE_ID'],
								 array(
										"bVarsFromForm" => false,
										"arUserField"   => $arUserField,
										"form_name"     => $formId,
								 ),
								 null,
								 array('HIDE_ICONS' => 'Y')
							);
							?>
						</div>
					</div>
				<? endforeach; ?>
			<? endif; ?>

			<div class="api_row">
				<div class="api-req"><?=Loc::getMessage("API_AUTH_REGISTER_REQ")?></div>
			</div>

			<? if($arParams['USE_PRIVACY'] == 'Y' && $arParams['MESS_PRIVACY']): ?>
				<div class="api_row api-row-privacy api-row-accept">
					<div class="api_controls">
						<div class="api-accept-label">
							<input type="checkbox"
							       name="PRIVACY_ACCEPTED"
							       value="Y">
							<div class="api-accept-text">
								<? if($arParams['MESS_PRIVACY_LINK']): ?>
									<a href="<?=$arParams['~MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['~MESS_PRIVACY']?></a>
								<? else: ?>
									<?=$arParams['~MESS_PRIVACY']?>
								<? endif ?>
							</div>
							<div class="api-error"><?=$arParams['~MESS_PRIVACY_CONFIRM']?></div>
						</div>
					</div>
				</div>
			<? endif ?>

			<? if($arResult['DISPLAY_USER_CONSENT']): ?>
				<div class="api_row api-row-user-consent api-row-accept">
					<div class="api_controls">
						<? foreach($arResult['DISPLAY_USER_CONSENT'] as $agreementId => $arAgreement): ?>
							<div class="api_control">
								<div class="api-accept-label" data-id="<?=$agreementId?>">
									<input type="checkbox"
									       name="USER_CONSENT_ID[]"
									       value="<?=$agreementId?>">
									<div class="api-accept-text"><?=$arAgreement['LABEL_TEXT'];?></div>
									<div class="api-error"><?=$arParams['~MESS_PRIVACY_CONFIRM']?></div>
								</div>
							</div>
						<? endforeach; ?>
					</div>
				</div>
			<? endif; ?>

			<? /* if($arParams['USE_PRIVACY'] == 'Y' && $arParams['MESS_PRIVACY']): ?>
				<div class="api_row api_privacy">
					<label>
						<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api_field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
						<div class="api_privacy_text">
							<? if($arParams['MESS_PRIVACY_LINK']): ?>
								<a href="<?=$arParams['~MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['~MESS_PRIVACY']?></a>
							<? else: ?>
								<?=$arParams['~MESS_PRIVACY']?>
							<? endif ?>
						</div>
					</label>
				</div>
			<? endif */ ?>
			<div class="api_row">
				<button type="button" class="api_button api_button_primary api_button_large api_button_wait api_width_1_1"><?=Loc::getMessage('API_AUTH_REGISTER_BUTTON')?></button>
			</div>
			<div class="api_row api_grid api_grid_width_1_2">
				<div>
					<a class="api_link api_auth_register_url"
					   href="<?=$arParams['LOGIN_URL']?>"
					   data-header="<?=CUtil::JSEscape($arParams['~LOGIN_MESS_HEADER'])?>"><?=Loc::getMessage('API_AUTH_REGISTER_LOGIN_URL')?></a>
				</div>
				<div class="api_text_right">
					<a class="api_link api_auth_restore_url"
					   href="<?=$arParams['RESTORE_URL']?>"
					   data-header="<?=CUtil::JSEscape($arParams['~RESTORE_MESS_HEADER'])?>"><?=Loc::getMessage('API_AUTH_REGISTER_RESTORE_URL')?></a>
				</div>
			</div>
		</form>
		<? if($arResult['AUTH_SERVICES']): ?>
			<div class="api-soc-auth api_text_center">
				<div class="api-soc-auth-title"><?=Loc::getMessage('API_SOC_AUTH_TITLE')?></div>
				<?
				$APPLICATION->IncludeComponent(
					 "bitrix:socserv.auth.form",
					 "flat",
					 array(
							"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
							"AUTH_URL"      => $arResult["AUTH_URL"],
							"POST"          => $arResult["POST"],
							"POPUP"         => "Y",
					 ),
					 false,
					 array("HIDE_ICONS" => "Y")
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

			$.fn.apiAuthRegister({
				wrapperId: '#<?=$formId?>_wrap',
				formId: '#<?=$formId?>',
				secureAuth: <?=($arResult["SECURE_AUTH"] ? 'true' : 'false')?>,
				secureData: <?=Json::encode($arResult['SECURE_DATA'])?>,
				REQUIRED_FIELDS: <?=Json::encode($arParams['REQUIRED_FIELDS'])?>,
				usePrivacy: '<?=$arParams['USE_PRIVACY'] == 'Y'?>',
				useConsent: '<?=!empty($arResult['DISPLAY_USER_CONSENT'])?>',
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
					data: $.extend({}, data, config),
					error: function (jqXHR, textStatus, errorThrown) {
						console.error('textStatus: ' + textStatus);
						console.error('errorThrown: ' + errorThrown);
					},
					success: function (response) {
						if (!!response.text) {
							$.fn.apiAlert({
								type: 'confirm',
								title: '<?=Loc::getMessage('API_AUTH_REGISTER_USER_CONSENT_TITLE')?>',
								width: 600,
								content: '<textarea rows="50" readonly>' + response.text + '</textarea>',
								labels: {
									ok: '<?=Loc::getMessage('API_AUTH_REGISTER_USER_CONSENT_BTN_ACCEPT')?>',
									cancel: '<?=Loc::getMessage('API_AUTH_REGISTER_USER_CONSENT_BTN_REJECT')?>',
								},
								callback: {
									onConfirm: function (isConfirm) {
										if (isConfirm) {
											checkbox.prop('checked', true).change();
											checkbox.parents('.api_control').find('.api-error').slideUp(200);
										}
										else {
											checkbox.prop('checked', false).change();
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