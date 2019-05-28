<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
$mrh_login = CSalePaySystemAction::GetParamValue("ShopLogin");
$mrh_pass1 =  CSalePaySystemAction::GetParamValue("ShopPassword");
$inv_id = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
$inv_number = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"];
$inv_desc =  CSalePaySystemAction::GetParamValue("OrderDescr");
$user_mail = CSalePaySystemAction::GetParamValue("EMAIL_USER");
$out_summ = number_format(floatval(CSalePaySystemAction::GetParamValue("SHOULD_PAY")), 2, ".", "");
$isTest = trim(CSalePaySystemAction::GetParamValue("IS_TEST"));
$crc = md5($mrh_login.":".$out_summ.":".$inv_id.":".$mrh_pass1);
$paymentType = trim(CSalePaySystemAction::GetParamValue("PAYMENT_VALUE"));
?>

<?if(strlen($isTest) > 0):
	?>
	<form action="http://test.robokassa.ru/Index.aspx" method="post" target="_blank">
<?else:
	?>
	<form action="https://merchant.roboxchange.com/Index.aspx" method="post" target="_blank">
<?endif;?>

<font class="tablebodytext">
<?=GetMessage("PYM_TITLE")?><br>
<?=GetMessage("PYM_ORDER")?> <?echo $inv_number."  ".CSalePaySystemAction::GetParamValue("DATE_INSERT")?><br>
<?=GetMessage("PYM_TO_PAY")?> <b><?echo SaleFormatCurrency(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), CSalePaySystemAction::GetParamValue("CURRENCY"))?></b>
<p>
<input type="hidden" name="FinalStep" value="1">
<input type="hidden" name="MrchLogin" value="<?=$mrh_login?>">
<input type="hidden" name="OutSum" value="<?=$out_summ?>">
<input type="hidden" name="InvId" value="<?=$inv_id?>">
<input type="hidden" name="Desc" value="<?=$inv_desc?>">
<input type="hidden" name="SignatureValue" value="<?=$crc?>">
<input type="hidden" name="Email" value="<?=$user_mail?>">
<?
if (strlen($paymentType) > 0 && $paymentType != "0")
{
	?>
	<input type="hidden" name="IncCurrLabel" value="<?=$paymentType?>">
	<?
}
?>
<input type="submit" name="Submit" value="<?=GetMessage("PYM_BUTTON")?>">

</p>
</font>
</form>