<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
$currency = CSalePaySystemAction::GetParamValue("CURRENCY");
if(strlen($currency) <= 0 || $currency == "RUR")
	$currency = "RUB";
?>
<form id="pay" name="pay" method="POST" action="https://paymaster.ru/Payment/Init">
	<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?= CSalePaySystemAction::GetParamValue("SHOULD_PAY") ?>">
	<input type="hidden" name="LMI_CURRENCY" value="<?=$currency?>">
	<input type="hidden" name="LMI_PAYMENT_DESC" value="Заказ <?= CSalePaySystemAction::GetParamValue("ORDER_ID") ?> от <?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("DATE_INSERT")) ?>">
	<input type="hidden" name="LMI_PAYMENT_NO" value="<?= CSalePaySystemAction::GetParamValue("ORDER_ID") ?>">
	<input type="hidden" name="LMI_MERCHANT_ID" value="<?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("SHOP_ACCT")) ?>">
	<input type="hidden" name="LMI_SIM_MODE" value="<?= htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("TEST_MODE")) ?>">
	<input type="hidden" name="LMI_RESULT_URL" value="<?=CSalePaySystemAction::GetParamValue("RESULT_URL")?>">
	<input type="hidden" name="LMI_SUCCESS_URL" value="<?=CSalePaySystemAction::GetParamValue("SUCCESS_URL")?>">
	<input type="hidden" name="LMI_FAIL_URL" value="<?=CSalePaySystemAction::GetParamValue("FAIL_URL")?>">
	<input type="hidden" name="LMI_PAYER_EMAIL" value="<?=CSalePaySystemAction::GetParamValue("LMI_PAYER_EMAIL")?>">
	<input type="hidden" name="LMI_PAYER_PHONE_NUMBER" value="<?=CSalePaySystemAction::GetParamValue("LMI_PAYER_PHONE_NUMBER")?>">
	<input type="hidden" name="LMI_SUCCESS_METHOD" value="1">
	<input type="hidden" name="LMI_FAIL_METHOD" value="1">
	<input type="submit" value="Оплатить заказ">
</form>