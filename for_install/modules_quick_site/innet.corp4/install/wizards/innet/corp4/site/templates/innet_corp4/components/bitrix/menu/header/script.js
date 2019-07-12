/*
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 200 && $menu.hasClass("default")) {
            $menu.fadeOut(0, function () {
                $(this).removeClass("default")
                    .addClass("fixed")
                    .fadeIn(0);
            });
            //$('.toogle-block' ).css("display", "none");
            if ($(window).width() > 960) {
                $('.header .toogle-block').css("display", "none");
                $('.header .toogle-block').fadeOut(0);
            }
        } else if ($(this).scrollTop() <= 200 && $menu.hasClass("fixed")) {
            $menu.fadeOut(0, function () {
                $(this).removeClass("fixed")
                    .addClass("default")
                    .fadeIn(0);
            });
            if ($(window).width() > 960) {
                //$menu.next('.toogle-block').css("display", "block");
                $('.header .toogle-block').fadeIn(0);
            }
            //$('.toogle-block' ).css("display", "block");
        }
    });
});
*/
