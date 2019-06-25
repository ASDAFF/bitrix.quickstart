<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-password change">
    <?$frame = $this->createFrame()->begin();?>
        <form method="post" action="<?=$arResult["AUTH_FORM"]?>">
            <?if (!empty($arResult["BACKURL"])):?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <?endif;?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="CHANGE_PWD">
            <table class="startshop-password-table">
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_LOGIN")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_CHECKWORD")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" /></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_PASSWORD")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;">
                        <input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_PASSWORD_CONFIRM")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;">
                        <input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="startshop-button startshop-button-standart" type="submit" name="change_pwd" value="<?=GetMessage("STARTSHOP_PASSWORD_TABLE_CHANGE")?>" style="margin-right: 10px;" />
                        <?=GetMessage('STARTSHOP_PASSWORD_TABLE_OR')?>
                        <a class="startshop-button startshop-button-standart" href="<?=$arResult["AUTH_AUTH_URL"]?>" style="margin-left: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_AUTHORIZE")?></a>
                    </td>
                </tr>
            </table>
            <div class="startshop-password-description">
                <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
                <p><?=GetMessage("STARTSHOP_PASSWORD_REQUIRE")?><span class="startshop-password-require">*</span></p>
            </div>
        </form>
    <?$frame->beginStub();?>
        <form method="post" action="<?=$arResult["AUTH_FORM"]?>">
            <table class="startshop-password-table">
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_LOGIN")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_LOGIN" maxlength="50" /></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_CHECKWORD")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;"><input class="startshop-input-text startshop-input-text-standart" type="text" name="USER_CHECKWORD" maxlength="50" /></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_PASSWORD")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;">
                        <input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_PASSWORD" maxlength="50" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_PASSWORD_CONFIRM")?> <span class="startshop-password-table-require">*</span>:</td>
                    <td style="padding-bottom: 10px;">
                        <input class="startshop-input-text startshop-input-text-standart" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="startshop-button startshop-button-standart" type="submit" name="change_pwd" value="<?=GetMessage("STARTSHOP_PASSWORD_TABLE_CHANGE")?>" style="margin-right: 10px;" />
                        <?=GetMessage('STARTSHOP_PASSWORD_TABLE_OR')?>
                        <a class="startshop-button startshop-button-standart" href="<?=$arResult["AUTH_AUTH_URL"]?>" style="margin-left: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_AUTHORIZE")?></a>
                    </td>
                </tr>
            </table>
            <div class="startshop-password-description">
                <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
                <p><?=GetMessage("STARTSHOP_PASSWORD_REQUIRE")?><span class="startshop-password-require">*</span></p>
            </div>
        </form>
    <?$frame->end();?>
</div>