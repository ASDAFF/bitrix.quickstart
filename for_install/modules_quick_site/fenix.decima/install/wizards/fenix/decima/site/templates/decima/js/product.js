$(function(){
    function hideLoader(){
        $('.loader').remove();
    }
    function showLoader(){
        hideLoader();
        $('body').append('<div class="loader"><div></div></div>')
    }
        function in_array(what, where) {
            for(var i=0; i<where.length; i++)
            if(what == where[i])
                return true;
            return false;
        }

        function parseactive(array){
            var props=array.PROPS;
            var nameProp;
            for(var i in props){
                nameProp=i;
                var classes=[];
                for (var x = 0; x < props[i].length; x++) {
                    classes.push(nameProp+'_'+props[i][x]);
                }
                var k=0;
                $('#'+nameProp+' option').each(function(){
                        k++;
                        $('#'+nameProp).parent().find('.chosen-results li').show();
                        if(in_array($(this).attr('id'), classes)===false){
                            $(this).attr('disabled', 'disabled'); 
                            $('#'+nameProp).trigger("chosen:updated");
                        }
                        else {
                            $(this).removeAttr('disabled');
                            $('#'+nameProp).trigger("chosen:updated");
                        }
                })
            }
            (function () {
                    if (jQuery().chosen) {
                        var config = {
                            '.chosen-select': {},
                            '.chosen-select-deselect': {allow_single_deselect: true},
                            '.chosen-select-searchless': {disable_search_threshold: 10, width: '100%'},
                            '.chosen-select-searchless-deselect': {disable_search_threshold: 10, width: '100%', allow_single_deselect: true}
                        };
                        for (var selector in config) {
                            jQuery(selector).chosen(config[selector]);
                        }
                    }
            })(); 
        }
        if($('select.sku').length){
            var value=$('select.sku').first().val();
            var ar=value.replace(/[']/g, "\"");
            var array=jQuery.parseJSON(ar);
            parseactive(array);    
        }    
        $('select.sku').on('change', function(){
                var cls='';
                var value=$(this).val();
                var ar=value.replace(/[']/g, "\"");
                var array=jQuery.parseJSON(ar);
                $('select.sku').each(function(){
                        var value1=$(this).val();
                        if(value1){
                            var ar1=value1.replace(/[']/g, "\"");
                            var array1=jQuery.parseJSON(ar1);
                            cls+='.'+$(this).attr('id')+'-'+array1.value;
                        }
                })
                parseactive(array);
                $('.offer').addClass('hide');
                $(cls).removeClass('hide'); 
                $('#offerID').val($(cls+'.thumbnailSlider').attr('item'));
                $('.flex-active-slide').resize();
        })

        $(document).on('click', '#basket_form .ui-spinner-button', function(){
                $(this).parents('form').find('button[name="BasketRefresh"]').click();
        }) 
        $(document).on('click', '.close-w', function(){
               $('.inner, .overlay').remove();
               return false;
        }) 
        
        $(document).on('click', '.item .buybtn', function(){
                var url=ajaxactions+this.search;
                var urlsuc=sitedir+'include/ajax/success.php'+this.search;
                var props='';
                if($(this).attr('props'))props='&prop='+$(this).attr('props');
               
                showLoader();
                $.get(url+props, function(data){
                     $('.shopping-cart-widget').html(data);
                     $.get(urlsuc, function(data){
                   hideLoader();
                   if($('.inner'))$('.inner, .overlay').remove();
                   $('body').append(data);  
                });
                     $.cartrefresh();
                });
                
                return false;
        })
        
        $(document).on('click', '.adddelay, .adddetail', function(e){
             e.preventDefault(); 
                $form=$(this).parents('form');
                var method=$form.attr('method');
                var action;
                var props='';
                if($(this).attr('props'))props='&prop='+$(this).attr('props');
                if($(this).hasClass('adddelay'))action='ADD2DELAY'; else action='ADD2BASKET';
                var url=ajaxactions;
                 
                var data=$form.serialize();
                  if(action=='ADD2BASKET')showLoader();
                  $.ajax({
                        type: method,
                        url: url+'?action='+action+props,
                        data: data,
                        dataType: "html",
                        success: function(html)
                        {     if(action=='ADD2BASKET'){
                                var urlsuc=sitedir+'include/ajax/success.php?id='+$('#offerID').val();
                                $.get(urlsuc, function(data){
                                        hideLoader();
                                        $('body').append(data);  
                                }); 
                            }
                            $('.shopping-cart-widget').html(html);
                            $.cartrefresh();
                        }  
                })
               
             return false; 
        })

        $(document).on('submit', '.shop-summary-item form, .shop-form', function(e){
                e.preventDefault();
                $form=$(this);
                var method=$form.attr('method');
                var url=ajaxactions;
                var data=$form.serialize();
                $.ajax({
                        type: method,
                        url: url,
                        data: data,
                        dataType: "html",
                        success: function(html)
                        {   
                            $('.shopping-cart-widget').html(html);
                            $.cartrefresh();
                        }  
                })
        }); 
        $(document).on('submit', '.review-form, .review-blog', function(e){
                e.preventDefault();
                $form=$(this);
                var method=$form.attr('method');
                var url;
                if($form.hasClass('review-blog'))url=sitedir+'include/ajax/reviews_blog.php';
                else url=sitedir+'include/ajax/reviews.php';
                
                var data=$form.serialize();
                $.ajax({
                        type: method,
                        url: url,
                        data: data,
                        dataType: "html",
                        success: function(html)
                        {   
                            $('#reviews').html(html);
                            $.rating();
                        }  
                })
        });        
        
        $(document).on('click', '.props li', function(e){
        var basketItemId=$(this).attr('data-element');
        var postData = {}; 
        var property = $(this).attr('data-property');
        var property_values = {};
        var action_var = 'action';
        property_values[property] = $(this).attr('data-value-id');   
        postData = {
            'basketItemId': basketItemId,
            'sessid': BX.bitrix_sessid(),
            'site_id': BX.message('SITE_ID'),
            'props': property_values,
            'action_var': action_var,
            'select_props': BX('column_headers').value,
            'offers_props': BX('offers_props').value,
            'quantity_float': BX('quantity_float').value,
            'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
            'price_vat_show_value': BX('price_vat_show_value').value,
            'hide_coupon': BX('hide_coupon').value,
            'use_prepayment': BX('use_prepayment').value
        };

        postData[action_var] = 'select_item';
        var all_sku_props = BX.findChildren(BX(basketItemId), {tagName: 'ul', className: 'sku_prop_list'}, true);
        if (!!all_sku_props && all_sku_props.length > 0)
        {
            for (var i = 0; all_sku_props.length > i; i++)
            {
                if (all_sku_props[i].id == 'prop_' + property + '_' + basketItemId)
                {
                    continue;
                }
                else
                {
                    var sku_prop_value = BX.findChildren(BX(all_sku_props[i].id), {tagName: 'li', className: 'bx_active'}, true);
                    if (!!sku_prop_value && sku_prop_value.length > 0)
                    {
                        for (var m = 0; sku_prop_value.length > m; m++)
                        {
                            if (sku_prop_value[m].hasAttribute('data-value-id'))
                                property_values[sku_prop_value[m].getAttribute('data-property')] = sku_prop_value[m].getAttribute('data-value-id');
                        }
                    }
                }
            }
        }
        $.ajax({
            url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
            method: 'POST',
            data: postData,
            dataType: 'json',
            complete: function(result)
            {
                $('.refresh').click();
            }
        });
              
        }); 

});

$(document).ready(function() {
    $(document).on('click', '#show-ocb-form', function(){
        $('#ocb-form-wrap').show();
        $('#ocb-form').show();
        $('#ocb-form-result').hide();
        return !1;
    });
     $(document).on('click', '.ocb-form-header-close', function(){
        $('.ocb-error-msg').each(function(index) { $(this).hide(); });
        $('.ocb-result-icon-success').hide();
        $('.ocb-result-icon-fail').hide();
        $('#ocb-form-wrap').fadeOut();
        return !1;
    });
      $(document).on('click', 'button.oneclick.disabled, .modules-button.disabled input', function(){
     return false;
    });
   $(document).on('submit', '#ocb-form', function(){
        $('.ocb-error-msg').each(function(index) { $(this).hide(); });
        var fieldId, fieldVal, checked = !0, self = $(this);
        var emailReg = RegExp("^[0-9a-zA-Z\-_\.]+@[0-9a-zA-Z\-]+[\.]{1}[0-9a-zA-Z\-]+[\.]?[0-9a-zA-Z\-]+$");
        var phoneReg = RegExp("^[+0-9\-\(\) ]+$");
        $('input[name^="new_order"]').each(function() {
            fieldId = $(this).attr('id');
            fieldVal = $(this).val();
            if ($(this).prev().children('ins').length > 0) {
                if (fieldVal=='') {
                    $('#' + fieldId + '-error').show();
                    checked = !1;
                }
            }
            if (fieldId.indexOf('PHONE')!=-1 && fieldVal!='' && !phoneReg.test(fieldVal)) {
                $('#' + fieldId + '-format-error').show();
                checked = !1;
            }
            if (fieldId.indexOf('EMAIL')!=-1 && fieldVal!='' && !emailReg.test(fieldVal)) {
                $('#' + fieldId + '-error').show();
                checked = !1;
            }
        });
        if (!checked) return !1;
        $('.modules-button', $(this)).addClass('disabled');
        $('.ocb-form-loader').show();
        if (0 < $('#ocb_antispam_check').length && $('#ocb-antispam').length === 0) {
            $('#ocb-params').prepend("<input id='ocb-antispam' type='hidden' name='antispam' value=''>");
        }
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'json',
            error: function(obj, text, err) {
                $('.ocb-form-loader').hide();
                alert('Error connecting server or getting server response!');
            },
            success: function(data) {
                if(data.ok!='Y') {
                    $('.ocb-result-icon-fail').show();
                    $('.ocb-result-text').text(data.msg);
                } else {
                    $('.ocb-result-icon-success').show();
                    if ($('#cart_line').length > 0)
                        $('#cart_line').html(data.msg);
                }
                $('.ocb-form-loader').hide();
                $('.modules-button', self).removeClass('disabled');
                $('#ocb-form').hide();
                $('#ocb-form-result').show();
                window.setTimeout(function() { $('.ocb-form-header-close').click(); }, 3000);
            }
        });

        return !1;
    });
});


