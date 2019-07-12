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
<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if (strlen($arResult["BACKURL"]) > 0){?>
    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?}?>
<div class="order-item">
    <div id="user_div_reg">
        <div class="order-info">
        <?=GetMessage("AUTH_FORGOT_PASSWORD_1")?><br />
    <table>
    <input type="hidden" name="AUTH_FORM" value="Y">
    <input type="hidden" name="TYPE" value="SEND_PWD">

        <tr>
            <td><label class="field-title"><?=GetMessage("AUTH_LOGIN")?></label></td>
            <td><div class="form-input"><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" /></div></td>
        </tr>
        <tr>
            <td><label class="field-title">E-Mail</label></td>
            <td><div class="form-input"><input type="text" name="USER_EMAIL" maxlength="255" /></div></td>
        </tr>
    </table>
        
        </div>
    </div>
</div>
<div class="order-buttons">
    <input type="submit" class="input-submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
    <br /><br /><br /><a href="<?=$arResult["AUTH_AUTH_URL"]?>"><b><?=GetMessage("AUTH_AUTH")?></b></a>
</div>

</form>
</div>

<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>