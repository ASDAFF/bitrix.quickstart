<script type="text/javascript">
    $(document).ready(function(){
        function msg_i(msg, type)
        {
            if(type == "ERROR")
            {
                $("#feedback-block-msg-i").show(600);
                $(".feedback-msg-i").css("color", "tomato").css("font-weight", "").html(msg);
            }
            else if(type == "MESSAGE")
            {
                $("#feedback-block-msg-i").show(600);
                $(".feedback-msg-i").css("color", "blue").css("font-weight", "").html(msg);
            }
        }
    
        $("#feedback-button-i").live("click", function(){
            var error = [];
            var noError = [];
    
            if($("#feedback-name-i").val() == "" || $("#feedback-name-i").val() == " " || $("#feedback-name-i").val() == feedbackLang["NAME"])
                error[error.length] = "#feedback-name-i";
            else
                noError[noError.length] = "#feedback-name-i";
            
            if($("#feedback-email-i").val() == "" || $("#feedback-email-i").val() == " " || $("#feedback-email-i").val() == feedbackLang["EMAIL"])
                error[error.length] = "#feedback-email-i";
            else
                noError[noError.length] = "#feedback-email-i";
            
            if($("#feedback-message-i").val() == "" || $("#feedback-message-i").val() == " " || $("#feedback-message-i").val() == feedbackLang["MESSAGE"])
                error[error.length] = "#feedback-message-i";
            else
                noError[noError.length] = "#feedback-message-i";
            
        <?if($arParams["CAPTCHA"] == "Y"):?>
            if($("#feedback-captcha-word-i").val() == "")
                error[error.length] = "#feedback-captcha-word-i";
            else
                noError[noError.length] = "#feedback-captcha-word-i";
        <?endif;?>
            
            var countError = error.length;
            if(countError > 0)
            {
                for(var i = 0; i <= Number(countError) - 1; i++)
                    $(error[i]).next("span").css("color", "tomato");
                //Вывод ошибки
                msg_i(feedbackLang["ERROR_1"], 'ERROR');
            }
            
            var noCountError = noError.length;
            if(noCountError > 0)
            {
                for(var i = 0; i <= Number(noCountError) - 1; i++)
                    $(noError[i]).next("span").css("color", "#818181");
            }
            
            if(countError == 0)
            {
                var name    = $("#feedback-name-i").val();
                var phone   = "-";
                var email   = $("#feedback-email-i").val();
                var message = $("#feedback-message-i").val();
            <?if($arParams["CAPTCHA"] == "Y"):?>
                var captcha_sid = $("#feedback-captcha-sid-i").val();
                var captcha_word = $("#feedback-captcha-word-i").val();
                var q = "name=" + name + "&phone=" + phone + "&message=" + message + "&email=" + email + "&captcha_sid=" + captcha_sid + "&captcha_word=" + captcha_word;
            <?else:?>
                var q = "name=" + name + "&phone=" + phone + "&message=" + message + "&email=" + email;
            <?endif;?>
    
                $.ajax({
                    url: "<?=$arResult["AJAX_PATH"]?>",
                    type: "POST",
                    data: q,
                    timeout: 3000,
                    beforeSend: function()
                    {
                        msg_i(feedbackLang["SEND"], 'MESSAGE');
                    },
                    success: function(data)
                    {
                        if(Number(data) == 1)
                            msg_i(feedbackLang["GOOD"], 'MESSAGE');
                        else if(Number(data) == 0)
                            msg_i(feedbackLang["ERROR"], 'ERROR');
                        else if(Number(data) == -1)
                            msg_i(feedbackLang["FIELDS"], 'ERROR');
                        
                        $("#feedback-button-i").attr('disabled', 'disabled');
                    },
                    error: function()
                    {
                        msg_i(feedbackLang["ERROR"], 'ERROR');
                        return false;
                    }
                });
            }
            return false;
        });
    });
</script>