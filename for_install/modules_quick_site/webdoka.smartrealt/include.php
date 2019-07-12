<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

global $DB, $APPLICATION;
define('ADMIN_MODULE_NAME', 'webdoka.smartrealt');
define('SMARTREALT_CATALOG_LIST_URL_DEF', '#TYPE_CODE#/#TRANSACTION_TYPE#/');
define('SMARTREALT_CATALOG_DETAIL_URL_DEF', '#TYPE_CODE#/#TRANSACTION_TYPE#/#NUMBER#/');
define('SMARTREALT_WEB_SERVICE_URL_DEF', 'http://soap.smartrealt.com/v1/');
$module_id = 'webdoka.smartrealt';

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/nusoap/nusoap.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/smartrealt_common.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/smartrealt_filter.class.php');  
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/smartrealt_url.class.php');  
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/smartrealt_options.class.php');  
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/general/smartrealt_webservice.class.php');  

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_data_object.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_webservice_data_object.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_rubric.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_rubric_group.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_catalog_element.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_catalog_element_type.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/classes/' . strtolower($DB->type) . '/smartrealt_catalog_element_photo.class.php');    


?>