/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

/*----------- ORDER Form -----------*/
jQuery(function () {

    var form = $('form[name=ORDER]');
    form.submit(function () {
        $('#form-loading-order').fadeIn();
        $('#error-order, #success-order, #beforesend-order').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-order').hide();
            $('#beforesend-order, #results-order').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    })

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });

        if (errors.length === 0)
            return true;
        else return false;
    }

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function (data) {
                $('#form-loading-order').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-order, #success-order').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                    $('[name*=QUANTITY]', form).val(1);
                } else $('#results-order, #error-order').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-order').hide();
                $('#results-order, #error-order').hide().fadeIn();
            }
        });
        return false;
    }
});

/*----------- COMMENTS Form -----------*/
jQuery(function () {
    var form = $('form[name=COMMENTS]');
    form.submit(function () {
        $('#form-loading-comments').fadeIn();
        $('#error-comments, #success-comments, #beforesend-comments').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-comments').hide();
            $('#beforesend-comments, #results-comments').fadeIn();
        }
        ;

        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
            if (!$(this).val()) {
                errors.push(1);
                $(this).addClass('error');
            } else $(this).removeClass('error');
        });
        if (errors.length === 0)
            return true;
        else return false;
    };

    function submission() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function (data) {
                $('#form-loading-comments').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-comments, #success-comments').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-comments, #error-comments').hide().fadeIn();
            },

            error: function (data) {
                $('#form-loading-comments').hide();
                $('#results-comments, #error-comments').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- FEEDBACK Form -----------*/
jQuery(function () {
    var form = $('form[name=FEEDBACK]');
    form.submit(function () {
        $('#form-loading-feedback').fadeIn();
        $('#error-feedback, #success-feedback, #beforesend-feedback').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-feedback').hide();
            $('#beforesend-feedback, #results-feedback').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-feedback').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-feedback, #success-feedback').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-feedback, #error-feedback').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-feedback').hide();
                $('#results-feedback, #error-feedback').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- FEEDBACK_MODAL Form -----------*/
jQuery(function () {
    var form = $('form[name=FEEDBACK_MODAL]');
    form.submit(function () {
        $('#form-loading-feedback-modal').fadeIn();
        $('#error-feedback-modal, #success-feedback-modal, #beforesend-feedback-modal').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-feedback-modal').hide();
            $('#beforesend-feedback-modal, #results-feedback-modal').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-feedback-modal').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-feedback-modal, #success-feedback-modal').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-feedback-modal').hide();
                $('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
            }
        });
        return false;
    };

    $('button.close').click(function () {
        $('#form-loading-feedback-modal, #results-feedback-modal').hide();
        $('input, textarea', form).removeClass('error');
        $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
    });
});

/*----------- CALLBACK Form -----------*/
jQuery(function () {
    var form = $('form[name=CALLBACK]');
    form.submit(function () {
        $('#form-loading-callback').fadeIn();
        $('#error-callback, #success-callback, #beforesend-callback').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-callback').hide();
            $('#beforesend-callback, #results-callback').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-callback').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-callback, #success-callback').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-callback, #error-callback').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-callback').hide();
                $('#results-callback, #error-callback').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- CONTACTS_MODAL Form -----------*/
jQuery(function () {
    var form = $('form[name=CONTACTS_MODAL]');
    form.submit(function () {
        $('#form-loading-contacts-modal').fadeIn();
        $('#error-contacts-modal, #success-contacts-modal, #beforesend-contacts-modal').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-contacts-modal').hide();
            $('#beforesend-contacts-modal, #results-contacts-modal').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-contacts-modal').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-contacts-modal, #success-contacts-modal').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-contacts-modal, #error-contacts-modal').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-contacts-modal').hide();
                $('#results-contacts-modal, #error-contacts-modal').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- CALLBACK_MODAL Form -----------*/
jQuery(function () {
    var form = $('form[name=CALLBACK_MODAL]');
    form.submit(function () {
        $('#form-loading-callback-modal').fadeIn();
        $('#error-callback-modal, #success-callback-modal, #beforesend-callback-modal').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-callback-modal').hide();
            $('#beforesend-callback-modal, #results-callback-modal').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-callback-modal').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-callback-modal, #success-callback-modal').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-callback-modal, #error-callback-modal').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-callback-modal').hide();
                $('#results-callback-modal, #error-callback-modal').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- CONTACTS Form -----------*/
jQuery(function () {
    var form = $('form[name=CONTACTS]');
    form.submit(function () {
        $('#form-loading-contacts').fadeIn();
        $('#error-contacts, #success-contacts, #beforesend-contacts').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-contacts').hide();
            $('#beforesend-contacts, #results-contacts').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-contacts').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-contacts, #success-contacts').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-contacts, #error-contacts').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-contacts').hide();
                $('#results-contacts, #error-contacts').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- ORDER_SCHEME Form -----------*/
jQuery(function () {
    var form = $('form[name=ORDER_SCHEME]');
    form.submit(function () {
        $('#form-loading-order-scheme').fadeIn();
        $('#error-order-scheme, #success-order-scheme, #beforesend-order-scheme').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-order-scheme').hide();
            $('#beforesend-order-scheme, #results-order-scheme').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-order-scheme').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-order-scheme, #success-order-scheme').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-order-scheme, #error-order-scheme').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-callback-modal').hide();
                $('#results-order-scheme, #error-order-scheme').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- NETWORK Form -----------*/
jQuery(function () {
    var form = $('form[name=NETWORK]');
    form.submit(function () {
        $('#form-loading-network').fadeIn();
        $('#error-network, #success-network, #beforesend-network').hide();
        if (validate()) {
            submission();
        } else {
            $('#form-loading-network').hide();
            $('#beforesend-network, #results-network').fadeIn();
        }
        ;
        $('input, select, textarea, button', form).blur();
        return false;
    });

    function validate() {
        var errors = [];
        $('.req', form).each(function () {
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
            success: function (data) {
                $('#form-loading-network').hide();
                $('input, textarea', form).removeClass('error');
                if (data.MESSAGE.ERROR < 1) {
                    $('#results-network, #success-network').fadeIn();
                    $('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
                } else $('#results-network, #error-network').hide().fadeIn();
            },
            error: function (data) {
                $('#form-loading-network').hide();
                $('#results-network, #error-network').hide().fadeIn();
            }
        });
        return false;
    };
});

/*----------- openGeneralModal -----------*/
jQuery(function () {
    function openGeneralModal(html, title) {
        if (!title) {
            title =  ' ';
        }
        $("#generalModal").html(html);
        $("#generalModal").attr("title", title);
        $("#generalModal").dialog('open');
        $('.ui-widget-overlay').on("click", function() {
            //Close the dialog
            $("#generalModal").dialog("close");
        });
        $("#generalModal").find('a').blur();
    }
});

jQuery(function () {
    function cartPopup() {
        var html = '' +
            'Товар добавлен в корзину<br />' +
            '<a class="fr"  onclick="ym(4916878, \'reachGoal\', \'click_cart\'); return true;" style="outline: none;" href="/personal/cart/"><i style="position: relative; margin-bottom: -12px;" class="ico-main ico-main-basket-on"></i>Перейти в корзину</a>';
        return html;
    }
});

jQuery(function () {
    function comparePopup() {
        var html = '' +
            'Товар добавлен к списку сравнения<br />' +
            '<a class="fr" style="outline: none;" href="/catalog/compare.php"><i style="position: relative; margin-bottom: -12px;" class="ico-main ico-main-compare-on"></i>К списку сравнения</a>';
        return html;
    }
});

/*----------- Add2Cart -----------*/
jQuery(function () {
    function Add2Cart(ID, url) {
        ID = checkID(ID);
        if (ID != false) {
            var html = cartPopup();
            openGeneralModal(html, 'Товар добавлен в корзину');
            $.ajax({
                data: {action: 'ADD2BASKET', id: ID},
                url: url,
                type: "POST",
                success: function(data, textStatus, j) {
                    updateBasketWidget();
                }
            });
        }
    }
});

/*----------- Add2Compare/DeleteFromCompare -----------*/
jQuery(function () {
    function Add2Compare(ID) {
        ID = checkID(ID);
        if (ID != false) {
            var html = comparePopup();
            openGeneralModal(html, 'Товар добавлен к списку сравнения');
            $.ajax({
                data: {action: 'ADD_TO_COMPARE_LIST', id: ID},
                url: "/catalog/",
                type: "POST",
                success: function(data, textStatus, j) {
                    //updateCompareWidget();
                }
            });
        }
    }

function DeleteFromCompare(ID) {
    ID = checkID(ID);
    if (ID != false) {
        $.ajax({
            data: {action: 'DELETE_FROM_COMPARE', id: ID},
            url: "/catalog/",
            type: "POST",
            success: function(data, textStatus, j) {
                //updateCompareWidget();
            }
        });
    }
}

});

$(document).ready(function() {

    $('.buy_').bind('click', function(){

        if(!$(this).hasClass('m-in_basket')){
            id = $(this).data('id');
            $.ajax({
                url: '/cart/basket_line.php',
                data: 'action=ADD2BASKET&id=' + id,
                success: function(data){
                    $('.b-minicart').html(data);
                }
            });

            $(this).addClass('m-in_basket')
                .html('<span class="b-catalog-list_item__cart">добавлен<br>в корзину</span>');
        }  else {

            location.href="/personal/cart/";

        }
    });

    $('.add2compare_').live('click', function(){

        if(!$(this).hasClass('m-compare__added')){

            $(this).addClass('m-compare__added')
                .find('span').text('Добавлен к сравнению');

            if($('.b-compare-added').hasClass('hidden_')){
                $('.b-compare-added').addClass('clearfix').removeClass('hidden_');
            }

            var id = $(this).data('id');
            $.ajax({
                url: '/api/?action=add2compare_&id=' + id,
                success: function(data){
                    $('.b-compare-added').html(data);
                }
            });
        }
        return false;
    });

    $('.compare_from_list_').live('click', function(){

        if($('.b-compare-added').hasClass('hidden_')){
            $('.b-compare-added').addClass('clearfix').removeClass('hidden_');
        }

        var id = $(this).data('id');
        $.ajax({
            url: '/api/?action=add2compare_&id=' + id,
            success: function(data){
                $('.b-compare-added').html(data);
            }
        });

        return false;
    });

    $('.compare_').live('click', function(){
        location.href = "/catalog/compare/";

    });


});