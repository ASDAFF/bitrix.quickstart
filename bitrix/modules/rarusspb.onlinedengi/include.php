<?
/**
 *
 * ������ ���������� ������� OnlineDengi ��� CMS 1� �������.
 * @copyright ������ OnlineDengi http://www.onlinedengi.ru/ (��� "�����������"), 2010
 *
 *
 *
 * ������������ �� ����� ���������� ������, ���������� ��� ������� ����� 
 * ��� �������������� ������, � ����� �����, � ��� ����� � ���� ���������
 * ������, �����-���� ��������, � ��� ����� ������� ������ � ������/������.
 *
 * ����������� ������� ����� ���������� �� ��������� ������.
 *
 * ����������� ����� ������������� ������, �������������� ������������     
 * ���������������� ���������� ���������.
 *
 */
 
IncludeModuleLangFile(__FILE__);

//
// ���������
//
if(!defined('ONLINEDENGI_PAYMENT_MODULE_ID')){
	define('ONLINEDENGI_PAYMENT_MODULE_ID', 'rarusspb.onlinedengi');

}
if(!defined('ONLINEDENGI_PAYMENT_REQUEST_URL')) {
        // ����� ������� �������, ���������� ������ �� ������
	//define('ONLINEDENGI_PAYMENT_REQUEST_URL', '/test_post.php');
	define('ONLINEDENGI_PAYMENT_REQUEST_URL', 'http://www.onlinedengi.ru/wmpaycheck.php');
}

if(!defined('ONLINEDENGI_PAYMENT_REQUEST_TYPE')) {
	// ��� ������� ��� ������
	define('ONLINEDENGI_PAYMENT_REQUEST_TYPE', 'post');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_CHARSET')) {
	// ��������� �������
	define('ONLINEDENGI_PAYMENT_RESPONSE_CHARSET', 'UTF-8');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH')) {
	// ���� � ������� ������ ���������� � ���������� �������
	define('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH', BX_PERSONAL_ROOT.'/tools/onlinedengi_result_rec.php');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT')) {
	// ������������ ������� ���� � ������� ������ ���������� � ���������� �������
	define('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH.'?ps=#PAY_SYSTEM_ID#&pt=#PERSON_TYPE_ID#');
}

if(!defined('ONLINEDENGI_PAYMENT_SUCCESS_SCRIPT')) {
	// ����� �������, ��������� ��������� ���������� �� �������� ������
	define('ONLINEDENGI_PAYMENT_SUCCESS_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].BX_PERSONAL_ROOT.'/tools/onlinedengi_succes.php');
}

if(!defined('ONLINEDENGI_PAYMENT_FAIL_SCRIPT')) {
	// ����� �������, ��������� ��������� ���������� � ��������� ������
	define('ONLINEDENGI_PAYMENT_FAIL_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].BX_PERSONAL_ROOT.'/tools/onlinedengi_fail.php');
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_URL')) {
        // ����� ������� �������, ��������� ����� �����
	define('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_URL', 'http://www.onlinedengi.ru/dev/xmltalk.php');
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_TIMEOUT')) {
	// ����� �������� ������ ��� ������� ������ �����, 20 ���
	define('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_TIMEOUT', 20);
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_TIME')) {
	// ����� ����� ���� ������ �����, 30 �����
	// !!! ��� ������ ����� ������ ����, ��� ������ ����������� ����, 
	// ��� �� ����� ������ ���� ����� ����������
        define('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_TIME', 1800);
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_PATH')) {
	// ���� ��� �������� ����
        define('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_PATH', 'onlinedengi_cache');
}

if(!defined('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE')) {
	// ���� ��� �������� ����
        define('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE', 'with_image');
}

//
// ����������� �������� �������
//
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/classes/general/onlinedengi_payment.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/classes/general/default_payment_handlers.php');

