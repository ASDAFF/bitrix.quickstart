$(document).ready(function(){
    function msg(msg, type)
    {
        if(type == "ERROR")
        {
            $("#feedback-block-msg").show(600);
            $(".feedback-msg").css("color", "tomato").css("font-weight", "").html(msg);
        }
        else if(type == "MESSAGE")
        {
            $("#feedback-block-msg").show(600);
            $(".feedback-msg").css("color", "blue").css("font-weight", "").html(msg);
        }
    }

    $("#feedback-button").live("click", function(){
        var error = [];
        var noError = [];

        if($("#feedback-name").val() == "" || $("#feedback-name").val() == " " || $("#feedback-name").val() == feedbackLang["NAME"])
            error[error.length] = "#feedback-name";
        else
            noError[noError.length] = "#feedback-name";
        
        if($("#feedback-phone").val() == "" || $("#feedback-phone").val() == " " || $("#feedback-phone").val() == feedbackLang["PHONE"])
            error[error.length] = "#feedback-phone";
        else
            noError[noError.length] = "#feedback-phone";
        
        if($("#feedback-email").val() == "" || $("#feedback-email").val() == " " || $("#feedback-email").val() == feedbackLang["EMAIL"])
            error[error.length] = "#feedback-email";
        else
            noError[noError.length] = "#feedback-email";
        
        if($("#feedback-message").val() == "" || $("#feedback-message").val() == " " || $("#feedback-message").val() == feedbackLang["MESSAGE"])
            error[error.length] = "#feedback-message";
        else
            noError[noError.length] = "#feedback-message";

        var countError = error.length;
        if(countError > 0)
        {
            for(var i = 0; i <= Number(countError) - 1; i++)
                $(error[i]).css("border", "2px tomato solid");
            //Вывод ошибки
            msg(feedbackLang["ERROR_1"], 'ERROR');
        }

        var noCountError = noError.length;
        if(noCountError > 0)
        {
            for(var i = 0; i <= Number(noCountError) - 1; i++)
                $(noError[i]).css("border", "");
        }

        if(countError == 0)
        {
            var name    = $("#feedback-name").val();
            var phone   = $("#feedback-phone").val();
            var email   = $("#feedback-email").val();
            var message = $("#feedback-message").val();

            $.ajax({
                url: tpl + "/ajax/ajax.sendmail.php",
                type: "POST",
                data: "name=" + name + "&phone=" + phone + "&message=" + message + "&email=" + email,
                timeout: 3000,
                beforeSend: function()
                {
                    msg(feedbackLang["SEND"], 'MESSAGE');
                },
                success: function(data)
                {
                    if(Number(data) == 1)
                        msg(feedbackLang["GOOD"], 'MESSAGE');
                    else if(Number(data) == 0)
                        msg(feedbackLang["ERROR"], 'ERROR');
                    else if(Number(data) == -1)
                        msg(feedbackLang["FIELDS"], 'ERROR');
                    
                    $("#feedback-button").attr('disabled', 'disabled');
                },
                error: function()
                {
                    msg(feedbackLang["ERROR"], 'ERROR');
                    return false;
                }
            });
        }
        return false;
    });
});