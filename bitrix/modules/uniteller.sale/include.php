<?php
/**
 * Файл подключается при подключении модуля во время выполнения скриптов сайта.
 * В нем должны находиться включения всех файлов с библиотеками функций и классов модуля.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'uniteller.sale',
	array(
		'CUnitellerAgentLog' => 'classes/general/uniteller_agent_log.php',
	)
);

/**
 * Класс для агента UnitellerAgent();.
 * Агент должен запускаться по крону, чтобы не мешал работать остальных пользователей.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
Class CUnitellerAgent {
	/**
	 * Функция агента.
	 * Получает список всех заказов платёжной системы Uniteller и для тех из них, статус которых мог измениться,
	 * проводит синхронизацию статусов заказов со статусами соответствующах платежей.
	 * В качестве возвращаемого значения функция-агент должна вернуть PHP код который будет использован при следующем запуске данной функции.
	 * @return string
	 */
	function UnitellerAgent() {
		set_time_limit(60 * 20);

		if (file_exists($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/uniteller.sale/payment/uniteller.sale/tools.php')) {
			if (!class_exists('ps_uniteller')) {
				include($_SERVER['DOCUMENT_ROOT'] . BX_ROOT .  '/modules/uniteller.sale/payment/uniteller.sale/tools.php');
			}
		} else {
			return 'CUnitellerAgent::UnitellerAgent();';
		}

		// Признак того, что агента запустил именно крон.
		if (!defined('UNITELLER_AGENT') || UNITELLER_AGENT !== true) {
			return 'CUnitellerAgent::UnitellerAgent();';
		}

		CModule::IncludeModule('sale');
		// Определяет ID обработчика платёжной системы Uniteller
		$uniteller_payment_id = -1;
		$dbPaySystem = CSalePaySystem::GetList();
		while ($arPaySystem = $dbPaySystem->Fetch()) {
			if (strtolower($arPaySystem['NAME']) == 'uniteller') {
				$uniteller_payment_id = (int)$arPaySystem['ID'];
			}
		}
		if ($uniteller_payment_id == -1) {
			return 'CUnitellerAgent::UnitellerAgent();';
		}

		// В функции-агенте не доступен глобальный объект $USER класса CUser.
		global $USER;
		if (!is_object($USER)) {
			$USER = new CUser;
		}
		// В CSaleOrder::GetList используется глобальный объект $USER класса CUser.
		$db_sales = CSaleOrder::GetList(false, array('PAY_SYSTEM_ID' => $uniteller_payment_id));

		while ($arOrder = $db_sales->Fetch()) {
			$arOrder = CSaleOrder::GetByID($arOrder['ID']);
			ps_uniteller::setMerchantData($arOrder['ID']);
			$uniteller_sync_time = strtotime($arOrder['PS_RESPONSE_DATE']);
			if ($uniteller_sync_time >= ps_uniteller::$date_fix_order_sync) {
				ps_uniteller::doSyncStatus($arOrder);
			}
		}

		return 'CUnitellerAgent::UnitellerAgent();';
	}
}

?>