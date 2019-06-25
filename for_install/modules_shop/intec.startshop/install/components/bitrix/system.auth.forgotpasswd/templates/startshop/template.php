<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-password forgot">
    <?$frame = $this->createFrame()->begin();?>
        <?if ($arParams['AUTH_RESULT']['TYPE'] == "OK"):?>
            <div class="startshop-password-notify">
                <?=html_entity_decode($arParams['AUTH_RESULT']['MESSAGE'])?>
            </div>
            <a class="startshop-button startshop-button-standart" href="<?=$arResult["AUTH_AUTH_URL"]?>"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_AUTHORIZE")?></a>
        <?else:?>
            <?if (!empty($arParams['AUTH_RESULT']['MESSAGE'])):?>
                <div class="startshop-password-notify">
                    <?=html_entity_decode($arParams['AUTH_RESULT']['MESSAGE'])?>
                </div>
            <?endif;?>
            <form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
                <?if (!empty($arResult["BACKURL"])):?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?endif?>
                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="SEND_PWD">
                <div class="startshop-password-description">
                    <?=GetMessage('STARTSHOP_PASSWORD_FORGOT')?>
                </div>

                <table class="startshop-password-table">
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_LOGIN")?></td>
                        <td style="padding-bottom: 10px;">
                            <input type="text" class="startshop-input-text startshop-input-text-standart" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_EMAIL")?></td>
                        <td style="padding-bottom: 10px;">
                            <input type="text" class="startshop-input-text startshop-input-text-standart" name="USER_EMAIL" maxlength="255" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" class="startshop-button startshop-button-standart" name="send_account_info" value="<?=GetMessage("STARTSHOP_PASSWORD_TABLE_SEND")?>" style="margin-right: 10px;" />
                            <?=GetMessage("STARTSHOP_PASSWORD_TABLE_OR")?>
                            <a class="startshop-button startshop-button-standart" href="<?=$arResult["AUTH_AUTH_URL"]?>" style="margin-left: 10px;"><?=GetMessage("STARTSHOP_PASSWORD_TABLE_AUTHORIZE")?></a>
                        </td>
                    </tr>
                </table>
            </form>
        <?endif;?>
    <?$frame->end();?>
</div>
