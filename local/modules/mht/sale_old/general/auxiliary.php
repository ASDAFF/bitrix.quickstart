<?
IncludeModuleLangFile(__FILE__);

/***********************************************************************/
/***********  CSaleAuxiliary  ******************************************/
/***********************************************************************/
class CAllSaleAuxiliary
{
	function PrepareItemMD54Where($val, $key, $operation, $negative, $field, &$arField, &$arFilter)
	{
		$val1 = md5($val);
		return "(".($negative=="Y"?" A.ITEM_MD5 IS NULL OR NOT ":"")."(A.ITEM_MD5 ".$operation." '".$GLOBALS["DB"]->ForSql($val1)."' )".")";
	}

	//********** CHECK **************//
	function CheckAccess($userID, $itemMD5, $periodLength, $periodType)
	{
		global $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return false;

		$itemMD5 = Trim($itemMD5);
		if (strlen($itemMD5) <= 0)
			return false;

		$periodLength = IntVal($periodLength);
		if ($periodLength <= 0)
			return False;

		$periodType = Trim($periodType);
		$periodType = ToUpper($periodType);
		if (strlen($periodType) <= 0)
			return False;

		$checkVal = 0;
		if ($periodType == "I")
			$checkVal = mktime(date("H"), date("i") - $periodLength, date("s"), date("m"), date("d"), date("Y"));
		elseif ($periodType == "H")
			$checkVal = mktime(date("H") - $periodLength, date("i"), date("s"), date("m"), date("d"), date("Y"));
		elseif ($periodType == "D")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") - $periodLength, date("Y"));
		elseif ($periodType == "W")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d") - 7 * $periodLength, date("Y"));
		elseif ($periodType == "M")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m") - $periodLength, date("d"), date("Y"));
		elseif ($periodType == "Q")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m") - 3 * $periodLength, date("d"), date("Y"));
		elseif ($periodType == "S")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m") - 6 * $periodLength, date("d"), date("Y"));
		elseif ($periodType == "Y")
			$checkVal = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y") - $periodLength);

		if ($checkVal <= 0)
			return False;

		$dbAuxiliary = CSaleAuxiliary::GetList(
				array(),
				array(
						"USER_ID" => $userID,
						"ITEM_MD5" => $itemMD5,
						">=DATE_INSERT" => Date($GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), $checkVal)
					),
				false,
				false,
				array("*")
			);
		if ($arAuxiliary = $dbAuxiliary->Fetch())
			return $arAuxiliary;

		return false;
	}

	//********** ADD, UPDATE, DELETE **************//
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty user field", "EMPTY_USER_ID");
			return false;
		}
		if ((is_set($arFields, "ITEM") || $ACTION=="ADD") && strlen($arFields["ITEM"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty item field", "EMPTY_ITEM");
			return false;
		}
		if ((is_set($arFields, "ITEM_MD5") || $ACTION=="ADD") && strlen($arFields["ITEM_MD5"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty item md5 field", "EMPTY_ITEM_MD5");
			return false;
		}
		if ((is_set($arFields, "DATE_INSERT") || $ACTION=="ADD") && strlen($arFields["DATE_INSERT"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty date insert field", "EMPTY_DATE_INSERT");
			return false;
		}

		if (is_set($arFields, "ITEM_MD5"))
			$arFields["ITEM_MD5"] = md5($arFields["ITEM_MD5"]);

		if (is_set($arFields, "USER_ID"))
		{
			$dbUser = CUser::GetByID($arFields["USER_ID"]);
			if (!$dbUser->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["USER_ID"], GetMessage("SGMA_NO_USER")), "ERROR_NO_USER_ID");
				return false;
			}
		}

		return True;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		return $DB->Query("DELETE FROM b_sale_auxiliary WHERE ID = ".$ID." ", true);
	}

	function DeleteByUserID($userID)
	{
		global $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return False;

		return $DB->Query("DELETE FROM b_sale_auxiliary WHERE USER_ID = ".$userID." ", true);
	}

	//********** EVENTS **************//
	function OnUserDelete($userID)
	{
		$userID = IntVal($userID);

		$bSuccess = True;

		if (!CSaleAuxiliary::DeleteByUserID($userID))
			$bSuccess = False;

		return $bSuccess;
	}

	//********** AGENTS **************//
	function DeleteOldAgent($periodLength, $periodType)
	{
		CSaleAuxiliary::DeleteByTime($periodLength, $periodType);

		global $pPERIOD;
		$pPERIOD = 12*60*60;
		return 'CSaleAuxiliary::DeleteOldAgent('.$periodLength.', "'.$periodType.'");';
	}
}
?>