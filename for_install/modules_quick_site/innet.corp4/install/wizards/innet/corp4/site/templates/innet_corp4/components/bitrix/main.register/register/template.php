<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

    <?$frame = $this->createFrame()->begin();?>

    <h2 class="title_popup"><?=GetMessage("AUTH_REGISTER")?></h2>
    <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">

        <?
        if(count($arResult["ERRORS"]) > 0) {
            foreach ($arResult["ERRORS"] as $key => $error)
                if (intval($key) == 0 && $key !== 0)
                    $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

            ShowError(implode("<br />", $arResult["ERRORS"]));echo '<br>';
        }

        if($USER->IsAuthorized()) {
            ShowMessage( array("MESSAGE"=>GetMessage("MAIN_REGISTER_AUTH"),"TYPE"=>"OK") );
        }
        ?>

        <?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
            <?if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true):?>

            <?else:?>

                <span class="left label-popup"><?=GetMessage("REGISTER_FIELD_".$FIELD)?>:</span><?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span class="starrequired">*</span><?endif?>
                    <?
                        switch ($FIELD)
                        {
                            case "PASSWORD":?>
                                <input class="right edit-popup" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" placeholder="******">
                                <div class="clearfix"></div>
                                <?
                                break;
                            case "CONFIRM_PASSWORD":?>
                                <input class="right edit-popup" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" placeholder="******">
                                <div class="clearfix"></div>
                                <?
                                break;

                            case "PERSONAL_GENDER":?>
                                <select name="REGISTER[<?=$FIELD?>]">
                                    <option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
                                    <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_MALE")?></option>
                                    <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
                                </select>
                                <div class="clearfix"></div>
                                <?
                                break;

                            case "PERSONAL_COUNTRY":
                            case "WORK_COUNTRY":
                                ?><select name="REGISTER[<?=$FIELD?>]"><?
                                foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value) {?>
                                    <option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                <?}?>
                                    </select>
                                <div class="clearfix"></div>
                                <?
                                break;

                            case "PERSONAL_PHOTO":
                            case "WORK_LOGO":
                                ?><input class="right edit-popup" size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" />
                            <div class="clearfix"></div><?
                                break;

                            case "PERSONAL_NOTES":
                            case "WORK_NOTES":
                                ?><textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea>
                            <div class="clearfix"></div><?
                                break;
                            default:
                                if ($FIELD == "PERSONAL_BIRTHDAY"):?><small><?=$arResult["DATE_FORMAT"]?></small><br /><?endif;
                                ?><input class="right edit-popup" size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
                                    <div class="clearfix"></div><?
                                if ($FIELD == "PERSONAL_BIRTHDAY")
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.calendar',
                                        '',
                                        array(
                                            'SHOW_INPUT' => 'N',
                                            'FORM_NAME' => 'regform',
                                            'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                            'SHOW_TIME' => 'N'
                                        ),
                                        null,
                                        array("HIDE_ICONS"=>"Y")
                                    );
                                ?><?
                        }?>
            <?endif?>
        <?endforeach?>


        <?if ($arResult["USE_CAPTCHA"] == "Y") {?>
            <div style="margin-bottom: 20px;overflow: hidden;">
            <span class="left label-popup"><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></span>
                <div class="clearfix"></div>
            <span class="left label-popup"><?=GetMessage("REGISTER_CAPTCHA_PROMT")?></span>:<span class="starrequired">*</span>
                <div class="clearfix"></div>
            <img style="float: left;" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
            <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
            <input style="float: left;height: 35px;width: 180px;margin-left: 15px;padding-left: 10px;" type="text" name="captcha_word" maxlength="50" value="" />
            </div>
        <?}?>

        <div class="clearfix"></div>

        <input type="submit" class="btn_popup" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>" />
    </form>
