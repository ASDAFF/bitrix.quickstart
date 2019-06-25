<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-register">
    <?$frame = $this->createFrame()->begin();?>
        <?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
            <div class="startshop-register-notify">
                <?=GetMessage('STARTSHOP_REGISTER_NOTYFY_CONFIRM_SENDED')?>
            </div>
        <?else:?>
            <?if (!empty($arParams['AUTH_RESULT']['MESSAGE'])):?>
                <div class="startshop-register-notify">
                    <?=html_entity_decode($arParams['AUTH_RESULT']['MESSAGE'])?>
                </div>
            <?endif;?>
            <?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
                <div class="startshop-register-notify">
                    <?=GetMessage('STARTSHOP_REGISTER_NOTIFY_CONFIRM')?>
                </div>
            <?endif?>
            <form method="post" action="<?=$arResult["AUTH_URL"]?>">
                <?if (!empty($arResult['BACKURL'])):?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?endif;?>
                <input type="hidden" name="AUTH_FORM" value="Y" />
                <input type="hidden" name="TYPE" value="REGISTRATION" />

                <table class="startshop-register-table">
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_NAME")?>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" /></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_LASTNAME")?>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" /></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_LOGIN")?> <span class="startshop-register-table-require">*</span>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" /></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_PASSWORD")?> <span class="startshop-register-table-require">*</span>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_PASSWORD_CONFIRM")?> <span class="startshop-register-table-require">*</span>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" /></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_EMAIL")?> <?if ($arResult["EMAIL_REQUIRED"]):?><span class="startshop-register-table-require">*</span><?endif;?>:</td>
                        <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" /></td>
                    </tr>
                    <?if ($arResult["USE_CAPTCHA"] == "Y"):?>
                        <tr>
                            <td style="padding-bottom: 10px;"></td>
                            <td style="padding-bottom: 10px;">
                                <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" style="margin-left: 20px;" width="180" height="40" alt="CAPTCHA" />
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_REGISTER_TABLE_CAPTCHA")?> <span class="startshop-register-table-require">*</span>:</td>
                            <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="captcha_word" maxlength="50" value="" /></td>
                        </tr>
                    <?endif;?>
                    <tr>
                        <td colspan="2">
                            <input class="startshop-button startshop-button-standart" style="margin-right: 10px;" type="submit" name="Register" value="<?=GetMessage("STARTSHOP_REGISTER_TABLE_REGISTER")?>" />
                            <?=GetMessage("STARTSHOP_REGISTER_TABLE_OR")?>
                            <a class="startshop-button startshop-button-standart" style="margin-left: 10px;" href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><?=GetMessage("STARTSHOP_REGISTER_TABLE_AUTHORIZE")?></a>
                        </td>
                    </tr>
                </table>
            </form>
        <?endif;?>
    <?$frame->end();?>
</div>
