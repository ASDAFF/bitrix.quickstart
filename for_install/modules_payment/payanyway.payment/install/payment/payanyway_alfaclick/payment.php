<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_alfaclick';
$unit_id = 587412;
$invoice = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_ALFA"])!=""){
	$alfaIdClient = trim($_POST["ALFA_IDCLIENT"]);
	$alfaPaymentPurpose = trim($_POST["ALFA_PAYMENTPURPOSE"]);
} else {
	$alfaIdClient = "";
	$alfaPaymentPurpose = "";
}

if (!$alfaIdClient)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<table class="payanyway-additional-data">
			<tr>
				<td colspan="2"><font class="tablebodytext" color="Red"><?= GetMessage("PAYANYWAY_ALFACLICK_DESC")?></font></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_ALFA_IDCLIENT")?></label></td>
				<td><input type="text" name="ALFA_IDCLIENT" value="<?= $alfaIdClient?>"></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_ALFA_PAYMENTPURPOSE")?></label></td>
				<td><input type="text" name="ALFA_PAYMENTPURPOSE" value="<?= $alfaPaymentPurpose?>"></td>
			</tr>
		</table>
		<input type="submit" name="SET_NEW_ALFA" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" />
	</form>
	<?
}
else
{
	$extraParameters["additionalParameters.alfaIdClient"] = $alfaIdClient;
	$extraParameters["additionalParameters.alfaPaymentPurpose"] = $alfaPaymentPurpose;
	
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}
