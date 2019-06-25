<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-register confirm">
    <?$frame = $this->createFrame()->begin();?>
        <?if (!empty($arResult["MESSAGE_TEXT"])):?>
            <div class="startshop-register-notify"><?echo $arResult["MESSAGE_TEXT"]?></div>
        <?endif;?>
        <?if($arResult["SHOW_FORM"]):?>
            <form method="post" action="<?=$arResult["FORM_ACTION"]?>">
                <table class="startshop-register-table">
                    <tr>
                        <td style="padding-bottom: 10px;">
                            <?=GetMessage("STARTSHOP_REGISTER_TABLE_LOGIN")?>:
                        </td>
                        <td style="padding-bottom: 10px;">
                            <input class="startshop-input-text startshop-input-text-standart" type="text" name="<?echo $arParams["LOGIN"]?>" maxlength="50" value="<?echo (strlen($arResult["LOGIN"]) > 0? $arResult["LOGIN"]: $arResult["USER"]["LOGIN"])?>" size="17" />
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 10px;">
                            <?=GetMessage("STARTSHOP_REGISTER_TABLE_CODE")?>:
                        </td>
                        <td style="padding-bottom: 10px;">
                            <input class="startshop-input-text startshop-input-text-standart" type="text" name="<?echo $arParams["CONFIRM_CODE"]?>" maxlength="50" value="<?=$arResult["CONFIRM_CODE"]?>" size="17" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><input class="startshop-button startshop-button-standart" type="submit" value="<?=GetMessage("STARTSHOP_REGISTER_TABLE_CONFIRM")?>" /></td>
                    </tr>
                </table>
                <input type="hidden" name="<?echo $arParams["USER_ID"]?>" value="<?echo $arResult["USER_ID"]?>" />
            </form>
        <?endif;?>
    <?$frame->end();?>
</div>
