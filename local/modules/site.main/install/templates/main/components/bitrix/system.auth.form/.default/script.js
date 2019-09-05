$(document).ready(function(){
    
    // фокус на поле логина
    $('.open-login-popup').click(function(){
        setTimeout(function(){
            $('#system-auth-form-login').focus();
        }, 300);
    });
})
