<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
    function checkForm () {
        var title;
        var elem;
        var dutyField = "<?=GetMessage('EMPTY_FIELD')?>";
        var check = true;

        function checkError (field, str) {
            $("[name="+field+"]").css("border", "2px solid red");
            check = false;
        }

    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_NAME")?>"';
            elem = document.preview.user_name.value;
            if (elem == '') checkError('user_name', dutyField + title);
        <?endif?>
    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TEL", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_TEL")?>"';
            elem = document.preview.user_tel.value;
            if (elem == '') checkError('user_tel', dutyField + title);
        <?endif?>
    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_EMAIL")?>"';
            elem = document.preview.user_email.value;
            if (elem == '') checkError('user_email', dutyField + title);
    <?endif?>
    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("SUBJECT", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_SUBJECT")?>"';
            elem = document.preview.user_subject.value;
            if (elem == '') checkError('user_subject', dutyField + title);
    <?endif?>
    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME_FROM", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_TIME_FROM")?>"';
            elem = document.preview.user_time_from.value;
            if (elem == '') checkError('user_time_from', dutyField + title);
        <?endif?>
    <?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME_TILL", $arParams["REQUIRED_FIELDS"])):?>
            title = '"<?=GetMessage("MFT_TIME_TILL")?>"';
            elem = document.preview.user_time_till.value;
            if (elem == '') checkError('user_time_till', dutyField + title);
        <?endif?>
		
        if (check) {
            $.ajax({
                type: "post",
                url: $("form[name=preview]").attr("action"),
                data: $("form[name=preview]").serialize(),
                success: function() {
                    $("#callorder-dialog").html($("#msg").html());
                }
            });
        }
        return false;
    }
	
	$(function() {
		$("#open-dialog").click(function() {
			$("#callorder-dialog").show().css("top",$(window).scrollTop()+($(window).height() - $("#callorder-dialog").height())/2);
			$("#overlay").show();
		});
		
		$("#callorder-dialog").delegate("#callback-close", "click", function(){
			$("#callorder-dialog, #overlay").hide();
			return false;
		});
	});
</script>
<div id="msg" style="display: none"><?=GetMessage("SUCCESS");?></div>
<div id="callorder-dialog" class="dialog" title="<?=GetMessage("TITLE");?>">
	<h3><?=GetMessage("TITLE");?></h3>
    <div class="modal-body">
        <form name="preview" action="<?=$templateFolder?>/ajax/callback.php" method="POST">
            <table width="100%">
                <tr class="form_item">
                    <td>
                        <label for=""><?=GetMessage("YOUR_NAME");?><span class="form_imp">*</span>:</label>
                    </td>
                    <td>
                        <input class="form_long_inp" id="username_callback" type="text" name="user_name" value="" placeholder="">
                    </td>
                </tr>
                <tr class="form_item">
                    <td>
                        <label for=""><?=GetMessage("CONTACT_PHONE");?><span class="form_imp">*</span>:</label>
                    </td>
                    <td>
                        <input class="form_long_inp" id="phone_callback" type="text" name="user_tel" value="" placeholder="8 812 1234567">
                    </td>
                </tr>
                <tr class="form_item">
                    <td>
                        <label for=""><?=GetMessage("EMAIL");?><span class="form_imp"></span>:</label>
                    </td>
                    <td>
                        <input class="form_long_inp" id="email_callback" type="text" name="user_email" value="" placeholder="">
                    </td>
                </tr>
                <tr class="form_item">
                    <td>
                        <label for=""><?=GetMessage("SUBJECT");?><span class="form_imp"></span>:</label>
                    </td>
                    <td>
                        <input class="form_long_inp" id="subject_callback" type="text" name="user_subject" value="" placeholder="">
                    </td>
                </tr>
                <tr class="form_item">
                    <td>
                        <label for=""><?=GetMessage("TIME");?><span class="form_imp">*</span>:</label>
                    </td>
                    <td>
                        <span class="text_abs_left"><?=GetMessage("TIME_FROM");?></span>
                        <input style="width:55px" class="form_min_inp" type="text" name="user_time_from" value="<?=$arParams["TIME_FROM"];?>" placeholder="<?=$arParams["TIME_FROM"];?>">
                        <?=GetMessage("TIME_TILL");?>
                        <input style="width:55px" class="form_min_inp" type="text" name="user_time_till" value="<?=$arParams["TIME_TILL"];?>" placeholder="<?=$arParams["TIME_TILL"];?>">
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <button class="button" type="submit" onclick="checkForm(); return false;"><?=GetMessage("SUBMIT");?></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="alert">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>