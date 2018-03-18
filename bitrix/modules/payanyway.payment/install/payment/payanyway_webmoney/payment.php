<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/../payanyway/", "/payment.php"));
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_webmoney';
$unit_id = 1017;
$invoice = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_ACCOUNTID"])!="")
	$accountId = trim($_POST["NEW_ACCOUNTID"]);
else
	$accountId = "";

$accountId = intval($accountId);

if (!$accountId)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<table class="payanyway-additional-data">
			<tr>
				<td colspan="2"><font class="tablebodytext" color="Red"><?= GetMessage("PAYANYWAY_WEBMONEY_ACCOUNTID")?></font></td>
			</tr>
			<tr>
				<td><select name="NEW_ACCOUNTID"><option value="2">WMR</option><option value="3">WMZ</option><option value="4">WME</option></select></td>
				<td><input type="submit" name="SET_NEW_ACCOUNTID" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" /></td>
			</tr>
		</table>
	</form>
	<?
}
else
{
	$extraParameters["paymentSystem.accountId"] = $accountId;
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}
