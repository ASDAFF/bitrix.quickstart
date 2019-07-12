<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<a name="order_form"></a>
<div id="order_form_div">
    <?if (!intval($_GET["ORDER_ID"])) {?>
    <div class="step">
        <span class="current"><a href="<?=SITE_DIR?>personal/cart/"><?=GetMessage("SELECTED_G")?></a></span> <span class="sep">&nbsp;</span> <?=GetMessage("ORDERS")?>
    </div>
    <?}?>
<NOSCRIPT>
 <div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>
<?
if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
    if(!empty($arResult["ERROR"]))
    {
        foreach($arResult["ERROR"] as $v)
            echo ShowError($v);
    }
    elseif(!empty($arResult["OK_MESSAGE"]))
    {
        foreach($arResult["OK_MESSAGE"] as $v)
            echo "<p class='sof-ok'>".$v."</p>";
    }

    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
}
else
{
    if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y")
    {
        if(strlen($arResult["REDIRECT_URL"]) > 0)
        {
            ?>
            <script>
            <!--
            //top.location.replace = '<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
            window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
            //setInterval("window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';",2000);
            //-->
            </script>
            <?
            die();
        }
        else
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
    }
    else
    {
        $FORM_NAME = 'ORDERFORM_'.RandString(5);
        if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y"){?>
        <div class="errors">
            <?foreach($arResult["ERROR"] as $v){?>
            <p><?=$v?></p>
            <?}?>
            <br />
        </div>
        <script>top.location.hash = '#order_form';</script>
        <?}?>

        <script>
        <!--
        function submitForm(val)
        {
            if(val != 'Y')
                document.getElementById('confirmorder').value = 'N';

            var orderForm = document.getElementById('ORDER_FORM_ID_NEW');

            jsAjaxUtil.InsertFormDataToNode(orderForm, 'order_form_div', true);
            orderForm.submit();
            console.log('start');
            setTimeout(function(){$(".page-limit-select").selectBox()}, 1500);

            return true;
        }
        function SetContact(profileId)
        {
            document.getElementById("profile_change").value = "Y";
            submitForm();
        }
        //-->
        </script>

        <div style="display:none;">
            <div id="order_form_id">
                <div id="order_form" class="order-checkout">

                    <?if(count($arResult["PERSON_TYPE"]) > 1){?>
                    <div class="order-item">
                        <div class="order-title">
                            <div class="order-title-inner">
                                <span><?=GetMessage("SOA_TEMPL_PERSON_TYPE")?></span>
                            </div>
                        </div>
                        <div class="order-info">
                        <?foreach($arResult["PERSON_TYPE"] as $v){?>
                            <input type="radio" id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onClick="submitForm()"> <label for="PERSON_TYPE_<?= $v["ID"] ?>"><?= $v["NAME"] ?></label>
                        <?}?>
                            <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
                        </div>
                    </div>
                        <?
                    }else{
                        if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0){
                            ?>
                            <input type="hidden" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
                            <input type="hidden" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
                            <?
                        }else{
                            foreach($arResult["PERSON_TYPE"] as $v){
                                ?>
                                <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">11
                                <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
                                <?
                            }
                        }
                    }

                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
                    ?>
                    <input type="hidden" name="confirmorder" id="confirmorder" value="Y">
                    <input type="hidden" name="profile_change" id="profile_change" value="N">

                    <div class="order-buttons">
                    <input type="button" name="submitbutton" onClick="submitForm('Y');" value="<?=GetMessage("SOA_TEMPL_BUTTON")?>">
                    </div>
                </div>
            </div>
        </div>

        <div id="form_new"></div>
        <script>
        <!--
        var newform = document.createElement("FORM");
        newform.method = "POST";
        newform.action = "";
        newform.name = "<?=$FORM_NAME?>";
        newform.id = "ORDER_FORM_ID_NEW";
        var im = document.getElementById('order_form_id');
        document.getElementById("form_new").appendChild(newform);
        newform.appendChild(im);
        //-->
        </script>

        <?
    }
}
?>
</div>