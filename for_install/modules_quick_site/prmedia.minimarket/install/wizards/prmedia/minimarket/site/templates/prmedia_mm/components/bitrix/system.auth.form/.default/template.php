<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if ($USER->IsAuthorized()): ?>
	<?php if (!empty($arParams['PRMEDIA_AUTH_FORM_REDIRECT_URL'])): ?>
		<?php LocalRedirect($arParams['PRMEDIA_AUTH_FORM_REDIRECT_URL']); ?>
	<?php endif; ?>
	<?php echo GetMessage('PRMEDIA_AUTH_FORM_USER_IS_AUTHORIZED'); ?>
	<?php return ?>
<?php endif; ?>
<?php if ($arParams['PRMEDIA_AUTH_FORM_SHOW_TITLE'] == 'Y'): ?>
	<h1><?php $APPLICATION->ShowTitle(false); ?></h1>
<?php endif; ?>
<?php if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR_MESSAGE']): ?>
	<?php ShowMessage($arResult['ERROR_MESSAGE']); ?>
<?php endif; ?>
<div class="auth-forms auth">
	<form name="system_auth_form_<?php echo $arResult['RND'] ?>" method="post" target="_top" action="<?php echo $arResult['AUTH_URL'] ?>">
		<div class="input">
			<label for="auth_user_login"><?php echo GetMessage('AUTH_LOGIN') ?> <span class="required">*</span>:</label>
			<input id="auth_user_login" type="text" name="USER_LOGIN" maxlength="255" value="<?php echo $arResult['LAST_LOGIN'] ?>">
		</div>
		<div class="input">
			<label for="auth_user_password"><?php echo GetMessage('AUTH_PASSWORD') ?> <span class="required">*</span>:</label>
			<input id="auth_user_password" type="password" name="USER_PASSWORD" maxlength="255" />
		</div>
		<?php if ($arResult["CAPTCHA_CODE"]): ?>
			<div class="input">
				<input type="hidden" name="captcha_sid" value="<? echo $arResult['CAPTCHA_CODE'] ?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>" width="80%" alt="CAPTCHA" />
				<label for="auth_captcha"><?php echo GetMessage('AUTH_CAPTCHA_PROMT') ?> <span class="required">*</span>:
				<input id="auth_captcha" type="text" name="captcha_word" maxlength="50" value="" size="15">
			</div>
		<?php endif; ?>
		<div class="input">
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="AUTH">
			<?php if (strlen($arResult['BACKURL']) > 0): ?>
				<input type="hidden" name="backurl" value="<?php echo $arResult['BACKURL'] ?>">
			<?php endif ?>
			<?php foreach ($arResult['POST'] as $key => $value): ?>
				<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>">
			<?php endforeach ?>
			<input type="submit" name="Login" value="<?php echo GetMessage('AUTH_AUTHORIZE') ?>">
		</div>
		<?php if ($arParams['NOT_SHOW_LINKS'] != 'Y'): ?>
			<p class="default-reg"><noindex><a href="<?php echo $arParams['FORGOT_PASSWORD_URL'] ?>" rel="nofollow"><?php echo GetMessage('AUTH_FORGOT_PASSWORD_2') ?></a></noindex>
				<? if ($arResult['NEW_USER_REGISTRATION'] == 'Y' && $arParams['AUTHORIZE_REGISTRATION'] != 'Y'): ?>
					<noindex style="display: block; margin: 10px 0 0;"><a href="<?php echo $arParams['REGISTER_URL'] ?>" rel="nofollow"><?php echo GetMessage('AUTH_REGISTER') ?></a></noindex>
				<? endif; ?>
			</p>
		<?php endif ?>
	</form>
</div>
<div class="default-reg">
<?php if ($arResult['AUTH_SERVICES']): ?>
	<p>
	<div><?php echo GetMessage('PRMEDIA_AUTH_FORM_ENTER_AS') ?>:</div>
	<?
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", array(
		"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
		"SUFFIX" => "form",
		), $component, array("HIDE_ICONS" => "Y")
	);
	?>
</p>
	<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", array(
		"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
		"CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
		"AUTH_URL" => $arResult["AUTH_URL"],
		"POST" => $arResult["POST"],
		"SHOW_TITLES" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
		"FOR_SPLIT" => $arResult["FOR_INTRANET"] ? 'Y' : 'N',
		"AUTH_LINE" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
		"POPUP"=>"Y",
		"SUFFIX"=>"form"
		), $component, array("HIDE_ICONS" => "Y"));?>
<?php endif; ?>
</div>
