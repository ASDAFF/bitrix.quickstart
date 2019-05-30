/**
 * Created by ASDAFF on 08.11.2017.
 */
/*----------- FEEDBACK Form -----------*/
jQuery(function() {
    var form = $('form[name=FEEDBACK]');
    form.submit(function() {
        $('#form-loading-feedback').fadeIn();
        $('#error-feedback, #success-feedback, #beforesend-feedback').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-feedback').hide();
            $('#beforesend-feedback, #results-feedback').fadeIn();
        };
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function() {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0) return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                $('#form-loading-feedback').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-feedback, #success-feedback').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-feedback, #error-feedback').hide().fadeIn();
            },
            error: function(data) {
                $('#form-loading-feedback').hide();
                $('#results-feedback, #error-feedback').hide().fadeIn();
            }
        });
        return false;
    };
});
/*----------- FEEDBACK_MODAL Form -----------*/
jQuery(function() {
    var form = $('form[name=FEEDBACK_MODAL]');
    form.submit(function() {
        $('#form-loading-feedback-modal').fadeIn();
        $('#error-feedback-modal, #success-feedback-modal, #beforesend-feedback-modal').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-feedback-modal').hide();
            $('#beforesend-feedback-modal, #results-feedback-modal').fadeIn();
        };
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function() {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0) return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                $('#form-loading-feedback-modal').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-feedback-modal, #success-feedback-modal').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
            },
            error: function(data) {
                $('#form-loading-feedback-modal').hide();
                $('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
            }
        });
        return false;
    };
    $('button.close').click(function() {
        $('#form-loading-feedback-modal, #results-feedback-modal').hide();
        $('input, textarea', form).removeClass('error');
        $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
    });
});
/*----------- CALLBACK Form -----------*/
jQuery(function() {
    var form = $('form[name=CALLBACK]');
    form.submit(function() {
        $('#form-loading-callback').fadeIn();
        $('#error-callback, #success-callback, #beforesend-callback').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-callback').hide();
            $('#beforesend-callback, #results-callback').fadeIn();
        };
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function() {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0) return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                $('#form-loading-callback').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-callback, #success-callback').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-callback, #error-callback').hide().fadeIn();
            },
            error: function(data) {
                $('#form-loading-callback').hide();
                $('#results-callback, #error-callback').hide().fadeIn();
            }
        });
        return false;
    };
});
/*----------- CALLBACK_MODAL Form -----------*/
jQuery(function() {
    var form = $('form[name=CALLBACK_MODAL]');
    form.submit(function() {
        $('#form-loading-callback-modal').fadeIn();
        $('#error-callback-modal, #success-callback-modal, #beforesend-callback-modal').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-callback-modal').hide();
            $('#beforesend-callback-modal, #results-callback-modal').fadeIn();
        };
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function() {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0) return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                $('#form-loading-callback-modal').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-callback-modal, #success-callback-modal').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-callback-modal, #error-callback-modal').hide().fadeIn();
            },
            error: function(data) {
                $('#form-loading-callback-modal').hide();
                $('#results-callback-modal, #error-callback-modal').hide().fadeIn();
            }
        });
        return false;
    };
});
/*----------- CONTACTS Form -----------*/
jQuery(function() {
    var form = $('form[name=CONTACTS]');
    form.submit(function() {
        $('#form-loading-contacts').fadeIn();
        $('#error-contacts, #success-contacts, #beforesend-contacts').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-contacts').hide();
            $('#beforesend-contacts, #results-contacts').fadeIn();
        };
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function() {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0) return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                $('#form-loading-contacts').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-contacts, #success-contacts').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-contacts, #error-contacts').hide().fadeIn();
            },
            error: function(data) {
                $('#form-loading-contacts').hide();
                $('#results-contacts, #error-contacts').hide().fadeIn();
            }
        });
        return false;
    };
});