<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="step">
    <?=GetMessage("TITLE")?>&nbsp;<span class="current">|&nbsp;<a href="<?=SITE_DIR?>personal/order/"><?=GetMessage("TITLE_ORDER")?></a>&nbsp;|&nbsp;<a href="<?=SITE_DIR?>wishlist/"><?=GetMessage("TITLE_WISH")?></a></span>
</div>
<?$arResult["strProfileError"] = explode("<br>", $arResult["strProfileError"]);?>
<?if (!empty($arResult["strProfileError"])) {?>
<div class="errors">
    <?foreach($arResult["strProfileError"] as $v){
        if (strlen(strip_tags($v)) < 1) {
            continue;
        }?>
    <p><?=strip_tags($v)?></p>
    <?}?>
</div>
<?}?>

<div id="order_form" class="profile">
<?if ($arResult['DATA_SAVED'] == 'Y'){
    echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
}?>

<div class="order-item">
<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
<?=$arResult["BX_SESSION_CHECK"]?>
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
    <div class="order-title">
        <div class="order-title-inner">
            <span><?=GetMessage("REG_SHOW_HIDE")?></span>
        </div>
    </div>
    <div id="user_div_reg">
        <div class="order-info">
<table class="profile-table data-table">
    <thead>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </thead>
    <tbody>
    <?
    if($arResult["ID"]>0)
    {
    ?>
        <?
        if (strlen($arResult["arUser"]["TIMESTAMP_X"])>0)
        {
        ?>
        <tr>
            <td><?=GetMessage('LAST_UPDATE')?></td>
            <td><input type="text" name="" value="<?=$arResult["arUser"]["TIMESTAMP_X"]?>" disabled /></td>
        </tr>
        <?
        }
        ?>
        <?
        if (strlen($arResult["arUser"]["LAST_LOGIN"])>0)
        {
        ?>
        <tr>
            <td><?=GetMessage('LAST_LOGIN')?></td>
            <td><input type="text" name="" value="<?=$arResult["arUser"]["LAST_LOGIN"]?>" disabled /></td>
        </tr>
        <?
        }
        ?>
    <?
    }
    ?>
    <tr>
        <td><?=GetMessage('NAME')?></td>
        <td><input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage('LAST_NAME')?></td>
        <td><input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage('SECOND_NAME')?></font></td>
        <td><input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage('EMAIL')?><span class="starrequired">*</span></td>
        <td><input type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage('LOGIN')?><span class="starrequired">*</span></td>
        <td><input type="text" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage('NEW_PASSWORD_REQ')?></td>
        <td><input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" />
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
        </td>
    </tr>
    <tr>
        <td><?=GetMessage('NEW_PASSWORD_CONFIRM')?></td>
        <td><input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /></td>
    </tr>
<?if($arResult["TIME_ZONE_ENABLED"] == true):?>
    <tr>
        <td colspan="2" class="profile-header"><?echo GetMessage("main_profile_time_zones")?></td>
    </tr>
    <tr>
        <td><?echo GetMessage("main_profile_time_zones_auto")?></td>
        <td>
            <select class="page-limit-select" name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')">
                <option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
                <option value="Y"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "Y"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
                <option value="N"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "N"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?echo GetMessage("main_profile_time_zones_zones")?></td>
        <td>
            <select class="page-limit-select" name="TIME_ZONE"<?if($arResult["arUser"]["AUTO_TIME_ZONE"] <> "N") echo ' disabled="disabled"'?>>
<?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
                <option value="<?=htmlspecialchars($tz)?>"<?=($arResult["arUser"]["TIME_ZONE"] == $tz? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialchars($tz_name)?></option>
<?endforeach?>
            </select>
        </td>
    </tr>
<?endif?>
    </tbody>
</table>
        </div>
    </div>
</div>

    <?// ********************* User properties ***************************************************?>
    <?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
<div class="order-item">
    <div class="order-title">
        <div class="order-title-inner">
            <span><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></a></span>
        </div>
    </div>
    <div id="user_div_user_properties" class="profile-block-<?=strpos($arResult["opened"], "user_properties") === false ? "hidden" : "shown"?>">
        <div class="order-info">
    <table class="data-table profile-table">
        <thead>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
        <?$first = true;?>
        <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
        <tr><td class="field-name">
            <?if ($arUserField["MANDATORY"]=="Y"):?>
                <span class="starrequired">*</span>
            <?endif;?>
            <?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td class="field-value">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:system.field.edit",
                    $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                    array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
        <?endforeach;?>
        </tbody>
    </table>
        </div>
    </div>
</div>
    <?endif;?>
    <?// ******************** /User properties ***************************************************?>
    <div class="order-buttons">
        <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p><br /><br />
        <input type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>" />
    </div>
</form>
</div>