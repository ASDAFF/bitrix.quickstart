<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$jsLang = '
<script type="text/javascript">
    var feedbackLang = {
        NAME: "'.GetMessage("NAME").'",
        PHONE: "'.GetMessage("PHONE").'",
        EMAIL: "'.GetMessage("EMAIL").'",
        MESSAGE: "'.GetMessage("MESSAGE").'",
        ERROR_1: "'.GetMessage("V1RT_FEEDBACK_JS_ERROR_1").'",
        FIELDS: "'.GetMessage("FIELDS").'",
        SEND: "'.GetMessage("SEND").'",
        ERROR: "'.GetMessage("ERROR").'",
        GOOD: "'.GetMessage("GOOD").'"
    };
</script>';
$APPLICATION->AddHeadString($jsLang);
?>
<script type="text/javascript">var tpl = '<?=SITE_TEMPLATE_PATH?>';</script>
<div class="block-1"><?=GetMessage("FEEDBACK_FORM")?></div>
    <form action="">
        <div class="block-6">
            <label class="block-6_1">
                <input type="text" id="feedback-name-i" value="" name="" />
				<span><?=GetMessage("NAME")?></span>
            </label>
            <label class="block-6_1">
                <input type="text" id="feedback-email-i" value="" name="" />
				<span><?=GetMessage("EMAIL")?></span>
            </label>
            <label class="block-6_2">
                <textarea id="feedback-message-i" cols="" rows=""></textarea>
				<span><?=GetMessage("MESSAGE")?></span>
            </label>
            
            <?if($arParams["CAPTCHA"] == "Y"):?>
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" style="margin: 0 0 20px 0;" width="218" height="40" alt="CAPTCHA"/>
                <label class="block-6_1">
                    <input type="hidden" id="feedback-captcha-sid-i" value="<?=$arResult["capCode"]?>"/>
                    <input type="text" id="feedback-captcha-word-i" maxlength="50" value="" name="" />
    				<span><?=GetMessage("CAPTCHA_CODE")?></span>
                </label>
            <?endif;?>
            
            <input type="submit" id="feedback-button-i" value=" <?=GetMessage("SUBMIT")?> "/>
            <div style="clear: both;"></div>
            <div id="feedback-block-msg-i" style="display: none;">
                <div class="feedback-msg-i"></div>
            </div>
        </div>
    </form>