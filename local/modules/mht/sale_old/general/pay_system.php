<?
IncludeModuleLangFile(__FILE__);

class CAllSalePaySystem
{
	static function DoProcessOrder(&$arOrder, $paySystemId, &$arErrors)
	{
		if (intval($paySystemId) > 0)
		{
			$arPaySystem = array();

			$dbPaySystem = CSalePaySystem::GetList(
				array("SORT" => "ASC", "PSA_NAME" => "ASC"),
				array(
					"ACTIVE" => "Y",
					"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"],
					"PSA_HAVE_PAYMENT" => "Y"
				)
			);

			while ($arPaySystem = $dbPaySystem->Fetch())
			{
				if ($arPaySystem["ID"] == $paySystemId)
				{
					$arOrder["PAY_SYSTEM_ID"] = $paySystemId;

					$arOrder["PAY_SYSTEM_PRICE"] = CSalePaySystemsHelper::getPSPrice(
						$arPaySystem,
						$arOrder["ORDER_PRICE"],
						$arOrder["PRICE_DELIVERY"],
						$arOrder["DELIVERY_LOCATION"]
					);
					break;
				}
			}

			if (empty($arPaySystem))
			{
				$arErrors[] = array("CODE" => "CALCULATE", "TEXT" => GetMessage('SKGPS_PS_NOT_FOUND'));
			}
		}
	}

	public static function DoLoadPaySystems($personType, $deliveryId = 0, $arDeliveryMap = null)
	{
		$arResult = array();

		$arFilter = array(
			"ACTIVE" => "Y",
			"PERSON_TYPE_ID" => $personType,
			"PSA_HAVE_PAYMENT" => "Y"
		);

		// $arDeliveryMap = array(array($deliveryId => 8), array($deliveryId => array(34, 22)), ...)
		if (is_array($arDeliveryMap) && (count($arDeliveryMap) > 0))
		{
			foreach ($arDeliveryMap as $val)
			{
				if (is_array($val[$deliveryId]))
				{
					foreach ($val[$deliveryId] as $v)
						$arFilter["ID"][] = $v;
				}
				elseif (IntVal($val[$deliveryId]) > 0)
					$arFilter["ID"][] = $val[$deliveryId];
			}
		}
		$dbPaySystem = CSalePaySystem::GetList(
			array("SORT" => "ASC", "PSA_NAME" => "ASC"),
			$arFilter
		);
		while ($arPaySystem = $dbPaySystem->GetNext())
			$arResult[$arPaySystem["ID"]] = $arPaySystem;

		return $arResult;
	}

	function GetByID($ID, $PERSON_TYPE_ID = 0)
	{
		global $DB;

		$ID = IntVal($ID);
		$PERSON_TYPE_ID = IntVal($PERSON_TYPE_ID);

		if ($PERSON_TYPE_ID > 0)
		{
			$strSql =
				"SELECT PS.*, PSA.ID as PSA_ID, PSA.NAME as PSA_NAME, ".
				"	PSA.ACTION_FILE as PSA_ACTION_FILE, PSA.RESULT_FILE as PSA_RESULT_FILE, ".
				"	PSA.NEW_WINDOW as PSA_NEW_WINDOW, PSA.PARAMS as PSA_PARAMS, ".
				"	PSA.HAVE_PAYMENT as PSA_HAVE_PAYMENT, PSA.HAVE_ACTION as PSA_HAVE_ACTION, ".
				"	PSA.HAVE_RESULT as PSA_HAVE_RESULT, PSA.HAVE_PREPAY as PSA_HAVE_PREPAY, PSA.HAVE_RESULT_RECEIVE as HAVE_RESULT_RECEIVE, ".
				"   PSA.ENCODING as PSA_ENCODING ".
				"FROM b_sale_pay_system PS, b_sale_pay_system_action PSA ".
				"WHERE PS.ID = PSA.PAY_SYSTEM_ID ".
				"	AND PS.ID = ".$ID." ".
				"	AND PSA.PERSON_TYPE_ID = ".$PERSON_TYPE_ID." ";
		}
		else
		{
			$strSql =
				"SELECT * ".
				"FROM b_sale_pay_system ".
				"WHERE ID = ".$ID."";
		}
		//echo $strSql."<br>";
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

		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && strlen($arFields["NAME"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPS_EMPTY_NAME"), "ERROR_NO_NAME");
			return false;
		}

		/*
		if (is_set($arFields, "LID") && $ACTION!="ADD")
			UnSet($arFields["LID"]);

		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPS_EMPTY_CURRENCY"), "ERROR_NO_CURRENCY");
			return false;
		}
		*/

		if (is_set($arFields, "LID"))
		{
			$dbSite = CSite::GetByID($arFields["LID"]);
			if (!$dbSite->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["LID"], GetMessage("SKGPS_NO_SITE")), "ERROR_NO_SITE");
				return false;
			}
		}


		if (is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"] = "N";
		if (is_set($arFields, "SORT") && IntVal($arFields["SORT"])<=0)
			$arFields["SORT"] = 100;

		return True;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = IntVal($ID);
		if (!CSalePaySystem::CheckFields("UPDATE", $arFields, $ID)) return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_pay_system", $arFields);
		$strSql = "UPDATE b_sale_pay_system SET ".$strUpdate." WHERE ID = ".$ID."";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$db_orders = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array("PAY_SYSTEM_ID" => $ID)
			);
		if ($db_orders->Fetch())
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGPS_ORDERS_TO_PAYSYSTEM"), "ERROR_ORDERS_TO_PAYSYSTEM");
			return False;
		}

		$DB->Query("DELETE FROM b_sale_pay_system_action WHERE PAY_SYSTEM_ID = ".$ID."", true);
		return $DB->Query("DELETE FROM b_sale_pay_system WHERE ID = ".$ID."", true);
	}
}
?>