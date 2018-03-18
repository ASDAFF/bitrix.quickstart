<?
global $APPLICATION;
global $arOptions;

$MODULE_ID = 'edost.delivery';
if (!CModule::IncludeModule('sale')) return false;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'bitrix/modules/'.$MODULE_ID.'/classes/general/delivery_edost.php');

CModule::AddAutoloadClasses($MODULE_ID, array('CEdostModifySaleOrderAjax' => 'general/edost_saleorderajax.php'));

// подключение виджета PickPoint (только на странице оформления заказа)
if (COption::GetOptionString('edost.delivery', 'show_pickpoint_map', '') == 'Y') {
	$shop_link = COption::GetOptionString('edost.delivery', 'order_link', '/personal/order/make');
	if (strpos($_SERVER['REQUEST_URI'], $shop_link) === 0) $APPLICATION->AddHeadString('<script type="text/javascript" src="http://www.pickpoint.ru/select/postamat.js" charset="utf-8"></script>');
}
?>