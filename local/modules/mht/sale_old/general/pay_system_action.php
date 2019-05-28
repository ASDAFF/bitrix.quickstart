<?
IncludeModuleLangFile(__FILE__);

class CAllSalePaySystemAction
{
	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);

		$strSql =
			"SELECT * ".
			"FROM b_sale_pay_system_action ".
			"WHERE ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function CheckFields($ACTION, &$arFields)
	{
		global $DB, $USER;

		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && strlen($arFields["NAME"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPSA_NO_NAME"), "ERROR_NO_NAME");
			return false;
		}
		if ((is_set($arFields, "PAY_SYSTEM_ID") || $ACTION=="ADD") && IntVal($arFields["PAY_SYSTEM_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPSA_NO_CODE"), "ERROR_NO_PAY_SYSTEM_ID");
			return false;
		}
		if ((is_set($arFields, "PERSON_TYPE_ID") || $ACTION=="ADD") && IntVal($arFields["PERSON_TYPE_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPSA_NO_ID_TYPE"), "ERROR_NO_PERSON_TYPE_ID");
			return false;
		}

		if (is_set($arFields, "NEW_WINDOW") && $arFields["NEW_WINDOW"] != "Y")
			$arFields["NEW_WINDOW"] = "N";
		if (is_set($arFields, "HAVE_PAYMENT") && $arFields["HAVE_PAYMENT"] != "Y")
			$arFields["HAVE_PAYMENT"] = "N";
		if (is_set($arFields, "HAVE_ACTION") && $arFields["HAVE_ACTION"] != "Y")
			$arFields["HAVE_ACTION"] = "N";
		if (is_set($arFields, "HAVE_RESULT") && $arFields["HAVE_RESULT"] != "Y")
			$arFields["HAVE_RESULT"] = "N";
		if (is_set($arFields, "HAVE_PREPAY") && $arFields["HAVE_PREPAY"] != "Y")
			$arFields["HAVE_PREPAY"] = "N";
		if (is_set($arFields, "HAVE_RESULT_RECEIVE") && $arFields["HAVE_RESULT_RECEIVE"] != "Y")
			$arFields["HAVE_RESULT_RECEIVE"] = "N";
		if (is_set($arFields, "ENCODING") && strlen($arFields["ENCODING"]) <= 0)
			$arFields["ENCODING"] = false;

		if (is_set($arFields, "PAY_SYSTEM_ID"))
		{
			if (!($arPaySystem = CSalePaySystem::GetByID($arFields["PAY_SYSTEM_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["PAY_SYSTEM_ID"], GetMessage("SKGPSA_NO_PS")), "ERROR_NO_PAY_SYSTEM");
				return false;
			}
		}

		if (is_set($arFields, "PERSON_TYPE_ID"))
		{
			if (!($arPersonType = CSalePersonType::GetByID($arFields["PERSON_TYPE_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["PERSON_TYPE_ID"], GetMessage("SKGPSA_NO_PERS_TYPE")), "ERROR_NO_PERSON_TYPE");
				return false;
			}
		}

		return True;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);
		return $DB->Query("DELETE FROM b_sale_pay_system_action WHERE ID = ".$ID."", true);
	}

	function SerializeParams($arParams)
	{
		return serialize($arParams);
	}

	function UnSerializeParams($strParams)
	{
		$arParams = unserialize($strParams);

		if (!is_array($arParams))
			$arParams = array();

		return $arParams;
	}

	function GetParamValue($key)
	{
		if (
			isset($_REQUEST["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_REQUEST)
			|| isset($_POST["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_POST)
			|| isset($_GET["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_GET)
			|| isset($_SESSION["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_SESSION)
			|| isset($_COOKIE["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_COOKIE)
			|| isset($_SERVER["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_SERVER)
			|| isset($_ENV["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_ENV)
			|| isset($_FILES["SALE_CORRESPONDENCE"]) || array_key_exists("SALE_CORRESPONDENCE", $_FILES)
			|| isset($_REQUEST["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_REQUEST)
			|| isset($_POST["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_POST)
			|| isset($_GET["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_GET)
			|| isset($_SESSION["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_SESSION)
			|| isset($_COOKIE["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_COOKIE)
			|| isset($_SERVER["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_SERVER)
			|| isset($_ENV["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_ENV)
			|| isset($_FILES["SALE_INPUT_PARAMS"]) || array_key_exists("SALE_INPUT_PARAMS", $_FILES)
			)
		{
			return False;
		}

		if($key === "BASKET_ITEMS" && isset($GLOBALS["SALE_INPUT_PARAMS"]["BASKET_ITEMS"]))
		{
			return $GLOBALS["SALE_INPUT_PARAMS"]["BASKET_ITEMS"];
		}
		elseif($key === "TAX_LIST" && isset($GLOBALS["SALE_INPUT_PARAMS"]["TAX_LIST"]))
		{
			return $GLOBALS["SALE_INPUT_PARAMS"]["TAX_LIST"];
		}

		if (!isset($GLOBALS["SALE_CORRESPONDENCE"]) || !is_array($GLOBALS["SALE_CORRESPONDENCE"]))
			return False;

		if (!array_key_exists($key, $GLOBALS["SALE_CORRESPONDENCE"]))
			return False;

		$type = $GLOBALS["SALE_CORRESPONDENCE"][$key]["TYPE"];
		$value = $GLOBALS["SALE_CORRESPONDENCE"][$key]["VALUE"];
		if (strlen($type) > 0)
		{
			if (isset($GLOBALS["SALE_INPUT_PARAMS"])
				&& is_array($GLOBALS["SALE_INPUT_PARAMS"])
				&& array_key_exists($type, $GLOBALS["SALE_INPUT_PARAMS"])
				&& is_array($GLOBALS["SALE_INPUT_PARAMS"][$type])
				&& array_key_exists($value, $GLOBALS["SALE_INPUT_PARAMS"][$type]))
			{
				$res = $GLOBALS["SALE_INPUT_PARAMS"][$type][$value];
			}
			elseif (isset($GLOBALS["SALE_INPUT_PARAMS"]) && ($type == "SELECT" || $type == "RADIO" || $type == "FILE"))
			{
				$res = $GLOBALS["SALE_CORRESPONDENCE"][$key]["VALUE"];
			}
			else
			{
				$res = False;
			}
		}
		else
		{
			/*
			if ((substr($value, 0, 1) == "=") && (strlen($value) > 1))
				eval("\$res=".substr($value, 1).";");
			else*/
			$res = $value;
		}
		return $res;
	}

	function InitParamArrays($arOrder, $orderID = 0, $psParams = "", $relatedData = array())
	{
		if(!is_array($relatedData))
		{
			$relatedData = array();
		}

		$GLOBALS["SALE_INPUT_PARAMS"] = array();
		$GLOBALS["SALE_CORRESPONDENCE"] = array();

		if (!is_array($arOrder) || count($arOrder) <= 0 || !array_key_exists("ID", $arOrder))
		{
			$arOrder = array();

			$orderID = IntVal($orderID);
			if ($orderID > 0)
				$arOrderTmp = CSaleOrder::GetByID($orderID);
			if(!empty($arOrderTmp))
			{
				foreach($arOrderTmp as $k => $v)
				{
					$arOrder["~".$k] = $v;
					$arOrder[$k] = htmlspecialcharsbx($v);
				}
			}
		}

		if (count($arOrder) > 0)
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"] = $arOrder;

		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"] = DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE"]) - DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"]);

		$arDateInsert = explode(" ", $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"]);
		if (is_array($arDateInsert) && count($arDateInsert) > 0)
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT_DATE"] = $arDateInsert[0];
		else
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT_DATE"] = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"];

		if(strlen(trim($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_BILL"])) > 0)
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_BILL_DATE"] = ConvertTimeStamp(MakeTimeStamp($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_BILL"]), 'SHORT');
		else
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_BILL_DATE"] = "";

		$userID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["USER_ID"]);
		if ($userID > 0)
		{
			$dbUser = CUser::GetByID($userID);
			if ($arUser = $dbUser->GetNext())
				$GLOBALS["SALE_INPUT_PARAMS"]["USER"] = $arUser;
		}

		$arCurOrderProps = array();
		if(isset($relatedData["PROPERTIES"]) && is_array($relatedData["PROPERTIES"]))
		{
			$properties = $relatedData["PROPERTIES"];
			foreach ($properties as $key => $value)
			{
				$arCurOrderProps["~".$key] = $value;
				$arCurOrderProps[$key] = htmlspecialcharsEx($value);
			}
		}
		else
		{
			$dbOrderPropVals = CSaleOrderPropsValue::GetList(
					array(),
					array("ORDER_ID" => $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]),
					false,
					false,
					array("ID", "CODE", "VALUE", "ORDER_PROPS_ID", "PROP_TYPE")
				);
			while ($arOrderPropVals = $dbOrderPropVals->Fetch())
			{
				$arCurOrderPropsTmp = CSaleOrderProps::GetRealValue(
						$arOrderPropVals["ORDER_PROPS_ID"],
						$arOrderPropVals["CODE"],
						$arOrderPropVals["PROP_TYPE"],
						$arOrderPropVals["VALUE"],
						LANGUAGE_ID
					);

				foreach ($arCurOrderPropsTmp as $key => $value)
				{
					$arCurOrderProps["~".$key] = $value;
					$arCurOrderProps[$key] = htmlspecialcharsEx($value);
				}
			}
		}

		if (count($arCurOrderProps) > 0)
			$GLOBALS["SALE_INPUT_PARAMS"]["PROPERTY"] = $arCurOrderProps;

		if (strlen($psParams) <= 0)
		{
			$dbPaySysAction = CSalePaySystemAction::GetList(
					array(),
					array(
							"PAY_SYSTEM_ID" => IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PAY_SYSTEM_ID"]),
							"PERSON_TYPE_ID" => IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PERSON_TYPE_ID"])
						),
					false,
					false,
					array("PARAMS")
				);

			if ($arPaySysAction = $dbPaySysAction->Fetch())
			{
				$psParams = $arPaySysAction["PARAMS"];
			}
		}
		$GLOBALS["SALE_CORRESPONDENCE"] = CSalePaySystemAction::UnSerializeParams($psParams);
		foreach($GLOBALS["SALE_CORRESPONDENCE"] as $key => $val)
		{
			$GLOBALS["SALE_CORRESPONDENCE"][$key]["~VALUE"] = $val["VALUE"];
			$GLOBALS["SALE_CORRESPONDENCE"][$key]["VALUE"] = htmlspecialcharsEx($val["VALUE"]);
		}

		if(isset($relatedData["BASKET_ITEMS"]) && is_array($relatedData["BASKET_ITEMS"]))
		{
			$GLOBALS["SALE_INPUT_PARAMS"]["BASKET_ITEMS"] = $relatedData["BASKET_ITEMS"];
		}

		if(isset($relatedData["TAX_LIST"]) && is_array($relatedData["TAX_LIST"]))
		{
			$GLOBALS["SALE_INPUT_PARAMS"]["TAX_LIST"] = $relatedData["TAX_LIST"];
		}
	}

	function IncludePrePaySystem($fileName, $bDoPayAction, &$arPaySysResult, &$strPaySysError, &$strPaySysWarning, $BASE_LANG_CURRENCY = False, $ORDER_PRICE = 0.0, $TAX_PRICE = 0.0, $DISCOUNT_PRICE = 0.0, $DELIVERY_PRICE = 0.0)
	{
		$strPaySysError = "";
		$strPaySysWarning = "";

		$arPaySysResult = array(
				"PS_STATUS" => false,
				"PS_STATUS_CODE" => false,
				"PS_STATUS_DESCRIPTION" => false,
				"PS_STATUS_MESSAGE" => false,
				"PS_SUM" => false,
				"PS_CURRENCY" => false,
				"PS_RESPONSE_DATE" => false,
				"USER_CARD_TYPE" => false,
				"USER_CARD_NUM" => false,
				"USER_CARD_EXP_MONTH" => false,
				"USER_CARD_EXP_YEAR" => false,
				"USER_CARD_CODE" => false
			);

		if ($BASE_LANG_CURRENCY === false)
			$BASE_LANG_CURRENCY = CSaleLang::GetLangCurrency(SITE_ID);

		include($fileName);
	}
}
?>