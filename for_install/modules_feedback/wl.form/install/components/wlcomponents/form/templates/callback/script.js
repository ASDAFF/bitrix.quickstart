$(function () {
    $('.callback').on('click', function (e) {
        e.preventDefault();

        $('#callBack .input_block').show();
        $('#callBack button[type=submit]').show();
        $('#callBack .alert-success').hide();
        $('#callBack .has-error').removeClass('has-error');

        $('#callBack input[type=text], #callBack input[type=phone]').val('');

        $('#callBack').modal();

        return false;
    });
});

function CallBackForm(form) {
    ClearErrors($(form).attr('id'));

    var action = $(form).attr('action'),
            loader = $(form).find('.loader').first(),
            success = $(form).find('.alert-success').first(),
            btn = $(form).find('button[type=submit]').first();

    btn.attr('disabled', 'disabled');
    loader.removeClass('hidden');
    $(success).slideUp();

    $.ajax({
        type: 'POST',
        url: action,
        data: $(form).serialize(),
        error: function () {
            alert('Connection error');
            btn.removeAttr('disabled');
            loader.addClass('hidden');
        },
        success: function (data) {
            var obj = jQuery.parseJSON(data);

            if (obj.ERRORS)
                ShowErrors($(form).attr('id'), obj.ERRORS);
            else {
                $(form).find('.input_block').first().slideUp('slow', function () {
                    btn.hide();
                    $(success).slideDown();
                });
            }

            btn.removeAttr('disabled');
            loader.addClass('hidden');
        }
    });

    return false;
}