<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-authorize<?=$arParams['USE_ADAPTABILITY'] == 'Y' ? ' adaptiv' : ''?>">
    <?$frame = $this->createFrame()->begin();?>
        <?if (!empty($arParams['AUTH_RESULT']['MESSAGE'])):?>
            <div class="startshop-authorize-notify">
                <?=html_entity_decode($arParams['AUTH_RESULT']['MESSAGE'])?>
            </div>
        <?endif;?>
        <div class="startshop-authorize-authorize">
            <div class="startshop-authorize-authorize-title"><?echo GetMessage("STARTSHOP_AUTHORIZE_AUTHORIZE_TITLE")?></div>
            <div class="startshop-authorize-authorize-form">
                <form name="startshop_authorize_form" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" class="bx_auth_form">
                    <input type="hidden" name="AUTH_FORM" value="Y" />
                    <input type="hidden" name="TYPE" value="AUTH" />
                    <?if (strlen($arParams["BACKURL"]) > 0 || strlen($arResult["BACKURL"]) > 0):?>
                        <input type="hidden" name="backurl" value="<?=($arParams["BACKURL"] ? $arParams["BACKURL"] : $arResult["BACKURL"])?>" />
                    <?endif?>
                    <?foreach ($arResult["POST"] as $key => $value):?>
                        <input type="hidden" name="<?=htmlspecialcharsbx($key)?>" value="<?=htmlspecialcharsbx($value)?>" />
                    <?endforeach?>
                    <input class="startshop-authorize-authorize-form-input-text startshop-input-text startshop-input-text-standart" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=GetMessage('STARTSHOP_AUTHORIZE_AUTHORIZE_FORM_LOGIN_PLACEHOLDER')?>"/>
                    <input class="startshop-authorize-authorize-form-input-text startshop-input-text startshop-input-text-standart" type="password" name="USER_PASSWORD" maxlength="255" placeholder="<?=GetMessage('STARTSHOP_AUTHORIZE_AUTHORIZE_FORM_PASSWORD_PLACEHOLDER')?>"/>
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
                    <?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
                        <div class="startshop-authorize-authorize-form-forgot_password">
                            <a class="startshop-link startshop-link-standart" href="<?=$arParams["AUTH_FORGOT_PASSWORD_URL"] ? $arParams["AUTH_FORGOT_PASSWORD_URL"] : $arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("STARTSHOP_AUTHORIZE_AUTHORIZE_FORM_FORGOT_PASSWORD")?></a>
                        </div>
                    <?endif?>
                    <?if ($arResult["STORE_PASSWORD"] == "Y"):?>
                        <label class="startshop-authorize-authorize-form-remember_me startshop-button-checkbox">
                            <input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" checked/>
                            <div class="selector"></div>
                            <div class="text"><?=GetMessage("STARTSHOP_AUTHORIZE_AUTHORIZE_FORM_REMEMBER_ME")?></div>
                        </label>
                    <?endif?>
                        <div class="startshop-authorize-authorize-form-authorize">
                            <input type="submit" name="Login" class="startshop-button startshop-button-standart" value="<?=GetMessage("STARTSHOP_AUTHORIZE_AUTHORIZE_FORM_AUTHORIZE")?>" />
                        </div>
                    <div class="clear"></div>
                </form>
            </div>
        </div>
        <div class="startshop-authorize-registration">
            <a href="<?=$arParams["AUTH_REGISTER_URL"] ? $arParams["AUTH_REGISTER_URL"] : $arResult["AUTH_REGISTER_URL"]?>" class="startshop-button startshop-button-standart">
                <?=GetMessage("STARTSHOP_AUTHORIZE_REGISTER");?>
            </a>
            <div class="startshop-authorize-registration-description">
                <?=GetMessage("STARTSHOP_AUTHORIZE_REGISTER_DESCRIPTION")?>
            </div>
        </div>
        <div class="startshop-authorize-delimiter"></div>
        <script type="text/javascript">
            <?if (strlen($arResult["LAST_LOGIN"])>0):?>
            try{document.startshop_authorize_form.USER_PASSWORD.focus();}catch(e){}
            <?else:?>
            try{document.startshop_authorize_form.USER_LOGIN.focus();}catch(e){}
            <?endif?>
        </script>
    <?$frame->end();?>
</div>


