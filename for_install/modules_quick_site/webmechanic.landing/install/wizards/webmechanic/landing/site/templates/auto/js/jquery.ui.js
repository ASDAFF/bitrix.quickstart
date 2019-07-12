$.fn.onlyDigits = function( json ) {
    return this.each(function() {
        $(this).keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) || 
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                     return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
};

function Wm_calucate(firspay, price, months, percent) {

    if (firspay > price) {
        return 0;
    }

    var p = (price - firspay) * -1;

    var i = percent / 1200;

    var n = months;

    var pmt = i * p * Math.pow((1 + i), n) / (1 - Math.pow((1 + i), n));

    return pmt;
}

function Wm_payment(animate) {

    if(typeof animate == 'undefined') {
        animate = false;
    }

    var pmt = Wm_calucate(
        parseInt($('input[name=fpay]').val()), 
        parseInt($('input[name=price]').val()), 
        parseInt($('input[name=month]').val()), 
        wm.percent
    );


    /*var oldprice = parseInt(accounting.unformat($('#summa').text()));
    
    if(animate && oldprice > 0 && (Math.abs(oldprice - pmt)) > 100) {
        
        (function myLoop (i) {          
            setTimeout(function () {   

                if(oldprice < pmt) {
                    var current = oldprice + (pmt - oldprice) / i;
                }
                else {
                    var current = pmt + (oldprice - pmt) / (50 - i);
                }
                
                $('#summa').html(Wm_correct_price(current));

                if (--i) myLoop(i);   
            }, 10)
        })(40); 
    
    }*/

    summa.options.duration = animate ? 100 : 0;
    summa.options.animation = animate ? 'slide' : 'count';
    summa.options.format = '( ddd)';
    summa.update(pmt);

    $('input[name=monthpay]').val(pmt);
}

function Wm_firstpay(val) {

    var def,
        cprice = parseInt(val * wm.fpay / 100),
        def = parseInt(val * 0.1);

    fpay = $("#credit-fpay");

    fpay.noUiSlider({
        start: def,
        range: [0, cprice],
        handles: 1,
        step: 10,
        slide: function() {
            Wm_update_fpay();
        }
    }, true);
    
    var f = accounting.formatMoney(fpay.val(), moneyOptions);
    fpay.find('.noUi-handle').append('<input name="credpay" class="slider-input" maxlength="9" autocomplete="off">');
    fpay.find('.noUi-handle input:text').val(f);


    $('[name=credpay]').onlyDigits();

    $('[name=credpay]').on('mousedown', function(e){
        e.stopPropagation();
    });

    $('[name=credpay]').on('blur', function(){
        var f = accounting.unformat($(this).val());
        fpay.val(f);
        Wm_update_fpay();
    });

    $('[name=credpay]').on('focusin', function(){
        var val = $(this).val();
        $(this).val(accounting.unformat(val)); 
    })
    .on('focusout', function(){
        var val = $(this).val();
        $(this).val(accounting.formatMoney(val, moneyOptions));
    });

    $('input[name=fpay]').val(fpay.val());
}

function Wm_update_month() {
    monthSlider.find('.noUi-handle input').val(parseInt(monthSlider.val()));
    $('input[name=month]').val(monthSlider.val());
    Wm_payment();
}

function Wm_update_fpay() {

    var v = accounting.unformat(fpay.val());
    var t = accounting.formatMoney(fpay.val(), moneyOptions);

    fpay.find('.noUi-handle input:text').val(t);
    $('input[name=fpay]').val(v);
    Wm_payment();
}


function Wm_set_product(item) {

    item.closest('.carousel').find('.product').removeClass('active-item');
    item.addClass('active-item');

    $('#carimage').fadeOut(200, function(){
        $('#carimage').attr('src', item.data('img')).fadeIn(200);
    });

    $('#modeltext').html(item.data('descr'));     // 
    $('[name=price]').val(item.data('price'));    // 

    $('[name=model_id]').val(item.data('id'));    //
    $('[name=mark]').val(item.data('vendor'));    // 
    $('[name=model]').val(item.data('model'));    // 
    $('#carname').html(item.data('model'));

    $('#modhref span').html(item.data('modif'));  // 
    $('input[name=mod]').val(item.data('modif'));


    Wm_firstpay(item.data('price'));
    Wm_payment(true);

    $('#detail-modal .modal-body').html(item.find('.detail').html());
    
}

function Wm_correct_price(price) {

    var options = {
        symbol : "руб.",
        decimal : ".",
        thousand: " ",
        precision : 0,
        format: "%v %s"
    };

    return accounting.formatMoney(price, options);
}

function Wm_switch_button(flag) {
    if (flag) {
        $('#btn-send').removeClass('disabled').prop('disabled', false);
    } else {
        $('#btn-send').addClass('disabled').prop('disabled', true);
    }
}

var monthSlider, 
    fpay,
    summa,
    moneyOptions = {
        symbol : "",
        decimal : ".",
        thousand: " ",
        precision : 0,
        format: "%v"
    };

window.odometerOptions = {
  duration: 500,
  theme: 'default'
};

$(function() {

    var carousel = $('#carousel-product');

    carousel.carousel({
        interval: false,
    });

    carousel.find('.product').on('click', function(){
        Wm_set_product($(this));
    });

    summa = new Odometer({
      el: $('#summa')[0],
      value: 0,
      format: '',
      theme: 'default'
    });

    Wm_set_product(carousel.find('.product:first'));
    
    monthSlider = $("#credit-month");

    monthSlider.noUiSlider({
        start: wm.start_month,
        range: [wm.min_month, wm.max_month],
        handles: 1,
        step: 1,
        slide: function() {
            Wm_update_month();
        },
        set: function(){
            Wm_update_month();
        },
    });

    monthSlider.find('.noUi-handle').append('<input name="credmonth" class="slider-input" maxlength="2" autocomplete="off">');
    monthSlider.find('.noUi-handle input').val(parseInt(monthSlider.val()));

    $('[name=credmonth]').on('mousedown', function(e){
        e.stopPropagation();
    });

    $('[name=credmonth]').on('blur', function(){
        var m = $(this).val();
        monthSlider.val(m);
        Wm_update_month();
    });

    $('input[name=month]').val(monthSlider.val());
    


    $('.terms input').on('change', function() {
        Wm_switch_button($(this).is(':checked'));
    });

    Wm_payment();
    Wm_switch_button(false);

    $('[name=credmonth]').onlyDigits();

});


