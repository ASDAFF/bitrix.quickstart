<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="auth-forms auth">
	<form name="bform" method="post" action="<?php echo $arResult['AUTH_URL'] ?>">
		<div class="input">
			<label for="auth_user_login"><?php echo GetMessage('AUTH_LOGIN') ?> <span class="required">*</span>:</label>
			<input id="auth_user_login" type="text" name="USER_LOGIN" maxlength="255" value="<?php echo $arResult['LAST_LOGIN'] ?>">
		</div>
		<div class="input">
			<label for="auth_user_login"><?php echo GetMessage('AUTH_CHECKWORD') ?> <span class="required">*</span>:</label>
			<input id="auth_user_login" type="text" name="USER_CHECKWORD" maxlength="255" value="<?php echo $arResult['USER_CHECKWORD'] ?>">
		</div>
		<div class="input">
			<label for="auth_user_password"><?php echo GetMessage('AUTH_NEW_PASSWORD_REQ') ?> <span class="required">*</span>:</label>
			<input id="auth_user_password" type="password" name="USER_PASSWORD" maxlength="255" value="<?php echo$arResult['USER_PASSWORD']?>" />
		</div>
		<div class="input">
			<label for="auth_user_password"><?php echo GetMessage('AUTH_NEW_PASSWORD_CONFIRM') ?> <span class="required">*</span>:</label>
			<input id="auth_user_password" type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?php echo$arResult['USER_CONFIRM_PASSWORD']?>" />
		</div>
		<div class="input">
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="CHANGE_PWD">
			<?php if (strlen($arResult['BACKURL']) > 0): ?>
				<input type="hidden" name="backurl" value="<?php echo $arResult['BACKURL'] ?>">
			<?php endif ?>
			<input type="submit" name="change_pwd" value="<?php echo GetMessage('AUTH_CHANGE') ?>">
		</div>
	</form>
</div>