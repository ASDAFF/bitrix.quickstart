$(document).ready(function(){
	if($("a.link").is("#eMarket-login")) {
        AuthPopup.init();
	}
});

var AuthPopup = {
    init: function(){
        $("#eMarket-login").on("click", function(event){


            var cur_page = $('[name = "eMarket_auth_cur_page"]').val();
            var backurl = $('[name = "eMarket_auth_backurl"]').val();
            var forgotPassUrl = $('[name = "eMarket_auth_forgotPassUrl"]').val();
            var site_id = $('[name = "eMarket_auth_site_id"]').val();



            $(".eMarket-popup").remove();
            $('#eMarket-auth').append(
                '<div class="eMarket-popup"></div>'+
                    '<div class="eMarket-popup-window"></div>'
            );

            $.ajax({
                url: cur_page+"/ajax.php",
                data: {backurl:backurl, forgotPassUrl:forgotPassUrl, site_id:site_id,},
                beforeSend: function(data){
                    $(".eMarket-popup").addClass('load');
                    $(".eMarket-popup").fadeIn();
                },
                success: function(data){
                    $(".eMarket-popup").removeClass('load');
                    $(".eMarket-popup-window").html(data);

                    var popup_window_height = $(".eMarket-popup-window").height();
                    var popup_window_width = $(".eMarket-popup-window").width();

                    var height = -(popup_window_height/2);
                    var width = -(popup_window_width/2);

                    //$(".eMarket-popup-window").css("margin-top", height);
                    //$(".eMarket-popup-window").css("margin-left", width);
                    $(".eMarket-popup-window").fadeIn();

                    $(".eMarket-popup, .eMarket-popup-window .close").on("click", function(){
                        AuthPopup.close();
                    });

                    $(".rm-pass-link").unbind('click').click(function(){
                        if($(this).hasClass('active'))
                        {
                            $(this).removeClass('active');
                            $("#rm_pass_block").slideUp();
                        }else{
                            $(".rm-pass-message").show();
                            $(".rm-pass-error-message").html('&nbsp;').hide();
                            $(this).addClass('active');
                            $("#rm_pass_block").slideDown();
                        }
                    });

                    $(".close-rm-pass-btn").unbind('click').click(function(){
                        $(this).parents('.popup_login_page').find('.rm-pass-link').removeClass('active');
                        $("#rm_pass_block").slideUp();
                    });
                }
            });
            event.preventDefault();
        });
    },
    close: function(){
        $(".eMarket-popup").fadeOut();
        $(".eMarket-popup-window").fadeOut();
        $(".eMarket-popup").remove();
        $(".eMarket-popup-window").remove();
    },
    RmPassErrorMessage: function(text){
        $(".rm-pass-message").hide();
        $(".rm-pass-error-message").html(text).show();

    },
    sendRmPass: function(){
        var email = $("#user_email").val();
        if(email.length > 0)
        {
            $.ajax({
                type: 'post',
                url: EmarketSite.SITE_DIR+'ajax/get_component.php',
                data: {
                    name: 'auth:emarket.ajaxauth',
                    params: {
                        FUNC_NAME: 'remember_pass',
                        EMAIL: email
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data.status == 'ok')
                    {
                        AuthPopup.close();
                    }
                    if(data.status == 'error')
                    {
                        AuthPopup.RmPassErrorMessage(data.message);
                    }
                }
            });
        }
        else
        {
            $("#user_email").css({'box-shadow': '0px 0px 4px #ff0000'}).unbind('click').click(function(){
                $(this).unbind('click').css({'box-shadow': ''});
            });
        }
        return false;
    },
    login: function(){
        var login = $("#user_login").val();
        var password = $("#user_password").val();
        var remember = 'N';
        if($("#USER_REMEMBER").prop('checked') == true)
        {
            remember = 'Y';
        }
        
        $(".login-error").html('').hide();
        var error = false;
        if(login.length == 0)
        {
            $("#user_login").css({'box-shadow': '0px 0px 4px #ff0000'}).unbind('click').click(function(){
                $(this).unbind('click').css({'box-shadow': ''});
            })
            error = true;
        }
        if(password.length == 0)
        {
            $("#user_password").css({'box-shadow': '0px 0px 4px #ff0000'}).unbind('click').click(function(){
                $(this).unbind('click').css({'box-shadow': ''});
            });
            error = true;

        }
        if(!error)
        {
            $.ajax({
                type: 'post',
                url: EmarketSite.SITE_DIR+'ajax/get_component.php',
                data: {
                    name: 'auth:emarket.ajaxauth',
                    params: {
                        FUNC_NAME: 'login',
                        LOGIN: login,
                        PASSWORD: password,
                        REMEMBER: 'Y'
                    }
                },
                dataType: 'json',
                success: function(data){
                    if(data.status == 'ok')
                    {
                        document.location.reload();
                    }
                    if(data.status == 'error')
                    {
                        $(".login-error").html(data.message).show();
                    }
                }
            });
        }
        return false;
    }
}
