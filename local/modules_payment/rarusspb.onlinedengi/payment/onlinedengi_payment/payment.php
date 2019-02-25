<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */


$APPLICATION->IncludeComponent(
	'onlinedengi_payment:order.pay', 
	"with_image_ajax",
	array(
	),
	false,
	array(
		'HIDE_ICONS' => 'N',
		'ACTIVE_COMPONENT' => 'Y'
	)
);

