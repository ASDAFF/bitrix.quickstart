<?php
/**
 * ���� ������������ ��� ����������� ������ �� ����� ���������� �������� �����.
 * � ��� ������ ���������� ��������� ���� ������ � ������������ ������� � ������� ������.
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
 * ����� ��� ������ UnitellerAgent();.
 * ����� ������ ����������� �� �����, ����� �� ����� �������� ��������� �������������.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
Class CUnitellerAgent {
	/**
	 * ������� ������.
	 * �������� ������ ���� ������� �������� ������� Uniteller � ��� ��� �� ���, ������ ������� ��� ����������,
	 * �������� ������������� �������� ������� �� ��������� ��������������� ��������.
	 * � �������� ������������� �������� �������-����� ������ ������� PHP ��� ������� ����� ����������� ��� ��������� ������� ������ �������.
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

		// ������� ����, ��� ������ �������� ������ ����.
		if (!defined('UNITELLER_AGENT') || UNITELLER_AGENT !== true) {
			return 'CUnitellerAgent::UnitellerAgent();';
		}

		CModule::IncludeModule('sale');
		// ���������� ID ����������� �������� ������� Uniteller
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

		// � �������-������ �� �������� ���������� ������ $USER ������ CUser.
		global $USER;
		if (!is_object($USER)) {
			$USER = new CUser;
		}
		// � CSaleOrder::GetList ������������ ���������� ������ $USER ������ CUser.
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