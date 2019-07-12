<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



<?if($arResult["FORM_TYPE"] == "login"):?>
	<script type="text/javascript" src="/bitrix/js/socialservices/ss.js"></script>
	<?
	if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
		ShowMessage($arResult['ERROR_MESSAGE']);
	?>

<form name="system_auth_form<?=$arResult["RND"]?>" method="post" id="system-login-form" target="_top" action="<?=$arParams["PROFILE_URL"]?>">
	<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?foreach ($arResult["POST"] as $key => $value):?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />

    <div class="pop-form">
		<div>
			<label><?=GetMessage("AUTH_LOGIN")?>:</label>
			<input type="text" class="input_text_style" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" />
		</div>

		<div>
			<noindex><a class="rem-pass" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
			<label><?=GetMessage("AUTH_PASSWORD")?></label>
			<input class="input_text_style" type="password" name="USER_PASSWORD" maxlength="50" size="17" />
		</div>


		<?if($arResult["SECURE_AUTH"]):?>
			<span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
				<div class="bx-auth-secure-icon"></div>
			</span>
			<noscript>
			<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
				<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
			</span>
			</noscript>
			<script type="text/javascript">
			document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
			</script>
		<?endif?>

		<?if ($arResult["CAPTCHA_CODE"]):?>
			<div>
				<label><?echo GetMessage("AUTH_CAPTCHA_PROMT")?></label>
				<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
				<input type="text" name="captcha_word" maxlength="50" value="" />
			</div>
		<?endif?>


		<div class="but">
			<input type="hidden" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
			<a class="button" rel="system-login-form"><span><?=GetMessage("AUTH_LOGIN_BUTTON")?></span></a>
		</div>

			<input type="image" src="<?=SITE_TEMPLATE_PATH?>/images/blank.gif" width="1" height="1" alt="" />


	</div>


</form>

<?else:?>

<form action="<?=$arResult["AUTH_URL"]?>">
	<table width="95%">
		<tr>
			<td align="center">
				<?=$arResult["USER_NAME"]?><br />
				[<?=$arResult["USER_LOGIN"]?>]<br />
				<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>
</form>
<?endif?>

