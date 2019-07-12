<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>
<? if ($arResult["AUTH_SERVICES"]): ?>
    <div class="b-auth__label"><? echo GetMessage("AUTH_TITLE") ?></div>
    <? endif ?>
<?    if ($arResult["AUTH_SERVICES"]):?>
<div class="b-auth__social">
    <?
    
        $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", array(
            "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
            "CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
            "AUTH_URL" => ($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"]),
            "POST" => $arResult["POST"],
            "SUFFIX" => "main",
                ), $component, array("HIDE_ICONS" => "Y")
        );?>
    
</div>
<hr class="b-separator-h"><?endif;
    ?>
<form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top" action="<?= SITE_DIR ?>auth/<? //=$arResult["AUTH_URL"] ?>" class="bx_auth_form">
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />
        <? if (strlen($arParams["BACKURL"]) > 0 || strlen($arResult["BACKURL"]) > 0): ?>
            <input type="hidden" name="backurl" value="<?= ($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"]) ?>" />
        <? endif ?>
        <? foreach ($arResult["POST"] as $key => $value): ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $value ?>" />
        <? endforeach ?>

        <strong><?= GetMessage("AUTH_LOGIN") ?></strong><br>
        <input class="input_text_style" type="text" name="USER_LOGIN" maxlength="255" value="<?= $arResult["LAST_LOGIN"] ?>" /><br><br>
        <strong><?= GetMessage("AUTH_PASSWORD") ?></strong><br>
        <input class="input_text_style" type="password" name="USER_PASSWORD" maxlength="255" /><br>

        <? if ($arResult["CAPTCHA_CODE"]): ?>
            <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>" />
            <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
            <? echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:
            <input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" size="15" />
        <? endif; ?>
        <span style="display:block;height:7px;"></span>
        <? if ($arResult["STORE_PASSWORD"] == "Y"): ?>
            <span class="rememberme"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" checked/><?= GetMessage("AUTH_REMEMBER_ME") ?></span>
        <? endif ?>

        <? if ($arParams["NOT_SHOW_LINKS"] != "Y"): ?>
            <noindex>
                <span class="forgotpassword" style="padding-left:75px;"><a href="<?= $arParams["AUTH_FORGOT_PASSWORD_URL"] ? $arParams["AUTH_FORGOT_PASSWORD_URL"] : $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>" rel="nofollow"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></a></span>
            </noindex>
        <? endif ?>
        <br><br><input type="submit" name="Login" class="bt_blue big shadow" value="<?= GetMessage("AUTH_AUTHORIZE") ?>" />
    </form>
<!--
<div class="b-auth__label">Или как пользователь Fabika.ru</div>
<form method="POST" action="/account/login/" class="b-auth__form b-auth__login__form">
    <div style="display:none"><input type="hidden" value="mTLlux4lfBuL6jrmwfEtnYuCK3G9OkQg" name="csrfmiddlewaretoken"></div>
    <div class="b-auth__field-label">E-mail:</div>
    <div class="b-auth__field-input">
        <input type="text" name="email">
        <div class="b-auth__switch-link b-auth__login__register">
            <a href="/registration/register/">Я хочу зарегистрироваться</a>
        </div>
    </div>
    <div class="b-auth__field-label">Пароль:</div>
    <div class="b-auth__field-input">
        <input type="password" name="password">
        <div class="b-auth__switch-link b-auth__login__reset_password">
            <a href="/account/password_reset/">Я не помню пароль</a>
        </div>
    </div>
    <div class="b-auth__field-submit">
        <input type="submit" value="Войти">
    </div>
</form>-->
<script type="text/javascript">
<? if (strlen($arResult["LAST_LOGIN"]) > 0): ?>
        try {
            document.form_auth.USER_PASSWORD.focus();
        } catch (e) {
        }
<? else: ?>
        try {
            document.form_auth.USER_LOGIN.focus();
        } catch (e) {
        }
<? endif ?>
</script>

