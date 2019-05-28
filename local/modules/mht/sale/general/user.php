<?
IncludeModuleLangFile(__FILE__);

Define("SALE_TIME_LOCK_USER", 600);
$GLOBALS["SALE_USER_ACCOUNT"] = Array();

/***********************************************************************/
/***********  CSaleUserAccount  ****************************************/
/***********************************************************************/
class CAllSaleUserAccount
{
	static function DoPayOrderFromAccount($userId, $currency, $orderId, $orderSum, $arOptions, &$arErrors)
	{
		if (!array_key_exists("ONLY_FULL_PAY_FROM_ACCOUNT", $arOptions))
			$arOptions["ONLY_FULL_PAY_FROM_ACCOUNT"] = COption::GetOptionString("sale", "ONLY_FULL_PAY_FROM_ACCOUNT", "N");

		$dbUserAccount = CSaleUserAccount::GetList(
			array(),
			array(
				"USER_ID" => $userId,
				"CURRENCY" => $currency,
			)
		);
		$arUserAccount = $dbUserAccount->Fetch();

		if (!$arUserAccount)
			return false;
		if ($arUserAccount["CURRENT_BUDGET"] <= 0)
			return false;
		if (($arOptions["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") && (doubleval($arUserAccount["CURRENT_BUDGET"]) < doubleval($orderSum)))
			return false;

		$withdrawSum = CSaleUserAccount::Withdraw(
			$userId,
			$orderSum,
			$currency,
			$orderId
		);

		if ($withdrawSum > 0)
		{
			$arFields = array(
				"SUM_PAID" => $withdrawSum,
				"USER_ID" => $userId
			);

			CSaleOrder::Update($orderId, $arFields);
			if ($withdrawSum == $orderSum)
				CSaleOrder::PayOrder($orderId, "Y", False, False);

			return true;
		}

		return false;
	}

	//********** ADD, UPDATE, DELETE **************//
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_USER_ID"), "EMPTY_USER_ID");
			return false;
		}
		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_CURRENCY"), "EMPTY_CURRENCY");
			return false;
		}

		if (is_set($arFields, "CURRENT_BUDGET") || $ACTION=="ADD")
		{
			$arFields["CURRENT_BUDGET"] = str_replace(",", ".", $arFields["CURRENT_BUDGET"]);
			$arFields["CURRENT_BUDGET"] = DoubleVal($arFields["CURRENT_BUDGET"]);
		}

		if ((is_set($arFields, "LOCKED") || $ACTION=="ADD") && $arFields["LOCKED"] != "Y")
			$arFields["LOCKED"] = "N";

		if (is_set($arFields, "USER_ID"))
		{
			$dbUser = CUser::GetByID($arFields["USER_ID"]);
			if (!$dbUser->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["USER_ID"], GetMessage("SKGU_NO_USER")), "ERROR_NO_USER_ID");
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

		$db_events = GetModuleEvents("sale", "OnBeforeUserAccountDelete");
		while ($arEvent = $db_events->Fetch())
		{
			if (ExecuteModuleEventEx($arEvent, Array($ID))===false)
			{
				return false;
			}
		}

		$arOldUserAccount = CSaleUserAccount::GetByID($ID);

		$dbTrans = CSaleUserTransact::GetList(array(), array("USER_ID" => $arOldUserAccount["USER_ID"], "CURRENCY" => $arOldUserAccount["CURRENCY"]), false, false, Array("ID", "USER_ID"));
		while($arTrans = $dbTrans -> Fetch())
			CSaleUserTransact::Delete($arTrans["ID"]);

		unset($GLOBALS["SALE_USER_ACCOUNT"]["SALE_USER_ACCOUNT_CACHE_".$ID]);
		unset($GLOBALS["SALE_USER_ACCOUNT"]["SALE_USER_ACCOUNT_CACHE1_".$arOldUserAccount["USER_ID"]."_".$arOldUserAccount["CURRENCY"]]);

		$res = $DB->Query("DELETE FROM b_sale_user_account WHERE ID = ".$ID." ", true);

		$dbEvents = GetModuleEvents("sale", "OnAfterUserAccountDelete");
		while ($arEvent = $dbEvents->Fetch())
		{
			ExecuteModuleEventEx($arEvent, Array($ID));
		}

		return $res;
	}


	//********** LOCK **************//
	function Lock($userID, $payCurrency)
	{
		global $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
			return False;

		$payCurrency = Trim($payCurrency);
		if (strlen($payCurrency) <= 0)
			return False;

		$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "CURRENCY" => $payCurrency),
				false,
				false,
				array("ID", "LOCKED", "DATE_LOCKED")
			);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			if ($arUserAccount["LOCKED"] == "Y")
			{
				if (!($dateLocked = MakeTimeStamp($arUserAccount["DATE_LOCKED"], CSite::GetDateFormat("FULL", SITE_ID))))
					$dateLocked = mktime(0, 0, 0, 1, 1, 1990);
			}

			if (defined("SALE_TIME_LOCK_USER") && IntVal(SALE_TIME_LOCK_USER) > 0)
				$timeLockUser = IntVal(SALE_TIME_LOCK_USER);
			else
				$timeLockUser = 10 * 60;

			if (($arUserAccount["LOCKED"] != "Y")
				|| (($arUserAccount["LOCKED"] == "Y") && ((time() - $dateLocked) > $timeLockUser)))
			{
				$arFields = array(
						"LOCKED" => "Y",
						"=DATE_LOCKED" => $DB->GetNowFunction()
					);
				if (CSaleUserAccount::Update($arUserAccount["ID"], $arFields))
					return True;
				else
					return False;
			}
			else
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_ACCOUNT_LOCKED"), "ACCOUNT_LOCKED");
				return False;
			}
		}
		else
		{
			$arFields = array(
					"USER_ID" => $userID,
					"CURRENT_BUDGET" => 0.0,
					"CURRENCY" => $payCurrency,
					"LOCKED" => "Y",
					"DATE_LOCKED" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)))
				);
			if (CSaleUserAccount::Add($arFields))
				return True;
			else
				return False;
		}
	}

	function UnLock($userID, $payCurrency)
	{
		$userID = IntVal($userID);
		if ($userID <= 0)
			return False;

		$payCurrency = Trim($payCurrency);
		if (strlen($payCurrency) <= 0)
			return False;

		$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "CURRENCY" => $payCurrency),
				false,
				false,
				array("ID", "LOCKED", "DATE_LOCKED")
			);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			if ($arUserAccount["LOCKED"] == "Y")
			{
				$arFields = array(
						"LOCKED" => "N",
						"DATE_LOCKED" => false
					);
				if (CSaleUserAccount::Update($arUserAccount["ID"], $arFields))
				{
					return True;
				}
				else
				{
					return False;
				}
			}
			else
			{
				return True;
			}
		}
		else
		{
			$arFields = array(
					"USER_ID" => $userID,
					"CURRENT_BUDGET" => 0.0,
					"CURRENCY" => $payCurrency,
					"LOCKED" => "N",
					"DATE_LOCKED" => false
				);
			if (CSaleUserAccount::Add($arFields))
				return True;
			else
				return False;
		}
	}

	function UnLockByID($ID)
	{
		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		if ($arUserAccount = CSaleUserAccount::GetByID($ID))
		{
			if ($arUserAccount["LOCKED"] == "Y")
			{
				$arFields = array(
						"LOCKED" => "N",
						"DATE_LOCKED" => false
					);
				if (CSaleUserAccount::Update($arUserAccount["ID"], $arFields))
					return True;
				else
					return False;
			}
			else
			{
				return True;
			}
		}
		else
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_NO_ACCOUNT"), "NO_ACCOUNT");
			return False;
		}
	}

	//********** ACTIONS **************//

	// Pay money from the local user account. Increase the local user account if necessary.
	// $userID - ID of the user
	// $paySum - payment sum
	// $payCurrency - currency
	// $orderID - ID of order (if known)
	// $useCC - increase the local user account from credit card if necessary (default - True)
	// Return True if the necessary sum withdraw from an account or False in other way
	function Pay($userID, $paySum, $payCurrency, $orderID = 0, $useCC = True)
	{
		global $DB;

		$errorCode = "";

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_USER_ID"), "EMPTY_USER_ID");
			return False;
		}

		$paySum = str_replace(",", ".", $paySum);
		$paySum = DoubleVal($paySum);
		if ($paySum <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_SUM"), "EMPTY_SUM");
			return False;
		}

		$payCurrency = Trim($payCurrency);
		if (strlen($payCurrency) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_CURRENCY"), "EMPTY_CURRENCY");
			return False;
		}

		$orderID = IntVal($orderID);

		$useCC = ($useCC ? True : False);

		if (!CSaleUserAccount::Lock($userID, $payCurrency))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_ERROR_LOCK"), "ACCOUNT_NOT_LOCKED");
			return False;
		}

		$currentBudget = 0.0;

		// Check current user account budget
		$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "CURRENCY" => $payCurrency)
			);
		if ($arUserAccount = $dbUserAccount->Fetch())
			$currentBudget = roundEx(DoubleVal($arUserAccount["CURRENT_BUDGET"]), SALE_VALUE_PRECISION);

		$withdrawSum = 0;
		if (($currentBudget < $paySum) && $useCC)
		{
			$payOverdraft = $paySum - $currentBudget;

			// Try to get money from credit cards
			$bPayed = False;
			$dbUserCards = CSaleUserCards::GetList(
					array("SORT" => "ASC"),
					array("USER_ID" => $userID, "CURRENCY" => $payCurrency, "ACTIVE" => "Y")
				);
			while ($arUserCard = $dbUserCards->Fetch())
			{
				if ($withdrawSum = CSaleUserCards::Withdraw($payOverdraft, $payCurrency, $arUserCard, $orderID))
				{
					$bPayed = True;
					break;
				}
			}

			if (!$bPayed)
			{
				$dbUserCards = CSaleUserCards::GetList(
						array("SORT" => "ASC"),
						array("USER_ID" => $userID, "CURRENCY" => "", "ACTIVE" => "Y")
					);
				while ($arUserCard = $dbUserCards->Fetch())
				{
					if ($withdrawSum = CSaleUserCards::Withdraw($payOverdraft, $payCurrency, $arUserCard, $orderID))
					{
						$bPayed = True;
						break;
					}
				}
			}

			if ($bPayed)
			{
				$arFields = array(
						"USER_ID" => $userID,
						"TRANSACT_DATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID))),
						"AMOUNT" => $withdrawSum,
						"CURRENCY" => $payCurrency,
						"DEBIT" => "Y",
						"ORDER_ID" => (($orderID > 0) ? $orderID : False),
						"DESCRIPTION" => "CC_CHARGE_OFF",
						"EMPLOYEE_ID" => ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : False)
					);
				CSaleUserTransact::Add($arFields);

				if ($arUserAccount)
				{
					$arFields = array(
							"CURRENT_BUDGET" => ($withdrawSum + $currentBudget)
						);
					CSaleUserAccount::Update($arUserAccount["ID"], $arFields);
				}
				else
				{
					$arFields = array(
							"USER_ID" => $userID,
							"CURRENT_BUDGET" => ($withdrawSum + $currentBudget),
							"CURRENCY" => $payCurrency
						);
					CSaleUserAccount::Add($arFields);
				}
			}
		}

		if ($withdrawSum + $currentBudget >= $paySum)
		{
			if ($arUserAccount)
			{
				$arFields = array(
						"CURRENT_BUDGET" => ($withdrawSum + $currentBudget - $paySum)
					);
				CSaleUserAccount::Update($arUserAccount["ID"], $arFields);
			}
			else
			{
				$arFields = array(
						"USER_ID" => $userID,
						"CURRENT_BUDGET" => ($withdrawSum + $currentBudget - $paySum),
						"CURRENCY" => $payCurrency
					);
				CSaleUserAccount::Add($arFields);
			}

			$arFields = array(
					"USER_ID" => $userID,
					"TRANSACT_DATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID))),
					"AMOUNT" => $paySum,
					"CURRENCY" => $payCurrency,
					"DEBIT" => "N",
					"ORDER_ID" => (($orderID > 0) ? $orderID : False),
					"DESCRIPTION" => "ORDER_PAY",
					"EMPLOYEE_ID" => ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : False)
				);
			CSaleUserTransact::Add($arFields);

			CSaleUserAccount::UnLock($userID, $payCurrency);
			return True;
		}

		CSaleUserAccount::UnLock($userID, $payCurrency);
		$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_NO_ENOUGH"), "CANT_PAY");

		return False;
	}


	// Pay money from the local user account. If there is not enough money on the local user
	// account then withdraw the max available sum.
	// $userID - ID of the user
	// $paySum - payment sum
	// $payCurrency - currency
	// $orderID - ID of order (if known)
	// Return withdrawn sum or False
	function Withdraw($userID, $paySum, $payCurrency, $orderID = 0)
	{
		global $DB;

		$errorCode = "";

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTYID"), "EMPTY_USER_ID");
			return False;
		}

		$paySum = str_replace(",", ".", $paySum);
		$paySum = DoubleVal($paySum);
		if ($paySum <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_SUM"), "EMPTY_SUM");
			return False;
		}

		$payCurrency = Trim($payCurrency);
		if (strlen($payCurrency) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_EMPTY_CUR"), "EMPTY_CURRENCY");
			return False;
		}

		$orderID = IntVal($orderID);

		if (!CSaleUserAccount::Lock($userID, $payCurrency))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGU_ACCOUNT_NOT_LOCKED"), "ACCOUNT_NOT_LOCKED");
			return False;
		}

		$currentBudget = 0.0;

		// Check current user account budget
		$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "CURRENCY" => $payCurrency)
			);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			$currentBudget = DoubleVal($arUserAccount["CURRENT_BUDGET"]);

			if ($currentBudget > 0)
			{
				$withdrawSum = $paySum;
				if ($withdrawSum > $currentBudget)
					$withdrawSum = $currentBudget;

				$arFields = array(
						"CURRENT_BUDGET" => ($currentBudget - $withdrawSum)
					);
				CSaleUserAccount::Update($arUserAccount["ID"], $arFields);

				$arFields = array(
						"USER_ID" => $userID,
						"TRANSACT_DATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID))),
						"AMOUNT" => $withdrawSum,
						"CURRENCY" => $payCurrency,
						"DEBIT" => "N",
						"ORDER_ID" => (($orderID > 0) ? $orderID : False),
						"DESCRIPTION" => "ORDER_PAY",
						"EMPLOYEE_ID" => ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : False)
					);
				CSaleUserTransact::Add($arFields);

				CSaleUserAccount::UnLock($userID, $payCurrency);
				return $withdrawSum;
			}
		}

		CSaleUserAccount::UnLock($userID, $payCurrency);
		return False;
	}


	// Modify sum of the current local user account.
	// $userID - ID of the user
	// $sum - payment sum
	// $currency - currency
	// $description - reason of modification
	// Return True on success or False in other way
	function UpdateAccount($userID, $sum, $currency, $description = "", $orderID = 0, $notes = "")
	{
		global $DB, $APPLICATION;

		$userID = (int)$userID;
		if ($userID <= 0)
		{
			$APPLICATION->ThrowException(GetMessage("SKGU_EMPTYID"), "EMPTY_USER_ID");
			return False;
		}
		$dbUser = CUser::GetByID($userID);
		if (!$dbUser->Fetch())
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $userID, GetMessage("SKGU_NO_USER")), "ERROR_NO_USER_ID");
			return False;
		}

		$sum = (float)str_replace(",", ".", $sum);

		$currency = trim($currency);
		if ($currency === '')
		{
			$APPLICATION->ThrowException(GetMessage("SKGU_EMPTY_CUR"), "EMPTY_CURRENCY");
			return False;
		}

		$orderID = (int)$orderID;
		if (!CSaleUserAccount::Lock($userID, $currency))
		{
			$APPLICATION->ThrowException(GetMessage("SKGU_ACCOUNT_NOT_WORK"), "ACCOUNT_NOT_LOCKED");
			return False;
		}

		$result = false;

		$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "CURRENCY" => $currency)
			);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			$arFields = array(
					"CURRENT_BUDGET" => $arUserAccount["CURRENT_BUDGET"] + $sum
				);
			$result = CSaleUserAccount::Update($arUserAccount["ID"], $arFields);
		}
		else
		{
			$arFields = array(
					"USER_ID" => $userID,
					"CURRENT_BUDGET" => $sum,
					"CURRENCY" => $currency,
					"LOCKED" => "Y",
					"DATE_LOCKED" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)))
				);
			$result = CSaleUserAccount::Add($arFields);
		}

		if ($result)
		{
			if (isset($GLOBALS["SALE_USER_ACCOUNT"]["SALE_USER_ACCOUNT_CACHE_".$userID."_".$currency]))
				unset($GLOBALS["SALE_USER_ACCOUNT"]["SALE_USER_ACCOUNT_CACHE_".$userID."_".$currency]);

			$arFields = array(
					"USER_ID" => $userID,
					"TRANSACT_DATE" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID))),
					"AMOUNT" => (($sum > 0) ? $sum : -$sum),
					"CURRENCY" => $currency,
					"DEBIT" => (($sum > 0) ? "Y" : "N"),
					"ORDER_ID" => (($orderID > 0) ? $orderID : False),
					"DESCRIPTION" => ((strlen($description) > 0) ? $description : False),
					"NOTES" => ((strlen($notes) > 0) ? $notes : False),
					"EMPLOYEE_ID" => ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : False)
				);
			CSaleUserTransact::Add($arFields);
		}

		CSaleUserAccount::UnLock($userID, $currency);
		return $result;
	}

	//********** EVENTS **************//
	function OnBeforeCurrencyDelete($Currency)
	{
		global $DB;
		if (strlen($Currency)<=0) return false;

		$cnt = CSaleUserAccount::GetList(array(), array("CURRENCY" => $Currency), array());
		if ($cnt > 0)
			return False;

		return True;
	}

	function OnUserDelete($userID)
	{
		$userID = IntVal($userID);

		$bSuccess = True;

		$dbUserAccounts = CSaleUserAccount::GetList(array(), array("USER_ID" => $userID), false, false, array("ID"));
		while ($arUserAccount = $dbUserAccounts->Fetch())
		{
			if (!CSaleUserAccount::Delete($arUserAccount["ID"]))
				$bSuccess = False;
		}

		return $bSuccess;
	}

	function OnBeforeUserDelete($userID)
	{
		$userID = IntVal($userID);

		$bCanDelete = True;

		$dbUserAccounts = CSaleUserAccount::GetList(
				array(),
				array("USER_ID" => $userID, "!CURRENT_BUDGET" => 0),
				false,
				false,
				array("ID")
			);
		if ($arUserAccount = $dbUserAccounts->Fetch())
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#USER_ID#", $userID, GetMessage("UA_ERROR_USER")), "ERROR_UACCOUNT");
			return False;
		}

		return $bCanDelete;
	}
}
?>