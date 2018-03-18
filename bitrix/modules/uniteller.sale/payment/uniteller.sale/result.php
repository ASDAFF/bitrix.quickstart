<?php
/**
 * ‘ормирует чек. ѕосылает запрос в платЄжную систему на получание результатов авторизации.
 * Ќа основании результатов этого запроса:
 *  - если нужно: мен€ет статус заказа в электронном магазине.
 *  - если нужно: посылает запрос на отмену платежа в платЄжную систему.
 *  - формирует чек дл€ показа пользователю.
 * ¬ызываетс€
 *  - при заходе пользователем на страницу "чек" из платЄжной системы.
 *  - при заходе пользователем на страницу "чек" из списка заказов.
 *  - администратором в административной части сайта при просмотре подробностей заказа в разделе "ќплата" при нажатии на ссылку "ѕолучить >>".
 *  - администратором в административной части сайта при просмотре списка заказов при групповой операции "ќбновить статусы платЄжных систем".
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

set_time_limit(60 * 20);

include(GetLangFileName(dirname(__FILE__) . '/', '/uniteller.php'));
if (!class_exists('ps_uniteller')) {
	include(dirname(__FILE__) . '/tools.php');
}

// от системы Uniteller запрос приходит с параметром Order_ID, а от интернет магазина с параметром ID.
if (isset($_GET['Order_ID'])) {
	$ORDER_ID = (int)$_GET['Order_ID'];
} elseif (isset($_GET['ID'])) {
	$ORDER_ID = (int)$_GET['ID'];
}

$arOrder = CSaleOrder::GetByID($ORDER_ID);
$aCheckData = array();
ps_uniteller::doSyncStatus($arOrder, $aCheckData);

// ‘ормирует html дл€ страницы чека.
$html = '';
if ($aCheckData['response_code'] !== '' || $aCheckData['error_message'] !== '') {
	if ($aCheckData['name_merchant'] !== '') {
		$html .= GetMessage('SALE_UNITELLER_MERCH_NAME') . $aCheckData['name_merchant'] . '<br>';
	}

	if ($aCheckData['name_lat'] !== '') {
		$html .= GetMessage('SALE_UNITELLER_MERCH_NAME_LAT') . $aCheckData['name_lat'] . '<br>';
	}

	if ($aCheckData['name_url'] !== '') {
		$html .= GetMessage('SALE_UNITELLER_MERCH_UNIQ_URL') . '<a href="http://' . $aCheckData['name_url'] . '">' . $aCheckData['name_url'] . '</a>' . '<br>';
	}

	if ($aCheckData['phone'] !== '') {
		$html .= GetMessage('SALE_UNITELLER_MERCH_TEL') . $aCheckData['phone'] . '<br>';
	}

	if ($aCheckData['email'] !== '') {
		$html .= GetMessage('SALE_UNITELLER_MERCH_EMAIL') . '<a href="mailto:' . $aCheckData['email'] . '">' . $aCheckData['email'] . '</a>' . '<br>';
	}

	// 12 - это длина Billnumber из технического регламента.
	if (strlen($aCheckData['billnumber']) == 12) {
		if (DoubleVal($aCheckData['total']) !== '' && $aCheckData['currency'] !== '') {
			$html .= GetMessage('SALE_UNITELLER_PS_SUM') . (int)$aCheckData['total'] . ' ' . $aCheckData['currency'] . '<br>';
		}

		if ($aCheckData['date'] !== '') {
			$html .= GetMessage('SALE_UNITELLER_PS_DATE') . $aCheckData['date'] . '<br>';
		}

		$html .= GetMessage('SALE_UNITELLER_BILLNUMBER') . $aCheckData['billnumber'] . '<br>';

		if ($aCheckData['lastname'] !== '' || $aCheckData['firstname'] !== '' || $aCheckData['middlename'] !== '') {
			$html .= GetMessage('SALE_UNITELLER_USER_FIO') . $aCheckData['lastname'] . ' ' . $aCheckData['firstname'] . ' ' . $aCheckData['middlename'] . '<br>';
		}

		if ($aCheckData['approvalcode'] !== '') {
			$html .= GetMessage('SALE_UNITELLER_APPROVEL_CODE') . $aCheckData['approvalcode'] . '<br>';
		}

		if ($aCheckData['paymenttransactiontype_id'] !== '') {
			$html .= GetMessage('SALE_UNITELLER_TRANSACTION_TYPE') . $aCheckData['paymenttransactiontype_id'] . '<br>';
		}
	} elseif ($aCheckData['error_message'] !== '') {
		$html .= '<br/><b>' . $aCheckData['error_message'] . '</b><br/>';
	} else {
		$html .= '<br><b>' . GetMessage('SALE_UNITELLER_ERROR') . '</b><br>';
	}

	if ($aCheckData['response_code'] !== '' && $aCheckData['response_code'] !== ps_uniteller::RESPONSE_CODE_SUCCES) {
		if (isset($aDesc[$aCheckData['response_code']])) {
			$html .= '<br><b>' . $aDesc[$aCheckData['response_code']] . '</b><br>';
		} else {
			$html .= '<br><b>' . $aCheckData['response_code'] . '</b><br>';
		}
	}

	if ($_GET['print'] != 'Y') {
		$html .= '<br><a target="_blanck" title="' . GetMessage('SALE_UNITELLER_PRINT_CHECK') . '" href="' . $GLOBALS['APPLICATION']->GetCurPage() . (($s = DeleteParam(array('print'))) == '' ? '?print=Y' : '?' . $s . '&print=Y') . '">' . GetMessage('SALE_UNITELLER_PRINT_CHECK') . '</a>';
	} else {
		$html .= '<script type="text/javascript">window.print();</script><br><button type="button" title="' . GetMessage('SALE_UNITELLER_CLOSE_WINDOW') . '" onclick="window.close();">' . GetMessage('SALE_UNITELLER_CLOSE_WINDOW') . '</button><br><br>';
	}

	// ≈сли это не страница чека, то ничего не выводить
	if (strpos($_SERVER['SCRIPT_NAME'], '/personal/ordercheck/check/index.php') !== false) {
		echo $html;
	}
} else {
	// ≈сли это не страница чека, то ничего не выводить
	if (strpos($_SERVER['SCRIPT_NAME'], '/personal/ordercheck/check/index.php') !== false) {
		echo '<br><b>' . GetMessage('SALE_UNITELLER_ERROR') . '</b><br>';
	} else {
		$errorMessage = GetMessage('SALE_UNITELLER_ERROR');
	}
}
return true;