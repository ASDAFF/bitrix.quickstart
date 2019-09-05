/*
 * Copyright (c) 6/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

//регистрация нового пользователя
$(document).on("click", '#register', function(e) {
    e.preventDefault();
    $.ajax({
        url: '/local/ajax/register/register.php',
        data: {
            name: $('#register_form input[name="name"]').val(),
            last_name: $('#register_form input[name="last_name"]').val(),
            phone: $('#register_form input[name="phone"]').val(),
            pass: $('#register_form input[name="passw"]').val(),
            mail: $('#register_form input[name="mail"]').val(),
        },
        dataType: 'json',
        success: function(result){
            if(result.TYPE == 'ERROR'){
                $('#register_result').html(result.MESSAGE).css({"color": "red"});
            }else{
                $('#register_result').html(result.MESSAGE).css({"color": "green"});
                setTimeout(function(){
                    location.reload();
                }, 1000);
            }
        }
    });
});