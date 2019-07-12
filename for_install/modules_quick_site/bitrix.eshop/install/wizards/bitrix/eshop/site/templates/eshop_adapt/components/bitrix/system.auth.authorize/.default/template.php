<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="login_page">
	<?
	ShowMessage($arParams["~AUTH_RESULT"]);
	ShowMessage($arResult['ERROR_MESSAGE']);
	?>
	<?if($arResult["AUTH_SERVICES"]):?>
	<h2><?echo GetMessage("AUTH_TITLE")?></h2>
	<?endif?>
	<?if($arResult["AUTH_SERVICES"]):
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
			array(
				"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
				"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
				"AUTH_URL"=>$arResult["AUTH_URL"],
				"POST"=>$arResult["POST"],
				"SUFFIX" => "main",
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);
	endif;?>
	<form name="form_auth" method="post" target="_top" action="<?=SITE_DIR?>auth/<?//=$arResult["AUTH_URL"]?>" class="bx_auth_form">
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if (strlen($arParams["BACKURL"]) > 0 || strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"])?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>

		<strong><?=GetMessage("AUTH_LOGIN")?></strong><br>
		<input class="input_text_style" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" /><br><br>
		<strong><?=GetMessage("AUTH_PASSWORD")?></strong><br>
		<input class="input_text_style" type="password" name="USER_PASSWORD" maxlength="255" /><br>
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

		<?if($arResult["CAPTCHA_CODE"]):?>
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:
			<input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" size="15" />
		<?endif;?>
		<span style="display:block;height:7px;"></span>
		<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<span class="rememberme"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" checked/><?=GetMessage("AUTH_REMEMBER_ME")?></span>
		<?endif?>

		<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
		<noindex>
			<span class="forgotpassword" style="padding-left:75px;"><a href="<?=$arParams["AUTH_FORGOT_PASSWORD_URL"] ? $arParams["AUTH_FORGOT_PASSWORD_URL"] : $arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></span>
		</noindex>
		<?endif?>
		<br><br><input type="submit" name="Login" class="bt_blue big shadow" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
	</form>
</div>
<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>

