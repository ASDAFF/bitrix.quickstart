/**
 * http://sheepla.ru
 * User: Evgeniy Khodakov
 * Date: 21.03.2013
 *
 * JavaScript file for checkout
 */
if (typeof(sheepla) !== 'undefined') {

    sheepla.checkout = {
        /* Vars */
        current_carrier : 'input[name=DELIVERY_ID]:checked',

        /* Show Sheepla widget for sale.order.ajax checkout */
        get_sale_order_ajax : function() {
            if (this.allow_sheepla_widget()) {
                //remove previews widget
                sheepla.query(".sheepla-profile").remove();
                var params = this.get_params();

                // prepare place for widget
		var holder = sheepla.query(this.current_carrier).parent().children('label').children('div:last').children('strong');
                if (typeof(holder) === 'undefined' || holder === null) {
                    holder = sheepla.query(this.current_carrier).parent();
                }

                holder.after(sheepla.query('<div></div>').addClass('sheepla-profile').attr('id','sheepla-worker-' + params.template_id));

                // show widget if all OK
                if(typeof(params.template_id) !== 'undefined'){
                    this.clear_cookies();
                    sheepla.get_map_widget(params.template_id, '#sheepla-worker-' + params.template_id, params);
                    sheepla.query(this.current_carrier).attr('onclick', 'if(sheepla.checkout.validate_form()){ ' + sheepla.query(this.current_carrier).attr("onclick")+'}else{ return false; }');
                    sheepla.query('input[name="submitbutton"]').attr('onclick', 'if(sheepla.checkout.validate_form()){ '+sheepla.query('input[name="submitbutton"]').attr("onclick")+'}else{ return false; }');
                } else {
                    holder.remove();
                }
            }
        },

        /* Show Sheepla widget for sale.order.full checkout */
        get_sale_order_full : function() {
            if (this.allow_sheepla_widget()) {
                sheepla.query('input[name="DELIVERY_ID"]').click(function() {
                    var patt=/sheepla/gi;

                    if(patt.test(sheepla.query(this).attr('id'))){
                        this.get_sale_order_full_template(this);
                        sheepla.query(this).parents('table').find('input[name="contButton"]').attr('onclick', 'return sheepla.checkout.validate_form();');
                    }else{
                        sheepla.query(this).parents('table').find('input[name="contButton"]').attr('onclick', '');
                    }
                });
                if(sheepla.query('input[id*="sheepla"]:checked').size()>0){
                    var _this = sheepla.query('input[id*="sheepla"]:checked');
                    this.get_sale_order_full_template(_this);
                    sheepla.query(_this).parents('table').find('input[name="contButton"]').attr('onclick', 'return sheepla.checkout.validate_form();');
                }
            }
        },

        get_sale_order_full_template : function(_this){
            sheepla.query(".sheepla-profile").remove();
            var tagName = sheepla.query(_this).parent().parent()[0].tagName;
            var params = this.get_params();

            if(tagName=="LI" || tagName=="li"){
                var partent_tr = sheepla.query(_this).closest('li');
                var partent_tr_clone = '<li class="sheepla-profile"><label id="sheepla-worker-' + params.template_id + '"></label></li>';
            }else{
                var partent_tr = sheepla.query(_this).closest('tr');
                var partent_tr_clone = '<tr class="sheepla-profile"><td></td><td></td><td id="sheepla-worker-' + params.template_id + '" colspan="2"></tr>';
            }

            partent_tr.after(partent_tr_clone);

            this.clear_cookies();
            sheepla.get_map_widget(params.template_id, '#sheepla-worker-' + params.template_id, params);
        },

        /* Check if we can show Sheepla widget */
        allow_sheepla_widget : function() {
            return (
                (typeof(sheepla.query('input[id^=ID_DELIVERY_sheepla_]:checked').val()) !== 'undefined')
                    &&(sheepla.query('.sheepla-profile').size() < 1)
                );
        },

        /* Collect params from checkout */
       get_params : function(){
            return {
                'user_name': this.get_user_name(),
                'email': this.get_email(),
                'phone': this.get_phone(),
                'zip_code': this.get_zip(),
                'city': this.get_city(),
                'template_id': this.get_template_id()
            }
        },

        get_user_name : function() {
            return sheepla.query('#ORDER_PROP_1').val();
        },

        get_email : function() {
            return sheepla.query('#ORDER_PROP_2').val();
        },

        get_phone : function() {
            return sheepla.query('#ORDER_PROP_3').val();
        },

        get_zip : function() {
            return sheepla.query('#ORDER_PROP_4').val();
        },

        get_city : function() {
            var city = sheepla.query('#ORDER_PROP_6_val').val();
            if (typeof(city) !== 'undefined') {
                return (city.indexOf(',') == -1) ? city : city.substr(0,city.indexOf(","));
            } else {
                return 'unknown';
            }
        },

        get_template_id : function() {
            var carr_id = sheepla.query(this.current_carrier).val();
            carr_id = carr_id.replace('sheepla:', '');
            return (typeof(sheepla.checkout.templates[carr_id]) != 'undefined') ? sheepla.checkout.templates[carr_id] : null;
        },

        /* Clear Sheepla cookies */
        clear_cookies : function() {
            sheepla.query.each(document.cookie.split(';'), function(i, val){        // going over cookies
                if (val.match(/sheepla/) != null){                                  // let's find needed cookie
                    var name = val.trim().split('=')[0];
                    if (name != 'sheepla-email'){
                        sheepla.share.set_cookie(name, '', new Date(0));
                    }
                }
            });
        },

        /* Clear Sheepla title */
        clear_title : function() {
            sheepla.query('div[id^=delivery_info_sheepla_]').each(function(){
                sheepla.query(this).parent().html(
                    sheepla.query(this).parent().html().replace(/Sheepla - /, '')
                );
            });

            sheepla.query('div[id^=delivery_info_sheepla_]').each(function(){
                sheepla.query(this).parent().children('p').css('padding-bottom','0px');
            });

            sheepla.query('div[id^=delivery_info_sheepla_]').each(function(){
                sheepla.query(this).remove();
            });
        },

        validate_form : function() {
            return sheepla.valid_special(false, true);
        },

        add_pop_to_addr : function() {
            pop_id = '';
            pop_addr = '';
            var _cookies = document.cookie.split(";");

            for (i = 0; i < _cookies.length; i++) {
                if (_cookies[i].match(/sheepla/) != null){                                  // let's find needed cookie
                    var name = _cookies[i].trim().split('=');
                    if (name[0].indexOf('-pup-box-address-content-') != -1) {
                        if (typeof(name[1]) !== 'undefined') {
                            // use sheepla.share.get_cookie instead name[1] because an error
                            pop_addr = sheepla.query('td', sheepla.query(sheepla.share.get_cookie(name[0]))).last().html().replace('<br>', ', ');
                        }
                    } else {
                        if (name[0].indexOf('-pup-') != -1) {
                            pop_id = name[1];
                        }
                    }
                }
            }
            if (typeof(sheepla.checkout.field_street) !== 'undefined') {
                sheepla.query(sheepla.checkout.field_street).val('Пункт выдачи: ' + pop_id + '; ' + pop_addr);
            }
        },

    };

    /* Bootstrap routines */
    sheepla.call_registry.readyx = function() {

        if (typeof(sheepla.checkout_type) === 'undefined') {
            // by default using sale.order.ajax
            sheepla.query('input[type=text]').live('blur',function(){
                sheepla.checkout.get_sale_order_ajax();
            });

            if(typeof sheepla.query('input[id^=ID_DELIVERY_sheepla_]:checked').val() !='undefined'){
                sheepla.checkout.get_sale_order_ajax();
            }

            setInterval('sheepla.checkout.get_sale_order_ajax()', 30);
        } else {
            // this for sale.order.full
            setInterval('sheepla.checkout.get_sale_order_full()', 30);
        }

        setInterval('sheepla.checkout.clear_title()', 35);
        sheepla.user.after.ui.unlock_screen = function() {
            sheepla.checkout.add_pop_to_addr();
        }
    };
}
