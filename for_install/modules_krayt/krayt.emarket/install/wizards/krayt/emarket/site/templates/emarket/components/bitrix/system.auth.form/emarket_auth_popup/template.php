<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<div class="popup_login_page">
	<?
	ShowMessage($arParams["~AUTH_RESULT"]);
	ShowMessage($arResult['ERROR_MESSAGE']);
	?>
	<?if($arResult["AUTH_SERVICES"]):?>
    <div class="close"></div>
	<h2><?echo GetMessage("AUTH_TITLE")?></h2>
	<?endif?>
	<?/*if($arResult["AUTH_SERVICES"]):
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
			array(
				"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
				"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
				"AUTH_URL"=> ($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"]),
				"POST"=>$arResult["POST"],
				"SUFFIX" => "main",
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);
	endif;*/?>
	<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="" onsubmit="return AuthPopup.login();" class="bx_auth_form">
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if (strlen($arParams["BACKURL"]) > 0 || strlen($arResult["BACKURL"]) > 0):?>
			<input type="hidden" name="backurl" value="<?=($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"])?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>

		<input class="input_text_style" type="text" id="user_login" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=GetMessage("AUTH_LOGIN")?>"/>
		<input class="input_text_style" type="password" id="user_password" name="USER_PASSWORD" maxlength="255" placeholder="<?=GetMessage("AUTH_PASSWORD")?>"/>

        <div class="login-error"></div>

		<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<span class="rememberme"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" checked/><label for="USER_REMEMBER"><?=GetMessage("AUTH_REMEMBER_ME")?></label></span>
		<?endif?>

		<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
		<noindex>
			<span class="forgotpassword"><a href="javascript:void(0);" class="rm-pass-link" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></span>
		</noindex>
		<?endif?>
		<input type="submit" name="Login" class="bt_green login-btn" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
	</form>
    <div id="rm_pass_block" class="remember-pass-block">
        <div class="close-rm-pass-btn"></div>
        <h3><?=GetMessage("AUTH_PASS_RECOVERY")?></h3>
        <form name="remember_me_form" class="bx_pass_recovery_form" action="" onsubmit="return AuthPopup.sendRmPass();">

            <div class="compact-block">
                <input type="email" class="input_text_style" id="user_email"/>
                <input type="submit" value="<?=GetMessage("AUTH_OK")?>" class="right-btn bt_green"/>
            </div>
            <p class="rm-pass-message"><?=GetMessage("AUTH_PASS_RECOVERY_TEXT")?></p>
            <p class="rm-pass-error-message">&nbsp;</p>
        </form>
    </div>
</div>
<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>
