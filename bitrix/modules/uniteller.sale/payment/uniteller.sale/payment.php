<?php
/**
 * Формирует пакет данных для отправки в систему Uniteller.
 * Форма с данными вставляется на страницы:
 *  - "Оформление заказа" в форму "Заказ сформирован".
 *  - "Мой заказ №???" в раздел "Оплата и доставка".
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include(GetLangFileName(dirname(__FILE__) . '/', '/uniteller.php'));
if (!class_exists('ps_uniteller')) {
	include(dirname(__FILE__) . '/tools.php');
}

$sOrderID = (strlen(CSalePaySystemAction::GetParamValue('ORDER_ID')) > 0) ? CSalePaySystemAction::GetParamValue('ORDER_ID') : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID'];
$sOrderID = trim($sOrderID);

$arOrder = CSaleOrder::GetByID($sOrderID);
$aCheckData = array();
ps_uniteller::doSyncStatus($arOrder, $aCheckData);

// Получаем данные из констант
ps_uniteller::setMerchantData($sOrderID);

// Если есть платёж, то выводим статус заказа.
if ($aCheckData['response_code'] !== '') {
	$arCurrentStatus = CSaleStatus::GetByID($arOrder['STATUS_ID']);
	echo '<br><strong>' . $arCurrentStatus['NAME'] . '</strong>';
} else {
	// Если оплата еще не была произведена, то выводим форму для оплаты заказа.
	$sDateInsert = (strlen(CSalePaySystemAction::GetParamValue('DATE_INSERT')) > 0) ? CSalePaySystemAction::GetParamValue('DATE_INSERT') : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['DATE_INSERT'];
	$sDateInsert = trim($sDateInsert);
	$fHouldPay = (strlen(CSalePaySystemAction::GetParamValue('SHOULD_PAY')) > 0) ? CSalePaySystemAction::GetParamValue('SHOULD_PAY') : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['SHOULD_PAY'];
	$sHouldPay = sprintf('%01.2f', $fHouldPay);
	$sCurrency = (strlen(CSalePaySystemAction::GetParamValue('CURRENCY')) > 0) ? CSalePaySystemAction::GetParamValue('CURRENCY') : $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['CURRENCY'];
	$sCurrency = trim($sCurrency);

	$iLiftime = (int)CSalePaySystemAction::GetParamValue('LIFE_TIME');
	$URL_RETURN_OK = trim(CSalePaySystemAction::GetParamValue('SUCCESS_URL'));
	$URL_RETURN_NO = trim(CSalePaySystemAction::GetParamValue('FAIL_URL'));

	if ($iLiftime > 0) {
		$sLiftime = (string)$iLiftime;
//		$signature = strtoupper(md5(ps_uniteller::$Shop_ID . $sOrderID . $sHouldPay . $iLiftime . ps_uniteller::$Password));
	} else {
		$sLiftime = '';
//		$signature = strtoupper(md5(ps_uniteller::$Shop_ID . $sOrderID . $sHouldPay . ps_uniteller::$Password));
	}
	$signature = strtoupper(md5(md5(ps_uniteller::$Shop_ID) . '&' . md5($sOrderID) . '&' . md5($sHouldPay)
		. '&' . md5('') . '&' . md5('') . '&' . md5($sLiftime) . '&' . md5('') . '&' . md5('') . '&' . md5('')
		. '&' . md5('') . '&' . md5(ps_uniteller::$Password)));
?>
<form action="<?= ps_uniteller::$url_uniteller_pay ?>" method="post" target="_blank">
	<font class="tablebodytext"><br><?= GetMessage('SUSP_ACCOUNT_NO') ?>
	<?= $sOrderID . GetMessage('SUSP_ORDER_FROM') . $sDateInsert ?><br> <?= GetMessage('SUSP_ORDER_SUM') ?><b><?= SaleFormatCurrency($sHouldPay, $sCurrency) ?>
	</b><br> <br> <input type="hidden" name="Shop_IDP"
		value="<?= ps_uniteller::$Shop_ID ?>">
		<input type="hidden" name="Order_IDP" value="<?= $sOrderID ?>"> <input
		type="hidden" name="Subtotal_P"
		value="<?= (str_replace(',', '.', $sHouldPay)) ?>"> <?if ($iLiftime > 0):?>
		<input type="hidden" name="Lifetime"
		value="<?= $iLiftime ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('LANGUAGE')) > 0):?>
		<input type="hidden" name="Language"
		value="<?= substr(CSalePaySystemAction::GetParamValue('LANGUAGE'), 0, 2) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('COMMENT')) > 0):?> <input
		type="hidden" name="Comment"
		value="<?= substr(CSalePaySystemAction::GetParamValue('COMMENT'), 0, 255) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('COUNTRY')) > 0):?> <input
		type="hidden" name="Country"
		value="<?= substr(CSalePaySystemAction::GetParamValue('COUNTRY'), 0, 3) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('STATE')) > 0):?> <input
		type="hidden" name="State"
		value="<?= substr(CSalePaySystemAction::GetParamValue('STATE'), 0, 3) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('FIRST_NAME')) > 0):?>
		<input type="hidden" name="FirstName"
		value="<?= substr(CSalePaySystemAction::GetParamValue('FIRST_NAME'), 0, 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('LAST_NAME')) > 0):?>
		<input type="hidden" name="LastName"
		value="<?= substr(CSalePaySystemAction::GetParamValue('LAST_NAME'),0 , 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('MIDDLE_NAME')) > 0): ?>
		<input type="hidden" name="MiddleName"
		value="<?= substr(CSalePaySystemAction::GetParamValue('MIDDLE_NAME'), 0, 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('EMAIL')) > 0): ?> <input
		type="hidden" name="Email"
		value="<?= substr(CSalePaySystemAction::GetParamValue('EMAIL'), 0, 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('PHONE')) > 0): ?> <input
		type="hidden" name="Phone"
		value="<?= substr(CSalePaySystemAction::GetParamValue('PHONE'), 0 , 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('ADDRESS')) > 0): ?>
		<input type="hidden" name="Address"
		value="<?= substr(CSalePaySystemAction::GetParamValue('ADDRESS'), 0, 128) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('CITY')) > 0): ?> <input
		type="hidden" name="City"
		value="<?= substr(CSalePaySystemAction::GetParamValue('CITY'), 0, 64) ?>"> <?endif;?>
		<?if (strlen(CSalePaySystemAction::GetParamValue('ZIP')) > 0): ?> <input
		type="hidden" name="Zip"
		value="<?= substr(CSalePaySystemAction::GetParamValue('ZIP'), 0, 64) ?>"> <?endif;?>
		<?if (strlen($signature) > 0): ?> <input type="hidden"
		name="Signature" value="<?= $signature ?>"> <?endif;?> <?if (strlen($URL_RETURN_OK) > 0): ?>
		<input type="hidden" name="URL_RETURN_OK"
		value="<?= substr($URL_RETURN_OK, 0, 128) ?>">
		<?endif;?> <?if (strlen($URL_RETURN_NO) > 0): ?>
		<input type="hidden" name="URL_RETURN_NO"
		value="<?= substr(($URL_RETURN_NO . '?ID=' . $sOrderID), 0, 128) ?>">
		<?endif;?> <input type="submit" name="Submit"
		value="<?echo GetMessage('SUSP_UNITELLER_PAY_BUTTON') ?>"> </font>
</form>
<p align="justify">
	<font class="tablebodytext"><b><?echo GetMessage('SUSP_DESC_TITLE') ?>
	</b> </font>
</p>
<p align="justify">
	<font class="tablebodytext"><?echo CSalePaySystemAction::GetParamValue('DESC') ?>
	</font>
</p>
<?php
}
?>