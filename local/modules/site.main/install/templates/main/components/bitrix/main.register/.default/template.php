<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="main-register main-register-default">
	<?if($USER->IsAuthorized()) {
		ShowNote(GetMessage('MAIN_REGISTER_AUTH'));
	} else {
		if (count($arResult['ERRORS']) > 0) {
			ShowError(implode("\n", $arResult['ERRORS']));
		} elseif ($arResult['USE_EMAIL_CONFIRMATION'] == 'Y') {
			ShowNote(GetMessage('REGISTER_EMAIL_WILL_BE_SENT'));
		}
		?>
		
		<form class="form form-register" method="post" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data" role="register">
			<?if($arResult['BACKURL']) {
				?><input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>"/><?
			}?>
			
			<h2><?=GetMessage('AUTH_REGISTER')?></h2>
			<?foreach ($arResult['SHOW_FIELDS'] as $field) {
				$required = $arResult['REQUIRED_FIELDS_FLAGS'][$field] == 'Y';
				$domId = 'id-' . md5('register-' . $field);
				?>
				<?if ($field == 'AUTO_TIME_ZONE' && $arResult['TIME_ZONE_ENABLED']) {
					?>
					<div class="form-group">
						<label class="control-label<?=$required ? ' required' : ''?>" for="<?=$domId?>auto"><?=GetMessage('main_profile_time_zones_auto')?></label>
						<select
							class="form-control"
							name="REGISTER[AUTO_TIME_ZONE]"
							id="<?=$domId?>auto"
							<?=$required ? 'required=""' : ''?>
							onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled = this.value != 'N'"
						>
							<option value=""><?=GetMessage('main_profile_time_zones_auto_def')?></option>
							<option value="Y"<?=$arResult['VALUES'][$field] == 'Y' ? ' selected=""' : ''?>><?=GetMessage('main_profile_time_zones_auto_yes')?></option>
							<option value="N"<?=$arResult['VALUES'][$field] == 'N' ? ' selected=""' : ''?>><?=GetMessage('main_profile_time_zones_auto_no')?></option>
						</select>
					</div>
					<div class="form-group">
						<label class="control-label<?=$required ? ' required' : ''?>" for="<?=$domId?>"><?=GetMessage('main_profile_time_zones_zones')?></label>
						<select
							class="form-control"
							name="REGISTER[TIME_ZONE]"
							id="<?=$domId?>"
							<?=$required ? 'required=""' : ''?>
							<?=isset($_REQUEST['REGISTER']['TIME_ZONE']) ? '' : 'disabled=""'?>
						>
							<?foreach ($arResult['TIME_ZONE_LIST'] as $tz => $tzName) {
								?><option value="<?=htmlspecialchars($tz)?>"<?=$arResult['VALUES']['TIME_ZONE'] == $tz ? ' selected=""' : ''?>>
									<?=htmlspecialchars($tzName)?>
								</option><?
							}?>
						</select>
					</div>
					<?
					continue;
				}?>
				
				<div class="form-group has-feedback">
					<label class="control-label<?=$required ? ' required' : ''?>" for="<?=$domId?>"><?=GetMessage('REGISTER_FIELD_' . $field)?></label>
					<?switch ($field) {
						case 'PASSWORD':
							?><input
								class="form-control"
								type="password"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								value="<?=$arResult['VALUES'][$field]?>"
								<?=$required ? 'required=""' : ''?>
								size="30"
								autocomplete="off"
							/><?
							if ($arResult['SECURE_AUTH']) {
								?>
								<noscript>
									<span class="glyphicon glyphicon-unlock form-control-feedback" title="<?=GetMessage('AUTH_NONSECURE_NOTE')?>"></span>
								</noscript>
								<script>
									document.write('<span class="glyphicon glyphicon-lock form-control-feedback" title="<?=GetMessage('AUTH_SECURE_NOTE')?>"></span>');
								</script>
								<?
							}?>
							<p class="help-block"><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']?></p>
							<?
							break;
						
						case 'CONFIRM_PASSWORD':
							?><input
								class="form-control"
								type="password"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								value="<?=$arResult['VALUES'][$field]?>"
								<?=$required ? 'required=""' : ''?>
								size="30"
								autocomplete="off"
							/><?
							break;
						
						case 'PERSONAL_GENDER':
							?><select
								class="form-control"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								<?=$required ? 'required=""' : ''?>
							>
								<option value=""><?=GetMessage('USER_DONT_KNOW')?></option>
								<option value="M"<?=$arResult['VALUES'][$field] == 'M' ? ' selected=""' : ''?>><?=GetMessage('USER_MALE')?></option>
								<option value="F"<?=$arResult['VALUES'][$field] == 'F' ? ' selected=""' : ''?>><?=GetMessage('USER_FEMALE')?></option>
							</select><?
							break;
						
						case 'PERSONAL_COUNTRY':
						case 'WORK_COUNTRY':
							?><select
								class="form-control"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								<?=$required ? 'required=""' : ''?>
							>
								<?foreach ($arResult['COUNTRIES']['reference_id'] as $key => $value) {
									?><option
										value="<?=$value?>"
										<?=$value == $arResult['VALUES'][$field] ? 'selected=""' : ''?>
									>
										<?=$arResult['COUNTRIES']['reference'][$key]?>
									</option><?
								}?>
							</select><?
							break;
						
						case 'PERSONAL_PHOTO':
						case 'WORK_LOGO':
							?><input
								class="form-control widget uploadpicker"
								type="file"
								name="REGISTER_FILES_<?=$field?>"
								id="<?=$domId?>"
								<?=$required ? 'required=""' : ''?>
								size="30"
							/><?
							break;
						
						case 'PERSONAL_NOTES':
						case 'WORK_NOTES':
							?><textarea
								class="form-control"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								<?=$required ? 'required=""' : ''?>
							>
								<?=$arResult['VALUES'][$field]?>
							</textarea><?
							break;
						
						case 'PERSONAL_BIRTHDAY':
							?>
							<small>(<?=$arResult['DATE_FORMAT']?>)</small>
							<input
								class="form-control widget datepicker"
								type="date"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								value="<?=$arResult['VALUES'][$field]?>"
								<?=$required ? 'required=""' : ''?>
								size="10"
							/>
							<?
							break;
						
						default:
							?><input
								class="form-control"
								type="text"
								name="REGISTER[<?=$field?>]"
								id="<?=$domId?>"
								value="<?=$arResult['VALUES'][$field]?>"
								<?=$required ? 'required=""' : ''?>
								size="30"
							/><?
					}
					?>
				</div>
				<?
			}?>
			
			<?if ($arResult['USER_PROPERTIES']['SHOW'] == 'Y') {
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?=trim($arParams['USER_PROPERTY_NAME']) ? $arParams['USER_PROPERTY_NAME'] : GetMessage('USER_TYPE_EDIT_TAB')?></h3>
					</div>
					<div class="panel-body">
						<?foreach ($arResult['USER_PROPERTIES']['DATA'] as $fieldName => $arUserField) {
							$domId = 'id-' . md5('register-' . $fieldName);
							?>
							<div class="form-group">
								<label class="control-label<?=$arUserField['MANDATORY'] == 'Y' ? ' required' : ''?>" for="<?=$domId?>">
									<?=$arUserField['EDIT_FORM_LABEL']?>
								</label>
								<?$APPLICATION->IncludeComponent(
									'bitrix:system.field.edit',
									$arUserField['USER_TYPE']['USER_TYPE_ID'],
									array(
										'bVarsFromForm' => $arResult['bVarsFromForm'],
										'arUserField' => $arUserField,
										'domId' => $domId,
									),
									null,
									array(
										'HIDE_ICONS' => 'Y'
									)
								)?>
							</div>
							<?
						}?>
					</div>
				</div>
				<?
			}?>
			
			<?if($arResult['USE_CAPTCHA'] == 'Y') {
				$isError = false;
				?>
				<div class="form-group group-captcha<?=$isError ? ' has-error' : ''?>">
					<label class="control-label required" for="field-captcha">
						<?/*=GetMessage('FORM_CAPTCHA_TABLE_TITLE')*/?>
						<?=GetMessage('REGISTER_CAPTCHA_PROMT')?>
					</label>
					<div class="row">
						<div class="col-md-4 col-xs-6">
							<img
								class="form-captcha-img"
								src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
								alt="captcha"
							/>
						</div>
						<div class="col-md-8 col-xs-6">
							<input
								class="form-control"
								type="text"
								name="captcha_word"
								id="field-captcha"
								required=""
								value=""
							/>
						</div>
					</div>
					<input type="hidden" name="captcha_sid" value="<?=htmlspecialchars($arResult['CAPTCHA_CODE'])?>"/>
				</div>
				<?
			}?>
			
			<div class="form-group form-toolbar">
				<input type="hidden" name="register_submit_button" value="y"/>
				<button class="btn btn-default" type="submit"><?=GetMessage('AUTH_REGISTER')?></button>
			</div>
			
			<div class="form-group form-info">
				<p class="help-block"><span class="required"></span> &mdash; <?=GetMessage('AUTH_REQ')?></p>
			</div>
		</form>
		<?
	}?>
</div>