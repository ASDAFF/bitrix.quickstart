<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
// format errors
if (count($arResult['ERRORS']) > 0)
{
	foreach ($arResult['ERRORS'] as $key => $error)
	{
		if (intval($key) == 0 && $key !== 0)
		{
			$arResult['ERRORS'][$key] = str_replace('#FIELD_NAME#', '&quot;' . GetMessage('REGISTER_FIELD_' . $key) . '&quot;', $error);
		}
	}
}
?>
<div class="auth-forms">
	<p class="default-reg"><?php echo GetMessage('PRMEDIA_MM_MR_TOP_NOTE') ?> <a href="<?php echo $arParams['PATH_TO_AUTH'] ?>" rel="nofollow"><?php echo GetMessage('PRMEDIA_MM_MR_AUTH_LINK') ?></a>.</p>

	<?php if (count($arResult['ERRORS']) > 0): ?>
		<p><?php echo ShowError(implode('<br />', $arResult['ERRORS'])) ?></p>
	<?php elseif ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y'): ?>
		<p><?php echo GetMessage('REGISTER_EMAIL_WILL_BE_SENT') ?></p>
	<?php endif; ?>

	<form method="post" action="<?php echo POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data">
		<?php $index = 0; ?>
		<?php foreach ($arResult['SHOW_FIELDS'] as $field): ?>
			<?php if ($index % 2 == 0): ?>
				<div class="clearfix">
			<?php endif; ?>
			<?php if ($field == 'AUTO_TIME_ZONE' && $arResult['TIME_ZONE_ENABLED'] == true): ?>
				<?php $index--; ?>
				<div class="input">
					<label><?php echo GetMessage('main_profile_time_zones_auto') ?><?php if ($arResult['REQUIRED_FIELDS_FLAGS'][$field] === 'Y'): ?> <span class="required">*</span><?php endif; ?></label>
					<select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled = (this.value != 'N')">
						<option value=""><?php echo GetMessage('main_profile_time_zones_auto_def') ?></option>
						<option value="Y"<?php echo $arResult['VALUES'][$field] === 'Y' ? ' selected="selected"' : '' ?>><?php echo GetMessage('main_profile_time_zones_auto_yes') ?></option>
						<option value="N"<?php echo $arResult['VALUES'][$field] === 'N' ? ' selected="selected"' : '' ?>><?php echo GetMessage('main_profile_time_zones_auto_no') ?></option>
					</select>
				</div>
				<div class="input">
					<label for="register_time_zone"><?php echo GetMessage('main_profile_time_zones_zones') ?>:</label>
					<select id="register_time_zone" name="REGISTER[TIME_ZONE]"<?php echo !isset($_REQUEST['REGISTER']['TIME_ZONE']) ? ' disabled="disabled"' : '' ?>>
						<?php foreach ($arResult['TIME_ZONE_LIST'] as $tz => $tz_name): ?>
							<option value="<?php echo htmlspecialcharsbx($tz) ?>"<?php echo $arResult['VALUES']['TIME_ZONE'] == $tz ? ' selected="selected"' : '' ?>><?php echo htmlspecialcharsbx($tz_name) ?></option>
						<?php endforeach ?>
					</select>
				</div>
			<?php else: ?>
				<div class="input">
					<label for="REGISTER_FIELD_<?php echo $field ?>"><?php echo GetMessage('REGISTER_FIELD_' . $field) ?><?php if ($arResult['REQUIRED_FIELDS_FLAGS'][$field] === 'Y'): ?> <span class="required">*</span><?php endif; ?>:</label>
				<?php
				switch ($field):
					case 'PASSWORD':
						?>
						<input size="30" type="password" name="REGISTER[<?php echo $field ?>]" value="<?php echo $arResult["VALUES"][$field] ?>" autocomplete="off" />
						<?php
						break;
					case 'CONFIRM_PASSWORD':
						?>
						<input size="30" type="password" name="REGISTER[<?php echo $field ?>]" value="<?php echo $arResult["VALUES"][$field] ?>" autocomplete="off" />
							<?php
						break;
					case 'PERSONAL_GENDER':
						?><select id="REGISTER_FIELD_<?php echo $field ?>" name="REGISTER[<?php echo $field ?>]">
							<option value=""><?php echo GetMessage('USER_DONT_KNOW') ?></option>
							<option value="M"<?php echo $arResult['VALUES'][$field] == 'M' ? ' selected="selected"' : '' ?>><?php echo GetMessage('USER_MALE') ?></option>
							<option value="F"<?php echo $arResult['VALUES'][$field] == 'F' ? ' selected="selected"' : '' ?>><?php echo GetMessage('USER_FEMALE') ?></option>
						</select>
						<?php
						break;
					case 'PERSONAL_COUNTRY':
					case 'WORK_COUNTRY':
						?><select id="REGISTER_FIELD_<?php echo $field ?>" name="REGISTER[<?php echo $field ?>]">
							<?php
							foreach ($arResult['COUNTRIES']['reference_id'] as $key => $value)
							{
								?><option value="<?php echo $value ?>"<?php if ($value == $arResult['VALUES'][$field]): ?> selected="selected"<?php endif; ?>><?php echo $arResult['COUNTRIES']['reference'][$key] ?></option>
								<?php
							}
							?></select>
						<?php
						break;
					case 'PERSONAL_PHOTO':
					case 'WORK_LOGO':
						?><input id="REGISTER_FIELD_<?php echo $field ?>" size="30" type="file" name="REGISTER_FILES_<?php echo $field ?>">
							<?php
							break;
						case 'PERSONAL_NOTES':
						case 'WORK_NOTES':
							?><textarea id="REGISTER_FIELD_<?php echo $field ?>" cols="30" rows="5" name="REGISTER[<?php echo $field ?>]"><?php echo $arResult['VALUES'][$field] ?></textarea>
							<?php
						break;
					default:
						if ($field == 'PERSONAL_BIRTHDAY'): ?>
							<small><?php echo $arResult["DATE_FORMAT"] ?></small><br />
						<?php endif; ?>
						<input id="REGISTER_FIELD_<?php echo $field ?>" size="30" type="text" name="REGISTER[<?php echo $field ?>]" value="<?php echo $arResult['VALUES'][$field] ?>" />
							<?php
						if ($field == "PERSONAL_BIRTHDAY"):
							$APPLICATION->IncludeComponent(
								'bitrix:main.calendar', '', array(
								'SHOW_INPUT' => 'N',
								'FORM_NAME' => 'regform',
								'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
								'SHOW_TIME' => 'N'
								), null, array('HIDE_ICONS' => 'Y')
							);
							?>
						<?php endif; ?>
				<?php endswitch; ?>
				</div>
			<?php endif; ?>
			<?php if ($index % 2 == 1): ?>
				</div>
			<?php endif; ?>
			<?php $index++ ?>
		<?php endforeach; ?>

		<!-- user properties -->
		<?php if ($arResult['USER_PROPERTIES']['SHOW'] === 'Y'): ?>
			<?php foreach ($arResult['USER_PROPERTIES']['DATA'] as $fn => $arUserField): ?>
				<?php if ($index % 2 == 0): ?>
					<div class="clearfix">
				<?php endif; ?>
				<div class="input">
					<label for="<?php echo $fn ?>"><?php echo $arUserField['EDIT_FORM_LABEL'] ?><?php if ($arUserField['MANDATORY'] == 'Y'): ?> <span class="required">*</span><?php endif; ?>:</label>
				<?php
				$APPLICATION->IncludeComponent(
					"bitrix:system.field.edit",
					$arUserField['USER_TYPE']['USER_TYPE_ID'],
					array(
						'bVarsFromForm' => $arResult['bVarsFromForm'],
						'arUserField' => $arUserField,
						'form_name' => 'regform',
						'id' => $fn
					), null, array('HIDE_ICONS' => 'Y'));
				?>
				</div>
				<?php if ($index % 2 == 1): ?>
					</div>
				<?php endif; ?>
				<?php $index++ ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ($index % 2 == 1): ?>
			</div>
		<?php endif; ?>

		<?php if ($arResult['USE_CAPTCHA'] == 'Y'): ?>
			<div class="input captcha">
				<input type="hidden" name="captcha_sid" value="<?php echo $arResult["CAPTCHA_CODE"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?php echo $arResult["CAPTCHA_CODE"]?>" alt="CAPTCHA" />
				<label for="reg_captcha_word"><?php echo GetMessage('PRMEDIA_MM_MR_CAPTCHA') ?> <span class="required">*</span>:</label>
				<input id="reg_captcha_word" type="text" name="captcha_word" maxlength="50" value="">
			</div>
		<?php endif; ?>
		<div class="clearfix">
			<div class="input">
				<?php if (strlen($arResult['BACKURL']) > 0): ?>
					<input type="hidden" name="backurl" value="<?php echo $arResult['BACKURL'] ?>" />
				<?php endif; ?>
				<input type="submit" name="register_submit_button" value="<?php echo GetMessage('PRMEDIA_MM_MR_SUBMIT') ?>">
			</div>
		</div>
	</form>
</div>