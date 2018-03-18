<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$payment_system = 'payanyway_euroset';
$unit_id = 248362;
$account_id = 136;
$invoice = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_RAPIDA_PHONE"])!="")
	$rapidaPhone = trim($_POST["RAPIDA_PHONE"]);
else
	$rapidaPhone = trim(CSalePaySystemAction::GetParamValue("CLIENT_PHONE"));

preg_match("/^(\+7)([\d]{10})$/", $rapidaPhone, $matches);

if (!$matches)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<table class="payanyway-additional-data">
			<tr>
				<td colspan="2"><font class="tablebodytext" color="Red"><?= GetMessage("PAYANYWAY_EUROSET_RAPIDAPHONE_HELP")?></font></td>
			</tr>
			<tr>
				<td><input type="text" name="RAPIDA_PHONE" size="30" value="<?= $rapidaPhone?>" /></td>
				<td><input type="submit" name="SET_RAPIDA_PHONE" value="<?= GetMessage("PAYANYWAY_EXTRA_PARAMS_OK")?>" /></td>
			</tr>
		</table>
	</form>
	<?
}
else
{
	$extraParameters["additionalParameters.rapidaPhone"] = $rapidaPhone;
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php"))
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/payment.php");
}
