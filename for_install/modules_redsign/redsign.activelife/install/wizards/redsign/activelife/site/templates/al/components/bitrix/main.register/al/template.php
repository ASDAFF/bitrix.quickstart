<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

//ob_start();
?>
<div class="row rsform rsform-auth">
    <div class="col col-md-4">
<?php if($USER->IsAuthorized()): ?>
    <?=Loc::getMessage('MAIN_REGISTER_AUTH');?>
<?php else: ?>
    <?/*<div class="panel__head"><?=Loc::getMessage('AUTH_REGISTER');?></div>*/?>
    <?
    if (count($arResult["ERRORS"]) > 0):
        foreach ($arResult["ERRORS"] as $key => $error)
            if (intval($key) == 0 && $key !== 0) 
                $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

        ShowError(implode("<br>", $arResult["ERRORS"]));

    elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):
    ?>
        <div class="alert alert-info"><?=Loc::getMessage('REGISTER_EMAIL_WILL_BE_SENT');?></div>
    <?endif?>

    <form class="js-ajax_form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data" data-fancybox-title="<?=Loc::getMessage("AUTH_REGISTER")?>">

        <?php if($arResult["BACKURL"] <> ''): ?>
            <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>">
        <?php endif; ?>

        <?php foreach($arResult["SHOW_FIELDS"] as $FIELD): ?>

            <?php if($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true): ?>

                <div class="form-group">
                    <label for="AUTO_TIME_ZONE"><?=Loc::getMessage('main_profile_time_zones_auto_def');?></label>
                    <select class="form-control" id="AUTO_TIME_ZONE" name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
                        <option value=""><?=Loc::getMessage("main_profile_time_zones_auto_def")?></option>
                        <option value="Y"<?=$arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("main_profile_time_zones_auto_yes")?></option>
                        <option value="N"<?=$arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("main_profile_time_zones_auto_no")?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="TIME_ZONE"><?=Loc::getMessage('main_profile_time_zones_zones');?></label>
                    <select class="form-control" name="REGISTER[TIME_ZONE]"<?if(!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"'?>>
                        <?php foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name): ?>
                            <option value="<?=htmlspecialcharsbx($tz)?>"<?=$arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : ""?>><?=htmlspecialcharsbx($tz_name)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <?php else: ?>

                <div class="form-group">
                    <label for="REGISTER[<?=$FIELD?>]">
                        <?=Loc::getMessage("REGISTER_FIELD_".$FIELD);?>
                        <?php if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?><span class="required">*</span><?php endif; ?>
                    </label>

                    <?php
                    switch($FIELD):
                        case "PASSWORD":
                    ?>

                            <input size="30" type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off" class="form-control">
                            <?php if($arResult["SECURE_AUTH"]): ?>
                            <noscript> <noscript><div class="authform__nonsecure-note"><?=Loc::getMessage('AUTH_NONSECURE_NOTE')?></div></noscript></noscript>
                            <?php endif; ?>
                            <?php break; ?>

                        <?php
                        case "CONFIRM_PASSWORD":
                        ?>
                            <input size="30" type="password" class="form-control" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off">
                            <?php break; ?>

                        <?php
                        case "PERSONAL_GENDER":
                        ?>
                            <select name="REGISTER[<?=$FIELD?>]" class="form-control">
                                <option value=""><?=Loc::getMessage("USER_DONT_KNOW")?></option>
                                <option value="M"<?=$arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("USER_MALE")?></option>
                                <option value="F"<?=$arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : ""?>><?=Loc::getMessage("USER_FEMALE")?></option>
                            </select>
                            <?php break; ?>

                        <?php
                        case "PERSONAL_COUNTRY":
                        case "WORK_COUNTRY":
                        ?>
                            <select name="REGISTER[<?=$FIELD?>]" class="form-control">
                                <?php foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value): ?>
                                    <option value="<?=$value?>"<?if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<?endif?>><?=$arResult["COUNTRIES"]["reference"][$key]?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php break; ?>

                        <?php
                        case "PERSONAL_NOTES":
                        case "WORK_NOTES":
                        ?>
                            <textarea cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea>
                            <?php break;?>
                            
                        <?php
                        case "PERSONAL_PHOTO":
                        case "WORK_LOGO":
                        ?>
                            <input class="form-control" size="30" type="file" name="REGISTER_FILES_<?=$FIELD?>" >
                            <?php break; ?>

                        <?php
                        case "PERSONAL_NOTES":
                        case "WORK_NOTES":
                        ?>
                            <textarea class="form-control" cols="30" rows="5" name="REGISTER[<?=$FIELD?>]"><?=$arResult["VALUES"][$FIELD]?></textarea>
                            <?php break; ?>
                            
                        <?php
                        default:
                        ?>
                            <input class="form-control" size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>">
                            <?php
                            if ($FIELD == "PERSONAL_BIRTHDAY") {
                                $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar',
                                    'al',
                                    array(
                                        'SHOW_INPUT' => 'N',
                                        'FORM_NAME' => 'regform',
                                        'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                        'SHOW_TIME' => 'N'
                                    ),
                                    null,
                                    array("HIDE_ICONS"=>"Y")
                                );
                            }
                            ?>
                    <?php endswitch; ?>

                </div>
            <?php endif; ?>

        <?php endforeach; ?>

        <?php if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"): ?>
            <div class="authform__title"><?=Loc::getMessage('AUTH_TITLE'); ?>
                <?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : Loc::getMessage("USER_TYPE_EDIT_TAB")?>
            </div>
            <?php foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
                <label for="<?=$arUserField["USER_TYPE"]["USER_TYPE_ID"]?>">
                    <?=$arUserField["EDIT_FORM_LABEL"];?>
                    <?php if ($arUserField["MANDATORY"]=="Y"): ?><span class="required">*</span><?php endif; ?>
                </label>
                <div class="form-group">
                    <?php
                    $APPLICATION->IncludeComponent(
                        "bitrix:system.field.edit",
                        $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                        array(
                            "bVarsFromForm" => $arResult["bVarsFromForm"],
                            "arUserField" => $arUserField,
                            "form_name" => "regform"),
                            null,
                            array("HIDE_ICONS"=>"Y")
                    );?>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

        <?php if ($arResult["USE_CAPTCHA"] == "Y"): ?>
            <div class="form-group clearfix">
                <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>">
                <img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" alt="CAPTCHA">
                <div class="l-overflow">
                    <input class="form-control" placeholder="<?=Loc::getMessage('AUTH_CAPTCHA_PROMT');?>" type="text" name="captcha_word" maxlength="50" value="" size="15">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group clearfix">
            <input class="btn" type="submit" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER")?>">
        </div>

        <p>
            <?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?><br>
            <span class="required">*</span> - <?=Loc::getMessage("AUTH_REQ")?>
        </p>

    </form>
<?php endif; ?>
        <div class="fancybox-footer">
            <!--noindex-->
            <a class="js-ajax_fancy" href="<?=$arParams["AUTH_AUTH_URL"]?>" title="<?=Loc::getMessage('AUTH_TITLE')?>" rel="nofollow"><?=Loc::getMessage('AUTH_TITLE')?></a>
            <?/*=GetMessage("AUTH_FIRST_ONE")*/?>
            <!--/noindex-->
        </div>
    </div>
</div>

<?php //$templateData['TEMPLATE_HTML'] = ob_get_flush(); ?>