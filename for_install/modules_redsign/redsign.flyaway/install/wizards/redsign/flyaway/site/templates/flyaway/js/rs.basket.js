;(function ($, BX, document, window) {
    'use strict';
    
    if(!window.RS) {
        window.RS = {};
    }
    
    if(
        !!window.RS.Basket || 
        !$ ||
        !BX
    ) {
        return;
    }
    
    function Basket() {
        this.inBasketProducts = [];
    }
    
    Basket.prototype.inbasket = function(ids, isRewrite) {
      
        isRewrite = isRewrite || false;
        
        if(ids) {
            ids = $.isArray(ids) ? ids : [ids];
            
            if(isRewrite) {
                this.inBasketProducts = ids;
            } else {
                this.inBasketProducts = $.merge(this.inBasketProducts, ids);
            }
            
            $(document).trigger("change.rs_flyaway.inbasket");
        } 
        
        
        return this.inBasketProducts;
    };
    
    Basket.prototype.add = function($formObj) {
        var data = $formObj.serialize() + '&ajax_basket=Y';
        var url = $formObj.parents(".js-element");
        console.log(data);
        console.log(url);
        return $.ajax({
            type: "POST",
            dataType: 'text',
            data: data
        });
    };
    
    Basket.prototype.updateQuantity = function(productId, newQuantity, data) {
        var defaultData = {
              sessid: BX.bitrix_sessid(),
              site_id: BX.message('SITE_ID'),
              select_props: 'QUANTITY',
              offers_props: '',
              quantity_float: 'N',
              count_discount_4_all_quantity: 'Y',
              price_vat_show_value: 'Y',
              hideCoupon: 'Y',
              use_prepayment: 'N',
              action_var: 'action',
              props: {}
            },
            url = "/bitrix/components/bitrix/sale.basket.basket/ajax.php";
    
        data = $.extend({}, defaultData, data);
        data[data.action_var] = data[data.action_var] || 'recalculate';
        data['QUANTITY_' + productId] = newQuantity;
        
        return $.post(url, data, null, 'text')
            .then(function(data) {
                return BX ? BX.parseJSON(data) : JSON.parse(data);
            });
    };
    
    Basket.prototype.delete = function(ids, data) { 
        var defaultData = {
              sessid: BX.bitrix_sessid(),
              site_id: BX.message('SITE_ID'),
              select_props: 'DELETE',
              offers_props: '',
              quantity_float: 'N',
              count_discount_4_all_quantity: 'Y',
              price_vat_show_value: 'Y',
              hideCoupon: 'Y',
              use_prepayment: 'N',
              action_var: 'action',
              props: {}
            },
            url = "/bitrix/components/bitrix/sale.basket.basket/ajax.php";
            
        ids = $.isArray(ids) ? ids : [ids];
        data = $.extend({}, defaultData, data);
        data[data.action_var] = data[data.action_var] || 'recalculate';
        
        $.each(ids, function(i, id) {
            data['DELETE_' + id] = 'Y';
        });
        
        
        return $.post(url, data, null, 'text')
            .then(function(data) {
                return BX ? BX.parseJSON(data) : JSON.parse(data);
            });
    };
    
    window.Basket = new Basket;
    
}(jQuery, BX, document, window));