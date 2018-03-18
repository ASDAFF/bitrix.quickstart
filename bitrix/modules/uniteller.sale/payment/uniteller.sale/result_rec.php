<?php
/**
 * Вызывается из публичной части электронного магазина при запросе на URL: http://домен_магазина/personal/ordercheck/result_rec.php
 * Обрабатывает этот запрос: если из платёжной системы пришли данные о смене статуса платежа, то меняет статус соответствующего заказа.
 *
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Order_ID']) && isset($_POST['Status']) && isset($_POST['Signature'])) {
	include(GetLangFileName(dirname(__FILE__) . '/', '/uniteller.php'));
	if (!class_exists('ps_uniteller')) {
		include(dirname(__FILE__) . '/tools.php');
	}

	// Получает данные для смены статуса.
	$order_real_id = (int)$_POST['Order_ID'];
	$status = trim($_POST['Status']);
	$signature = trim($_POST['Signature']);

	CModule::IncludeModule('sale');
	if ($arOrder = CSaleOrder::GetByID($order_real_id)) {
		CSalePaySystemAction::InitParamArrays($arOrder, $arOrder['ID']);

		// Определяет ID обработчика платёжной системы Uniteller
		$uniteller_payment_id = -1;
		$dbPaySystem = CSalePaySystem::GetList();
		while ($arPaySystem = $dbPaySystem->Fetch()) {
			if (strtolower($arPaySystem['NAME']) == 'uniteller') {
				$uniteller_payment_id = (int)$arPaySystem['ID'];
			}
		}

		$order_payment_id = (int)$arOrder['PAY_SYSTEM_ID'];

		ps_uniteller::setMerchantData($order_real_id);
		$sign = strtoupper(md5($order_real_id . $status . ps_uniteller::$Password));

		// Сигнатура совпадает с данными заказа и заказ принадлежит платежной системе Uniteller.
		if ($sign === $signature && $order_payment_id === $uniteller_payment_id) {
			$status = strtolower($status);
			$statusCode = ps_uniteller::getStatusCode($order_real_id);

			// Заказ в состоянии 'до блокировки средств'.
			if ($statusCode === 'O') {
				ps_uniteller::setStatusCode($order_real_id, $status);
			}
			// Заказ в состоянии 'блокировка средств', а платёж - нет.
			if ($statusCode === 'A'
				&& ($status === 'paid' || $status === 'canceled')
			) {
				ps_uniteller::setStatusCode($order_real_id, $status);
			}
			// Заказ в состоянии 'средства сняты', а платёж - нет.
			if ($statusCode === 'P'
				&& ($status === 'authorized' || $status === 'canceled')
			) {
				ps_uniteller::setStatusCode($order_real_id, $status);
			}
			// Заказ в состоянии 'средства возвращены', а платёж в состоянии 'средства заблокированны' или 'средства сняты'.
			if ($statusCode === 'C'
				&& ($status === 'authorized' || $status === 'paid')
			) {
				if (!ps_uniteller::setUnitellerCancel($order_real_id)) {
					// Если отменить платёж не удалось, то меняем статус заказа.
					ps_uniteller::setStatusCode($order_real_id, $status);
				}
			}
			// Заказ в состоянии 'неизвестно'.
			if ($statusCode === 'W') {
				ps_uniteller::setStatusCode($order_real_id, $status);
			}
		}
	}
}