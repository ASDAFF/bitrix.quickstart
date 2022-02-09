<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
    <!--
    function ChangeGenerate(val)
    {
        if(val)
            {
            document.getElementById("sof_choose_login").style.display='none';
        }
        else
            {
            document.getElementById("sof_choose_login").style.display='block';
            document.getElementById("NEW_GENERATE_N").checked = true;
        }

        try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
    }
    //-->
</script>
<? echo "<pre>", print_r($arResult["AUTH"]), "</pre>";?>
<table border="0" cellspacing="0" cellpadding="1">
    <tr>
        <?if ($USER->IsAuthorized()):?>
            <td valign="top">

                <?=bitrix_sessid_post()?>
                <?
                    foreach ($arResult["POST"] as $key => $value)
                    {
                    ?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
                    <?
                    }
                ?>
                <table class="sale_order_full_table">

                    <tr>
                        <td>     
                            <select id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" onchange="submitForm()">
                                <?
                                    foreach($arResult["PERSON_TYPE"] as $v)
                                    {
                                    ?><option value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " selected=\"selected\"";?> ><?= $v["NAME"] ?></option><?
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <?echo GetMessage("STOF_NAME")?> <span class="starrequired">*</span><br />
                            <input type="text" name="NEW_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_NAME"]?>">&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <?echo GetMessage("STOF_LASTNAME")?> <span class="starrequired">*</span><br />
                            <input type="text" name="NEW_LAST_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>">&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>E-Mail <span class="starrequired">*</span><br />
                            <input type="text" name="USER_LOGIN" maxlength="40" size="40" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>">&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td nowrap><?echo GetMessage("STOF_PASSWORD")?> <span class="starrequired">*</span><br />
                            <input type="password" name="USER_PASSWORD" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>" maxlength="40" size="40">&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td nowrap><a href="<?=$arParams["PATH_TO_AUTH"]?>?forgot_password=yes&back_url=<?= urlencode($APPLICATION->GetCurPageParam()); ?>"><?echo GetMessage("STOF_FORGET_PASSWORD")?></a></td>
                    </tr>
                    <tr>
                        <td nowrap align="center">
                            <input type="button" onClick="ShowOrder();" value="<?echo GetMessage("STOF_NEXT_STEP")?>">
                            <input type="hidden" name="do_authorize" id="auth" value="Y" >
                        </td>
                    </tr>
                </table>

            </td>
            <?else:?>
            <td valign="top">
                <?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>

                    <?=bitrix_sessid_post()?>
                    <?
                        foreach ($arResult["POST"] as $key => $value)
                        {
                        ?>
                        <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
                        <?
                        }
                    ?>
                    <table class="sale_order_full_table">
                        <tr>
                            <td>     
                                <select id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" onchange="submitForm()">
                                    <?
                                        foreach($arResult["PERSON_TYPE"] as $v)
                                        {
                                        ?><option value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " selected=\"selected\"";?> ><?= $v["NAME"] ?></option><?
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <?echo GetMessage("STOF_NAME")?> <span class="starrequired">*</span><br />
                            <input type="text" name="NEW_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_NAME"]?>">&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            <?echo GetMessage("STOF_LASTNAME")?> <span class="starrequired">*</span><br />
                            <input type="text" name="NEW_LAST_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>">&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td nowrap>
                            E-Mail <span class="starrequired">*</span><br />
                            <input type="text" name="NEW_EMAIL" size="40" value="<?=$arResult["AUTH"]["NEW_EMAIL"]?>">&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <input type="hidden" id="NEW_GENERATE" name="NEW_GENERATE" value="Y" OnClick="ChangeGenerate(false)">
                    <tr>
                        <td>
                            <div id="sof_choose_login">
                                <table>
                                    <tr>
                                        <td><?echo GetMessage("STOF_LOGIN")?> <span class="starrequired">*</span><br />
                                            <input type="text" name="NEW_LOGIN" size="40" value="<?=$arResult["AUTH"]["NEW_LOGIN"]?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?echo GetMessage("STOF_PASSWORD")?> <span class="starrequired">*</span><br />
                                            <input type="password" name="NEW_PASSWORD" size="40">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?echo GetMessage("STOF_RE_PASSWORD")?> <span class="starrequired">*</span><br />
                                            <input type="password" name="NEW_PASSWORD_CONFIRM" size="40">
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </td>
                    </tr>


                    <?
                        if($arResult["AUTH"]["captcha_registration"] == "Y") //CAPTCHA
                        {
                        ?>
                        <tr>
                            <td><br /><b><?=GetMessage("CAPTCHA_REGF_TITLE")?></b></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="captcha_sid" value="<?=$arResult["AUTH"]["capCode"]?>">
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["AUTH"]["capCode"]?>" width="180" height="40" alt="CAPTCHA">
                            </td>
                        </tr>
                        <tr valign="middle">
                            <td>
                                <span class="starrequired">*</span><?=GetMessage("CAPTCHA_REGF_PROMT")?>:<br />
                                <input type="text" name="captcha_word" size="30" maxlength="50" value="">
                            </td>
                        </tr>
                        <?
                        }
                    ?>
                    <tr>
                        <td align="center">
                            <input type="button" onclick="ShowOrder();" value="<?echo GetMessage("STOF_NEXT_STEP")?>">
                            <input type="hidden" name="do_register" id="reg" value="Y" >
                        </td>
                    </tr>
                </table>

                <?endif;?>
        </td>
        <?endif;?>
    </tr>
</table>
<?echo GetMessage("STOF_REQUIED_FIELDS_NOTE")?><br /><br />
<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
    <?echo GetMessage("STOF_EMAIL_NOTE")?><br /><br />
    <?endif;?>
<?echo GetMessage("STOF_PRIVATE_NOTES")?>
