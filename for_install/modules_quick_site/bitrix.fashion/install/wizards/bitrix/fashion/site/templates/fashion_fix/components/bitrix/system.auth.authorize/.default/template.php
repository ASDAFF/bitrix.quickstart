<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$arParams["~AUTH_RESULT"]["MESSAGE"] = explode("<br>", $arParams["~AUTH_RESULT"]["MESSAGE"]);?>
<?$arResult['ERROR_MESSAGE'] = explode("<br>", $arResult['ERROR_MESSAGE']);?>
<?$arParams["~AUTH_RESULT"]["MESSAGE"] = array_merge($arParams["~AUTH_RESULT"]["MESSAGE"], $arResult['ERROR_MESSAGE']);?>
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
<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<div class="order-item">
    <div id="user_div_reg">
        <div class="order-info">
            <table>

    <input type="hidden" name="AUTH_FORM" value="Y" />
    <input type="hidden" name="TYPE" value="AUTH" />
    <?if (strlen($arResult["BACKURL"]) > 0):?>
    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
    <?endif?>
    <?foreach ($arResult["POST"] as $key => $value){?>
    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
    <?}?>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_LOGIN")?></label></td>
        <td><div class="form-input"><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="input-field" /></div></td>
    </tr>
    <tr>
        <td><label class="field-title"><?=GetMessage("AUTH_PASSWORD")?></label></td>
        <td><div class="form-input"><input type="password" name="USER_PASSWORD" maxlength="50" class="input-field" />
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
        </div></td>
    </tr>
    <?if ($arResult["STORE_PASSWORD"] == "Y"){?>
    <tr>
        <td></td>
        <td>
            <input style="margin-left:10px" type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label>
        </td>
    </tr>
    <?}?>
            </table>
        </div>
    </div>
</div>
<div class="order-buttons">
    <input type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
</div>
</form>
<script>
<?if (strlen($arResult["LAST_LOGIN"])>0){?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?}else{?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?}?>
</script>

<?if($arResult["AUTH_SERVICES"]):?>
<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", 
    array(
        "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
        "CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
        "AUTH_URL"=>$arResult["AUTH_URL"],
        "POST"=>$arResult["POST"],
    ), 
    $component, 
    array("HIDE_ICONS"=>"Y")
);?>
<?endif?>

</div>