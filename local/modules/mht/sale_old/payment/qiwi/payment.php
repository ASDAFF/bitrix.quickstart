<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
include(GetLangFileName(dirname(__FILE__)."/", "/qiwi.php"));

if ($_SERVER["REQUEST_METHOD"] == "POST" && trim($_POST["SET_NEW_PHONE"])!="")
	$phone = trim($_POST["NEW_PHONE"]);
else
	$phone = trim(CSalePaySystemAction::GetParamValue("CLIENT_PHONE"));
	
$phone = preg_replace("/([^\d]+)/", "", $phone);
$orderID = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$shouldPay = str_replace(",", ".", CSalePaySystemAction::GetParamValue("SHOULD_PAY"));
$comment = htmlspecialcharsbx(GetMessage("SALE_COMMENT", array("#ID#" => $orderID)));
$shopID = CSalePaySystemAction::GetParamValue("SHOP_ID");
	
if (strlen($phone) != 10)
{
	?>
	<form method="post" action="<?= POST_FORM_ACTION_URI?>">
		<p><font color="Red"><?= GetMessage("SALE_ERROR_PHONE")?></font></p>
		<input type="text" name="NEW_PHONE" size="30" value="<?= $phone?>" />
		<input type="submit" name="SET_NEW_PHONE" value="<?= GetMessage("SALE_SEND_NEW_PHONE")?>" />
	</form>
	<?
}
else
{
	?>
	<form action="https://w.qiwi.ru/setInetBill<?=((ToUpper(SITE_CHARSET) == "UTF-8") ? "_utf" : "")?>.do" method="post" target="_blank">
		<input type="hidden" name="from" value="<?=CSalePaySystemAction::GetParamValue("SHOP_ID")?>" />
		<input type="hidden" name="to" value="<?= $phone;?>" />
		<input type="hidden" name="lifetime" value="<?= IntVal(CSalePaySystemAction::GetParamValue("BILL_LIFETIME"))?>" />
		<input type="hidden" name="check_agt" value="false" />
		<input type="hidden" name="txn_id" value="<?= $orderID?>" />
		<input type="hidden" name="summ" value="<?= $shouldPay?>" />
		<input type="hidden" name="com" value="<?= $comment?>" />
		<input type="submit" name="go" value="<?= GetMessage("SALE_DO_BILL")?>" />
	</form>
	<?
}
?>