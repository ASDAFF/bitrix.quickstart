<?
IncludeModuleLangFile(__FILE__);

/***********************************************************************/
/***********  CPrice  **************************************************/
/***********************************************************************/
class CAllPrice
{
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "PRODUCT_ID") || $ACTION=="ADD") && IntVal($arFields["PRODUCT_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("KGP_EMPTY_PRODUCT"), "EMPTY_PRODUCT_ID");
			return false;
		}
		if ((is_set($arFields, "CATALOG_GROUP_ID") || $ACTION=="ADD") && IntVal($arFields["CATALOG_GROUP_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("KGP_EMPTY_CATALOG_GROUP"), "EMPTY_CATALOG_GROUP_ID");
			return false;
		}
		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("KGP_EMPTY_CURRENCY"), "EMPTY_CURRENCY");
			return false;
		}

		if (is_set($arFields, "PRICE") || $ACTION=="ADD")
		{
			$arFields["PRICE"] = str_replace(",", ".", $arFields["PRICE"]);
			$arFields["PRICE"] = DoubleVal($arFields["PRICE"]);
		}

		if ((is_set($arFields, "QUANTITY_FROM") || $ACTION=="ADD") && IntVal($arFields["QUANTITY_FROM"]) <= 0)
			$arFields["QUANTITY_FROM"] = False;
		if ((is_set($arFields, "QUANTITY_TO") || $ACTION=="ADD") && IntVal($arFields["QUANTITY_TO"]) <= 0)
			$arFields["QUANTITY_TO"] = False;

		return True;
	}

	function Update($ID, $arFields,$boolRecalc = false)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		if (!CPrice::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$boolBase = false;
		$arFields['RECALC'] = ($boolRecalc === true ? true : false);

		$db_events = GetModuleEvents("catalog", "OnBeforePriceUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, &$arFields))===false)
				return false;

		if (!empty($arFields['RECALC']) && $arFields['RECALC'] === true)
		{
			CPrice::ReCountFromBase($arFields,$boolBase);
		}

		$strUpdate = $DB->PrepareUpdate("b_catalog_price", $arFields);
		$strSql = "UPDATE b_catalog_price SET ".$strUpdate." WHERE ID = ".$ID." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if ($boolBase == true)
		{
			CPrice::ReCountForBase($arFields);
		}

		$events = GetModuleEvents("catalog", "OnPriceUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = IntVal($ID);
		if ($ID <= 0)
			return false;

		$db_events = GetModuleEvents("catalog", "OnBeforePriceDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$mxRes = $DB->Query("DELETE FROM b_catalog_price WHERE ID = ".$ID." ", true);

		$events = GetModuleEvents("catalog", "OnPriceDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		return $mxRes;
	}

	function GetBasePrice($productID, $quantityFrom = false, $quantityTo = false)
	{
		global $DB;

		$productID = IntVal($productID);
		if ($quantityFrom !== false)
			$quantityFrom = IntVal($quantityFrom);
		if ($quantityTo !== false)
			$quantityTo = IntVal($quantityTo);

		$arFilter = array(
				"BASE" => "Y",
				"PRODUCT_ID" => $productID
			);

		if ($quantityFrom !== false)
			$arFilter["QUANTITY_FROM"] = $quantityFrom;
		if ($quantityTo !== false)
			$arFilter["QUANTITY_TO"] = $quantityTo;

		$db_res = CPrice::GetList(
				array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
				$arFilter
			);
		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	function SetBasePrice($ProductID, $Price, $Currency, $quantityFrom = 0, $quantityTo = 0, $bGetID = false)
	{
		global $DB;

		$bGetID = ($bGetID == true ? true : false);

		$arFields = array();
		$arFields["PRICE"] = DoubleVal($Price);
		$arFields["CURRENCY"] = $Currency;
		$arFields["QUANTITY_FROM"] = IntVal($quantityFrom);
		$arFields["QUANTITY_TO"] = IntVal($quantityTo);
		$arFields["EXTRA_ID"] = False;

		$ID = false;
		if ($arBasePrice = CPrice::GetBasePrice($ProductID, $quantityFrom, $quantityTo))
		{
			//CPrice::Update($arBasePrice["ID"], $arFields);
			$ID = CPrice::Update($arBasePrice["ID"], $arFields);
		}
		else
		{
			$arBaseGroup = CCatalogGroup::GetBaseGroup();
			$arFields["CATALOG_GROUP_ID"] = $arBaseGroup["ID"];
			$arFields["PRODUCT_ID"] = $ProductID;

			//CPrice::Add($arFields);
			$ID = CPrice::Add($arFields);
		}
		if (!$ID)
		{
			return false;
		}
		else
		{
			return ($bGetID ? $ID : true);
		}
	}

	function ReCalculate($TYPE, $ID, $VAL)
	{
		$ID = IntVal($ID);
		if ($TYPE=="EXTRA")
		{
			$db_res = CPrice::GetList(
					array("EXTRA_ID" => "ASC"),
					array("EXTRA_ID" => $ID)
				);
			while ($res = $db_res->Fetch())
			{
				unset($arFields);
				$arFields = array();
				if ($arBasePrice = CPrice::GetBasePrice($res["PRODUCT_ID"], $res["QUANTITY_FROM"], $res["QUANTITY_TO"]))
				{
					$arFields["PRICE"] = RoundEx($arBasePrice["PRICE"] * (1 + 1 * $VAL / 100), 2);
					$arFields["CURRENCY"] = $arBasePrice["CURRENCY"];
					CPrice::Update($res["ID"], $arFields);
				}
			}
		}
		else
		{
			$db_res = CPrice::GetList(array("PRODUCT_ID" => "ASC"), array("PRODUCT_ID" => $ID));
			while ($res = $db_res->Fetch())
			{
				if (IntVal($res["EXTRA_ID"])>0)
				{
					$res1 = CExtra::GetByID($res["EXTRA_ID"]);
					unset($arFields);
					$arFields["PRICE"] = $VAL * (1 + 1 * $res1["PERCENTAGE"] / 100);
					CPrice::Update($res["ID"], $arFields);
				}
			}
		}
	}

	function OnCurrencyDelete($Currency)
	{
		global $DB;
		if (strlen($Currency)<=0) return false;

		$strSql =
			"DELETE FROM b_catalog_price ".
			"WHERE CURRENCY = '".$DB->ForSql($Currency)."' ";

		return $DB->Query($strSql, true);
	}

	function OnIBlockElementDelete($ProductID)
	{
		global $DB;
		$ProductID = IntVal($ProductID);
		$strSql =
			"DELETE ".
			"FROM b_catalog_price ".
			"WHERE PRODUCT_ID = ".$ProductID." ";
		return $DB->Query($strSql, true);
	}

	function DeleteByProduct($ProductID, $arExceptionIDs = array())
	{
		global $DB;

		$ProductID = IntVal($ProductID);
		if ($ProductID <= 0)
			return false;
		$db_events = GetModuleEvents("catalog", "OnBeforeProductPriceDelete");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ProductID,&$arExceptionIDs))===false)
				return false;

		for ($i = 0, $intCount = count($arExceptionIDs); $i < $intCount; $i++)
			$arExceptionIDs[$i] = intval($arExceptionIDs[$i]);
		$arExceptionIDs[] = 0;

		$strExceptionIDs = implode(',',$arExceptionIDs);

		$strSql =
			"DELETE ".
			"FROM b_catalog_price ".
			"WHERE PRODUCT_ID = ".$ProductID." ".
			"	AND ID NOT IN (".$strExceptionIDs.") ";

		$mxRes = $DB->Query($strSql, true);

		$events = GetModuleEvents("catalog", "OnProductPriceDelete");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ProductID,$arExceptionIDs));

		return $mxRes;
	}

	function ReCountForBase(&$arFields)
	{
		static $arExtraList = array();
		$boolSearch = false;

		$arFilter = array('PRODUCT_ID' => $arFields['PRODUCT_ID'],'!CATALOG_GROUP_ID' => $arFields['CATALOG_GROUP_ID']);
		if (isset($arFields['QUANTITY_FROM']))
			$arFilter['QUANTITY_FROM'] = $arFields['QUANTITY_FROM'];
		if (isset($arFields['QUANTITY_TO']))
			$arFilter['QUANTITY_TO'] = $arFields['QUANTITY_TO'];

		$rsPrices = CPrice::GetList(array('CATALOG_GROUP_ID' => 'asc',"QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),$arFilter,false,false,array('ID','EXTRA_ID'));
		while ($arPrice = $rsPrices->Fetch())
		{
			$arPrice['EXTRA_ID'] = intval($arPrice['EXTRA_ID']);
			if ($arPrice['EXTRA_ID'] > 0)
			{
				$boolSearch = array_key_exists($arPrice['EXTRA_ID'],$arExtraList);
				if (!$boolSearch)
				{
					$arExtra = CExtra::GetByID($arPrice['EXTRA_ID']);
					if (!empty($arExtra))
					{
						$boolSearch = true;
						$arExtraList[$arExtra['ID']] = $arExtra['PERCENTAGE'];
					}
				}
				if ($boolSearch)
				{
					$arNewPrice = array(
						'CURRENCY' => $arFields['CURRENCY'],
						'PRICE' => RoundEx($arFields["PRICE"] * (1 + DoubleVal($arExtraList[$arPrice['EXTRA_ID']])/100), CATALOG_VALUE_PRECISION),
					);
					CPrice::Update($arPrice['ID'],$arNewPrice,false);
				}
			}
		}
	}

	function ReCountFromBase(&$arFields, &$boolBase)
	{
		$arBaseGroup = CCatalogGroup::GetBaseGroup();
		if (!empty($arBaseGroup))
		{
			if ($arFields['CATALOG_GROUP_ID'] == $arBaseGroup['ID'])
			{
				$boolBase = true;
			}
			else
			{
				if (!empty($arFields['EXTRA_ID']) && intval($arFields['EXTRA_ID']) > 0)
				{
					$arExtra = CExtra::GetByID($arFields['EXTRA_ID']);
					if (!empty($arExtra))
					{
						$arFilter = array('PRODUCT_ID' => $arFields['PRODUCT_ID'],'CATALOG_GROUP_ID' => $arBaseGroup['ID']);
						if (isset($arFields['QUANTITY_FROM']))
							$arFilter['QUANTITY_FROM'] = $arFields['QUANTITY_FROM'];
						if (isset($arFields['QUANTITY_TO']))
							$arFilter['QUANTITY_TO'] = $arFields['QUANTITY_TO'];
						$rsBasePrices = CPrice::GetList(array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
													$arFilter,false,array('nTopCount' => 1),array('PRICE','CURRENCY'));
						if ($arBasePrice = $rsBasePrices->Fetch())
						{
							$arFields['CURRENCY'] = $arBasePrice['CURRENCY'];
							$arFields['PRICE'] = RoundEx($arBasePrice["PRICE"] * (1 + DoubleVal($arExtra["PERCENTAGE"])/100), CATALOG_VALUE_PRECISION);
						}
					}
					else
					{
						$arFields['EXTRA_ID'] = 0;
					}
				}
			}
		}
	}
}
?>