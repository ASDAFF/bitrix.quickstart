<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = isset($payment_system) ? $payment_system : "payanyway";
$extraParameters = isset($extraParameters) ? $extraParameters : array();
$unit_id = isset($unit_id) ? $unit_id : null;
$account_id = isset($account_id) ? $account_id : null;
$invoice = isset($invoice) ? $invoice : false;


$MNT_PAYMENT_SERVER = CSalePaySystemAction::GetParamValue("MNT_PAYMENT_SERVER");
$action = $invoice ? CSalePaySystemAction::GetParamValue("PAYANYWAY_PAY_URL") : "https://".$MNT_PAYMENT_SERVER."/assistant.htm";

$MNT_ID = trim( CSalePaySystemAction::GetParamValue("MNT_ID") );
$MNT_TRANSACTION_ID = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]; 
$MNT_CURRENCY_CODE = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$MNT_AMOUNT = number_format(CSalePaySystemAction::GetParamValue("MNT_AMOUNT"), 2, ".", "");
$MNT_TEST_MODE = CSalePaySystemAction::GetParamValue("MNT_TEST_MODE") == "1" ? "1" : "0";
$MNT_SIGNATURE = md5($MNT_ID . $MNT_TRANSACTION_ID . $MNT_AMOUNT . $MNT_CURRENCY_CODE . $MNT_TEST_MODE . CSalePaySystemAction::GetParamValue("DATA_INTEGRITY_CODE"));

$host = COption::GetOptionString("main", "server_name", $_SERVER["HTTP_HOST"]);
if($host == "") $host = $_SERVER["HTTP_HOST"];
$host = $_SERVER['HTTPS'] == 'on' ? 'https://'.$host : 'http://'.$host;
?>
<form action="<?= $action?>" method="post">
	<font class="tablebodytext">
		<?= GetMessage("PAYMENT_PAYANYWAY_TO_PAY")?><b><?= SaleFormatCurrency(CSalePaySystemAction::GetParamValue("MNT_AMOUNT"), $MNT_CURRENCY_CODE)?></b>
		<p>
			<input type="hidden" name="MNT_ID" value="<?= $MNT_ID?>">
			<input type="hidden" name="MNT_TRANSACTION_ID" value="<?= $MNT_TRANSACTION_ID?>">
			<input type="hidden" name="MNT_CURRENCY_CODE" value="<?= $MNT_CURRENCY_CODE?>">
			<input type="hidden" name="MNT_AMOUNT" value="<?= $MNT_AMOUNT?>">
			<input type="hidden" name="MNT_TEST_MODE" value="<?= $MNT_TEST_MODE?>">
			<input type="hidden" name="MNT_SIGNATURE" value="<?= $MNT_SIGNATURE?>">
			<input type="hidden" name="MNT_DESCRIPTION" value="Заказ номер #<?= $MNT_TRANSACTION_ID?>">
			<input type="hidden" name="paymentSystem" value="<?= $payment_system?>">
			<input type="hidden" name="MNT_SUCCESS_URL" value="<?= $host . "/personal/order/"?>">
			<input type="hidden" name="MNT_FAIL_URL" value="<?= $host. "/personal/order/"?>">
			
			<? foreach($extraParameters as $name=>$value):?>
			<input type="hidden" name="<?= $name?>" value="<?= $value?>">
			<?endforeach;?>
			
			<? if ($invoice):?>
			<input type="hidden" name="action" value="invoice">
			<? endif;?>
			
			<? if ($unit_id):?>
			<input type="hidden" name="paymentSystem.unitId" value="<?= $unit_id?>">
			<? endif;?>
			
			<? if ($account_id):?>
			<input type="hidden" name="paymentSystem.accountId" value="<?= $account_id?>">
			<? endif;?>
			
			<? if ($payment_system !== 'payanyway'):?>
			<input type="hidden" name="followup" value="true">
			<input type="hidden" name="javascriptEnabled" value="true">
			<? endif;?>
			
			<input type="submit" value="<?= GetMessage("PAYMENT_PAYANYWAY_BUTTON")?>">
		</p>
	</font>
</form>