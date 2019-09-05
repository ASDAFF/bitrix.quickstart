/*
 * Copyright (c) 6/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$(document).on("click", '#subscribe_submit', function(e) {
    e.preventDefault();
    var parent = $(this).parent();
    $.ajax({
        url: '/local/ajax/subscribe/subscribe.php',
        data: {
            mail: parent.find('input[name="mail"]').val(),
        },
        dataType: 'json',
        success: function(result){
            if(result.error){
                alert(result.error);
            }else{
                alert(result.sendMsg);
            }
        }
    });
});