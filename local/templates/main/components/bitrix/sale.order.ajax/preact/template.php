<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;

$this->addExternalJs($this->GetFolder().'/preact.js');
$this->addExternalJs($this->GetFolder().'/preactProvider.js');

foreach (['basket', 'block', 'person', 'error', 'shipment', 'innerPayment', 'payment', 'property', 'location', 'total'] as $script) {
	$this->addExternalJs($this->GetFolder().'/js/'.$script.'.js');
}

if (isset($_GET['ORDER_ID']) && strlen($_GET['ORDER_ID']) > 0) {
	include Main\Application::getDocumentRoot().$templateFolder.'/confirm.php';
	return false;
}

if ($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
	include Main\Application::getDocumentRoot().$templateFolder.'/empty.php';
	return false;
}
?>
<div id="sale_order">
	<div class="sale_order_form">
		<div class="sale_order_basket"></div>
		<div class="sale_order_steps"></div>
		<div class="sale_order_total"></div>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		preactProvider.render(SaleOrder, document.getElementById('sale_order'), <?=json_encode($arResult['JS_DATA'], JSON_UNESCAPED_UNICODE)?>);
	})
</script>
<?
/*
if ($arParams['USER_CONSENT'] === 'Y') {
	$APPLICATION->IncludeComponent(
		'bitrix:main.userconsent.request',
		'',
		array(
			'ID' => $arParams['USER_CONSENT_ID'],
			'IS_CHECKED' => $arParams['USER_CONSENT_IS_CHECKED'],
			'IS_LOADED' => $arParams['USER_CONSENT_IS_LOADED'],
			'AUTO_SAVE' => 'N',
			'SUBMIT_EVENT_NAME' => 'bx-soa-order-save',
			'REPLACE' => array(
				'button_caption' => isset($arParams['~MESS_ORDER']) ? $arParams['~MESS_ORDER'] : $arParams['MESS_ORDER'],
				'fields' => $arResult['USER_CONSENT_PROPERTY_DATA']
			)
		)
	);
}
?>