<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
include(GetLangFileName(dirname(__FILE__)."/", "/liqpay.php"));

$merchant_id = CSalePaySystemAction::GetParamValue("MERCHANT_ID");
$signature = CSalePaySystemAction::GetParamValue("SIGN");
$url = "https://liqpay.com/?do=clickNbuy";

$resultUrl = CSalePaySystemAction::GetParamValue("PATH_TO_RESULT_URL");
$serverUrl = CSalePaySystemAction::GetParamValue("PATH_TO_SERVER_URL");

$orderID = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$shouldPay = (strlen(CSalePaySystemAction::GetParamValue("SHOULD_PAY")) > 0) ? CSalePaySystemAction::GetParamValue("SHOULD_PAY") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$currency = (strlen(CSalePaySystemAction::GetParamValue("CURRENCY")) > 0) ? CSalePaySystemAction::GetParamValue("CURRENCY") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$phone = CSalePaySystemAction::GetParamValue("PHONE");
$pay_method = CSalePaySystemAction::GetParamValue("PAY_METHOD");
?>
<?=GetMessage("PAYMENT_DESCRIPTION_PS")?> <b>LiqPAY.com</b>.<br /><br />
<?=GetMessage("PAYMENT_DESCRIPTION_SUM")?>: <b><?=CurrencyFormat($shouldPay, $currency)?></b><br /><br />
<?
if ($currency == "RUB")
	$currency = "RUR";

$xml = "<request>
		<version>1.2</version>
		<result_url>".$resultUrl."</result_url>
		<server_url>".$serverUrl."</server_url>
		<merchant_id>".$merchant_id."</merchant_id>
		<order_id>ORDER_".$orderID."</order_id>
		<amount>".$shouldPay."</amount>
		<currency>".$currency."</currency>
		<description>Payment for Order ".$orderID."</description>
		<default_phone>".$phone."</default_phone>
		<pay_way>".$pay_method."</pay_way>
		</request>";

$xml_encoded = base64_encode($xml);
$lqsignature = base64_encode(sha1($signature.$xml.$signature,1));
?>
<form action="<?= $url?>" method="post">
	<input type="hidden" name="operation_xml" value="<?= $xml_encoded?>" />
	<input type="hidden" name="signature" value="<?= $lqsignature?>" />
	<input type="submit" value="<?= GetMessage("PAYMENT_PAY")?>" />
</form>
