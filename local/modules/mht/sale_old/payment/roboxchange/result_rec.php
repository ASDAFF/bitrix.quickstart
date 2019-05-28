<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
$inv_id = IntVal($_REQUEST["InvId"]);

if($inv_id > 0)
{
	$bCorrectPayment = True;

	$out_summ = trim($_REQUEST["OutSum"]);
	$crc = trim($_REQUEST["SignatureValue"]);
	
	if (!($arOrder = CSaleOrder::GetByID($inv_id)))
		$bCorrectPayment = False;

	if ($bCorrectPayment)
		CSalePaySystemAction::InitParamArrays($arOrder, $inv_id);

	$changePayStatus =  trim(CSalePaySystemAction::GetParamValue("CHANGE_STATUS_PAY"));
	$mrh_pass2 =  CSalePaySystemAction::GetParamValue("ShopPassword2");
	$strCheck = md5($out_summ.":".$inv_id.":".$mrh_pass2);

	if ($bCorrectPayment && ToUpper($crc) != ToUpper($strCheck))
		$bCorrectPayment = False;
	
	if($bCorrectPayment)
	{
		$strPS_STATUS_DESCRIPTION = GetMessage('SALE_RES_NUMBER').": ".$inv_id;
		$strPS_STATUS_DESCRIPTION .= "; ".GetMessage('SALE_RES_DATEPAY').": ".date("d.m.Y H:i:s");
		if (isset($_REQUEST["IncCurrLabel"]) && strlen($_REQUEST["IncCurrLabel"]) > 0)
			$strPS_STATUS_DESCRIPTION .= "; ".GetMessage('SASP_RES_PAY_TYPE').": ".$_REQUEST["IncCurrLabel"];
		
		$strPS_STATUS_MESSAGE = GetMessage('SASP_RES_PAYED');
		
		$arFields = array(
				"PS_STATUS" => "Y",
				"PS_STATUS_CODE" => "-",
				"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
				"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
				"PS_SUM" => $out_summ,
				"PS_CURRENCY" => $arOrder["CURRENCY"],
				"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
			);

		if (roundEx(($arOrder["PRICE"]-$arOrder["SUM_PAID"]), 2) == roundEx($out_summ, 2) && $changePayStatus == "Y")
			CSaleOrder::PayOrder($arOrder["ID"], "Y");
		
		$APPLICATION->RestartBuffer();
		if(CSaleOrder::Update($arOrder["ID"], $arFields))
			echo "OK".$arOrder["ID"];
		die();
	}
}
?>