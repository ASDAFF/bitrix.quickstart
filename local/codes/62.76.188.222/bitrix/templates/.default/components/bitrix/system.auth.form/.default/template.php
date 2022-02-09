<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-auth-window" id="b-auth-window">
	<h3 class="b-h3 m-auth__h3">Авторизация</h3>
<?if($arResult["FORM_TYPE"] == "login"):?>

<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>
<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?if($arResult["BACKURL"] <> ''):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	 
	<label class="b-auth__label"><?=GetMessage("AUTH_LOGIN")?></label>
	<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" class="b-text" /> 
	<label class="b-auth__label"><?=GetMessage("AUTH_PASSWORD")?></label>
	<input type="password" name="USER_PASSWORD" maxlength="50" size="17" class="b-text"  />
	<div class="b-auth-remmember clearfix">
		<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<div class="b-auth-remmember__me">
				<label class="b-checkbox">
					<input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" /><?=GetMessage("AUTH_REMEMBER_ME")?>
				</label>
			</div>
		<?endif?>
		<div class="b-auth-remmember__forgot">
			<noindex><a href="/personal/?forgot_password=yes" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
		</div>
	</div>

	<?if ($arResult["CAPTCHA_CODE"]):?>

		<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
		<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
	<?endif?>
		<input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" class="b-button" />
	 
</form>
<?endif?>
</div>

<?
if(!$_REQUEST['forgot_password'] && !$_REQUEST['change_password']){
    if (count($arResult["ERROR_MESSAGE"]["MESSAGE"])){?>
            <script>
            $(document).ready(function() {
                    $("#b-auth__login").trigger('click');
            });
            </script>
    <?}
}?>