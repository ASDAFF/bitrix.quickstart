<?define('STOP_STATISTICS', true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(dirname(__FILE__)."/../include.php");
include(dirname(__FILE__)."/../constants.php");
IncludeModuleLangFile(dirname(__FILE__)."/status.php");
IncludeModuleLangFile(__FILE__);
$arRights = $obModule->GetGroupRight();
$arReturn = Array();
if ($arRights > "D") 
{
	
	if($iOrderID = IntVal($_REQUEST["ID"]))
	{
		if($obModule->CheckSiteRights(Array("ORDER_ID"=>$iOrderID)))
		{
			switch($_REQUEST["TYPE"])
			{
				case "approve":
					$arReturn = $obModule->ConfirmOrder($iOrderID,false,$_REQUEST["COURIER_MODE"]);
					if($arReturn["status"]=="FAILED")
					{
						$sError = $arReturn["result"];
					}
					if($ex = $APPLICATION->GetException())
					{
						$sError = $ex->GetString();
					}
				break;
				case "print":
					$arReturn = $obModule->GetContract($iOrderID);
					if($arReturn["status"]=="FAILED")
					{
						$sError = $arReturn["result"];
					}
					elseif($ex = $APPLICATION->GetException())
					{
						$sError = $ex->GetString();
					}
					else
					{
						$obTCSOrder->Update($iOrderID,Array("PRINTED"=>"Y"));
						$arResult = Array(
							"status" => "ok",
							"show_document"=>1,
							"order_id"=>$iOrderID,
							"ID"=>$iOrderID
						);	
						$_SESSION["TCSBANK"]["ORDER_PDF"][$iOrderID] = $arReturn["result"];
						echo $obModule->PHPArrayToJS($arResult);					
						exit();
					}
				break;
				case "subscribe":
					if(IntVal($_REQUEST["SUBSCRIBE"]))
					{
						$bOK = $obModule->OrderCompleted($iOrderID);
					}
					else
					{
						$bOK = $obModule->CancelOrder($iOrderID,false, $_REQUEST["REASON"]);
					}

					if(!$bOK)		
					{
						$sError = $obModule->LAST_ERROR;
					}
				break;
				case "return":
					$arReturn = $obModule->ReturnOrder($iOrderID,$_REQUEST["RETURNED_AMOUNT"], $_REQUEST["CASH_RETURNED_TO_CUSTOMER"]);

					if($arReturn["status"]=="FAILED")
					{
						$sError = $arReturn["result"];
					}
					elseif($ex = $APPLICATION->GetException())
					{
						$sError = $ex->GetString();
					}
					elseif(!$arReturn)
					{
						$sError = $obModule->LAST_ERROR;
					}
					else
					{
						$arResult = Array(
							"status" => "ok",
							"show_document"=>1,
							"order_id"=>$iOrderID,
							"ID"=>$iOrderID
						);	
						$_SESSION["TCSBANK"]["ORDER_PDF"][$iOrderID] = $arReturn["result"];
						echo $obModule->PHPArrayToJS($arResult);					
						exit();
					}					

				break;
				case "reform":
					$bOK = $obModule->ReformOrder($iOrderID,$_REQUEST["DOWN_PAYMENT"], $_REQUEST["PAYMENT_COUNT"]);
					if(!$bOK)		
					{
						$sError = $obModule->LAST_ERROR;
					}
				break;
				case "comment":
					$sText = $obModule->Decode(trim($_REQUEST["TEXT"]));
					if(!strlen($sText))
					{
						$sError = GetMessage("TCS_EMPTY_COMMENT");
					}
					else
					{
						$obOrder = new CTCSOrder;
						if($obOrder->Update($iOrderID, Array("COMMENT"=>$sText)))		
						{
							$sError = $obOrder->LAST_ERROR;
						}
					}

				break;
			
			
				default:
					$sError = GetMessage("TCS_UNKNOWN_OPERATION");
				break;
			}
		}
		else $sError = GetMessage("TCS_INVALID_SITE",Array("ORDER_ID"=>$iOrderID));
	}
	else $sError = GetMessage("TCS_WRONG_ID");
}
else $sError = GetMessage("TCS_NO_RIGHTS");

if($sError)
{
	$arResult = Array(
		"status" => "error",
		"message" => $sError,
		
	);
	$arResult["ID"]=$iOrderID;
}
else
{
	$arResult["status"]="ok";
	$arResult["ID"]=$iOrderID;
}

echo $obModule->PHPArrayToJS($arResult);
?>