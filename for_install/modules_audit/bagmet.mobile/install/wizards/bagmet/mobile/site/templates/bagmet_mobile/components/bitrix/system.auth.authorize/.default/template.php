<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>
<div class="page_wrapper">
	<div class="auth_form auth_left">
	<?/*if($arResult["AUTH_SERVICES"]):?>
		<div class="bx-auth-title"><?echo GetMessage("AUTH_TITLE")?></div>
	<?endif*/?>
		<h4 class="auth_form_title"><?=GetMessage("AUTH_PLEASE_AUTH")?></h4>

		<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

			<input type="hidden" name="AUTH_FORM" value="Y" />
			<input type="hidden" name="TYPE" value="AUTH" />
			<?if (strlen($arResult["BACKURL"]) > 0):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
			<?endif?>
			<?foreach ($arResult["POST"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>

			<table>
				<tr>
					<td><label for="USER_LOGIN"><?=GetMessage("AUTH_LOGIN")?></label></td>
					<td><input class="bx-auth-input" type="text" name="USER_LOGIN" id="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
				</tr>
				<tr>
					<td><label for="USER_PASSWORD"><?=GetMessage("AUTH_PASSWORD")?></label></td>
					<td><input class="bx-auth-input" type="password" name="USER_PASSWORD" id="USER_PASSWORD" maxlength="255" />
	<?if($arResult["SECURE_AUTH"]):?>
					<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
						<div class="bx-auth-secure-icon"></div>
					</span>
					<noscript>
					<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
						<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
					</span>
					</noscript>
	<script type="text/javascript">
	document.getElementById('bx_auth_secure').style.display = 'inline-block';
	</script>
	<?endif?>
					</td>
				</tr>
				<?if($arResult["CAPTCHA_CODE"]):?>
					<tr>
						<td></td>
						<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
					</tr>
					<tr>
						<td class="bx-auth-label"><?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:</td>
						<td><input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" size="15" /></td>
					</tr>
				<?endif;?>
	<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
				<tr>
					<td></td>
					<td><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label></td>
				</tr>
	<?endif?>
				<tr>
					<td></td>
					<td>
						<br/>
						<input class="login_button" type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
						<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
							<noindex>
								<a class="forgot_pass_link" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
							</noindex>
						<?endif?>
					</td>
				</tr>
			</table>


		</form>
	</div>

	<?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?>
		<div class="auth_reg_link">
		<noindex>
			<h4 class="auth_form_title"><?=GetMessage("AUTH_REGISTER")?></h4>
			<p>
				<?=GetMessage("AUTH_FIRST_ONE")?><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER2")?></a><br />
			</p>
		</noindex>
		</div>
	<?endif?>
	<div class="splitter"></div>
	<br/>

<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
		"AUTH_URL"=>$arResult["AUTH_URL"],
		"POST"=>$arResult["POST"],
	),
	$component,
	array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>
</div>
