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
<div class="log r-star-shape">
    <div class="cn tl"></div>
    <div class="cn tr"></div>
    <div class="cnt">
     <div class="userpic roundBorder roundBorder1">
         <div class="no-photo"></div>
         <div class="c tl"></div>
         <div class="c tr"></div>
         <div class="c bl"></div>
         <div class="c br"></div>
     </div>
     <div class="username">
      <h5 class="login">
      <a href="/auth/" onclick="return authFormWindow.ShowLoginForm()"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>
      <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
		     <?=GetMessage("AUTH_OR")?> <a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTRATION")?></a><br />
	     <?endif?>
      </h5>
     </div>
    </div>
    <div class="cn bl"></div>
    <div class="cn br"></div>
</div>
<?/*
<div class="logout">
	<a href="/auth/" onclick="return authFormWindow.ShowLoginForm()"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>
 <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
		<?=GetMessage("AUTH_OR")?> <a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTRATION")?></a><br />
	<?endif?>
</div>
*/?>
<?else:?>
<?endif?>