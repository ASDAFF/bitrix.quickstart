<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/qiwi.php"));

$orderID = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$shopID = CSalePaySystemAction::GetParamValue("SHOP_ID");
$password = CSalePaySystemAction::GetParamValue("SHOP_PASS");
$changePayStatus =  trim(CSalePaySystemAction::GetParamValue("CHANGE_STATUS_PAY"));

$query = '<?xml version="1.0" encoding="'.LANG_CHARSET.'"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:checkBill xmlns:ns2="http://server.ishop.mw.ru/"><login>'.$shopID.'</login><password>'.$password.'</password><txn>'.$orderID.'</txn></ns2:checkBill></soap:Body></soap:Envelope>';

$host = "ishop.qiwi.ru";
$port = 80;
$path = "/services/ishop";

$fp = @fsockopen($host, $port, $errnum, $errstr, 30);
if ($fp)
{ 
	$content = "";
	$out = "";
	$out .= "POST ".$path." HTTP/1.1\r\n";
	$out .= "Host: ".$host." \r\n";
	$out .= "Content-Type: application/soap+xml; charset=".LANG_CHARSET."\r\n";
	$out .= "Content-length: ".strlen($query)."\r\n\r\n";
	$out .= $query;
	$out .= "\r\n";
	fputs($fp, $out);
	while(!feof($fp)) $content.=fgets($fp, 128);
	fclose($fp);
	
	if(strlen($content) > 0)
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
		$objXML = new CDataXML();
		$objXML->LoadString($content);
		$arResult = $objXML->GetArray();

		if(!empty($arResult))
		{
			$method = $arResult["Envelope"]["#"]["Body"][0]["#"]["checkBillResponse"];
			if(!empty($method))
			{
				if($arOrder = CSaleOrder::GetByID($orderID))
				{
					$phone = $method[0]["#"]["user"][0]["#"];
					$amount = $method[0]["#"]["amount"][0]["#"];
					$date = $method[0]["#"]["date"][0]["#"];
					$status = $method[0]["#"]["status"][0]["#"];
					
					//$strPS_STATUS_DESCRIPTION = GetMessage("SALE_STATUS")." - ".$status." (".GetMessage("CLASS_STATUS_".$status).")"." ; ";
					$strPS_STATUS_DESCRIPTION = GetMessage("SALE_STATUS_PHONE")." - ".$phone."; ";
					$strPS_STATUS_DESCRIPTION .= GetMessage("SALE_STATUS_AMOUNT")." - ".$amount."; ";
					$strPS_STATUS_DESCRIPTION .= GetMessage("SALE_STATUS_DATE")." - ".$date."; ";
					$strPS_STATUS_MESSAGE = GetMessage("CLASS_STATUS_".$status);

					$arFields = array(
							"PS_STATUS" => ($status == 60 ? "Y" : "N"),
							"PS_STATUS_CODE" => $status,
							"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
							"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
							"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
						);
					if($status == 60 && DoubleVal($arOrder["PRICE"]) == DoubleVal($amount) && $changePayStatus == "Y")
						CSaleOrder::PayOrder($orderID, "Y", true, true);

					CSaleOrder::Update($orderID, $arFields);
				}
			}
		}
	}
}

?>