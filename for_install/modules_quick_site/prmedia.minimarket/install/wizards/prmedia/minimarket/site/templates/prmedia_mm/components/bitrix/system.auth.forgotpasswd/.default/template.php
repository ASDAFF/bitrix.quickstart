<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if ($USER->IsAuthorized()): ?>
	<?php if (!empty($arParams['PRMEDIA_FORGOT_PASS_REDIRECT_URL'])): ?>
		<?php LocalRedirect($arParams['PRMEDIA_FORGOT_PASS_REDIRECT_URL']); ?>
		<?php return; ?>
	<?php endif; ?>
<?php endif; ?>
<?php if (!empty($_POST['forgot_pwd_complite'])): ?>
	<?php echo GetMessage('PRMEDIA_FORGOT_PASS_SUBMIT_MESSAGE'); ?>
	<?php return; ?>
<?php endif; ?>
<?php ShowMessage($arParams['~AUTH_RESULT']); ?>
<div class="auth-forms fp">
	<form name="bform" method="post" action="<?php echo POST_FORM_ACTION_URI ?>">
		<p><?php echo GetMessage('AUTH_FORGOT_PASSWORD_0')?> <a href="<?php echo $arParams['PRMEDIA_FORGOT_PASS_PATH_TO_AUTH'] ?>"><?php echo GetMessage("AUTH_AUTH") ?></a><br>
			<?php echo GetMessage("AUTH_FORGOT_PASSWORD_1") ?>
		</p>
		<div class="input">
			<label for="fp_user_login"> <?php echo GetMessage('AUTH_LOGIN') ?></label>
			<input id="fp_user_login" type="text" name="USER_LOGIN" maxlength="50" value="<?php echo $arResult['LAST_LOGIN'] ?>">
		</div>
		<div class="input">
			<label for="fp_user_email"><?php echo GetMessage("AUTH_EMAIL") ?></label>
			<input id="fp_user_email" type="text" name="USER_EMAIL" maxlength="255">
		</div>
		<div class="input">
			<?php if (strlen($arResult['BACKURL']) > 0): ?>
				<input type="hidden" name="backurl" value="<?php echo $arResult['BACKURL'] ?>" />
			<?php endif; ?>
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="SEND_PWD">
			<input type="hidden" name="forgot_pwd_complite" value="Y" />
			<input type="submit" name="send_account_info" value="<?php echo GetMessage("AUTH_SEND") ?>" />
		</div>
	</form>
</div>
<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
</script>