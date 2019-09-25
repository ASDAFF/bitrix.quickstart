<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arParamsToDelete = array(
	"login",
	"logout",
	"register",
	"forgot_password",
	"change_password",
	"confirm_registration",
	"confirm_code",
	"confirm_user_id",
);

$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes", array_merge($arParamsToDelete, array("logout_butt", "backurl")), $get_index_page=false);

$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/js/main/utils.js");
?><noindex><?
if ($arResult["FORM_TYPE"] == "login"):
?>
<div id="forum-login-form-window">

<a href="" onclick="return ForumCloseLoginForm()" rel="nofollow" style="float:right;"><?=GetMessage("AUTH_CLOSE_WINDOW")?></a>

<form method="post" target="_top" action="<?=POST_FORM_ACTION_URI?>">
	<?
	if (strlen($arResult["BACKURL"]) > 0)
	{
	?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?
	}
	?>
	<?
	foreach ($arResult["POST"] as $key => $value)
	{
	?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?
	}
	?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />

	<table width="95%">
			<tr>
				<td colspan="2">
				<?=GetMessage("AUTH_LOGIN")?>:<br />
				<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" /></td>
			</tr>
			<tr>
				<td colspan="2">
				<?=GetMessage("AUTH_PASSWORD")?>:<br />
				<input type="password" name="USER_PASSWORD" maxlength="50" size="17" /></td>
			</tr>
		<?
		if ($arResult["STORE_PASSWORD"] == "Y") 
		{
		?>
			<tr>
				<td valign="top"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /></td>
				<td width="100%"><label for="USER_REMEMBER"><?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
			</tr>
		<?
		}
		if ($arResult["CAPTCHA_CODE"])
		{
		?>
			<tr>
				<td colspan="2">
				<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
				<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
				<input type="text" name="captcha_word" maxlength="50" value="" /></td>
			</tr>
		<?
		}
		?>
			<tr>
				<td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
			</tr>

			<tr>
				<td colspan="2"><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></td>
			</tr>
		<?
		if($arResult["NEW_USER_REGISTRATION"] == "Y")
		{
		?>
			<tr>
				<td colspan="2"><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a><br /></td>
			</tr>
		<?
		}
		?>
	</table>	
</form>
</div>
<a href="<?=$arResult["AUTH_URL"]?>" onclick="return ForumShowLoginForm(this);" target="_self" rel="nofollow"><span><?=GetMessage("AUTH_LOGIN_BUTTON")?></span></a>
<?
else:
?>
<a href="<?
	?><?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("logout=yes",
	array("login", "logout", "register", "forgot_password", "change_password", BX_AJAX_PARAM_ID)))?><?
	?>" rel="nofollow"><span><?=GetMessage("AUTH_LOGOUT_BUTTON")?></span></a>
<?
endif;
?>
</noindex>