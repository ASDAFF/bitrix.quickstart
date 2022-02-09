<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$req = "";
if(strlen($_REQUEST['tx']) > 0) // PDT
{
	$req = 'cmd=_notify-synch';
	$tx_token = $_REQUEST['tx'];
	$auth_token = CSalePaySystemAction::GetParamValue("IDENTITY_TOKEN");
	$req .= "&tx=".$tx_token."&at=".$auth_token;

	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
}
elseif(strlen($_POST['txn_id']) > 0 && $_SERVER["REQUEST_METHOD"] == "POST") // IPN
{
	$tx = trim($_POST["txn_id"]);
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) 
	{
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";	
}
if(strlen($req) > 0)
{
	$domain = "";

	if(CSalePaySystemAction::GetParamValue("TEST") == "Y")
		$domain = "sandbox.";

	if(CSalePaySystemAction::GetParamValue("SSL_ENABLE") == "Y")
		$fp = fsockopen ("ssl://www.".$domain."paypal.com", 443, $errno, $errstr, 30);
	else
		$fp = fsockopen ("www.".$domain."paypal.com", 80, $errno, $errstr, 30);

	if($fp)
	{
		fputs ($fp, $header . $req);
		$res = "";
		$headerdone = false;
		while(!feof($fp)) 
		{
			$line = fgets ($fp, 1024);
			if(strcmp($line, "\r\n") == 0)
				$headerdone = true;
			elseif($headerdone)
				$res .= $line;
		}

		// parse the data
		$lines = explode("\n", $res);
		$keyarray = array();
		if(strcmp ($lines[0], "SUCCESS") == 0)
		{
			for ($i=1; $i<count($lines);$i++)
			{
				list($key,$val) = explode("=", $lines[$i]);
				$keyarray[urldecode($key)] = urldecode($val);
			}
			
			$strPS_STATUS_MESSAGE = "";
			$strPS_STATUS_MESSAGE .= "Name: ".$keyarray["first_name"]." ".$keyarray["last_name"]."; ";
			$strPS_STATUS_MESSAGE .= "Email: ".$keyarray["payer_email"]."; ";
			$strPS_STATUS_MESSAGE .= "Item: ".$keyarray["item_name"]."; ";
			$strPS_STATUS_MESSAGE .= "Amount: ".$keyarray["mc_gross"]."; ";
			
			$strPS_STATUS_DESCRIPTION = "";
			$strPS_STATUS_DESCRIPTION .= "Payment status - ".$keyarray["payment_status"]."; ";
			$strPS_STATUS_DESCRIPTION .= "Payment sate - ".$keyarray["payment_date"]."; ";
			$arOrder = CSaleOrder::GetByID($keyarray["custom"]);
			$arFields = array(
					"PS_STATUS" => "Y",
					"PS_STATUS_CODE" => "-",
					"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
					"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
					"PS_SUM" => $keyarray["mc_gross"],
					"PS_CURRENCY" => $keyarray["mc_currency"],
					"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
					"USER_ID" => $arOrder["USER_ID"],
				);
			$arFields["PAY_VOUCHER_NUM"] = $tx_token;
			$arFields["PAY_VOUCHER_DATE"] = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)));

			if (IntVal($arOrder["PRICE"]) == IntVal($keyarray["mc_gross"])
				 && $keyarray["receiver_email"] == CSalePaySystemAction::GetParamValue("BUSINESS")
				 && $keyarray["payment_status"] == "Completed"
				)
				CSaleOrder::PayOrder($arOrder["ID"], "Y");

			CSaleOrder::Update($arOrder["ID"], $arFields);

			$firstname = $keyarray['first_name'];
			$lastname = $keyarray['last_name'];
			$itemname = $keyarray['item_name'];
			$amount = $keyarray['mc_gross'];
			
			echo "<p><h3>".GetMessage("PPL_T1")."</h3></p>";
			
			echo "<b>".GetMessage("PPL_T2")."</b><br>\n";
			echo "<li>".GetMessage("PPL_T3").": $firstname $lastname</li>\n";
			echo "<li>".GetMessage("PPL_T4").": $itemname</li>\n";
			echo "<li>".GetMessage("PPL_T5").": $amount</li>\n";
		}
		elseif(strcmp ($res, "VERIFIED") == 0)
		{
			$strPS_STATUS_MESSAGE = "";
			$strPS_STATUS_MESSAGE .= GetMessage("PPL_T3").": ".$_POST["first_name"]." ".$_POST["last_name"]."; ";
			$strPS_STATUS_MESSAGE .= "Email: ".$_POST["payer_email"]."; ";
			$strPS_STATUS_MESSAGE .= GetMessage("PPL_T4").": ".$_POST["item_name"]."; ";
			$strPS_STATUS_MESSAGE .= GetMessage("PPL_T5").": ".$_POST["mc_gross"]."; ";
			
			$strPS_STATUS_DESCRIPTION = "";
			$strPS_STATUS_DESCRIPTION .= "Payment status - ".$_POST["payment_status"]."; ";
			$strPS_STATUS_DESCRIPTION .= "Payment sate - ".$_POST["payment_date"]."; ";
			$arOrder = CSaleOrder::GetByID($_POST["custom"]);
			$arFields = array(
					"PS_STATUS" => "Y",
					"PS_STATUS_CODE" => "-",
					"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
					"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
					"PS_SUM" => $_POST["mc_gross"],
					"PS_CURRENCY" => $_POST["mc_currency"],
					"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
					"USER_ID" => $arOrder["USER_ID"],
				);
			$arFields["PAY_VOUCHER_NUM"] = $tx;
			$arFields["PAY_VOUCHER_DATE"] = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)));

			if (IntVal($arOrder["PRICE"]) == IntVal($_POST["mc_gross"])
				 && $_POST["receiver_email"] == CSalePaySystemAction::GetParamValue("BUSINESS")
				 && $_POST["payment_status"] == "Completed"
				 && strlen($arOrder["PAY_VOUCHER_NUM"]) <= 0 
				 && $arOrder["PAY_VOUCHER_NUM"] != $tx
				)
				CSaleOrder::PayOrder($arOrder["ID"], "Y");
				
			if(strlen($arOrder["PAY_VOUCHER_NUM"]) <= 0 || $arOrder["PAY_VOUCHER_NUM"] != $tx)
				CSaleOrder::Update($arOrder["ID"], $arFields);
		}
		else
			echo "<p>".GetMessage("PPL_I1")."</p>";
	}
	else
		echo "<p>".GetMessage("PPL_I2")."</p>";

	fclose ($fp);
}
?>

<?=GetMessage("PPL_I3")?><br /><br /><?=GetMessage("PPL_I4")?>