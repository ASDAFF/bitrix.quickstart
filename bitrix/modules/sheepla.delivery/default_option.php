<?
$sheepla_delivery_default_option = array(
	'sheepla_adminApiKey' => '',
	'sheepla_publicApiKey' => '',
	'sheepla_apiUrl' => 'https://api.sheepla.com/',
	'sheepla_jsUrl' => 'https://api.sheepla.com/Content/GetWidgetAPIJavaScript',
	'sheepla_cssUrl' => 'https://api.sheepla.com/Content/GetWidgetAPICss',
	'sheepla_syncAll' => '1',
	'sheepla_checkout' => 'personal/order/make/',
    'sheepla_ok' => '0',
    /** Order page jQuery selectors */
    'sheepla_jQDeliverySelector' => 'input[id^=ID_DELIVERY_sheepla_sheepla_{SHEEPLA_DB_ID}_{SHEEPLA_TEMPLATE}]',
    'sheepla_jQDeliveryCitySelector' => 'sheepla.query(\'#ORDER_PROP_6\').val()',
    'sheepla_jQDeliverySelectorShort' => 'input[id^=ID_DELIVERY_sheepla_]',
    'sheepla_jQLocationSelector' => 'sheepla.query(\'#ORDER_PROP_6\').val()',
    'sheepla_jQLabelSelector' => '.parent().children(\'label\')',
    /** Admin order page */
    'sheepla_adminOrderAddUrl' => 'sale_order_new.php',
    'sheepla_adminOrderEditUrl' => 'sale_order_edit.php',
    'sheepla_adminOrderViewUrl' => 'sale_order_detail.php',
    'sheepla_orderViewSheeplaSelector' => '#btn_allow_delivery',
    /** Admin order page jQuery selectors */
    'sheepla_adminOrderjQSelector' => '#DELIVERY_ID',    
    'sheepla_adminOrderjQSelectorShort' => '#DELIVERY_SELECT',
    'sheepla_adminOrderjQLocationSelector' => 'sheepla.query(\'#CITY_ORDER_PROP_6 option:selected\').val()',
    'sheepla_adminOrderjQLabelSelector' => '.parent()',
    
    
);
?>
