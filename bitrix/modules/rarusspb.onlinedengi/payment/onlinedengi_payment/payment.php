<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 *
 * ������ ���������� ������� OnlineDengi ��� CMS 1� �������.
 * @copyright ������ OnlineDengi http://www.onlinedengi.ru/ (��� "�����������"), 2010
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

