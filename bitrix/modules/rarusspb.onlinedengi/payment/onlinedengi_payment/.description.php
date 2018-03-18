<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** * * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс. * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010 * */

if(!CModule::IncludeModule('rarusspb.onlinedengi')) {
	return;
}

include(GetLangFileName(dirname(__FILE__).'/', '/.description.php'));

$psTitle = GetMessage('ONLINEDENGI_PS_TITLE');
$psDescription = GetMessage('ONLINEDENGI_PS_DESCRIPTION');

if(file_exists($_SERVER['DOCUMENT_ROOT'].ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH)) {
	$psDescription .= GetMessage('ONLINEDENGI_PS_DESCRIPTION_RES', array('#FILE_PATH#' => "javascript:WizardWindow.Open('onlinedengi_payment:result_rec','".bitrix_sessid()."')"));
}

// Получим список доступных способов оплаты
$arOnlineDengiAvailablePaymentTypes = COnlineDengiPayment::GetPaymentTypesList();

$arPSCorrespondence = array(
	'ONLINEDENGI_PROJECT' => array(
		'NAME' => GetMessage('ONLINEDENGI_PROJECT_N'),
		'DESCR' => GetMessage('ONLINEDENGI_PROJECT_D'),
		'VALUE' => '',
		'TYPE' => ''
	),
	'ONLINEDENGI_SOURCE' => array(
		'NAME' => GetMessage('ONLINEDENGI_SOURCE_N'),
		'DESCR' => GetMessage('ONLINEDENGI_SOURCE_D'),
		'VALUE' => '',
		'TYPE' => ''
	),
	'ONLINEDENGI_SECRET_KEY' => array(
		'NAME' => GetMessage('ONLINEDENGI_SECRET_KEY_N'),
		'DESCR' => GetMessage('ONLINEDENGI_SECRET_KEY_D'),
		'VALUE' => '',
		'TYPE' => ''
	),
	'ONLINEDENGI_AMOUNT' => array(
		'NAME' => GetMessage('ONLINEDENGI_AMOUNT_N'),
		'DESCR' => GetMessage('ONLINEDENGI_AMOUNT_D'),
		'VALUE' => 'SHOULD_PAY',
		'TYPE' => 'ORDER'
	),
	'ONLINEDENGI_NICKNAME' => array(
		'NAME' => GetMessage('ONLINEDENGI_NICKNAME_N'),
		'DESCR' => GetMessage('ONLINEDENGI_NICKNAME_D'),
		'VALUE' => 'ID',
		'TYPE' => 'ORDER'
	),
	'ONLINEDENGI_NICK_EXTRA' => array(
		'NAME' => GetMessage('ONLINEDENGI_NICK_EXTRA_N'),
		'DESCR' => GetMessage('ONLINEDENGI_NICK_EXTRA_D'),
		'VALUE' => '',
		'TYPE' => ''
	),
	'ONLINEDENGI_ORDER_ID' => array(
		'NAME' => GetMessage('ONLINEDENGI_ORDER_ID_N'),
		'DESCR' => GetMessage('ONLINEDENGI_ORDER_ID_D'),
		'VALUE' => '',
		'TYPE' => ''
	),

	'ONLINEDENGI_COMMENT' => array(
		'NAME' => GetMessage('ONLINEDENGI_COMMENT_N'),
		'DESCR' => GetMessage('ONLINEDENGI_COMMENT_D'),
		'VALUE' => '',
		'TYPE' => ''
	),

	'ONLINEDENGI_CONVERT_ROUND_UP' => array(
		'NAME' => GetMessage('ONLINEDENGI_CONVERT_ROUND_UP_N'),
		'DESCR' => GetMessage('ONLINEDENGI_CONVERT_ROUND_UP_D'),
		'VALUE' => '1',
		'TYPE' => ''
	),
	'ONLINEDENGI_WRONG_PAY_MODE' => array(
		'NAME' => GetMessage('ONLINEDENGI_WRONG_PAY_MODE_N'),
		'DESCR' => GetMessage('ONLINEDENGI_WRONG_PAY_MODE_D'),
		'VALUE' => '0',
		'TYPE' => ''
	),
	'ONLINEDENGI_OVERPAY_MODE' => array(
		'NAME' => GetMessage('ONLINEDENGI_OVERPAY_MODE_N'),
		'DESCR' => GetMessage('ONLINEDENGI_OVERPAY_MODE_D'),
		'VALUE' => '0',
		'TYPE' => ''
	),

	'ONLINEDENGI_OVERPAY_STATUS' => array(
		'NAME' => GetMessage('ONLINEDENGI_OVERPAY_STATUS_N'),
		'DESCR' => GetMessage('ONLINEDENGI_OVERPAY_STATUS_D', array('#HREF#' => '/bitrix/admin/sale_status.php?lang='.LANG)),
		'VALUE' => '',
		'TYPE' => ''
	),
	'ONLINEDENGI_DEFICIT_PAY_STATUS' => array(
		'NAME' => GetMessage('ONLINEDENGI_DEFICIT_PAY_STATUS_N'),
		'DESCR' => GetMessage('ONLINEDENGI_DEFICIT_PAY_STATUS_D', array('#HREF#' => '/bitrix/admin/sale_status.php?lang='.LANG)),
		'VALUE' => '',
		'TYPE' => ''
	),

	// При проводке посредством внутреннего счета пользователя, на счет будут зачислены и списаны средства равные стоимости заказа,
	// а это значит, что при отмене заказа полная сумма будет списана обратно на счет покупателя (не фактически оплаченная!!!).
	// Интернет-магазин версия 8.5.2 (2009-12-14), транзакции используются практически везде, параметр теряет смысл
	/*
	'ONLINEDENGI_USE_WITHDRAW' => array(
		'NAME' => GetMessage('ONLINEDENGI_USE_WITHDRAW_N'),
		'DESCR' => GetMessage('ONLINEDENGI_USE_WITHDRAW_D'),
		'VALUE' => '0',
		'TYPE' => ''
	),
	*/

	'ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE' => array(
		'NAME' => GetMessage('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE_N'),
		'DESCR' => GetMessage('ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE_D'),
		'VALUE' => '',
		'TYPE' => ''
	),
);

if(is_array($arOnlineDengiAvailablePaymentTypes)) {
	foreach($arOnlineDengiAvailablePaymentTypes as $arOnlineDengiPaymentType) {
		$sCode_ = 'ONLINEDENGI_AVAILABLE_TYPE_'.$arOnlineDengiPaymentType['id'];
		$arPSCorrespondence[$sCode_] = array(
			'NAME' => GetMessage('ONLINEDENGI_AVAILABLE_PRE').GetMessage($arOnlineDengiPaymentType['lang']).'['.$arOnlineDengiPaymentType['id'].']',
			'DESCR' => GetMessage('ONLINEDENGI_AVAILABLE_TYPE_D'),
			'VALUE' => $arOnlineDengiPaymentType['default'],
			'TYPE' => ''
		);
	}
}

// событие OnAfterPaymentsCorrespondence
$rsItems = GetModuleEvents('rarusspb_onlinedengi', 'OnAfterPaymentsCorrespondence');
while($arItem = $rsItems->Fetch()) {
	$arPSCorrespondence = ExecuteModuleEvent($arItem, $arPSCorrespondence);
}
