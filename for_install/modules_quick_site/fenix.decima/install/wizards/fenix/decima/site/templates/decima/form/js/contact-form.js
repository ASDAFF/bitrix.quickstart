/**
 * Contact Form
 */
jQuery(document).ready(function ($) {
	"use strict";
    var debug = false; //show system errors

    $('.validateIt').submit(function () {
        var $f = $(this);
        var showErrors = $f.attr('data-show-errors') == 'true';
        var hideForm = $f.attr('data-hide-form') == 'true';

        var emailSubject = $f.attr('data-email-subject');

        var $submit = $f.find('[type="submit"]');

        //prevent double click
        if ($submit.hasClass('disabled')) {
            return false;
        }

        $('[name="field[]"]', $f).each(function (key, e) {
            var $e = $(e);

	        /* var p = $e.attr('placeholder'); */
	        /* get label text instead placeholder */

	          var p = $e.parent().find("label").text();

            if (p) {
                var t = $e.attr('required') ? '[required]' : '[optional]';
                var type = $e.attr('type') ? $e.attr('type') : 'unknown';
                t = t + '[' + type + ']';

                var n = $e.attr('name').replace('[]', '[' + p + ']');

                n = n + t;
                $e.attr('data-previous-name', $e.attr('name'));
                $e.attr('name', n);
            }
        });

        $submit.addClass('disabled');

        $f.append('<input class="temp" type="hidden" name="email_subject" value="' + emailSubject + '">');

        /*$.ajax({
            url: $f.attr('action'),
            method: 'post',
            data: $f.serialize(),
            dataType: 'json',
            success: function (data) {
                $('span.error', $f).remove();
                $('.error', $f).removeClass('error');
                $('.form-group', $f).removeClass('has-error');

                if (data.errors) {
                    $.each(data.errors, function (i, k) {
                        var input = $('[name^="' + i + '"]', $f).addClass('error');
                        if (showErrors) {
                            input.after('<span class="error help-block">' + k + '</span>');
                        }

                        if (input.parent('.form-group')) {
                            input.parent('.form-group').addClass('has-error');
                        }
                    });
                } else {
                    var item = data.success ? '.successMessage' : '.errorMessage';
                    if (hideForm) {
                        $f.fadeOut(function () {
                            $f.parent().find(item).show();
                        });
                    } else {
                        $f.parent().find(item).fadeIn();
                        $f[0].reset();
                    }
                }

                $submit.removeClass('disabled');
                cleanupForm($f);
            },
            error: function (data) {
                if (debug) {
                    alert(data.responseText);
                }
                $submit.removeClass('disabled');
                cleanupForm($f);
            }
        });*/

     //   return false;
    });

    function cleanupForm($f) {
        $f.find('.temp').remove();

        $f.find('[data-previous-name]').each(function () {
            var $e = jQuery(this);
            $e.attr('name', $e.attr('data-previous-name'));
            $e.removeAttr('data-previous-name');
        });
    }
});