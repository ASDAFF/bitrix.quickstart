/**
 * http://sheepla.ru
 * User: Evgeniy Khodakov
 * Date: 21.03.2013
 *
 * JavaScript file for bitrix admin interface
 */
if (typeof(sheepla) !== 'undefined') {

    sheepla.admin = {
        /* Vars */
        pop_id : null,

        set_widget : function(){
            if(typeof sheepla.query('#DELIVERY_ID_auto :selected').val() != 'undefined'){
                var curDelivery = sheepla.query('#DELIVERY_ID_auto :selected').val();
            }else{
                var curDelivery = sheepla.query('#DELIVERY_ID :selected').val();
            }
            if(typeof curDelivery == 'undefined') {
                console.log('curDelivery is undefined');
                return;
            }

            if (curDelivery.indexOf('sheepla') != -1) {
                sheepla.query('input[type=submit]').hide();
                sheepla.query.ajax({
                    type: "POST",
                    url: "/bitrix/tools/sheepla.delivery/ajax.php",
                    data: ({action: 'gettemplate', delivery: curDelivery}),
                    dataType: "text",
                    success: function(data) {

                        var template_id = parseInt(data);
                        if (template_id > 0) {
                            sheepla.admin.create_special_widget(template_id);
                            sheepla.admin.sync_dynamic_pricing(template_id);
                            sheepla.query('input[type=submit]').show();
                        }else{
                            sheepla.query('#sheepla-widget-control').html('');
                            sheepla.query('input[type=submit]').show();
                        }
                    }
                });
            } // end if (curDelivery.indexOf('sheepla') != -1)
        },

        create_special_widget : function(template_id){
            sheepla.query(".sheepla-profile").remove();
            var partent_tr = sheepla.query('#DELIVERY_ID').closest('tr');
            var partent_tr_clone = sheepla.query(partent_tr).clone();

            sheepla.query(partent_tr_clone).children('td').html('');
            sheepla.query(partent_tr_clone).addClass('sheepla-profile');
            sheepla.query(partent_tr_clone).children('td:first-child').next('td').attr('id','sheepla-worker-' + template_id);

            partent_tr.after(partent_tr_clone);

            this.clear_cookies();
            sheepla.get_map_widget(template_id, '#sheepla-worker-' + template_id, this.get_params());
        },


        sync_dynamic_pricing : function(template_id){
            //getting the delivery settings: city and zip
            var zip = this.get_zip_code();
            var city = this.get_city();

            // collect info about products
            var products = [];
            sheepla.query('tr[id*=BASKET_TABLE_ROW_]').each(function() {
                var re = /BASKET_TABLE_ROW_([0-9]+)/;
                var id = re.exec(sheepla.query(this).attr('id'))[1];

                products.push({
                    id: id,
                    name: sheepla.query('#PRODUCT\\[' + id + '\\]\\[NAME\\]').val(),
                    sku: null,
                    qty: sheepla.query('#PRODUCT\\[' + id + '\\]\\[QUANTITY\\]').val(),
                    weight: sheepla.query('#PRODUCT\\[' + id + '\\]\\[WEIGHT\\]').val(),
                    priceGross: sheepla.query('#PRODUCT\\[' + id + '\\]\\[PRICE\\]').val()
                });
            });
            sheepla.query('input[type=submit]').hide();
            sheepla.query.ajax({
                type: "POST",
                url: "/bitrix/tools/sheepla.delivery/ajax.php",
                data: ({action: 'getdynamicpricing',
                    template_id: template_id,
                    zip: zip,
                    city: city,
                    products: products
                }),
                dataType: "text",
                success: function(data) {
                    sheepla.query('input[type=submit]').show();
                    var price = parseInt(data);
                    if (price >= 0){
                        sheepla.query('#DELIVERY_ID_PRICE').val(price);
                        fChangeDeliveryPrice();
                    }
                    if(price == -1){
                        sheepla.query('#sheepla-widget-control').html('');
                    }
                }
            });

        },

        /* Collect params from order edit page */
        get_params : function(){
            return {
                'user_name': this.get_user_name(),
                'email': this.get_email(),
                'phone': this.get_phone(),
                'zip_code': this.get_zip_code(),
                'city': this.get_city(),
                'defaultPopCarrierCode': this.get_pickup_point()
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

        get_zip_code : function() {
            return sheepla.query('#ORDER_PROP_4').val();
        },

        get_city : function() {
            return sheepla.query('#CITY_ORDER_PROP_6 option:selected').text();
        },

        get_order_id : function() {
            var order_id = sheepla.query('#adm-title').html().match(/[0-9]+/);
            return (typeof(order_id) != 'undefined') ? order_id : null;
        },

	get_pickup_point: function() {
            if (sheepla.admin.pop_id !== null) {
                return sheepla.admin.pop_id;
            }

            var order_id = this.get_order_id();

            sheepla.query.ajax({
                type: "POST",
                url: "/bitrix/modules/sheepla.delivery/ajax.php",
                data: ({action: 'getpickuppoint', order_id: parseInt(order_id) }),
                dataType: "text",
                success: function(data) {
                    if (typeof(data) !== 'undefined') {
                        if (data != -1) {
                            //alert(data);
                            sheepla.admin.pop_id = data;
                        }
                    }
                }
            });

            return sheepla.admin.pop_id;
	},

        add_area : function() {
            sheepla.query('#btn_allow_delivery').before('<tr"><td class="adm-detail-content-cell-l" width="40%"></td><td id="sheepla-status" class="btn_order adm-detail-content-cell-r" valign="middle"></tr>');
        },

        is_sheepla_delivery : function() {
            return (sheepla.query('#allow_delivery_name').html().search('sheepla') > 0);
        },

        // clear Sheepla cookies
        clear_cookies : function() {
            sheepla.query.each(document.cookie.split(';'), function(i, val){        // going over cookies
                if (val.match(/sheepla/) != null){                                  // let's find needed cookie
                    var name = val.trim().split('=')[0];
                    if (name != 'sheepla-email'){
                        sheepla.share.set_cookie(name, '', new Date(0));
                    }
                }
            });
        }

    };

    /* Bootstrap routines */
    sheepla.call_registry.readyx = function() {
        if(window.location.href.search('sale_order_detail.php') > 0){
            // show widget on order details page
            if (sheepla.admin.is_sheepla_delivery()) {
                var order_id = sheepla.admin.get_order_id();
                if (order_id !== null) {
                    sheepla.admin.add_area();
                    setTimeout('sheepla.get_shipment_status_standard('+order_id+',"#sheepla-status",1);');
                }
            }
        }

	if(window.location.href.search('sale_order_new.php') > 0 || window.location.href.search('sale_order_edit.php') > 0) {
            sheepla.admin.get_pickup_point();
	}

        // show widget on order edit page
        sheepla.query('#DELIVERY_ID').live('change',function(){
            sheepla.admin.set_widget();
        });
        var oldHTML = sheepla.query('#BASKET_TABLE').html();
        setInterval(function() {
            if((typeof oldHTML !='undefined')&&(oldHTML !=null)){
                if( Math.abs(oldHTML.length - sheepla.query('#BASKET_TABLE').html().length)>100 ) {
                    oldHTML = sheepla.query('#BASKET_TABLE').html();
                    sheepla.admin.set_widget();
                }
            }
        }, 100);

    };
}