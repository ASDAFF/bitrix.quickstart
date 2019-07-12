<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED): ?>
	<?php 
	echo '<div style="border: solid 1px #000; padding: 5px; font-weight:bold; color: #ff0000;">';
	echo GetMessage('PRMEDIA_MINIMARKET_DEMO_EXPIRED');
	echo '</div>';
	return;
	?>
<?php endif; ?>
<?php
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
?>
<a href="<?php echo $APPLICATION->GetCurPage() ?>?logout=yes" class="logout-link">&nbsp;</a>
<div class="auth-forms profile-form">
	<?php ShowError($arResult['strProfileError']); ?>
	<?php
	if ($arResult['DATA_SAVED'] == 'Y')
	{
		ShowNote(GetMessage('PROFILE_DATA_SAVED'));
	}
	?>
	<form method="post" action="<?php echo $arResult['FORM_TARGET'] ?>" enctype="multipart/form-data">
		<?php echo $arResult['BX_SESSION_CHECK'] ?>
		<input type="hidden" name="lang" value="<?php echo LANG ?>">
		<input type="hidden" name="ID" value="<?php echo $arResult['ID'] ?>">
		<div class="clearfix">
			<div class="input">
				<label for="profile_user_name"><?php echo GetMessage('NAME') ?> <span class="required">*</span>:</label>
				<input id="profile_user_name" type="text" name="NAME" maxlength="255" value="<?php echo $arResult['arUser']['NAME'] ?>">
			</div>
			<div class="input">
				<label for="profile_user_last_name"><?php echo GetMessage('LAST_NAME') ?> <span class="required">*</span>:</label>
				<input id="profile_user_last_name" type="text" name="LAST_NAME" maxlength="255" value="<?php echo $arResult['arUser']['LAST_NAME'] ?>">
			</div>
		</div>
		<div class="clearfix">
			<div class="input">
				<label for="profile_user_second_name"><?php echo GetMessage('SECOND_NAME') ?> <span class="required">*</span>:</label>
				<input id="profile_user_second_name" type="text" name="SECOND_NAME" maxlength="255" value="<?php echo $arResult['arUser']['SECOND_NAME'] ?>">
			</div>
			<div class="input">
				<label for="profile_user_login"><?php echo GetMessage('LOGIN') ?> <span class="required">*</span>:</label>
				<input id="profile_user_login" type="text" autocomplete="off" name="LOGIN" maxlength="255" value="<?php echo $arResult['arUser']['LOGIN'] ?>">
			</div>
		</div>
		<?php if ($arResult['arUser']['EXTERNAL_AUTH_ID'] == ''): ?>
			<div class="clearfix">
				<div class="input">
					<label for="profile_user_new_password"><?php echo GetMessage('NEW_PASSWORD_REQ') ?> <span class="required">*</span>:</label>
					<input id="profile_user_new_password" type="password" autocomplete="off" name="NEW_PASSWORD" maxlength="255" />
				</div>
				<div class="input">
					<label for="profile_user_new_password_confirm"><?php echo GetMessage('NEW_PASSWORD_CONFIRM') ?> <span class="required">*</span>:</label>
					<input id="profile_user_new_password_confirm" type="password" autocomplete="off" name="NEW_PASSWORD_CONFIRM" maxlength="255" />
				</div>
			</div>
		<?php endif; ?>
		<div class="clearfix">
			<div class="input">
				<label for="profile_user_email"><?php echo GetMessage('EMAIL') ?> <span class="required">*</span>:</label>
				<input id="profile_user_email" type="text" name="EMAIL" maxlength="255" value="<?php echo $arResult['arUser']['EMAIL'] ?>">
			</div>
		</div>
		<div class="clearfix">
			<div class="input" style="margin-top: 1em;">
				<input type="submit" name="save" value="<?php echo ($arResult['ID'] > 0 ? GetMessage('MAIN_SAVE') : GetMessage('MAIN_ADD')) ?>">
			</div>
		</div>
	</form>
</div>