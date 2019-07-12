<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div class="feedback">
<h2 id="orderForm"><?php echo GetMessage('SMARTREALT_FORM_TITLE');?></h2>
<?php
    if(!empty($arResult["ERROR_MESSAGE"]))
    {
        foreach($arResult["ERROR_MESSAGE"] as $v)
            ShowError($v);
    }
    else if(strlen($arResult["OK_MESSAGE"]) > 0)
    {
        ShowNote($arResult["OK_MESSAGE"]);
        echo '</div>';
        return;
    }
?>
<form action="#orderForm" method="POST">
    <?=bitrix_sessid_post()?>
    <div class="field name">
        <div class="label">
            <?=GetMessage("SMARTREALT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>
        </div>
        <input type="text" name="NAME" value="<?=htmlspecialchars($arResult["NAME"])?>">
    </div>
    <div class="field email">
        <div class="label">
            <?=GetMessage("SMARTREALT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>
        </div>
        <input type="text" name="EMAIL" value="<?=htmlspecialchars($arResult["EMAIL"])?>">
    </div>
    <div class="field telephone">
        <div class="label">
            <?=GetMessage("SMARTREALT_TELEPHONE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>
        </div>
        <input type="text" name="PHONE" value="<?=htmlspecialchars($arResult["PHONE"])?>">
    </div>
    <div class="field objectNumber">
        <div class="label">
            <?=GetMessage("SMARTREALT_OBJECT_NUMBER")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("OBJECT_NUMBER", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>
        </div>
        <input type="text" name="OBJECT_NUMBER" value="<?=htmlspecialchars($arResult["OBJECT_NUMBER"])?>" <?php echo strlen($arParams['OBJECT_NUMBER_DEFAULT']) >0?'readonly="readonly"':''?>> 
    </div>
    <div class="field message">
        <div class="label">
            <?=GetMessage("SMARTREALT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>
        </div>
        <textarea name="MESSAGE"><?=htmlspecialchars($arResult["MESSAGE"])?></textarea>
    </div>
    <?if($arParams["USE_CAPTCHA"] == "Y"):?>
    <div class="field captcha">
        <input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
        <div class="label"><?=GetMessage("SMARTREALT_CAPTCHA_CODE")?><span class="req">*</span></div>
        <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" align="left" alt="CAPTCHA">
        <input type="text" name="captcha_word" maxlength="10" value="">
    </div>
    <?endif;?>
    <div class="submit">
        <input type="submit" name="submit" value="<?=GetMessage("SMARTREALT_SUBMIT")?>">
    </div>
</form>
</div>
