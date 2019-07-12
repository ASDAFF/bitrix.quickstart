<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?$arParams["~AUTH_RESULT"]["MESSAGE"] = explode("<br>", $arParams["~AUTH_RESULT"]["MESSAGE"]);?>
<?if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"])) {?>
<div class="errors">
    <?foreach($arParams["~AUTH_RESULT"]["MESSAGE"] as $v){
        if (strlen(strip_tags($v)) < 1) {
            continue;
        }?>
    <p><?=strip_tags($v)?></p>
    <?}?>
</div>
<?}?>
<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
<div class="field"><?echo GetMessage("AUTH_EMAIL_SENT")?></div>
<?else:?>

<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
    <div class="field"><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></div>
<?endif?>
<!--noindex-->
<div id="order_form" class="register">
<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
<div class="order-item">
    <div class="order-title">
        <div class="order-title-inner">
            <span><?=GetMessage("TITLE")?></span>
        </div>
    </div>
    <div id="user_div_reg">
        <div class="order-info">
<table>
<?if (strlen($arResult["BACKURL"]) > 0){?>
    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?}?>
    <input type="hidden" name="AUTH_FORM" value="Y" />
    <input type="hidden" name="TYPE" value="REGISTRATION" />
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_NAME")?></label></td>
        <td><div class="form-input"><input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_LAST_NAME")?></label></td>
        <td><div class="form-input"><input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_LOGIN_MIN")?><span class="starrequired">*</span></label></td>
        <td><div class="form-input"><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_PASSWORD_REQ")?><span class="starrequired">*</span></label></td>
        <td><div class="form-input"><input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_CONFIRM")?><span class="starrequired">*</span></label></td>
        <td><div class="form-input"><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title">E-Mail<span class="starrequired">*</span></label></td>
        <td><div class="form-input"><input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" /></div></td>
    </tr>
<?// ********************* User properties ***************************************************?>
<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
    <tr><td colspan="2"><div class="field"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></div></td></tr>
    <?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
    <tr>
        <td><label class="field-title">
            <?=$arUserField["EDIT_FORM_LABEL"]?><?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?>
        </label></td>
        <td><div class="form-input">
            <?$APPLICATION->IncludeComponent(
                "bitrix:system.field.edit",
                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"), null, array("HIDE_ICONS"=>"Y"));?>
        </div></td>
    </tr>
    <?endforeach;?>
<?endif;?>
<?// ******************** /User properties ***************************************************

    /* CAPTCHA */
    if ($arResult["USE_CAPTCHA"] == "Y")
    {
        ?>
    <tr>
        <td><br /><label class="field-title"><?=GetMessage("CAPTCHA_REGF_PROMT")?><span class="starrequired">*</span></label></td>
        <td><br /><p style="clear: left;"><input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
            <img style="margin-left:10px" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></p>
            <div class="form-input"><input type="text" name="captcha_word" maxlength="50" value="" /></div>
        </td>
    </tr>
        <?
    }
    /* CAPTCHA */
    ?>
</table>
        </div>
    </div>
</div>
<div class="order-buttons">
    <p><?=GetMessage("LOGIN_REQUIREMENTS")?></p>
    <p><?=$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p><br /><br />
    <input type="submit" class="input-submit" name="Register" value="<?=GetMessage("AUTH_REGISTER")?>" />
</div>


</form>
</div>
<script type="text/javascript">
document.bform.USER_NAME.focus();
</script>
<?endif?>