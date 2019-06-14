<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

//$APPLICATION->SetTitle("PayAnyWay invoice");

CModule::IncludeModule("sale");
$arOrder = CSaleOrder::GetByID(IntVal($_REQUEST['orderId']));
CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);


$payment_method = $_REQUEST['paymentSystem'];
include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/".$payment_method."/", "/payment.php"));

require_once(dirname(__FILE__).'/MonetaAPI/MonetaWebService.php');
switch (CSalePaySystemAction::GetParamValue("MNT_PAYMENT_SERVER"))
{
	case "demo.moneta.ru":
		$service = new MonetaWebService("https://demo.moneta.ru/services.wsdl", CSalePaySystemAction::GetParamValue('PAYANYWAY_LOGIN'), CSalePaySystemAction::GetParamValue('PAYANYWAY_PASSWORD'));
		break;
	case "www.payanyway.ru":
		$service = new MonetaWebService("https://www.moneta.ru/services.wsdl", CSalePaySystemAction::GetParamValue('PAYANYWAY_LOGIN'), CSalePaySystemAction::GetParamValue('PAYANYWAY_PASSWORD'));
		break;
}

try
{
	$request = new MonetaInvoiceRequest();
	$request->payer = $_REQUEST['paymentSystem_accountId'];
	$request->payee = $_REQUEST['MNT_ID'];
	$request->amount = $_REQUEST['MNT_AMOUNT'];
	$request->clientTransaction = $_REQUEST['MNT_TRANSACTION_ID'];

	$operationInfo = new MonetaOperationInfo();
	$a = new MonetaKeyValueAttribute();
	$a->key = 'CUSTOMURLPARAMETERS';
	$a->value = "orderId=".$_REQUEST['orderId'];
	$operationInfo->addAttribute($a);
	
	if ($payment_method == 'payanyway_post')
	{
		$a1 = new MonetaKeyValueAttribute();
		$a1->key = 'mailofrussiaindex';
		$a1->value = $_REQUEST['additionalParameters_mailofrussiaSenderIndex'];
		$operationInfo->addAttribute($a1);
		$a2 = new MonetaKeyValueAttribute();
		$a2->key = 'mailofrussiaaddress';
		$a2->value = $_REQUEST['additionalParameters_mailofrussiaSenderAddress'];
		$operationInfo->addAttribute($a2);
		$a3 = new MonetaKeyValueAttribute();
		$a3->key = 'mailofrussianame';
		$a3->value = $_REQUEST['additionalParameters_mailofrussiaSenderName'];
		$operationInfo->addAttribute($a3);
	} 
	elseif ($payment_method == 'payanyway_euroset')
	{
		$a1 = new MonetaKeyValueAttribute();
		$a1->key = 'rapidamphone';
		$a1->value = $_REQUEST['additionalParameters_rapidaPhone'];
		$operationInfo->addAttribute($a1);
	}
	$request->operationInfo = $operationInfo;

	$response = $service->Invoice($request);
	if ($payment_method == 'payanyway_euroset')
	{
		$response1 = $service->GetOperationDetailsById($response->transaction);
		foreach ($response1->operation->attribute as $attr)
		{
			if ($attr->key == 'rapidatid')
			{
				$transaction_id = $attr->value;
			}
		}
	}
	else
	{
		$transaction_id = $response->transaction;
	}
	
	$title = GetMessage("PAW_INVOICE_CREATED_TTL");
	$invoice =  array( 'status' => $response->status,
					   'system' => $payment_method,
					   'transaction' => str_pad($transaction_id, 10, "0", STR_PAD_LEFT),
					   'amount' => $_REQUEST['MNT_AMOUNT']." ".$_REQUEST['MNT_CURRENCY_CODE'] );
}
catch (Exception $e)
{
	$title = GetMessage("PAW_INVOICE_ERROR_TTL");
	$invoice = array( 'status' => 'FAILED',
					  'error_message' => $e->getMessage());
}			

$APPLICATION->SetTitle($title);

if ($invoice['status'] != 'FAILED')
{
	if (in_array($payment_method, array('payanyway_banktransfer', 'payanyway_post')))
	{
?><p><?php echo str_replace('%transaction%', $invoice['transaction'], GetMessage("PAW_INVOICE_CREATED")); ?></p><?php
	}
	else
	{
?><h3><?php echo GetMessage("PAW_INVOICE_CREATED_1")." ".$invoice['transaction']; ?></h3><?php
?><p><?php echo GetMessage("PAW_INVOICE_CREATED_2"); ?></p><?php
?><p><?php echo $invoice['transaction']; ?></p><?php
?><p><?php echo GetMessage("PAW_INVOICE_CREATED_3")." ".$invoice['amount']; ?></p><?php
	}
}
else
{
?><p><?php echo $invoice['error_message'];?></p><?php
}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>