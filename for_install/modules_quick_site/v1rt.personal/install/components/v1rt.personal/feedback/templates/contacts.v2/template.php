<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
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
<h2><?=GetMessage("FEEDBACK_FORM")?></h2>
<script type="text/javascript">var tpl = '<?=SITE_TEMPLATE_PATH?>';</script>
<table class="post-form-feedback">
    <tbody>
        <tr>
            <td width="150"><span style="color: tomato;">*</span> <label for="feedback-name"><?=GetMessage("NAME")?>:</label></td>
            <td><input type="text" id="feedback-name"/></td>
        </tr>
        <tr>
            <td><span style="color: tomato;">*</span> <label for="feedback-phone"><?=GetMessage("PHONE")?>:</label></td>
            <td><input type="text" id="feedback-phone"/></td>
        </tr>
        <tr>
            <td><span style="color: tomato;">*</span> <label for="feedback-email"><?=GetMessage("EMAIL")?>:</label></td>
            <td><input type="text" id="feedback-email"/></td>
        </tr>
        <tr>
            <td style="vertical-align: top !important;"><span style="color: tomato;">*</span> <label for="feedback-message"><?=GetMessage("MESSAGE")?>:</label></td>
            <td><textarea id="feedback-message" rows="" cols=""></textarea></td>
        </tr>
        <tr>
            <td colspan="2">
                <sup><span style="color: tomato;">*</span></sup> - <?=GetMessage("FIELDS")?>
            </td>
        </tr>
        <tr id="feedback-block-msg" style="display: none;">
            <td colspan="2" class="feedback-msg">
                
            </td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" id="feedback-button" value=" <?=GetMessage("SUBMIT")?> "/></td>
        </tr>
    </tbody>
</table>