<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arResult["FORM_TYPE"] == "login"):?>

<div id="login-form-window">
<div id="login-form-window-header">
<div onclick="return authFormWindow.CloseLoginForm()" id="close-form-window" title="Закрыть окно">Закрыть</div><b>Авторизация</b>
</div>
<form method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<table align="center" cellspacing="0" cellpadding="4">
		<tr>
			<td align="right" width="30%"><?=GetMessage("AUTH_LOGIN")?>:</td>
			<td><input type="text" name="USER_LOGIN" id="auth-user-login" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="12" tabindex="1" /></td>
		</tr>
		<tr>
			<td align="right"><?=GetMessage("AUTH_PASSWORD")?>:</td>
			<td><input type="password" name="USER_PASSWORD" maxlength="50" size="12" tabindex="2" /><br /></td>
		</tr>
		<?if($arResult["CAPTCHA_CODE"]):?>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
		</tr>
		<tr>
			<td><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
			<td><input type="text" name="captcha_word" maxlength="50" value="" tabindex="3" /></td>
		</tr>
		<?endif;?>
		<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
		<tr>
			<td></td>
			<td><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" tabindex="4" checked="checked" /><label class="remember-text" for="USER_REMEMBER"><?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
		</tr>
		<?endif?>
		<tr>
			<td></td>
			<td>
				<input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" tabindex="5" /><br />
				<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a><br />
				<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
					<a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTER")?></a><br />
				<?endif?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
		</tr>
	</table>
</form>
<?if ($arResult["SHOW_ERRORS"] == "Y" && $arResult["ERROR"] === true):?>  
	<span class="errortext"><?=(is_array($arResult["ERROR_MESSAGE"]) ? ShowError($arResult["ERROR_MESSAGE"]["MESSAGE"]) : ShowError($arResult["ERROR_MESSAGE"]))?></span>
  <script>
    authFormWindow.ShowLoginForm();
  </script>
<?endif?>
</div>

<div class="logout">
	<a href="/auth/" onclick="return authFormWindow.ShowLoginForm()"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>
 <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
		<?=GetMessage("AUTH_OR")?> <a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTRATION")?></a><br />
	<?endif?>
</div>

<?else:

	$isNTLM = false;
	if (COption::GetOptionString("ldap", "use_ntlm", "N") == "Y")
	{
		$ntlm_varname = trim(COption::GetOptionString("ldap", "ntlm_varname", "REMOTE_USER"));
		if (array_key_exists($ntlm_varname, $_SERVER) && strlen($_SERVER[$ntlm_varname]) > 0)
			$isNTLM = true;
	}

	$params = DeleteParam(array("logout", "login", "back_url_pub"));
	$logoutUrl = $APPLICATION->GetCurPage()."?logout=yes".htmlspecialchars($params == ""? "":"&".$params);

	$js = '/bitrix/js/main/utils.js';
	$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$js.'?v='.filemtime($_SERVER['DOCUMENT_ROOT'].$js).'"></script>');

	$js = '/bitrix/js/main/popup_menu.js';
	$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$js.'?v='.filemtime($_SERVER['DOCUMENT_ROOT'].$js).'"></script>');
?>
<script>
var items = [
	{'ICONCLASS': 'auth-button-message', 'TEXT': '<b><a href="/company/personal/messages/"><?=GetMessage("AUTH_NEW_MESSAGES")?></a></b><br /><?=GetMessage("AUTH_NEW_MESSAGES_DESCR")?>', 'ONCLICK': 'window.location = \'/company/personal/messages/\';', 'TITLE': '<?=GetMessage("AUTH_NEW_MESSAGES")?>'},
	{'ICONCLASS': 'auth-button-page', 'TEXT': '<b><a href="/company/personal/"><?=GetMessage("AUTH_PERSONAL_PAGE")?></a></b><br /><?=GetMessage("AUTH_PERSONAL_PAGE_DESCR")?>', 'ONCLICK': 'window.location = \'/company/personal/\';', 'TITLE': '<?=GetMessage("AUTH_PERSONAL_PAGE")?>'},
	{'ICONCLASS': 'auth-button-tasks', 'TEXT': '<b><a href="/company/personal/user/<?=$USER->GetID()?>/tasks/"><?=GetMessage("AUTH_TASKS")?></a></b><br /><?=GetMessage("AUTH_TASKS_DESCR")?>', 'ONCLICK': 'window.location = \'/company/personal/user/<?=$USER->GetID()?>/tasks/\';', 'TITLE': '<?=GetMessage("AUTH_TASKS")?>'},
	{'ICONCLASS': 'auth-button-calendar', 'TEXT': '<b><a href="/company/personal/user/<?=$USER->GetID()?>/calendar/"><?=GetMessage("AUTH_CALENDAR")?></a></b><br /><?=GetMessage("AUTH_CALENDAR_DESCR")?>', 'ONCLICK': 'window.location = \'/company/personal/user/<?=$USER->GetID()?>/calendar/\';', 'TITLE': '<?=GetMessage("AUTH_CALENDAR")?>'},
	{'ICONCLASS': 'auth-button-profile', 'TEXT': '<b><a href="<?=$arResult["PROFILE_URL"]?>"><?=GetMessage("AUTH_PROFILE")?></a></b><br /><?=GetMessage("AUTH_PROFILE_DESCR")?>', 'ONCLICK': 'window.location = \'<?=$arResult["PROFILE_URL"]?>\';', 'TITLE': '<?=GetMessage("AUTH_PROFILE")?>'}
];
var xx_menu = new PopupMenu('xx_menu');
function ShowAuthMenu(el)
{
	xx_menu.ShowMenu(el, items);
}
</script>
	<div class="logout">
    <a href="<?=$logoutUrl?>"><?=GetMessage("AUTH_LOGOUT_BUTTON")?></a>
	</div>
<?endif?>