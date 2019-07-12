<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$inv_id = IntVal($_REQUEST["inv_id"]);
if(IntVal($inv_id)>0)
{
	$bCorrectPayment = True;

	$inv_id = IntVal($_REQUEST["inv_id"]);
	$out_summ = $_REQUEST["OutSum"];
	$crc = $_REQUEST["SignatureValue"];
	
	if (!($arOrder = CSaleOrder::GetByID(IntVal($inv_id))))
		$bCorrectPayment = False;

	if ($bCorrectPayment)
		CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);

	$mrh_pass2 =  CSalePaySystemAction::GetParamValue("ShopPassword2");
	
	$strCheck = md5($out_summ.":".$inv_id.":".$mrh_pass2);
	
	if ($bCorrectPayment && strtoupper($CHECKSUM) != strtoupper($strCheck))
		$bCorrectPayment = False;
	
	if($bCorrectPayment)
	{
		$arFields = array(
				"PS_STATUS" => "Y",
				"PS_STATUS_CODE" => "-",
				"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
				"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
				"PS_SUM" => $out_summ,
				"PS_CURRENCY" => "",
				"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
			);

		// You can comment this code if you want PAYED flag not to be set automatically
		if ($arOrder["PRICE"] == $out_summ)
		{
			CSaleOrder::PayOrder($arOrder["ID"], "Y");
		}

		if(CSaleOrder::Update($arOrder["ID"], $arFields))
			echo "OK";
	
	}
}
?>