<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_dengimail';
$unit_id = 545234;
$invoice = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_EMAIL"])!="")
	$dmrBuyerEmail = trim($_POST["NEW_EMAIL"]);
else
	$dmrBuyerEmail = "";

$dmrBuyerEmail = filter_var($dmrBuyerEmail, FILTER_VALIDATE_EMAIL);

if (!$dmrBuyerEmail)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<table class="payanyway-additional-data">
			<tr>
				<td colspan="2"><font class="tablebodytext" color="Red"><?= GetMessage("PAYANYWAY_DENGIMAIL_EMAIL")?></font></td>
			</tr>
			<tr>
				<td><input type="text" name="NEW_EMAIL" size="30" value="<?= $dmrBuyerEmail?>" /></td>
				<td><input type="submit" name="SET_NEW_EMAIL" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" /></td>
			</tr>
		</table>
	</form>
	<?
}
else
{
	$extraParameters["additionalParameters.dmrBuyerEmail"] = $buyerEmail;
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}
