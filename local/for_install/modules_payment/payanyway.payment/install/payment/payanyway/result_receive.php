<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");


	if(isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_OPERATION_ID']) 
	   && isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE']) 
	   && isset($_REQUEST['MNT_SIGNATURE']) && isset($_REQUEST['orderId']) && IntVal($_REQUEST['orderId']) > 0)
	{
		$bCorrectPayment = True;
		
		if (!($arOrder = CSaleOrder::GetByID(IntVal($_REQUEST['orderId']))))
			$bCorrectPayment = False;

		if ($bCorrectPayment)
			CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
		
		if ($_REQUEST['MNT_SIGNATURE'] != md5($_REQUEST['MNT_ID'] . $_REQUEST['MNT_TRANSACTION_ID'] . $_REQUEST['MNT_OPERATION_ID'] . $_REQUEST['MNT_AMOUNT'] . 
								 $_REQUEST['MNT_CURRENCY_CODE'] . $_REQUEST['MNT_TEST_MODE'] . CSalePaySystemAction::GetParamValue("DATA_INTEGRITY_CODE")))
			$bCorrectPayment = False;

		if ($bCorrectPayment)
		{
			$arFields = array(
					"PS_STATUS" => "Y",
					"PS_STATUS_CODE" => "-",
					"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
					"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
					"PS_SUM" => $_REQUEST['MNT_AMOUNT'],
					"PS_CURRENCY" => "",
					"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
					"USER_ID" => $arOrder["USER_ID"]
				);

			// You can comment this code if you want PAYED flag not to be set automatically
			if ($arOrder["PRICE"] == $_REQUEST['MNT_AMOUNT'])
			{
				$arFields["PAYED"] = "Y";
				$arFields["DATE_PAYED"] = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)));
				$arFields["EMP_PAYED_ID"] = false;
			}

			if(CSaleOrder::Update($arOrder["ID"], $arFields)) 
				echo "SUCCESS";
			
		} else echo "FAIL";
	} else echo "FAIL";
	
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");	
?>