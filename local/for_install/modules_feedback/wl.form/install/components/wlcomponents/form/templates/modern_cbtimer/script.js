$(function () {

    $('.wl-mclb__input').mask("+7 (999) 999-99-99");

    $('.wl-mclb__btn').on('click', function (e) {

        e.preventDefault();

        if (!$('.wl-mclb').hasClass('open') && !$('.wl-mclb').hasClass('success')) {
            $('.wl-mclb').addClass('open');
            $('.wl-mclb__close').show();
            $('.wl-mclb .has-error').removeClass('has-error');
            $('.wl-mclb__input').val('');
        } else if ($('.wl-mclb').hasClass('success')) {
            $('.wl-mclb__title, .wl-mclb__input').show();
            $('.wl-mclb__success').hide();
            $('.wl-mclb').removeClass('success');
            $('.wl-mclb').removeClass('open');
            $('.wl-mclb__btn span').html($('.wl-mclb__btn').data('order'));
            $('.wl-mclb__close').hide();
        } else {
            $('.wl-mclb').submit();
        }

        return false;

    });

    $('.wl-mclb__close').on('click', function (e) {

        e.preventDefault();

        if ($('.wl-mclb').hasClass('open')) {
            $('.wl-mclb').removeClass('open');
            $(this).hide();
        }

        if ($('.wl-mclb').hasClass('success')) {
            $('.wl-mclb__title, .wl-mclb__input').show();
            $('.wl-mclb__success').hide();
            $('.wl-mclb').removeClass('success');
            $('.wl-mclb__btn span').html($('.wl-mclb__btn').data('order'));
        }

        return false;

    });

});

function ModernCheck(form) {

    $('.wl-mclb').click();

    ClearErrors($(form).attr('id'));

    var action = $(form).attr('action'),
            btn = $(form).find('.wl-mclb__btn').first(),
            loader = $(btn).find('i').first();

    $(loader).attr('class', 'fa fa-spinner');

    $.ajax({
        type: 'POST',
        url: action,
        data: $(form).serialize(),
        error: function () {
            alert('Connection error');
            $(loader).attr('class', 'fa fa-phone');
        },
        success: function (data) {
            var obj = jQuery.parseJSON(data);

            if (obj.ERRORS)
                ShowErrors($(form).attr('id'), obj.ERRORS);
            else {

                // start timer
                var period = 29;
                $('.wl-mclb__timer').html(period);
                var wlTimer = setInterval(function () {

                    period = parseInt($('.wl-mclb__timer').html()) - 1;
                    if (period < 0)
                        clearInterval(wlTimer);
                    else {
                        if (period < 10)
                            period = '0' + period;
                        $('.wl-mclb__timer').html(period);
                    }
                }, 1000);

                $('.wl-mclb__title, .wl-mclb__input').fadeOut('slow', function () {
                    $('.wl-mclb__success').fadeIn('slow');
                    $('.wl-mclb').addClass('success');
                    $('.wl-mclb__btn span').html($('.wl-mclb__btn').data('success'));
                });
            }

            $(loader).attr('class', 'fa fa-phone');
        }
    });

    return false;
}