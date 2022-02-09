<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/discount_coupon.php");

class CCatalogDiscountCoupon extends CAllCatalogDiscountCoupon
{
	public function Add($arFields, $bAffectDataFile = true)
	{
		global $DB;
		global $USER;

		foreach (GetModuleEvents("catalog", "OnBeforeCouponAdd", true) as $arEvent)
		{
			if (false === ExecuteModuleEventEx($arEvent, array(&$arFields, &$bAffectDataFile)))
				return false;
		}

		$bAffectDataFile = false;
		$arFields1 = array();
		if (isset($USER) && $USER instanceof CUser && 'CUser' == get_class($USER))
		{
			if (!array_key_exists('CREATED_BY', $arFields) || intval($arFields["CREATED_BY"]) <= 0)
				$arFields["CREATED_BY"] = intval($USER->GetID());
			if (!array_key_exists('MODIFIED_BY', $arFields) || intval($arFields["MODIFIED_BY"]) <= 0)
				$arFields["MODIFIED_BY"] = intval($USER->GetID());
		}
		if (array_key_exists('TIMESTAMP_X', $arFields))
			unset($arFields['TIMESTAMP_X']);
		if (array_key_exists('DATE_CREATE', $arFields))
			unset($arFields['DATE_CREATE']);

		$arFields1['TIMESTAMP_X'] = $DB->GetNowFunction();
		$arFields1['DATE_CREATE'] = $DB->GetNowFunction();

		if (!CCatalogDiscountCoupon::CheckFields("ADD", $arFields, 0))
			return false;

		$arInsert = $DB->PrepareInsert("b_catalog_discount_coupon", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($arInsert[0])>0)
			{
				$arInsert[0] .= ", ";
				$arInsert[1] .= ", ";
			}
			$arInsert[0] .= $key;
			$arInsert[1] .= $value;
		}

		$strSql =
			"INSERT INTO b_catalog_discount_coupon(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$ID = intval($DB->LastID());

		foreach (GetModuleEvents("catalog", "OnCouponAdd", true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));
		}

		return $ID;
	}

	public function Update($ID, $arFields)
	{
		global $DB;
		global $USER;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		foreach (GetModuleEvents("catalog", "OnBeforeCouponUpdate", true) as $arEvent)
		{
			if (false === ExecuteModuleEventEx($arEvent, array($ID, &$arFields)))
				return false;
		}

		$arFields1 = array();

		if (array_key_exists('CREATED_BY',$arFields))
			unset($arFields['CREATED_BY']);
		if (array_key_exists('DATE_CREATE',$arFields))
			unset($arFields['DATE_CREATE']);
		if (array_key_exists('TIMESTAMP_X', $arFields))
			unset($arFields['TIMESTAMP_X']);

		if (!CCatalogDiscountCoupon::CheckFields("UPDATE", $arFields, $ID))
			return false;

		if (isset($USER) && $USER instanceof CUser && 'CUser' == get_class($USER))
		{
			if (!array_key_exists('MODIFIED_BY', $arFields) || intval($arFields["MODIFIED_BY"]) <= 0)
				$arFields["MODIFIED_BY"] = intval($USER->GetID());
		}
		$arFields1['TIMESTAMP_X'] = $DB->GetNowFunction();

		$strUpdate = $DB->PrepareUpdate("b_catalog_discount_coupon", $arFields);
		if (!empty($strUpdate))
		{
			foreach ($arFields1 as $key => $value)
			{
				if (strlen($strUpdate)>0) $strUpdate .= ", ";
				$strUpdate .= $key."=".$value." ";
			}

			$strSql = "UPDATE b_catalog_discount_coupon SET ".$strUpdate." WHERE ID = ".$ID;
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		foreach (GetModuleEvents("catalog", "OnCouponUpdate", true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));
		}

		return $ID;
	}

	public function Delete($ID, $bAffectDataFile = true)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		foreach (GetModuleEvents("catalog", "OnBeforeCouponDelete", true) as $arEvent)
		{
			if (false === ExecuteModuleEventEx($arEvent, array($ID, &$bAffectDataFile)))
				return false;
		}

		$bAffectDataFile = false;

		$DB->Query("DELETE FROM b_catalog_discount_coupon WHERE ID = ".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach (GetModuleEvents("catalog", "OnCouponDelete", true) as $arEvent)
		{
			if (false === ExecuteModuleEventEx($arEvent, array($ID)))
				return false;
		}

		return true;
	}

	public function DeleteByDiscountID($ID, $bAffectDataFile = true)
	{
		global $DB;

		$bAffectDataFile = false;
		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		$DB->Query("DELETE FROM b_catalog_discount_coupon WHERE DISCOUNT_ID = ".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		return true;
	}

	public function GetByID($ID)
	{
		global $DB;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		$strSql =
			"SELECT CD.ID, CD.DISCOUNT_ID, CD.ACTIVE, CD.COUPON, CD.ONE_TIME, ".
			$DB->DateToCharFunction("CD.DATE_APPLY", "FULL")." as DATE_APPLY, ".
			$DB->DateToCharFunction("CD.TIMESTAMP_X", "FULL")." as TIMESTAMP_X, ".
			"CD.CREATED_BY, CD.MODIFIED_BY, ".$DB->DateToCharFunction('CD.DATE_CREATE', 'FULL').' as DATE_CREATE, '.
			"CD.DESCRIPTION ".
			"FROM b_catalog_discount_coupon CD WHERE CD.ID = ".$ID;

		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;

		return false;
	}

	public function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		$arFields = array(
			"ID" => array("FIELD" => "CD.ID", "TYPE" => "int"),
			"DISCOUNT_ID" => array("FIELD" => "CD.DISCOUNT_ID", "TYPE" => "string"),
			"ACTIVE" => array("FIELD" => "CD.ACTIVE", "TYPE" => "char"),
			"ONE_TIME" => array("FIELD" => "CD.ONE_TIME", "TYPE" => "char"),
			"COUPON" => array("FIELD" => "CD.COUPON", "TYPE" => "string"),
			"DATE_APPLY" => array("FIELD" => "CD.DATE_APPLY", "TYPE" => "datetime"),
			"DISCOUNT_NAME" => array("FIELD" => "CDD.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_catalog_discount CDD ON (CD.DISCOUNT_ID = CDD.ID)"),
			"DESCRIPTION" => array("FIELD" => "CD.DESCRIPTION","TYPE" => "string"),
			"TIMESTAMP_X" => array("FIELD" => "CD.TIMESTAMP_X", "TYPE" => "datetime"),
			"MODIFIED_BY" => array("FIELD" => "CD.MODIFIED_BY", "TYPE" => "int"),
			"DATE_CREATE" => array("FIELD" => "CD.DATE_CREATE", "TYPE" => "datetime"),
			"CREATED_BY" => array("FIELD" => "CD.CREATED_BY", "TYPE" => "int"),
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (is_array($arGroupBy) && empty($arGroupBy))
		{
			$strSql =
				"SELECT ".$arSqls["SELECT"]." ".
				"FROM b_catalog_discount_coupon CD ".
				"	".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_discount_coupon CD ".$arSqls["FROM"]." ";
		if (!empty($arSqls["WHERE"]))
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])<=0)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_catalog_discount_coupon CD ".$arSqls["FROM"]." ";
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if (is_array($arNavStartParams) && intval($arNavStartParams["nTopCount"])>0)
				$strSql .= "LIMIT ".intval($arNavStartParams["nTopCount"]);

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	public function CouponApply($intUserID, $strCoupon)
	{
		global $DB;
		global $CATALOG_ONETIME_COUPONS_ORDER;

		$mxResult = false;

		$intUserID = intval($intUserID);
		if (0 > $intUserID)
			$intUserID = 0;

		$strCoupon = strval($strCoupon);
		$rsCoupons = CCatalogDiscountCoupon::GetList(
			array(),
			array("COUPON" => $strCoupon, 'ACTIVE' => 'Y'),
			false,
			false,
			array("ID", "ONE_TIME")
		);
		if ($arCoupon = $rsCoupons->Fetch())
		{
			$arCoupon['ID'] = intval($arCoupon['ID']);
			$arFields = array(
				"DATE_APPLY" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)))
			);

			if (self::TYPE_ONE_TIME == $arCoupon["ONE_TIME"])
			{
				$arFields["ACTIVE"] = "N";

				if (0 < $intUserID)
				{
					CCatalogDiscountCoupon::EraseCouponByManage($intUserID, $strCoupon);
				}
				else
				{
					CCatalogDiscountCoupon::EraseCoupon($strCoupon);
				}
			}
			elseif (self::TYPE_ONE_ORDER == $arCoupon["ONE_TIME"])
			{
				if (!is_array($CATALOG_ONETIME_COUPONS_ORDER))
				{
					$CATALOG_ONETIME_COUPONS_ORDER = array();
					AddEventHandler("sale", "OnBasketOrder", 'CatalogDeactivateOneTimeCoupons');
					AddEventHandler("sale", "OnDoBasketOrder", 'CatalogDeactivateOneTimeCoupons');
				}
				if (!array_key_exists($arCoupon['ID'], $CATALOG_ONETIME_COUPONS_ORDER))
					$CATALOG_ONETIME_COUPONS_ORDER[$arCoupon['ID']] = array(
						'COUPON' => $strCoupon,
						'USER_ID' => $intUserID,
					);
			}

			$strUpdate = $DB->PrepareUpdate("b_catalog_discount_coupon", $arFields);
			if (!empty($strUpdate))
			{
				$strSql = "UPDATE b_catalog_discount_coupon SET ".$strUpdate." WHERE ID = ".$arCoupon['ID'];
				$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				$mxResult = $arCoupon['ID'];
			}
		}

		return $mxResult;
	}

	public function __CouponOneOrderDisable($arCoupons)
	{
		global $DB;
		if (!is_array($arCoupons))
			$arCoupons = array(intval($arCoupons));
		if (!empty($arCoupons))
		{
			$arWhere = array();
			foreach ($arCoupons as &$intCouponID)
			{
				$intCouponID = intval($intCouponID);
				if (0 < $intCouponID)
					$arWhere[] = $intCouponID;
			}
			if (isset($intCouponID))
				unset($intCouponID);
			$strSql = "UPDATE b_catalog_discount_coupon SET ACTIVE='N' WHERE ID IN (".implode(', ', $arWhere).") AND ONE_TIME='".self::TYPE_ONE_ORDER."'";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
	}
}
?>