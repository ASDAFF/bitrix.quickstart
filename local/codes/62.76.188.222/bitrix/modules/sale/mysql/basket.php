<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/basket.php");

class CSaleBasket extends CAllSaleBasket
{
	/**
	* The function remove old subscribe product
	*
	* @param string $LID - site for cleaning
	* @return true false
	*/
	function _ClearProductSubscribe($LID)
	{
		global $DB;

		$subProp = COption::GetOptionString("sale", "subscribe_prod", "");
		$arSubProp = unserialize($subProp);

		$dayDelete = IntVal($arSubProp[$LID]["del_after"]);

		$strSql =
			"DELETE ".
			"FROM b_sale_basket ".
			"WHERE ((ORDER_ID IS NULL) OR (ORDER_ID = 0)) AND CAN_BUY = 'N' AND SUBSCRIBE = 'Y' AND TO_DAYS(DATE_INSERT) < (TO_DAYS(NOW()) - ".$dayDelete.") LIMIT 500";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return true;
	}

	function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB, $USER;

		if (!is_array($arOrder) && !is_array($arFilter))
		{
			$arOrder = strval($arOrder);
			$arFilter = strval($arFilter);
			if (strlen($arOrder) > 0 && strlen($arFilter) > 0)
				$arOrder = array($arOrder => $arFilter);
			else
				$arOrder = array();
			if (is_array($arGroupBy))
				$arFilter = $arGroupBy;
			else
				$arFilter = array();
			$arGroupBy = false;

			if (ToUpper($arFilter["ORDER_ID"]) == "NULL")
			{
				$arFilter["ORDER_ID"] = 0;
			}
		}

		if (count($arSelectFields) <= 0)
		{
			$arSelectFields = array(
				"ID",
				"FUSER_ID",
				"ORDER_ID",
				"PRODUCT_ID",
				"PRODUCT_PRICE_ID",
				"PRICE", "CURRENCY",
				"DATE_INSERT",
				"DATE_UPDATE",
				"WEIGHT",
				"QUANTITY",
				"LID",
				"DELAY",
				"NAME",
				"CAN_BUY",
				"MODULE",
				"CALLBACK_FUNC",
				"NOTES",
				"ORDER_CALLBACK_FUNC",
				"PAY_CALLBACK_FUNC",
				"CANCEL_CALLBACK_FUNC",
				"PRODUCT_PROVIDER_CLASS",
				"DETAIL_PAGE_URL",
				"DISCOUNT_PRICE",
				"CATALOG_XML_ID",
				"PRODUCT_XML_ID",
				"DISCOUNT_NAME",
				"DISCOUNT_VALUE",
				"DISCOUNT_COUPON",
				"VAT_RATE",
				"USER_ID",
				"SUBSCRIBE",
				"BARCODE_MULTI",
				"RESERVED",
				"DEDUCTED",
				"RESERVE_QUANTITY",
				"CUSTOM_PRICE"
			);
		}
		elseif (in_array("*", $arSelectFields))
		{
			$arSelectFields = array(
				"ID",
				"FUSER_ID",
				"ORDER_ID",
				"PRODUCT_ID",
				"PRODUCT_PRICE_ID",
				"PRICE",
				"CURRENCY",
				"DATE_INSERT",
				"DATE_UPDATE",
				"WEIGHT",
				"QUANTITY",
				"LID",
				"DELAY",
				"NAME",
				"CAN_BUY",
				"MODULE",
				"CALLBACK_FUNC",
				"NOTES",
				"ORDER_CALLBACK_FUNC",
				"PAY_CALLBACK_FUNC",
				"CANCEL_CALLBACK_FUNC",
				"PRODUCT_PROVIDER_CLASS",
				"DETAIL_PAGE_URL",
				"DISCOUNT_PRICE",
				"CATALOG_XML_ID",
				"PRODUCT_XML_ID",
				"DISCOUNT_NAME",
				"DISCOUNT_VALUE",
				"DISCOUNT_COUPON",
				"VAT_RATE",
				"ORDER_ALLOW_DELIVERY",
				"ORDER_PAYED",
				"ORDER_PRICE",
				"USER_ID",
				"SUBSCRIBE",
				"BARCODE_MULTI",
				"RESERVED",
				"DEDUCTED",
				"RESERVE_QUANTITY",
				"CUSTOM_PRICE"
			);
		}

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "B.ID", "TYPE" => "int"),
				"FUSER_ID" => array("FIELD" => "B.FUSER_ID", "TYPE" => "int"),
				"ORDER_ID" => array("FIELD" => "B.ORDER_ID", "TYPE" => "int"),
				"PRODUCT_ID" => array("FIELD" => "B.PRODUCT_ID", "TYPE" => "int"),
				"PRODUCT_PRICE_ID" => array("FIELD" => "B.PRODUCT_PRICE_ID", "TYPE" => "int"),
				"PRICE" => array("FIELD" => "B.PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "B.CURRENCY", "TYPE" => "string"),
				"DATE_INSERT" => array("FIELD" => "B.DATE_INSERT", "TYPE" => "datetime"),
				"DATE_UPDATE" => array("FIELD" => "B.DATE_UPDATE", "TYPE" => "datetime"),
				"WEIGHT" => array("FIELD" => "B.WEIGHT", "TYPE" => "double"),
				"QUANTITY" => array("FIELD" => "B.QUANTITY", "TYPE" => "double"),
				"LID" => array("FIELD" => "B.LID", "TYPE" => "string"),
				"DELAY" => array("FIELD" => "B.DELAY", "TYPE" => "char"),
				"NAME" => array("FIELD" => "B.NAME", "TYPE" => "string"),
				"CAN_BUY" => array("FIELD" => "B.CAN_BUY", "TYPE" => "char"),
				"MODULE" => array("FIELD" => "B.MODULE", "TYPE" => "string"),
				"CALLBACK_FUNC" => array("FIELD" => "B.CALLBACK_FUNC", "TYPE" => "string"),
				"NOTES" => array("FIELD" => "B.NOTES", "TYPE" => "string"),
				"ORDER_CALLBACK_FUNC" => array("FIELD" => "B.ORDER_CALLBACK_FUNC", "TYPE" => "string"),
				"PAY_CALLBACK_FUNC" => array("FIELD" => "B.PAY_CALLBACK_FUNC", "TYPE" => "string"),
				"CANCEL_CALLBACK_FUNC" => array("FIELD" => "B.CANCEL_CALLBACK_FUNC", "TYPE" => "string"),
				"PRODUCT_PROVIDER_CLASS" => array("FIELD" => "B.PRODUCT_PROVIDER_CLASS", "TYPE" => "string"),
				"DETAIL_PAGE_URL" => array("FIELD" => "B.DETAIL_PAGE_URL", "TYPE" => "string"),
				"DISCOUNT_PRICE" => array("FIELD" => "B.DISCOUNT_PRICE", "TYPE" => "double"),
				"CATALOG_XML_ID" => array("FIELD" => "B.CATALOG_XML_ID", "TYPE" => "string"),
				"PRODUCT_XML_ID" => array("FIELD" => "B.PRODUCT_XML_ID", "TYPE" => "string"),
				"DISCOUNT_NAME" => array("FIELD" => "B.DISCOUNT_NAME", "TYPE" => "string"),
				"DISCOUNT_VALUE" => array("FIELD" => "B.DISCOUNT_VALUE", "TYPE" => "string"),
				"DISCOUNT_COUPON" => array("FIELD" => "B.DISCOUNT_COUPON", "TYPE" => "string"),
				"VAT_RATE" => array("FIELD" => "B.VAT_RATE", "TYPE" => "double"),
				"SUBSCRIBE" => array("FIELD" => "B.SUBSCRIBE", "TYPE" => "char"),
				"BARCODE_MULTI" => array("FIELD" => "B.BARCODE_MULTI", "TYPE" => "char"),
				"RESERVED" => array("FIELD" => "B.RESERVED", "TYPE" => "char"),
				"DEDUCTED" => array("FIELD" => "B.DEDUCTED", "TYPE" => "char"),
				"RESERVE_QUANTITY" => array("FIELD" => "B.RESERVE_QUANTITY", "TYPE" => "double"),
				"CUSTOM_PRICE" => array("FIELD" => "B.CUSTOM_PRICE", "TYPE" => "char"),

				"ORDER_ALLOW_DELIVERY" => array("FIELD" => "O.ALLOW_DELIVERY", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_order O ON (O.ID = B.ORDER_ID)"),
				"ORDER_PAYED" => array("FIELD" => "O.PAYED", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_order O ON (O.ID = B.ORDER_ID)"),
				"ORDER_PRICE" => array("FIELD" => "O.PRICE", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_order O ON (O.ID = B.ORDER_ID)"),
				"USER_ID" => array("FIELD" => "F.USER_ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_fuser F ON (F.ID = B.FUSER_ID)"),

				"ALL_PRICE" => array("FIELD" => "(B.PRICE+B.DISCOUNT_PRICE)", "TYPE" => "double"),
				"SUM_PRICE" => array("FIELD" => "(B.PRICE*B.QUANTITY)", "TYPE" => "double"),
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_basket B ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_basket B ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";
		// echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_basket B ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	function GetPropsList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (!is_array($arOrder) && !is_array($arFilter))
		{
			$arOrder = strval($arOrder);
			$arFilter = strval($arFilter);
			if (strlen($arOrder) > 0 && strlen($arFilter) > 0)
				$arOrder = array($arOrder => $arFilter);
			else
				$arOrder = array();
			if (is_array($arGroupBy))
				$arFilter = $arGroupBy;
			else
				$arFilter = array();
			$arGroupBy = false;
		}

		// FIELDS -->
		$arFields = array(
				"ID" => array("FIELD" => "BP.ID", "TYPE" => "int"),
				"BASKET_ID" => array("FIELD" => "BP.BASKET_ID", "TYPE" => "int"),
				"NAME" => array("FIELD" => "BP.NAME", "TYPE" => "string"),
				"VALUE" => array("FIELD" => "BP.VALUE", "TYPE" => "string"),
				"CODE" => array("FIELD" => "BP.CODE", "TYPE" => "string"),
				"SORT" => array("FIELD" => "BP.SORT", "TYPE" => "int")
			);
		// <-- FIELDS

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_sale_basket_props BP ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!1!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return False;
		}

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_basket_props BP ".
			"	".$arSqls["FROM"]." ";
		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_basket_props BP ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			//echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			//echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			//echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	//************** ADD, UPDATE, DELETE ********************//
	function Add($arFields)
	{
		global $DB;

		if (isset($arFields["ID"]))
			unset($arFields["ID"]);

		CSaleBasket::Init();
		if (!CSaleBasket::CheckFields("ADD", $arFields))
			return false;

		foreach(GetModuleEvents("sale", "OnBeforeBasketAdd", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array(&$arFields))===false)
				return false;

		$bFound = false;
		$bEqAr = false;
		$db_res = CSaleBasket::GetList(
				array("LID" => "ASC"),
				array(
						"FUSER_ID" => $arFields["FUSER_ID"],
						"PRODUCT_ID" => $arFields["PRODUCT_ID"],
						"LID" => $arFields["LID"],
						"ORDER_ID" => "NULL"
					),
				false,
				false,
				array("ID", "QUANTITY")
			);
		while($res = $db_res->Fetch())
		{
			if(!$bEqAr)
			{
				$arPropsCur = Array();
				$arPropsOld = Array();

				//$dbProp = CSaleBasket::GetPropsList(Array("ID" => "DESC"), Array("BASKET_ID" => $ID));
				if(is_array($arFields["PROPS"]))
				{
					foreach($arFields["PROPS"] as $arProp)
					{
						if(strlen($arProp["VALUE"]) > 0)
						{
							if(strlen($arProp["CODE"]) > 0)
								$propID = $arProp["CODE"];
							else
								$propID = $arProp["NAME"];
							$arPropsCur[$propID] = $arProp["VALUE"];
						}
					}
				}

				$dbProp = CSaleBasket::GetPropsList(Array("ID" => "DESC"), Array("BASKET_ID" => $res["ID"]));
				while($arProp = $dbProp->Fetch())
				{
					if(strlen($arProp["VALUE"]) > 0)
					{
						if(strlen($arProp["CODE"]) > 0)
							$propID = $arProp["CODE"];
						else
							$propID = $arProp["NAME"];
						$arPropsOld[$propID] = $arProp["VALUE"];
					}
				}

				$bEqAr = false;
				if(count($arPropsCur) == count($arPropsOld))
				{
					$bEqAr = true;
					foreach($arPropsCur as $key => $val)
					{
						if($bEqAr && (strlen($arPropsOld[$key]) <= 0 || $arPropsOld[$key] != $val))
							$bEqAr = false;
					}
				}


				if($bEqAr)
				{
					$ID = $res["ID"];
					$arFields["QUANTITY"] += $res["QUANTITY"];
					CSaleBasket::Update($ID, $arFields);
					$bFound = true;
					continue;
				}
			}
		}

		if(!$bFound)
		{
			$arInsert = $DB->PrepareInsert("b_sale_basket", $arFields);

			$strSql =
				"INSERT INTO b_sale_basket(".$arInsert[0].", DATE_INSERT, DATE_UPDATE) ".
				"VALUES(".$arInsert[1].", ".$DB->GetNowFunction().", ".$DB->GetNowFunction().")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			$ID = IntVal($DB->LastID());
			$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]++;

			if (is_array($arFields["PROPS"]) && count($arFields["PROPS"])>0)
			{
				foreach ($arFields["PROPS"] as $prop)
				{
					if(strlen($prop["NAME"]) > 0)
					{
						$arInsert = $DB->PrepareInsert("b_sale_basket_props", $prop);

						$strSql =
							"INSERT INTO b_sale_basket_props(BASKET_ID, ".$arInsert[0].") ".
							"VALUES(".$ID.", ".$arInsert[1].")";
						$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
					}
				}
			}
		}

		foreach(GetModuleEvents("sale", "OnBasketAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $arFields));

		return $ID;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		foreach(GetModuleEvents("sale", "OnBeforeBasketDelete", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID))===false)
				return false;

		$DB->Query("DELETE FROM b_sale_basket_props WHERE BASKET_ID = ".$ID." ", true);
		if(IntVal($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]) > 0 )
			$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]--;

		$DB->Query("DELETE FROM b_sale_store_barcode WHERE BASKET_ID = ".$ID." ", true);

		$DB->Query("DELETE FROM b_sale_basket WHERE ID = ".$ID." ", true);

		foreach(GetModuleEvents("sale", "OnBasketDelete", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID));

		//return $DB->Query("DELETE FROM b_sale_basket WHERE ID = ".$ID." ", true);
		return true;
	}

	function DeleteAll($FUSER_ID = 0, $bIncOrdered = false)
	{
		global $DB, $APPLICATION;

		$bIncOrdered = ($bIncOrdered ? True : False);
		$FUSER_ID = IntVal($FUSER_ID);
		if ($FUSER_ID <= 0)
			return false;

		$arFilter = Array("FUSER_ID" => $FUSER_ID);
		if (!$bIncOrdered)
			$arFilter["ORDER_ID"] = "NULL";

		$dbBasket = CSaleBasket::GetList(array("NAME" => "ASC"), $arFilter);
		while ($arBasket = $dbBasket->Fetch())
		{
			$DB->Query("DELETE FROM b_sale_basket_props WHERE BASKET_ID = ".$arBasket["ID"]." ", true);
			$DB->Query("DELETE FROM b_sale_store_barcode WHERE BASKET_ID = ".$arBasket["ID"]." ", true);
			$DB->Query("DELETE FROM b_sale_basket WHERE ID = ".$arBasket["ID"]." ", true);
		}

		$_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID] = 0;

		return true;
	}
/*
	function TransferBasket($FROM_FUSER_ID, $TO_FUSER_ID)
	{
		global $DB;

		$FROM_FUSER_ID = IntVal($FROM_FUSER_ID);
		$TO_FUSER_ID = IntVal($TO_FUSER_ID);

		if (($TO_FUSER_ID>0) && (CSaleUser::GetList(array("ID"=>$TO_FUSER_ID))))
		{
			$strSql =
				"UPDATE b_sale_basket SET ".
				"	FUSER_ID = ".$TO_FUSER_ID." ".
				"WHERE FUSER_ID = ".$FROM_FUSER_ID." ";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			return true;
		}
		return false;
	}
*/

	function GetLeave($arOrder = Array(), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = Array())
	{
		global $DB;
		if(empty($arSelectFields) || in_array("*", $arSelectFields))
			$arSelectFields = Array("FUSER_ID", "USER_ID", "QUANTITY_ALL", "PRICE_ALL", "PR_COUNT", "CURRENCY", "DATE_INSERT_MIN", "DATE_UPDATE_MAX", "LID", "USER_NAME", "USER_LAST_NAME", "USER_LOGIN", "USER_EMAIL");

		$arFields = array(
				"ID" => array("FIELD" => "B.ID", "TYPE" => "int"),
				"FUSER_ID" => array("FIELD" => "B.FUSER_ID", "TYPE" => "int"),
				"ORDER_ID" => array("FIELD" => "B.ORDER_ID", "TYPE" => "int"),
				"PRODUCT_ID" => array("FIELD" => "B.PRODUCT_ID", "TYPE" => "int"),
				"PRICE" => array("FIELD" => "B.PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "B.CURRENCY", "TYPE" => "string"),
				"DATE_INSERT" => array("FIELD" => "B.DATE_INSERT", "TYPE" => "datetime"),
				"DATE_UPDATE" => array("FIELD" => "B.DATE_UPDATE", "TYPE" => "datetime"),
				"WEIGHT" => array("FIELD" => "B.WEIGHT", "TYPE" => "double"),
				"QUANTITY" => array("FIELD" => "B.QUANTITY", "TYPE" => "double"),
				"LID" => array("FIELD" => "B.LID", "TYPE" => "string"),
				"DELAY" => array("FIELD" => "B.DELAY", "TYPE" => "char"),
				"NAME" => array("FIELD" => "B.NAME", "TYPE" => "string"),
				"CAN_BUY" => array("FIELD" => "B.CAN_BUY", "TYPE" => "char"),
				"MODULE" => array("FIELD" => "B.MODULE", "TYPE" => "string"),
				"CALLBACK_FUNC" => array("FIELD" => "B.CALLBACK_FUNC", "TYPE" => "string"),
				"NOTES" => array("FIELD" => "B.NOTES", "TYPE" => "string"),
				"ORDER_CALLBACK_FUNC" => array("FIELD" => "B.ORDER_CALLBACK_FUNC", "TYPE" => "string"),
				"PAY_CALLBACK_FUNC" => array("FIELD" => "B.PAY_CALLBACK_FUNC", "TYPE" => "string"),
				"CANCEL_CALLBACK_FUNC" => array("FIELD" => "B.CANCEL_CALLBACK_FUNC", "TYPE" => "string"),
				"DETAIL_PAGE_URL" => array("FIELD" => "B.DETAIL_PAGE_URL", "TYPE" => "string"),
				"DISCOUNT_PRICE" => array("FIELD" => "B.DISCOUNT_PRICE", "TYPE" => "double"),
				"CATALOG_XML_ID" => array("FIELD" => "B.CATALOG_XML_ID", "TYPE" => "string"),
				"PRODUCT_XML_ID" => array("FIELD" => "B.PRODUCT_XML_ID", "TYPE" => "string"),
				"DISCOUNT_NAME" => array("FIELD" => "B.DISCOUNT_NAME", "TYPE" => "string"),
				"DISCOUNT_VALUE" => array("FIELD" => "B.DISCOUNT_VALUE", "TYPE" => "string"),
				"DISCOUNT_COUPON" => array("FIELD" => "B.DISCOUNT_COUPON", "TYPE" => "string"),
				"VAT_RATE" => array("FIELD" => "B.VAT_RATE", "TYPE" => "double"),
				"SUBSCRIBE" => array("FIELD" => "B.SUBSCRIBE", "TYPE" => "char"),
				"USER_ID" => array("FIELD" => "F.USER_ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_fuser F ON (F.ID = B.FUSER_ID)"),
				"QUANTITY_ALL" => array("FIELD" => "SUM(B.QUANTITY)", "TYPE" => "double"),
				"PRICE_ALL" => array("FIELD" => "SUM(B.QUANTITY*B.PRICE)", "TYPE" => "double"),
				"PR_COUNT" => array("FIELD" => "COUNT(B.ID)", "TYPE" => "int"),
				"DATE_INSERT_MIN" => array("FIELD" => "MIN(B.DATE_INSERT)", "TYPE" => "datetime"),
				"DATE_UPDATE_MAX" => array("FIELD" => "MAX(B.DATE_UPDATE)", "TYPE" => "datetime"),
				"NAME_SEARCH" => array("FIELD" => "U.NAME, U.LAST_NAME, U.SECOND_NAME, U.EMAIL, U.LOGIN, U.ID", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_NAME" => array("FIELD" => "U.NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_LAST_NAME" => array("FIELD" => "U.LAST_NAME", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_LOGIN" => array("FIELD" => "U.LOGIN", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_EMAIL" => array("FIELD" => "U.EMAIL", "TYPE" => "string", "FROM" => "LEFT JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_GROUP_ID" => array("FIELD" => "UG.GROUP_ID", "TYPE" => "int", "FROM" => "LEFT JOIN b_user_group UG ON (UG.USER_ID = F.USER_ID)"),
			);

		$arFilter["ORDER_ID"] = false;
		if(!in_array("FUSER_ID", $arSelectFields))
			$arSelectFields[] = "FUSER_ID";
		if(!in_array("USER_ID", $arSelectFields))
			$arSelectFields[] = "USER_ID";
		if(!in_array("LID", $arSelectFields))
			$arSelectFields[] = "LID";

		$arFilterH = Array();
		if(!empty($arFilter))
		{
			foreach($arFilter as $k => $v)
			{
				if(strpos($k, "QUANTITY_ALL") !== false || strpos($k, "PRICE_ALL") !== false || strpos($k, "PR_COUNT") !== false)
				{
					$arFilterH[$k] = $v;
					unset($arFilter[$k]);
				}
			}
		}

		if(!empty($arFilterH))
			$arSqlsH = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilterH, false, $arSelectFields);
		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_basket B ".
			"	".$arSqls["FROM"]." ";
		$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		$strSql .= "GROUP BY B.FUSER_ID, F.USER_ID, B.LID ";
		if (strlen($arSqlsH["WHERE"]) > 0)
			$strSql .= "HAVING ".$arSqlsH["WHERE"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";
		// echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_basket B ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			$strSql_tmp .= "GROUP BY B.FUSER_ID, F.USER_ID, B.LID ";
			if (strlen($arSqlsH["WHERE"]) > 0)
				$strSql_tmp .= "HAVING ".$arSqlsH["WHERE"]." ";

			// echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = $dbRes->SelectedRowsCount();

			$dbRes = new CDBResult();

			// echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			// echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}

class CSaleUser extends CAllSaleUser
{
	function Add()
	{
		global $DB, $USER;

		$arFields = array(
				"=DATE_INSERT" => $DB->GetNowFunction(),
				"=DATE_UPDATE" => $DB->GetNowFunction(),
				"USER_ID" => ($USER->IsAuthorized() ? IntVal($USER->GetID()) : False)
			);

		$ID = CSaleUser::_Add($arFields);
		$ID = IntVal($ID);

		$secure = false;
		if(COption::GetOptionString("sale", "use_secure_cookies", "N") == "Y" && CMain::IsHTTPS())
			$secure=1;
		$GLOBALS["APPLICATION"]->set_cookie("SALE_UID", $ID, false, "/", false, $secure, "Y", false);

		return $ID;
	}

	function _Add($arFields)
	{
		global $DB;

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1)=="=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSaleUser::CheckFields("ADD", $arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_sale_fuser", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0])>0) $arInsert[0] .= ", ";
			$arInsert[0] .= $key;
			if (strlen($arInsert[1])>0) $arInsert[1] .= ", ";
			$arInsert[1] .= $value;
		}

		$strSql =
			"INSERT INTO b_sale_fuser(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = IntVal($DB->LastID());

		return $ID;
	}


	function DeleteOld($nDays)
	{
		global $DB;

		$nDays = IntVal($nDays);
		$strSql =
			"SELECT ID ".
			"FROM b_sale_fuser ".
			"WHERE TO_DAYS(DATE_UPDATE)<(TO_DAYS(NOW())-".$nDays.") LIMIT 300";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		while ($ar_res = $db_res->Fetch())
		{
			CSaleBasket::DeleteAll($ar_res["ID"], false);
			CSaleUser::Delete($ar_res["ID"]);
		}
		return true;
	}

	function GetBuyersList($arOrder = Array(), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = Array())
	{
		global $DB;
		if(empty($arSelectFields) || in_array("*", $arSelectFields))
			$arSelectFields = Array("ID", "ACTIVE", "LID", "DATE_REGISTER", "LOGIN", "EMAIL", "NAME", "LAST_NAME", "SECOND_NAME", "PERSONAL_PHONE", "USER_ID", "LAST_LOGIN", "TIMESTAMP_X", "PERSONAL_BIRTHDAY", "ORDER_COUNT", "ORDER_SUM", "CURRENCY", "LAST_ORDER_DATE");

		$arFields_m = array("ACTIVE", "LOGIN", "EMAIL", "NAME", "LAST_NAME", "SECOND_NAME", "PERSONAL_PHONE");
		$arFields_md = array("LAST_LOGIN", "DATE_REGISTER", "TIMESTAMP_X", "PERSONAL_BIRTHDAY");

		$CURRENCY = "";
		if(strlen($arFilter["CURRENCY"]) > 0)
		{
			$CURRENCY = $arFilter["CURRENCY"];
			unset($arFilter["CURRENCY"]);
		}
		else
		{
			CModule::IncludeModule("currency");
			$CURRENCY = CCurrency::GetBaseCurrency();
		}

		$LID = "";
		if(strlen($arFilter["LID"]) > 0)
		{
			$LID = $arFilter["LID"];
			unset($arFilter["LID"]);
		}
		else
		{
			$rsSites = CSite::GetList($by="id", $order="asc", array("ACTIVE" => "Y"));
			$arSite = $rsSites->Fetch();
			$LID = $arSite["ID"];
		}

		$arFields = array(
				"ID" => array("FIELD" => "F.ID", "TYPE" => "int"),
				"LID" => array("FIELD" => "O1.LID", "TYPE" => "string"),
				"ORDER_COUNT" => array("FIELD" => "(SELECT COUNT(O3.PRICE) FROM b_sale_order O3 WHERE O3.USER_ID=F.USER_ID AND O3.CURRENCY = '".$DB->ForSQL($CURRENCY)."' AND O3.PAYED = 'Y' AND O3.LID = '".$DB->ForSQL($LID)."' )", "TYPE" => "double"),
				"ORDER_SUM" => array("FIELD" => "(SELECT SUM(O3.PRICE) FROM b_sale_order O3 WHERE O3.USER_ID=F.USER_ID AND O3.CURRENCY = '".$DB->ForSQL($CURRENCY)."' AND O3.PAYED = 'Y' AND O3.LID = '".$DB->ForSQL($LID)."' )", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "O1.CURRENCY", "TYPE" => "string", "FROM" => "LEFT JOIN b_sale_order O1 ON (O1.USER_ID=U.ID AND O1.CURRENCY = '".$DB->ForSQL($CURRENCY)."' AND O1.LID = '".$DB->ForSQL($LID)."' AND O1.PAYED = 'Y')"),
				"LAST_ORDER_DATE" => array("FIELD" => "(SELECT MAX(O2.DATE_INSERT) FROM b_sale_order O2 WHERE (O2.USER_ID=F.USER_ID))", "TYPE" => "datetime"),
				"NAME_SEARCH" => array("FIELD" => "U.NAME, U.LAST_NAME, U.SECOND_NAME, U.EMAIL, U.LOGIN, U.ID", "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (U.ID = F.USER_ID)"),
				"USER_ID" => array("FIELD" => "F.USER_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_user U ON (U.ID = F.USER_ID)"),
			);

		foreach($arFields_m as $val)
		{
			$arFields[$val] = array("FIELD" => "U.".$val, "TYPE" => "string", "FROM" => "INNER JOIN b_user U ON (U.ID = F.USER_ID)");
		}
		foreach($arFields_md as $val)
		{
			$arFields[$val] = array("FIELD" => "U.".$val, "TYPE" => "datetime", "FROM" => "INNER JOIN b_user U ON (U.ID = F.USER_ID)");
		}

		if(!in_array("USER_ID", $arSelectFields))
			$arSelectFields[] = "USER_ID";

		$arFilterH = Array();
		if(!empty($arFilter))
		{
			foreach($arFilter as $k => $v)
			{
				if(strpos($k, "ORDER_SUM") !== false || strpos($k, "ORDER_COUNT") !== false || strpos($k, "LAST_ORDER_DATE") !== false)
				{
					$arFilterH[$k] = $v;
					unset($arFilter[$k]);
				}
			}
		}

		if(!empty($arFilterH))
			$arSqlsH = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilterH, false, $arSelectFields);
		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		$strSql =
			"SELECT ".$arSqls["SELECT"]." ".
			"FROM b_sale_fuser F ".
			"	".$arSqls["FROM"]." ";
		if(strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		$strSql .= "GROUP BY F.USER_ID ";
		if (strlen($arSqlsH["WHERE"]) > 0)
			$strSql .= "HAVING ".$arSqlsH["WHERE"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";
		// echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp =
				"SELECT COUNT('x') as CNT ".
				"FROM b_sale_fuser F ".
				"	".$arSqls["FROM"]." ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			$strSql_tmp .= "GROUP BY F.USER_ID ";
			if (strlen($arSqlsH["WHERE"]) > 0)
				$strSql_tmp .= "HAVING ".$arSqlsH["WHERE"]." ";

			// echo "!2.1!=".htmlspecialcharsbx($strSql_tmp)."<br>";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = $dbRes->SelectedRowsCount();

			$dbRes = new CDBResult();

			// echo "!2.2!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);

			// echo "!3!=".htmlspecialcharsbx($strSql)."<br>";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}
?>