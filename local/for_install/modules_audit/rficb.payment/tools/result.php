<?
define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);
function ToLog( $str )
{
	$log = fopen( $_SERVER['DOCUMENT_ROOT'] . '/log_rfi.txt', 'a' );
	fwrite( $log, date( 'Y.m.d H:i:s' ) . ":\n" . $str . "\n---------------\n\n" );
	fclose( $log );
}
if(!require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"))
    die('prolog_before.php not found!');
if(!CModule::IncludeModule("sale")) die('sale module not found');
IncludeModuleLangFile(__FILE__);
if(!CModule::IncludeModule("rficb.payment")) die('rficb.payment module not found');
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    global $APPLICATION;
    $module_id = "rficb.payment";
    $request = $_POST;
	ToLog(print_r($request,true));
    $transaction_id = $request["tid"];
    $order_id = intval($request["order_id"]);
	if($order_id == 0) $order_id = intval($request["comment"]);
	$strStatus = "";
    if (!($arOrder = CSaleOrder::GetByID($order_id))){
        AddMessage2Log(GetMessage("RFICB.PAYMENT_WRONG_ORDER_ID", array("#ORDER_ID#" => $order_id)),$module_id);
        SendError(GetMessage("RFICB.PAYMENT_WRONG_ORDER_ID", array("#ORDER_ID#" => $order_id)),$module_id);
        mail('romanof76@gmail.com',$_SERVER["SERVER_NAME"],GetMessage("RFICB.PAYMENT_WRONG_ORDER_ID"));
    } else {
        if(isset($request["command"])) {
            $check = (CRficbPayment::VerifyCheckHold($request, $arOrder["LID"]))?1:0;
            if ($check) {
                if($request["command"] == 'funds_blocked') {
                    $status = CSaleOrder::StatusOrder($arOrder["ID"],CRficbPayment::GetHoldStatus($arOrder["LID"]));
					if($mail = CRficbPayment::GetHoldMail($arOrder["LID"])) {
						mail($mail,"hold",GetMessage("RFICB.MAIL_HOLD_STATUS", array("#ORDER_ID#" => $order_id)));
					}
                 } 
            }
        }
        else { 
            if (!(CRficbPayment::VerifyCheck($request, $arOrder["LID"]))) {
                $strStatus .= GetMessage("RFICB.PAYMENT_PAYMENT_ID", array("#TRANSACTION_ID#" => $transaction_id));
                $strStatus .= GetMessage("RFICB.PAYMENT_SIGNS_DONT_MATCH", array("#ORDER_ID#" => $order_id));
                $arFields = array(
                    "PS_STATUS" => "N",
                    "PS_STATUS_MESSAGE" => $strStatus,
                    "PS_RESPONSE_DATE" =>date("d-m-Y H:i:s"),
                    "USER_ID" => $arOrder["USER_ID"]
                );
                CSaleOrder::Update($arOrder["ID"], $arFields);
            } else {
				$strStatus .= GetMessage("RFICB.PAYMENT_PAYMENT_ID", array("#TRANSACTION_ID#" => $transaction_id));
				$strStatus .= GetMessage("RFICB.PAYMENT_PAYMENT_FOR_ORDER_SUCCESFUL", array("#ORDER_ID#" => $order_id));
                if ($arOrder["PRICE"] <= $request["system_income"]){
                    $payed = "Y";
                    CSaleOrder::PayOrder($arOrder["ID"], "Y");
                } else {
                    $payed = "N";
                    $strStatus .= GetMessage("RFICB.PAYMENT_NOT_FULL_PAYMENT");
                }
                $arFields = array(
                    "PAYED" => $payed,
                    "PS_STATUS" => "Y",
                    "STATUS_ID" => "P",
                    "SUM_PAID" => $request["system_income"],
                    "PS_STATUS_MESSAGE" => $strStatus,
                    "PS_SUM" => $request["system_income"],
                );
				CSaleOrder::Update($arOrder["ID"], $arFields);
                $mess .= serialize($arFields)."\r\n";
			}
		} 
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
