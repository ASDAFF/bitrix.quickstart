<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
	include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
	if(!CModule::IncludeModule("onpay.sale")) return;

	$order_id = CSalePaySystemAction::GetParamValue("ORDER_ID");
	$sum = floatval(CSalePaySystemAction::GetParamValue("SHOULD_PAY"));
	$currency = CSalePaySystemAction::GetParamValue("CURRENCY");
	$user_email = CSalePaySystemAction::GetParamValue("EMAIL");
	
	$login = COnpayPayment::GetLogin();
	$key = COnpayPayment::GetApiInKey();
	$curr = COnpayPayment::GetWMCurrency($currency);
	$path = str_replace(array('#ORDER_ID#', '#ID#'), array($order_id, $order_id), COnpayPayment::GetSuccessUrl());
	
	$sum_for_md5 = COnpayPayment::toFloat($sum);
	$convert = COnpayPayment::GetConvert() == "Y" ? "yes" : "no";
	$form_id = intval(COnpayPayment::GetFormId());
	$form_id = $form_id ? $form_id : COnpayPayment::$_df_form_id;
	$pay_mode = COnpayPayment::$_df_pay_mode;
	
	$md5check = md5("{$pay_mode};{$sum_for_md5};{$curr};{$order_id};{$convert};{$key}"); //Создаем проверочную строку, которая защищает платежную ссылку от изменений
	$url = COnpayPayment::$pay_url."{$login}?f={$form_id}&pay_mode={$pay_mode}&pay_for={$order_id}&price={$sum}&ticker={$curr}&convert={$convert}&md5={$md5check}&user_email=".urlencode($user_email)."&url_success=".urlencode($path); //Формируем платежную ссылку
	if(COnpayPayment::GetPriceFinal() == "Y") {
		$url .= "&price_final=true";
	}
	if($lang = COnpayPayment::GetLang()) {
		$url .= "&ln={$lang}";
	}
	if($ext_params = COnpayPayment::GetExtParams()) {
		$url .= "&{$ext_params}";
	}
	
	if(!COnpayPayment::CheckOrderPayed($order_id)) {

?>
<p><b><?=GetMessage("ONPAY.SALE_PAYMENT_ONPAY__ORDER_CAPTION", array("#SUM#" => $sum,"#CURRENCY#" => $currency,"#ORDER_ID#" => $order_id))?></b><p>

<form action="<?=$url;?>" method="post" target="_blank">
<table><tr><td><img src="<?=COnpayPayment::$logo_url?>" style="float:left;margin-right:10px;" /><input type="submit" name="submit"  value="<?=GetMessage("ONPAY.SALE_PAYMENT_ONPAY__FORM_SUBMIT")?>" /><br style="clear:left;" /></td></tr></table>
</form>
<?	} else {
		echo "<p><b>".GetMessage("ONPAY.SALE_PAYMENT_ONPAY__ORDER_PAYED_CAPTION")."</b></p>";
	}?>