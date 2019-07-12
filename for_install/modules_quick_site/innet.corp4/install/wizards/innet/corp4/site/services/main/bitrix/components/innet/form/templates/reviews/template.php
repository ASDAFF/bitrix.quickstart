<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?$frame = $this->createFrame()->begin();?>
<?$frame->beginStub();?>
    <?if(!empty($arResult["ERROR_MESSAGE"])) {
        foreach($arResult["ERROR_MESSAGE"] as $v)
            ShowError($v);
    }
    if(strlen($arResult["OK_MESSAGE"]) > 0) {?>
        <div class="form-ok-text"><?=$arResult["OK_MESSAGE"]?></div>
    <?}?>
    <div class="title3"><?=GetMessage("INNET_FORM_TEMPLATE_HEADER_REVIEWS")?></div>
    <form class="form-question" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
        <?=bitrix_sessid_post()?>
        <div class="question-form-group">
            <input name="user_name" type="text" class="question-input" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_NAME")?>" value="<?=$arResult["AUTHOR_NAME"]?>">
            <input name="user_email" type="text" class="question-input" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_EMAIL")?>" value="<?=$arResult["AUTHOR_EMAIL"]?>">
        </div>
        <textarea name="comment" placeholder="<?=GetMessage("INNET_FORM_TEMPLATE_COMMENT")?>"><?=$arResult["MESSAGE"]?></textarea>
        <div class="mf-captcha" style="display:inline-block; width: 100%;">
            <?if($arParams["USE_CAPTCHA"] == "Y"):?>
                <div class="mf-text"><?=GetMessage("INNET_FORM_TEMPLATE_CAPTCHA")?></div>
                <input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
                <div class="mf-text" style="margin: 0 0 20px;"><?=GetMessage("INNET_FORM_TEMPLATE_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
                <img style="float:left; margin-right: 20px; margin-bottom:20px;" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
                <input style="height: 40px;padding-left: 5px;width: 180px;font-size: 18px;border: 1px solid #DEDEDE;margin-left: 0px;margin-bottom: 20px;" type="text" name="captcha_word" size="30" maxlength="50" value="">
            <?endif;?>
            <input type="hidden" name="location" value="<?=$_SERVER['SERVER_NAME'] . $APPLICATION->GetCurPage()?>">
            <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
            <input type="submit" name="submit" style="float: right;" class="btn" value="<?=GetMessage('INNET_FORM_TEMPLATE_SUBMIT')?>">
        </div>
    </form>
<?$frame->beginStub();?>
<?$frame->end();?>