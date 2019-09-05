/*
 * Copyright (c) 6/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */
/*----------- ajax обновление корзины -----------*/
//удаление товара из корзины в попапе
$(document).on("click", '.t-basket__close', function(e) {
    e.preventDefault();
    var id = $(this).attr('itemId');
    $.ajax({
        url: '/local/ajax/delforbasket.php',
        data: {
            id: id,
        },
        dataType: 'json',
        success: function(result){
            $('.header__h-info li.h-info__item-basket span.h-info__count').text(parseInt($('.header__h-info li.h-info__item-basket span.h-info__count').text())-1);

            // начало аджакс запроса обновления корзины
            $.ajax({
                url:'/local/ajax/cart/addtocart.php',
                type:'POST',
                data:{'basket':'refresh'},
                success: function(data) {
                    $('#basket_popup').html(data);

                },
            })
            // конец запроса
        }
    });
});
