$(function () {

    if ($('.phone_masked').length)
        $('.phone_masked').mask("+7 (999) 999-99-99");

});

function ClearErrors(idForm) {

    var errorBlock = $('#' + idForm + ' .alert-danger');

    $('#' + idForm + ' .has-error').removeClass('has-error');

    if (errorBlock.length){
        $(errorBlock).find('li').remove();
        $(errorBlock).slideUp();
    }
}

function ShowErrors(idForm, arErrors) {

    var errorBlock = $('#' + idForm + ' .alert-danger');
    
    if (errorBlock.length)
        $(errorBlock).find('li').remove();

    $(arErrors).each(function () {
        $('#' + idForm + ' [name="' + this.NAME + '"]').parents('div').first().addClass('has-error');
        
        if (errorBlock.length && this.MESSAGE){
            $(errorBlock).find('ul').first().append('<li>' + this.MESSAGE + '</li>');
        }
    });


    if (errorBlock.length)
        $(errorBlock).slideDown();
}

function PlFormCheck(form) {

    ClearErrors($(form).attr('id'));

    var action = $(form).attr('action'),
            loader = $(form).find('.loader').first(),
            success = $(form).find('.alert-success').first(),
            btn = $(form).find('button').first();

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
            
            if(obj.ERRORS)
                ShowErrors($(form).attr('id'), obj.ERRORS);
            else {
                $(form).find('input[type=phone]:not([disabled=disabled])').val('');
                $(form).find('input[type=text]:not([disabled=disabled])').val('');
                $(form).find('textarea:not([disabled=disabled])').val('');
                $(success).slideDown();
            }
                
            btn.removeAttr('disabled');
            loader.addClass('hidden');
        }
    });

    return false;
}