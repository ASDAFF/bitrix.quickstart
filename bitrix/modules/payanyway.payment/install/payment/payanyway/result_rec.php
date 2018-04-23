<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "invoice") {
	
	$arOrder = CSaleOrder::GetByID(IntVal($_REQUEST['MNT_TRANSACTION_ID']));
	CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);


	$payment_method = $_REQUEST['paymentSystem'];
	include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/".$payment_method."/", "/payment.php"));

	require_once(dirname(__FILE__).'/MonetaAPI/MonetaWebService.php');
	$payment_server = CSalePaySystemAction::GetParamValue("MNT_PAYMENT_SERVER");
	switch ($payment_server)
	{
		case "demo.moneta.ru":
			$service = new MonetaWebService("https://demo.moneta.ru/services.wsdl", CSalePaySystemAction::GetParamValue('PAYANYWAY_LOGIN'), CSalePaySystemAction::GetParamValue('PAYANYWAY_PASSWORD'));
			break;
		case "www.payanyway.ru":
			$service = new MonetaWebService("https://service.payanyway.ru/services.wsdl", CSalePaySystemAction::GetParamValue('PAYANYWAY_LOGIN'), CSalePaySystemAction::GetParamValue('PAYANYWAY_PASSWORD'));
			break;
	}

	try
	{
		$totalAmount = $_REQUEST['MNT_AMOUNT']." ".$_REQUEST['MNT_CURRENCY_CODE'];
		$fee = "-";
		
		// запрос стоимости и комиссии
		if (isset($_REQUEST['paymentSystem_accountId'])) {
			$transactionRequestType = new MonetaForecastTransactionRequest();
			$transactionRequestType->payer = $_REQUEST['paymentSystem_accountId'];
			$transactionRequestType->payee = $_REQUEST['MNT_ID'];
			$transactionRequestType->amount = $_REQUEST['MNT_AMOUNT'];
			$transactionRequestType->clientTransaction = $_REQUEST['MNT_TRANSACTION_ID'];
			$forecast = $service->ForecastTransaction($transactionRequestType);
			$totalAmount = number_format($forecast->payerAmount,2,'.','')." ".$forecast->payerCurrency;
			$fee = number_format($forecast->payerFee,2,'.','')." ".$forecast->payerCurrency;
		}

		$request = new MonetaInvoiceRequest();
		if (isset($_REQUEST['paymentSystem_accountId']))
			$request->payer = $_REQUEST['paymentSystem_accountId'];
		$request->payee = $_REQUEST['MNT_ID'];
		$request->amount = $_REQUEST['MNT_AMOUNT'];
		$request->clientTransaction = $_REQUEST['MNT_TRANSACTION_ID'];

		$operationInfo = new MonetaOperationInfo();

		if ($payment_method == 'payanyway_post')
		{
			$a = new MonetaKeyValueAttribute();
			$a->key = 'mailofrussiaindex';
			$a->value = $_REQUEST['additionalParameters_mailofrussiaSenderIndex'];
			$operationInfo->addAttribute($a);
			$a1 = new MonetaKeyValueAttribute();
			$a1->key = 'mailofrussiaregion';
			$a1->value = iconv(LANG_CHARSET,"UTF-8",$_REQUEST['additionalParameters_mailofrussiaSenderRegion']);
			$operationInfo->addAttribute($a1);
			$a2 = new MonetaKeyValueAttribute();
			$a2->key = 'mailofrussiaaddress';
			$a2->value = iconv(LANG_CHARSET,"UTF-8",$_REQUEST['additionalParameters_mailofrussiaSenderAddress']);
			$operationInfo->addAttribute($a2);
			$a3 = new MonetaKeyValueAttribute();
			$a3->key = 'mailofrussianame';
			$a3->value = iconv(LANG_CHARSET,"UTF-8",$_REQUEST['additionalParameters_mailofrussiaSenderName']);
			$operationInfo->addAttribute($a3);
		} 
		elseif ($payment_method == 'payanyway_euroset')
		{
			$a = new MonetaKeyValueAttribute();
			$a->key = 'rapidamphone';
			$a->value = $_REQUEST['additionalParameters_rapidaPhone'];
			$operationInfo->addAttribute($a);
		}
		$request->operationInfo = $operationInfo;

		$response = $service->Invoice($request);
		$operation_id = $response->transaction;
		if ($payment_method == 'payanyway_euroset') {
			$response1 = $service->GetOperationDetailsById($response->transaction);
			foreach ($response1->operation->attribute as $attr) {
				if ($attr->key == 'rapidatid') {
					$transaction_id = $attr->value;
				}
			}
		} else {
			$transaction_id = $response->transaction;
		}

		$title = GetMessage("PAW_INVOICE_CREATED_TTL");
		$invoice =  array( 'status' => $response->status,
						   'system' => $payment_method,
						   'transaction' => str_pad($transaction_id, 10, "0", STR_PAD_LEFT),
						   'operation' => $operation_id,
						   'amount' => $totalAmount,
						   'fee' => $fee,
						   'unitid' => $_REQUEST['paymentSystem_unitId'],
						   'payment_server' => $payment_server);
	}
	catch (Exception $e)
	{
		$title = GetMessage("PAW_INVOICE_ERROR_TTL");
		$invoice = array( 'status' => 'FAILED',
						  'error_message' => $e->getMessage());
	}			

	$APPLICATION->SetTitle($title);

	if ($invoice['status'] !== 'FAILED')
	{
		echo str_replace(
				array('%transaction%', '%operation%', '%amount%', '%unitid%', '%payment_server%', '%fee%'), 
				array($invoice['transaction'], $invoice['operation'], $invoice['amount'], $invoice['unitid'], $invoice['payment_server'], $invoice['fee']), 
				GetMessage("PAW_INVOICE_CREATED"));
	}
	else
	{
		echo "<p>".iconv("UTF-8",LANG_CHARSET,$invoice['error_message'])."</p>";
	}
	
} else {
	define("NO_KEEP_STATISTIC", true);
	define("NOT_CHECK_PERMISSIONS", true);

	$bCorrectPayment = true;
	$changePayStatus = false;
	$responseCode = 500;

	/* orderId для старой схемы оформления заказа */
	if (!($arOrder = CSaleOrder::GetByID(IntVal($_REQUEST['MNT_TRANSACTION_ID']))) &&
		!($arOrder = CSaleOrder::GetByID(IntVal($_REQUEST['orderId']))))
	{
		$bCorrectPayment = false;
	}

	if ($bCorrectPayment) {
		if (isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE']) && isset($_REQUEST['MNT_SIGNATURE']))  {
			if (_checkSignature()){
				$amount = (float) $_REQUEST['MNT_AMOUNT'];
				if ( !isset($_REQUEST['MNT_COMMAND']) && ($arOrder["PRICE"] == $amount) ) {
					$arFields = array(
							"PS_STATUS" => "Y",
							"PS_STATUS_CODE" => "200",
							"PS_STATUS_DESCRIPTION" => "",
							"PS_STATUS_MESSAGE" => GetMessage('PAYANYWAY_PAYMENT_CONFIRMED'),
							"PS_SUM" => $_REQUEST['MNT_AMOUNT'],
							"PS_CURRENCY" => $_REQUEST['MNT_CURRENCY_CODE'],
							"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
						);
					if (CSaleOrder::Update($arOrder["ID"], $arFields)) {
						CSaleOrder::PayOrder($arOrder["ID"], "Y");
						if (CSalePaySystemAction::GetParamValue("CHANGE_ORDER_STATUS") == "Y") {
							CSaleOrder::StatusOrder($arOrder["ID"], "P");
						}
						$responseCode = 200;
						$changePayStatus = true;
					}
				} else {
					switch($_REQUEST['MNT_COMMAND']) {
						case "CHECK":
							if ($arOrder["CANCELED"] == "Y" || $arOrder["PAYED"] == "Y")
								$responseCode = 500;
							else
								$responseCode = 402;
							break;
						case "CANCELLED_CREDIT":
							/*отмена зачисления*/
							if (CSaleOrder::CancelOrder($arOrder["ID"], "Y", "отменена платежной системой"))
								$responseCode = 200;
							break;
						default:
							$responseCode = 200;
							break;
					}
				}
			}
		}
	}
	
	$APPLICATION->RestartBuffer();

	header("Content-type: application/xml");
	echo _getXMLResponse($responseCode);
	exit;

}

function _checkSignature() {
	$params = '';
	if (isset($_REQUEST['MNT_COMMAND'])) $params .= $_REQUEST['MNT_COMMAND'];
	$params .= $_REQUEST['MNT_ID'] . $_REQUEST['MNT_TRANSACTION_ID'];
	if (isset($_REQUEST['MNT_OPERATION_ID'])) $params .= $_REQUEST['MNT_OPERATION_ID'];
	if (isset($_REQUEST['MNT_AMOUNT'])) $params .= $_REQUEST['MNT_AMOUNT'];
	$params .= $_REQUEST['MNT_CURRENCY_CODE'];
	if (isset($_REQUEST['MNT_SUBSCRIBER_ID'])) $params .= $_REQUEST['MNT_SUBSCRIBER_ID'];
	$params .= $_REQUEST['MNT_TEST_MODE'];
	
	$signature = md5($params . CSalePaySystemAction::GetParamValue("DATA_INTEGRITY_CODE"));
	
	if(strcasecmp($signature, $_REQUEST['MNT_SIGNATURE'] ) == 0) {
		return true;
	}
	return false;
}

function _getXMLResponse($resultCode)
{
	$signature = md5($resultCode . $_REQUEST['MNT_ID'] . $_REQUEST['MNT_TRANSACTION_ID'] . CSalePaySystemAction::GetParamValue("DATA_INTEGRITY_CODE"));
	$result = '<?xml version="1.0" encoding="UTF-8" ?>';
	$result .= '<MNT_RESPONSE>';
	$result .= '<MNT_ID>' . $_REQUEST['MNT_ID'] . '</MNT_ID>';
	$result .= '<MNT_TRANSACTION_ID>' . $_REQUEST['MNT_TRANSACTION_ID'] . '</MNT_TRANSACTION_ID>';
	$result .= '<MNT_RESULT_CODE>' . $resultCode . '</MNT_RESULT_CODE>';
	$result .= '<MNT_SIGNATURE>' . $signature . '</MNT_SIGNATURE>';
	$result .= '</MNT_RESPONSE>';
	return $result;
}
