<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>

<div class="bx-auth-profile">

<?ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<script type="text/javascript">
<!--
var opened_sections = [<?
$arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"]."_user_profile_open"];
$arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
if (strlen($arResult["opened"]) > 0)
{
	echo "'".implode("', '", explode(",", $arResult["opened"]))."'";
}
else
{
	$arResult["opened"] = "reg";
	echo "'reg'";
}
?>];
//-->

var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
</script>
<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
<?=$arResult["BX_SESSION_CHECK"]?>
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('reg')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("REG_SHOW_HIDE")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "reg") === false ? "is-hidden" : "is-visible"?>" id="user_div_reg">
        <div class="row">
            <?php if ($arResult["ID"] > 0): ?>
                <? if (strlen($arResult["arUser"]["TIMESTAMP_X"]) > 0): ?>
                    <div class="form-group col-xs-12 col-sm-6"><?=GetMessage('LAST_UPDATE')?>: <?=$arResult["arUser"]["TIMESTAMP_X"]?></div>
                <?php endif; ?>
                <? if (strlen($arResult["arUser"]["LAST_LOGIN"]) > 0): ?>
                    <div class="form-group col-xs-12 col-sm-6"><?=GetMessage('LAST_LOGIN')?>: <?=$arResult["arUser"]["LAST_LOGIN"]?></div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="TITLE" value="<?=$arResult["arUser"]["TITLE"]?>" placeholder="<?echo GetMessage("main_profile_title")?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" placeholder="<?=GetMessage('NAME')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" placeholder="<?=GetMessage('LAST_NAME')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" placeholder="<?=GetMessage('SECOND_NAME')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" placeholder="<?=GetMessage('EMAIL')?><?if($arResult["EMAIL_REQUIRED"]):?>*<?endif?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" placeholder="<?=GetMessage('LOGIN')?>*">
            </div>
            <?php if($arResult["arUser"]["EXTERNAL_AUTH_ID"] == ''): ?>
                <div class="form-group col-xs-12 col-sm-6">
                    <input class="form-control" type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" placeholder="<?=GetMessage('NEW_PASSWORD_REQ')?>">
                    <?php if($arResult["SECURE_AUTH"]): ?>
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
                </div>
                <div class="form-group col-xs-12 col-sm-6">
                    <input class="form-control" type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" placeholder="<?=GetMessage('NEW_PASSWORD_CONFIRM')?>">
                </div>
            <?php endif; ?>

            <?php if($arResult["TIME_ZONE_ENABLED"] == true): ?>
                <div class="form-group col-xs-12 col-sm-6">
                    <div class="row">
                        <div class="col-xs-6"><?echo GetMessage("main_profile_time_zones_auto")?></div>
                        <div class="col-xs-6">
                            <select class="form-control" name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')">
                                <option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
                                <option value="Y"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "Y"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
                                <option value="N"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "N"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
                            </select>
                        </div>
                    </div>
                </div>  
                <div class="form-group col-xs-12 col-sm-6">
                    <div class="row">
                        <div class="col-xs-6"><?echo GetMessage("main_profile_time_zones_zones")?></div>
                        <div class="col-xs-6">
                            <select class="form-control" name="TIME_ZONE"<?if($arResult["arUser"]["AUTO_TIME_ZONE"] <> "N") echo ' disabled="disabled"'?>>
                                <?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
                                    <option value="<?=htmlspecialcharsbx($tz)?>"<?=($arResult["arUser"]["TIME_ZONE"] == $tz? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialcharsbx($tz_name)?></option>
                                <?endforeach?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('personal')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("USER_PERSONAL_INFO")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "personal") === false ? "is-hidden" : "is-visible"?>" id="user_div_personal">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_PROFESSION" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PROFESSION"]?>" placeholder="<?=GetMessage('USER_PROFESSION')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_WWW" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_WWW"]?>" placeholder="<?=GetMessage('USER_WWW')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_ICQ" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_ICQ"]?>" placeholder="<?=GetMessage('USER_ICQ')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <select class="form-control" name="PERSONAL_GENDER">
                    <option value="" disabled selected><?=GetMessage('USER_GENDER')?></option>
                    <option value="M"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "M" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_MALE")?></option>
                    <option value="F"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "F" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.calendar',
                    'al',
                    array(
                        'SHOW_INPUT' => 'Y',
                        'FORM_NAME' => 'form1',
                        'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
                        'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
                        'INPUT_ADDITIONAL_ATTR' => 'class="form-control" placeholder="'.GetMessage("USER_BIRTHDAY_DT").' ('.$arResult["DATE_FORMAT"].')"',
                        'SHOW_TIME' => 'N'
                    ),
                    null,
                    array('HIDE_ICONS' => 'Y')
                );?>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("USER_PHOTO")?></div>
                    <div class="col-xs-6">
                        <?=$arResult["arUser"]["PERSONAL_PHOTO_INPUT"]?>
                        <?php if (strlen($arResult["arUser"]["PERSONAL_PHOTO"]) > 0): ?>
                            <br><?=$arResult["arUser"]["PERSONAL_PHOTO_HTML"]?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12"><?=GetMessage("USER_PHONES")?></div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_PHONE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" placeholder="<?=GetMessage('USER_PHONE')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_FAX" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_FAX"]?>" placeholder="<?=GetMessage('USER_FAX')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_MOBILE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_MOBILE"]?>" placeholder="<?=GetMessage('USER_MOBILE')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_PAGER" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PAGER"]?>" placeholder="<?=GetMessage('USER_PAGER')?>">
            </div>
            <div class="form-group col-xs-12"><?=GetMessage("USER_POST_ADDRESS")?></div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage('USER_COUNTRY')?></div>
                    <div class="col-xs-6"><?=$arResult["COUNTRY_SELECT"]?></div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_STATE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_STATE"]?>" placeholder="<?=GetMessage('USER_STATE')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_CITY" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_CITY"]?>" placeholder="<?=GetMessage('USER_CITY')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_ZIP" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_ZIP"]?>" placeholder="<?=GetMessage('USER_ZIP')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="PERSONAL_STREET" placeholder="<?=GetMessage("USER_STREET")?>"><?=$arResult["arUser"]["PERSONAL_STREET"]?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="PERSONAL_MAILBOX" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_MAILBOX"]?>" placeholder="<?=GetMessage('USER_MAILBOX')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="PERSONAL_NOTES" placeholder="<?=GetMessage("USER_NOTES")?>"><?=$arResult["arUser"]["PERSONAL_NOTES"]?></textarea>
            </div>

        </div>
    </div>
</div>      
		
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('work')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("USER_WORK_INFO")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "work") === false ? "is-hidden" : "is-visible"?>" id="user_div_work">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_COMPANY" maxlength="255" value="<?=$arResult["arUser"]["WORK_COMPANY"]?>" placeholder="<?=GetMessage('USER_COMPANY')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_WWW" maxlength="255" value="<?=$arResult["arUser"]["WORK_WWW"]?>" placeholder="<?=GetMessage('USER_WWW')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_DEPARTMENT" maxlength="255" value="<?=$arResult["arUser"]["WORK_DEPARTMENT"]?>" placeholder="<?=GetMessage('USER_DEPARTMENT')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_POSITION" maxlength="255" value="<?=$arResult["arUser"]["WORK_POSITION"]?>" placeholder="<?=GetMessage('USER_POSITION')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="WORK_PROFILE" placeholder="<?=GetMessage("USER_WORK_PROFILE")?>"><?=$arResult["arUser"]["WORK_PROFILE"]?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("USER_LOGO")?></div>
                    <div class="col-xs-6">
                        <?=$arResult["arUser"]["WORK_LOGO_INPUT"]?>
                        <?php if (strlen($arResult["arUser"]["WORK_LOGO"]) > 0): ?>
                            <br /><?=$arResult["arUser"]["WORK_LOGO_HTML"]?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12"><?=GetMessage("USER_PHONES")?></div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_PHONE" maxlength="255" value="<?=$arResult["arUser"]["WORK_PHONE"]?>" placeholder="<?=GetMessage('USER_PHONE')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_FAX" maxlength="255" value="<?=$arResult["arUser"]["WORK_FAX"]?>" placeholder="<?=GetMessage('USER_FAX')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_PAGER" maxlength="255" value="<?=$arResult["arUser"]["WORK_PAGER"]?>" placeholder="<?=GetMessage('USER_PAGER')?>">
            </div>
            <div class="form-group col-xs-12"><?=GetMessage("USER_POST_ADDRESS")?></div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("USER_COUNTRY")?></div>
                    <div class="col-xs-6"><?=$arResult["COUNTRY_SELECT_WORK"]?></div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_STATE" maxlength="255" value="<?=$arResult["arUser"]["WORK_STATE"]?>" placeholder="<?=GetMessage('USER_STATE')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_CITY" maxlength="255" value="<?=$arResult["arUser"]["WORK_CITY"]?>" placeholder="<?=GetMessage('USER_CITY')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_ZIP" maxlength="255" value="<?=$arResult["arUser"]["WORK_ZIP"]?>" placeholder="<?=GetMessage('USER_ZIP')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="WORK_STREET" placeholder="<?=GetMessage("USER_STREET")?>"><?=$arResult["arUser"]["WORK_STREET"]?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="WORK_MAILBOX" maxlength="255" value="<?=$arResult["arUser"]["WORK_MAILBOX"]?>" placeholder="<?=GetMessage('USER_MAILBOX')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="WORK_NOTES" placeholder="<?=GetMessage("USER_NOTES")?>"><?=$arResult["arUser"]["WORK_NOTES"]?></textarea>
            </div>
        </div>
    </div>
</div>   

<?php if ($arResult["INCLUDE_FORUM"] == "Y"): ?>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('forum')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("forum_INFO")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "forum") === false ? "is-hidden" : "is-visible"?>" id="user_div_forum">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("forum_SHOW_NAME")?></div>
                    <div class="col-xs-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="forum_SHOW_NAME" value="Y" <?if ($arResult["arForumUser"]["SHOW_NAME"]=="Y") echo "checked=\"checked\"";?>>
                                <svg class="checkbox__icon icon-check icon-svg"><use xlink:href="#svg-check"></use></svg
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="forum_DESCRIPTION" maxlength="255" value="<?=$arResult["arForumUser"]["DESCRIPTION"]?>" placeholder="<?=GetMessage('forum_DESCRIPTION')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="forum_INTERESTS" placeholder="<?=GetMessage('forum_INTERESTS')?>"><?=$arResult["arForumUser"]["INTERESTS"]; ?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="forum_SIGNATURE" placeholder="<?=GetMessage("forum_SIGNATURE")?>"><?=$arResult["arForumUser"]["SIGNATURE"]; ?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("forum_AVATAR")?></div>
                    <div class="col-xs-6">
                        <?=$arResult["arForumUser"]["AVATAR_INPUT"]?>
                        <?php if (strlen($arResult["arForumUser"]["AVATAR"]) > 0): ?>
                            <br /><?=$arResult["arForumUser"]["AVATAR_HTML"]?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("forum_AVATAR")?></div>
                    <div class="col-xs-6"><?=$arResult["arForumUser"]["AVATAR_INPUT"]?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($arResult["INCLUDE_BLOG"] == "Y"): ?>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('blog')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("blog_INFO")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "blog") === false ? "is-hidden" : "is-visible"?>" id="user_div_blog">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="blog_ALIAS" maxlength="255" value="<?=$arResult["arBlogUser"]["ALIAS"]?>" placeholder="<?=GetMessage('blog_ALIAS')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <input class="form-control" type="text" name="blog_DESCRIPTION" maxlength="255" value="<?=$arResult["arBlogUser"]["DESCRIPTION"]?>" placeholder="<?=GetMessage('blog_DESCRIPTION')?>">
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="blog_INTERESTS" placeholder="<?=GetMessage('blog_INTERESTS')?>"><?echo $arResult["arBlogUser"]["INTERESTS"]; ?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("blog_AVATAR")?></div>
                    <div class="col-xs-6">
                        <?=$arResult["arBlogUser"]["AVATAR_INPUT"]?>
                        <?php if (strlen($arResult["arBlogUser"]["AVATAR"]) > 0): ?>
                            <br><?=$arResult["arBlogUser"]["AVATAR_HTML"]?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($arResult["INCLUDE_LEARNING"] == "Y"): ?>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('learning')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("learning_INFO")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "learning") === false ? "is-hidden" : "is-visible"?>" id="user_div_learning">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("learning_PUBLIC_PROFILE");?></div>
                    <div class="col-xs-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="student_PUBLIC_PROFILE" value="Y" <?if ($arResult["arStudent"]["PUBLIC_PROFILE"]=="Y") echo "checked=\"checked\"";?> >
                                <svg class="checkbox__icon icon-check icon-svg"><use xlink:href="#svg-check"></use></svg>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="student_RESUME" placeholder="<?=GetMessage("learning_RESUME");?>"><?=$arResult["arStudent"]["RESUME"]; ?></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6"><?=GetMessage("learning_TRANSCRIPT");?></div>
                    <div class="col-xs-6"><?=$arResult["arStudent"]["TRANSCRIPT"];?>-<?=$arResult["ID"]?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?endif;?>

<?php if($arResult["IS_ADMIN"]): ?>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('admin')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=GetMessage("USER_ADMIN_NOTES")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "admin") === false ? "is-hidden" : "is-visible"?>" id="user_div_admin">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <textarea class="form-control" cols="30" rows="5" name="ADMIN_NOTES" placeholder="<?=GetMessage("USER_ADMIN_NOTES")?>"><?=$arResult["arUser"]["ADMIN_NOTES"]?></textarea>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
<div class="panel">
    <div class="panel__head">
        <a class="panel__more" href="javascript:void(0)" onclick="SectionClick('user_properties')"><?=GetMessage("USER_SHOW_HIDE")?></a>
        <div class="panel__name"><?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></div>
    </div>
    <div class="panel__body <?=strpos($arResult["opened"], "user_properties") === false ? "is-hidden" : "is-visible"?>" id="user_div_user_properties">
        <div class="row">
            <?$first = true;?>
            <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="row">
                    <div class="col-xs-6">
                        <?if ($arUserField["MANDATORY"]=="Y"):?>
                            <span class="starrequired">*</span>
                        <?endif;?>
                        <?=$arUserField["EDIT_FORM_LABEL"]?>
                    </div>
                    <div class="col-xs-6">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:system.field.edit",
                            $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                            array(
                                "bVarsFromForm" => $arResult["bVarsFromForm"],
                                "arUserField" => $arUserField
                            ),
                            null,
                            array("HIDE_ICONS"=>"Y")
                        );?>
                    </div>
                </div>
            </div>
            <?endforeach;?>
        </div>
    </div>
</div>
<?php endif; ?>

	<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
	<p>
        <button class="btn btn1" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">
            <svg class="btn__icon icon-floppy icon-svg"><use xlink:href="#svg-floppy"></use></svg><?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>
        </button>&nbsp;&nbsp;
        <input class="btn btn3" type="reset" value="<?=GetMessage('MAIN_RESET');?>">
    </p>
</form>
<?
if($arResult["SOCSERV_ENABLED"])
{
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		false
	);
}
?>
</div>