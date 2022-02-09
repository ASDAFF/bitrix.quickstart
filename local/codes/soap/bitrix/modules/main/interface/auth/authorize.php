<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$store_password = COption::GetOptionString("main", "store_password", "Y");
$bNeedCaptcha = $APPLICATION->NeedCAPTHAForLogin($last_login);
?>

<div class="login-main-popup-wrap login-popup-wrap<?=$bNeedCaptcha?" login-captcha-popup-wrap" : ""?>" id="authorize">
	<input type="hidden" name="TYPE" value="AUTH">
	<div class="login-popup">
		<div class="login-popup-title"><?=GetMessage('AUTH_TITLE')?></div>
		<div class="login-popup-title-description"><?=GetMessage("AUTH_PLEASE_AUTH")?></div>
		<div class="login-popup-field">
			<div class="login-popup-field-title"><?=GetMessage("AUTH_LOGIN")?></div>
			<div class="login-input-wrap">
				<input type="email" class="login-input" onfocus="BX.addClass(this.parentNode, 'login-input-active')" onblur="BX.removeClass(this.parentNode, 'login-input-active')" name="USER_LOGIN" value="<?echo htmlspecialcharsbx($last_login)?>" tabindex="1">
				<div class="login-inp-border"></div>
			</div>
		</div>
		<div class="login-popup-field" id="authorize_password">
			<div class="login-popup-field-title"><?=GetMessage("AUTH_PASSWORD")?></div>
			<div class="login-input-wrap">
				<input type="password" class="login-input" onfocus="BX.addClass(this.parentNode, 'login-input-active')" onblur="BX.removeClass(this.parentNode, 'login-input-active')" name="USER_PASSWORD" tabindex="2">
				<div class="login-inp-border"></div>
			</div>
			<input type="submit" value="" class="login-btn-green" name="Login" tabindex="4" onfocus="BX.addClass(this, 'login-btn-green-hover');" onblur="BX.removeClass(this, 'login-btn-green-hover')">
			<div class="login-loading">
				<img class="login-waiter" alt="" src="/bitrix/panel/main/images/login-waiter.gif">
			</div>
		</div>
<?
if($store_password=="Y"):
?>
		<div class="login-popup-checbox-block">
			<input type="checkbox" class="adm-designed-checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" tabindex="3" onfocus="BX.addClass(this.nextSibling, 'login-popup-checkbox-label-active')" onblur="BX.removeClass(this.nextSibling, 'login-popup-checkbox-label-active')"><label for="USER_REMEMBER" class="adm-designed-checkbox-label"></label>
			<label for="USER_REMEMBER" class="login-popup-checkbox-label"><?=GetMessage("AUTH_REMEMBER_ME")?></label>
		</div>
<?
endif;

$CAPTCHA_CODE = '';
if($bNeedCaptcha)
	$CAPTCHA_CODE = $APPLICATION->CaptchaGetCode();

?>
		<input type="hidden" name="captcha_sid" value="<?=$CAPTCHA_CODE?>" />
		<div class="login-popup-field login-captcha-field">
			<div class="login-popup-field-title"><?=GetMessage("AUTH_CAPTCHA_PROMT")?></div>
			<div class="login-input-wrap">
				<span class="login-captcha-wrap" id="captcha_image"><?if($bNeedCaptcha):?><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$CAPTCHA_CODE?>" width="180" height="40" alt="CAPTCHA" /><?endif;?></span><input type="text" onfocus="BX.addClass(this.parentNode, 'login-input-active')" onblur="BX.removeClass(this.parentNode, 'login-input-active')" name="captcha_word" class="login-input" tabindex="5" autocomplete="off">
				<div class="login-inp-border"></div>
			</div>
		</div>
<?
if($not_show_links!="Y"):
?>
		<a class="login-popup-link login-popup-forget-pas" href="javascript:void(0)" onclick="BX.adminLogin.toggleAuthForm('forgot_password')"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
<?
endif;
?>
	</div>
</div>
<script type="text/javascript">
BX.adminLogin.registerForm(new BX.authFormAuthorize('authorize', {url: '<?echo CUtil::JSEscape($authUrl."?login=yes".(($s=DeleteParam(array("logout", "login"))) == ""? "":"&".$s));?>'}));
</script>
