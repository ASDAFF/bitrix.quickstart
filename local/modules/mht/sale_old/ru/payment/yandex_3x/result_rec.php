<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

$orderSumAmount = $_POST["orderSumAmount"];
$orderSumCurrencyPaycash = $_POST["orderSumCurrencyPaycash"];
$orderSumBankPaycash = $_POST["orderSumBankPaycash"];
$action = $_POST["action"];
$orderCreatedDatetime = $_POST["orderCreatedDatetime"];
$paymentType = $_POST["paymentType"];
$customerNumber = IntVal($_POST["customerNumber"]);
$invoiceId = $_POST["invoiceId"];
$md5 = $_POST["md5"];
$paymentDateTime = $_POST["paymentDateTime"];

$bCorrectPayment = True;
if(!($arOrder = CSaleOrder::GetByID($customerNumber)))
{
	$bCorrectPayment = False;
	$code = "200"; //неверные параметры
	$techMessage = "ID заказа неизвестен.";
}

if ($bCorrectPayment)
	CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);

$Sum = CSalePaySystemAction::GetParamValue("SHOULD_PAY");
$Sum = number_format($Sum, 2, ',', '');
$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
$scid = CSalePaySystemAction::GetParamValue("SCID");
$customerNumber = CSalePaySystemAction::GetParamValue("ORDER_ID");
$shopPassword = CSalePaySystemAction::GetParamValue("SHOP_KEY");
$changePayStatus =  trim(CSalePaySystemAction::GetParamValue("CHANGE_STATUS_PAY"));
$head = "";

$strCheck = md5(implode(";", array($action, $orderSumAmount, $orderSumCurrencyPaycash, $orderSumBankPaycash, $shopId, $invoiceId,  $customerNumber, $shopPassword)));

if ($bCorrectPayment && ToUpper($md5) != ToUpper($strCheck))
{
	$bCorrectPayment = False;
	$code = "1"; // ошибка авторизации
}

if ($bCorrectPayment)
{
	if ($action=="checkOrder")
	{
		$head = "checkOrderResponse";

		if(doubleval($arOrder["PRICE"]) == doubleval($orderSumAmount))
		{
			$code = "0";
		}
		else
		{
			$code = "100"; //неверные параметры
			$techMessage = "Сумма заказа не верна.";
		}
	}
	elseif ($action=="paymentAviso")
	{
		$head = "paymentAvisoResponse";

		$strPS_STATUS_DESCRIPTION = "";
		$strPS_STATUS_DESCRIPTION .= "номер плательщика - ".$customerNumber."; ";
		$strPS_STATUS_DESCRIPTION .= "дата платежа - ".$paymentDateTime."; ";
		$strPS_STATUS_MESSAGE = "";

		$arFields = array(
				"PS_STATUS" => "Y",
				"PS_STATUS_CODE" => substr($action, 0, 5),
				"PS_STATUS_DESCRIPTION" => "",
				"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
				"PS_SUM" => $orderSumAmount,
				"PS_CURRENCY" => $orderSumCurrencyPaycash,
				"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
			);

		// You can comment this code if you want PAYED flag not to be set automatically
		if (floatval($arOrder["PRICE"]) == floatval($orderSumAmount))
		{
			if ($changePayStatus == "Y")
			{
				if ($arOrder["PAYED"] == "Y")
				{
					$code = "0";
				}
				else
				{
					if (!CSaleOrder::PayOrder($arOrder["ID"], "Y", true, true))
					{
						$code = "1000";
						$techMessage = "Ошибка оплаты заказа.";
					}
					else
						$code = "0";
				}
			}
		}
		else
		{
			$code = "200"; //неверные параметры
			$techMessage = "Сумма заказа не верна.";
		}

		if(CSaleOrder::Update($arOrder["ID"], $arFields))
			if(strlen($techMessage)<=0 && strlen($code)<=0)
				$code = "0";
	}
	else
	{
		$code = "200"; //неверные параметры
		$techMessage = "Не известен тип запроса.";
	}
}

$APPLICATION->RestartBuffer();
$dateISO = date("Y-m-d\TH:i:s").substr(date("O"), 0, 3).":".substr(date("O"), -2, 2);
header("Content-Type: text/xml");
header("Pragma: no-cache");
$text = "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";

if (strlen($head) > 0) // for common-HTTP 3.0. Will be empty if action is not supported yet or payment is not correct
{
	$text .= "<".$head." performedDatetime=\"".$dateISO."\"";
	if (strlen($techMessage) > 0)
		$text .= " code=\"".$code."\" shopId=\"".$shopId."\" invoiceId=\"".$invoiceId."\" techMessage=\"".$techMessage."\"/>";
	else
		$text .= " code=\"".$code."\" shopId=\"".$shopId."\" invoiceId=\"".$invoiceId."\"/>";
}

echo $text;
die();
?>