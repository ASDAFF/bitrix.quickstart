<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$arParams["~AUTH_RESULT"]["MESSAGE"] = explode("<br>", $arParams["~AUTH_RESULT"]["MESSAGE"]);?>
<?if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"])) {?>
<div<?if($arParams["~AUTH_RESULT"]["TYPE"] == "ERROR"){?> class="errors"<?}else{?> class="note"<?}?>>
    <?foreach($arParams["~AUTH_RESULT"]["MESSAGE"] as $v){
        if (strlen(strip_tags($v)) < 1) {
            continue;
        }?>
    <p><?=strip_tags($v)?></p>
    <?}?>
</div>
<?}?>

<div id="order_form" class="register">
<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
<div class="order-item">
    <div id="user_div_reg">
        <div class="order-info">
            <table>
<?if (strlen($arResult["BACKURL"]) > 0): ?>
<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<? endif ?>
<input type="hidden" name="AUTH_FORM" value="Y">
<input type="hidden" name="TYPE" value="CHANGE_PWD">
<tr>
    <td><label class="field-title"><?=GetMessage("AUTH_LOGIN")?><span class="starrequired">*</span></label></td>
    <td><div class="form-input"><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></div></td>
</tr>
<tr>
    <td><label class="field-title"><?=GetMessage("AUTH_CHECKWORD")?><span class="starrequired">*</span></label></td>
    <td><div class="form-input"><input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" /></div></td>
</tr>
<tr>
    <td><label class="field-title"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?><span class="starrequired">*</span></label></td>
    <td><div class="form-input"><input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" /></div></td>
</tr>
<tr>
    <td><label class="field-title"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?><span class="starrequired">*</span></label></td>
    <td><div class="form-input"><input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>"  /></div></td>
</tr>
            </table>
        </div>
    </div>
</div>
<div class="order-buttons">
    <p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p><br /><br />
    <input type="submit" class="input-submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" />
    <br /><br /><br /><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
</div>

</form>
</div>

<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>