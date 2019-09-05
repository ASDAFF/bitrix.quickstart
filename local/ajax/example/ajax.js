/*
 * Copyright (c) 6/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$(document).ready(function () {
    $(document).on("click", '#feedback_save, #feedback_save2', function(e) {
        e.preventDefault();
        var parent = $(this).parent();
        $.ajax({
            url: '/local/ajax/feedback.php',
            data: {
                phone: parent.find('input[name="user_phone"]').val(),
                name: parent.find('input[name="user_name"]').val()
            },
            dataType: 'json',
            success: function(result){
                alert(result.error);
            }
        });
    });
});