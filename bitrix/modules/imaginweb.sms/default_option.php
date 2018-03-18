<?php
/**
 * Default module settings.
 */

$imaginweb_sms_default_option = array(
	"host" 			=> "http://gate.mobilmoney.ru/",
	"host2" 		=> "http://turbosms.in.ua/api/wsdl.html",
	"property_phone"	=> "PHONE",
	"new_order"		=> GetMessage("IMAGINWEB_SMS_SPASIBO_VAS_ZAKAZ")." #ORDER_NUMBER# ".GetMessage("IMAGINWEB_SMS_PRINAT_I_PEREDAN_V_S"),
	"allow_anonymous" => "Y",
	"show_auth_links" => "Y",
	"subscribe_max_lenght" => "210",
    "subscribe_field_phone" => "PERSONAL_PHONE",
	"posting_interval" => "20",
	"default_from" => "",
	"subscribe_auto_method" => "agent",
	"subscribe_max_sms_per_hit" => "5"
);
