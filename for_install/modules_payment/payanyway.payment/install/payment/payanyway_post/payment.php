<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/../payanyway/", "/payment.php"));
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_post';
$unit_id = 1029;
$account_id = 15;
$invoice = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_MAILOFRUSSIA"])!=""){
	$mailofrussiaSenderIndex = trim($_POST["MAILOFRUSSIA_SENDER_INDEX"]);
	$mailofrussiaSenderRegion = trim($_POST["MAILOFRUSSIA_SENDER_REGION"]);
	$mailofrussiaSenderAddress = trim($_POST["MAILOFRUSSIA_SENDER_ADDRESS"]);
	$mailofrussiaSenderName = trim($_POST["MAILOFRUSSIA_SENDER_NAME"]);
} else {
	$mailofrussiaSenderIndex = "";
	$mailofrussiaSenderRegion = "";
	$mailofrussiaSenderAddress = "";
	$mailofrussiaSenderName = "";
}

if (!$mailofrussiaSenderIndex || !$mailofrussiaSenderRegion || !$mailofrussiaSenderAddress || !$mailofrussiaSenderName)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<table class="payanyway-additional-data">
			<tr>
				<td colspan="2"><font class="tablebodytext" color="Red"><?= GetMessage("PAYANYWAY_POST_PARAMS")?></font></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_POST_SENDERINDEX")?></label></td>
				<td><input type="text" name="MAILOFRUSSIA_SENDER_INDEX" value="<?= $mailofrussiaSenderIndex?>"></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_POST_SENDERREGION")?></label></td>
				<td><input type="text" name="MAILOFRUSSIA_SENDER_REGION" value="<?= $mailofrussiaSenderRegion?>"></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_POST_SENDERADDRESS")?></label></td>
				<td><input type="text" name="MAILOFRUSSIA_SENDER_ADDRESS" value="<?= $mailofrussiaSenderAddress?>"></td>
			</tr>
			<tr>
				<td class="tablebodytext"><label><?= GetMessage("PAYANYWAY_POST_SENDERNAME")?></label></td>
				<td><input type="text" name="MAILOFRUSSIA_SENDER_NAME" value="<?= $mailofrussiaSenderName?>"></td>
			</tr>
		</table>
		<input type="submit" name="SET_NEW_MAILOFRUSSIA" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" />
	</form>
	<?
}
else
{
	$extraParameters["additionalParameters.mailofrussiaSenderIndex"] = $mailofrussiaSenderIndex;
	$extraParameters["additionalParameters.mailofrussiaSenderRegion"] = $mailofrussiaSenderRegion;
	$extraParameters["additionalParameters.mailofrussiaSenderAddress"] = $mailofrussiaSenderAddress;
	$extraParameters["additionalParameters.mailofrussiaSenderName"] = $mailofrussiaSenderName;
	
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}

