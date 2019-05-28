<?
IncludeModuleLangFile(__FILE__);

class CAllSaleOrderPropsValue
{
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "ORDER_ID") || $ACTION=="ADD") && IntVal($arFields["ORDER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOPV_EMPTY_ORDER_ID"), "EMPTY_ORDER_ID");
			return false;
		}
		
		if ((is_set($arFields, "ORDER_PROPS_ID") || $ACTION=="ADD") && IntVal($arFields["ORDER_PROPS_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOPV_EMPTY_PROP_ID"), "EMPTY_ORDER_PROPS_ID");
			return false;
		}

		if (is_set($arFields, "ORDER_ID"))
		{
			if (!($arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ORDER_ID"], GetMessage("SKGOPV_NO_ORDER_ID")), "ERROR_NO_ORDER");
				return false;
			}
		}

		if (is_set($arFields, "ORDER_PROPS_ID"))
		{
			if (!($arOrder = CSaleOrderProps::GetByID($arFields["ORDER_PROPS_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["ORDER_PROPS_ID"], GetMessage("SKGOPV_NO_PROP_ID")), "ERROR_NO_PROPERY");
				return false;
			}
			
			if (is_set($arFields, "ORDER_ID"))
			{
				$arFilter = Array(
						"ORDER_ID" => $arFields["ORDER_ID"],
						"ORDER_PROPS_ID" => $arFields["ORDER_PROPS_ID"],
					);
				if(IntVal($ID) > 0)
					$arFilter["!ID"] = $ID;
				$dbP = CSaleOrderPropsValue::GetList(Array(), $arFilter);
				if($arP = $dbP->Fetch())
				{
					$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGOPV_DUPLICATE_PROP_ID", Array("#ID#" => $arFields["ORDER_PROPS_ID"], "#ORDER_ID#" => $arFields["ORDER_ID"])), "ERROR_DUPLICATE_PROP_ID");
					return false;
				}
			}
		}

		return True;
	}

	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$strSql =
			"SELECT * ".
			"FROM b_sale_order_props_value ".
			"WHERE ID = ".$ID."";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($res = $db_res->Fetch())
		{
			return $res;
		}
		return False;
	}

	function Update($ID, $arFields)
	{
		global $DB;
		$ID = IntVal($ID);

		if (!CSaleOrderPropsValue::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_sale_order_props_value", $arFields);
		$strSql = 
			"UPDATE b_sale_order_props_value SET ".
			"	".$strUpdate." ".
			"WHERE ID = ".$ID." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);

		$strSql = "DELETE FROM b_sale_order_props_value WHERE ID = ".$ID." ";
		return $DB->Query($strSql, True);
	}

	function DeleteByOrder($orderID)
	{
		global $DB;
		$orderID = IntVal($orderID);

		$strSql = "DELETE FROM b_sale_order_props_value WHERE ORDER_ID = ".$orderID." ";
		return $DB->Query($strSql, True);
	}
}
?>