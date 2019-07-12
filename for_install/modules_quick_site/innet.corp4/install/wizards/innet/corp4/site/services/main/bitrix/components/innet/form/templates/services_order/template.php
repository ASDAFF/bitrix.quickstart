<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?$frame = $this->createFrame()->begin();?>
<?$frame->beginStub();?>
    <div class="title3"><?=GetMessage("INNET_FORM_TEMPLATE_HEADER_ORDER_SERVICES")?></div>
    <?=GetMessage("INNET_FORM_TEMPLATE_MANAGER")?>

    <form action="<?=POST_FORM_ACTION_URI?>" method="POST">
        <?=bitrix_sessid_post()?>

        <?if(!empty($arResult["ERROR_MESSAGE"])) {
            foreach($arResult["ERROR_MESSAGE"] as $v)
                ShowError($v);
        }?>

        <?if(strlen($arResult["OK_MESSAGE"]) > 0) {?>
            <div class="form-ok-text"><?=$arResult["OK_MESSAGE"]?></div>
        <?}?>

        <input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_NAME")?>">
        <input type="text" name="user_phone" value="<?=$arResult["AUTHOR_PHONE"]?>" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_PHONE")?>">
        <input type="text" name="user_email" value="<?=(!empty($arResult["AUTHOR_EMAIL"])) ? $arResult["AUTHOR_EMAIL"] : htmlspecialcharsbx($USER->GetEmail())?>" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_EMAIL")?>">
        <textarea placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_COMMENT")?>" name="comment"></textarea>

        <?if($arParams["USE_CAPTCHA"] == "Y"):?>
            <div class="mf-captcha">
                <div class="mf-text"><?=GetMessage("INNET_FORM_TEMPLATE_CAPTCHA")?></div>
                <input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
                <div class="mf-text"><?=GetMessage("INNET_FORM_TEMPLATE_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
                <img style="float:left;" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
                <input style="height: 40px;padding-left: 5px;width: 140px;font-size: 18px;border: 1px solid #DEDEDE;margin-left: 10px;margin-bottom: 20px;" type="text" name="captcha_word" size="30" maxlength="50" value="">
            </div>
        <?endif;?>

        <input type="hidden" name="location" value="<?=$_SERVER['SERVER_NAME'] . $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
        <input type="submit" name="submit" class="btn" value="<?=GetMessage('INNET_FORM_TEMPLATE_SUBMIT')?>">
    </form>
<?$frame->beginStub();?>
<?$frame->end();?>