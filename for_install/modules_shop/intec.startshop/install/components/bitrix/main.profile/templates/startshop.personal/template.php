<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-profile personal<?=$arParams['USE_ADAPTABILITY'] == 'Y' ? ' adaptiv' : ''?>">
    <form method="POST" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
        <?$frame = $this->createFrame()->begin();?>
            <div class="startshop-profile-personal">
                <?=$arResult["BX_SESSION_CHECK"]?>
                <input type="hidden" name="lang" value="<?=LANG?>" />
                <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
                <input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_NAME')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="NAME" value="<?=$arResult["arUser"]["NAME"]?>" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_NAME')?>"/>
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="LAST_NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_LASTNAME')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="LAST_NAME" value="<?=$arResult["arUser"]["LAST_NAME"]?>" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_LASTNAME')?>">
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="SECOND_NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PATRONYMIC')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="SECOND_NAME" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PATRONYMIC')?>">
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="PERSONAL_PHONE"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PHONE')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_PHONE" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PHONE')?>" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="EMAIL"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_EMAIL')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="EMAIL" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_EMAIL')?>" value="<?=$arResult["arUser"]["EMAIL"]?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_ZIP"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_ZIP')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_ZIP" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_ZIP')?>" value="<?=$arResult["arUser"]["PERSONAL_ZIP"]?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_CITY"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_CITY')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_CITY" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_CITY')?>" value="<?=$arResult["arUser"]["PERSONAL_CITY"]?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_STREET"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_STREET')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_STREET" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_STREET')?>" value="<?=$arResult["arUser"]["PERSONAL_STREET"]?>" />
                </div>
            </div>
            <div class="startshop-profile-password">
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="NEW_PASSWORD"><?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD')?>:</label>
                    <input class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="NEW_PASSWORD_CONFIRM"><?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD_CONFIRM')?>:</label>
                    <input class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD_CONFIRM')?>" />
                </div>
            </div>
            <div style="display: block; clear: both; padding-top: 10px; padding-bottom: 10px;">
                <input name="save" value="<?=GetMessage("STARTSHOP_PROFILE_PERSONAL_SAVE")?>" class="startshop-button startshop-button-standart" type="submit" />
            </div>
        <?$frame->beginStub();?>
            <div class="startshop-profile-personal">
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_NAME')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="NAME" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_NAME')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="LAST_NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_LASTNAME')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="LAST_NAME" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_LASTNAME')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="SECOND_NAME"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PATRONYMIC')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="SECOND_NAME" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PATRONYMIC')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption"  for="PHONE"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PHONE')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PHONE" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_PHONE')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="EMAIL"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_EMAIL')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="EMAIL" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_EMAIL')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_ZIP"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_ZIP')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_ZIP" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_ZIP')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_CITY"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_CITY')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_CITY" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_CITY')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="PERSONAL_STREET"><?=GetMessage('STARTSHOP_PROFILE_PERSONAL_STREET')?>:</label>
                    <input type="text" class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" name="PERSONAL_STREET" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PERSONAL_STREET')?>" />
                </div>
            </div>
            <div class="startshop-profile-password">
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="NEW_PASSWORD"><?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD')?>:</label>
                    <input class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD')?>" />
                </div>
                <div class="startshop-profile-row">
                    <label class="startshop-profile-row-caption" for="NEW_PASSWORD_CONFIRM"><?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD_CONFIRM')?>:</label>
                    <input class="startshop-profile-row-input startshop-input-text startshop-input-text-standart" type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('STARTSHOP_PROFILE_PASSWORD_PASSWORD_CONFIRM')?>" />
                </div>
            </div>
            <div style="display: block; clear: both; padding-top: 10px; padding-bottom: 10px;">
                <input name="save" value="<?=GetMessage("STARTSHOP_PROFILE_PERSONAL_SAVE")?>" class="startshop-button startshop-button-standart" type="submit" />
            </div>
        <?$frame->end();?>
    </form>
</div>

