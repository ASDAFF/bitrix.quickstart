<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 *
 *
 * Пользователь не может копировать модуль, передавать его третьим лицам 
 * или распространять модуль, в любой форме, в том числе в виде исходного
 * текста, каким-либо способом, в том числе сдавать модуль в аренду/прокат.
 *
 * Запрещается удалять любую информацию об авторских правах.
 *
 * Запрещается любое использование модуля, противоречащее действующему     
 * законодательству Российской Федерации.
 *
 */
 
IncludeModuleLangFile(__FILE__);

//
// Константы
//
if(!defined('ONLINEDENGI_PAYMENT_MODULE_ID')){
	define('ONLINEDENGI_PAYMENT_MODULE_ID', 'rarusspb.onlinedengi');

}
if(!defined('ONLINEDENGI_PAYMENT_REQUEST_URL')) {
        // адрес скрипта сервиса, примающего запрос на оплату
	//define('ONLINEDENGI_PAYMENT_REQUEST_URL', '/test_post.php');
	define('ONLINEDENGI_PAYMENT_REQUEST_URL', 'http://www.onlinedengi.ru/wmpaycheck.php');
}

if(!defined('ONLINEDENGI_PAYMENT_REQUEST_TYPE')) {
	// тип запроса при оплате
	define('ONLINEDENGI_PAYMENT_REQUEST_TYPE', 'post');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_CHARSET')) {
	// кодировка запроса
	define('ONLINEDENGI_PAYMENT_RESPONSE_CHARSET', 'UTF-8');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH')) {
	// путь к скрипту приема оповещения о зачислении средств
	define('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH', BX_PERSONAL_ROOT.'/tools/onlinedengi_result_rec.php');
}

if(!defined('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT')) {
	// передаваемый сервису путь к скрипту приема оповещения о зачислении средств
	define('ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH.'?ps=#PAY_SYSTEM_ID#&pt=#PERSON_TYPE_ID#');
}

if(!defined('ONLINEDENGI_PAYMENT_SUCCESS_SCRIPT')) {
	// адрес скрипта, отдающего сообщение покупателю об успешной оплате
	define('ONLINEDENGI_PAYMENT_SUCCESS_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].BX_PERSONAL_ROOT.'/tools/onlinedengi_succes.php');
}

if(!defined('ONLINEDENGI_PAYMENT_FAIL_SCRIPT')) {
	// адрес скрипта, отдающего сообщение покупателю о неудачной оплате
	define('ONLINEDENGI_PAYMENT_FAIL_SCRIPT', 'http://'.$_SERVER['SERVER_NAME'].BX_PERSONAL_ROOT.'/tools/onlinedengi_fail.php');
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_URL')) {
        // адрес скрипта сервиса, отдающего курсы валют
	define('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_URL', 'http://www.onlinedengi.ru/dev/xmltalk.php');
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_TIMEOUT')) {
	// время ожидания ответа при запросе курсов валют, 20 сек
	define('ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_TIMEOUT', 20);
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_TIME')) {
	// время жизни кэша курсов валют, 30 минут
	// !!! чем больше время жизник кэша, тем больше вероятность того, 
	// что во время заказа курс будет устаревшим
        define('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_TIME', 1800);
}

if(!defined('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_PATH')) {
	// путь для хранения кэша
        define('ONLINEDENGI_PAYMENT_CURRENCY_CACHE_PATH', 'onlinedengi_cache');
}

if(!defined('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE')) {
	// путь для хранения кэша
        define('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE', 'with_image');
}

//
// Подключение основных классов
//
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/classes/general/onlinedengi_payment.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/classes/general/default_payment_handlers.php');

