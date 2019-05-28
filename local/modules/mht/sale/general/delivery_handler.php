<?
IncludeModuleLangFile(__FILE__);

class CAllSaleDeliveryHandler
{
	public static $actions = array(
		"REQUEST_SELF" => 0, // Request to delivery company to wait a cargo
		"REQUEST_TAKE" => 1  // Request to delivery company to take a cargo
	);

	// public: Initialize
	// includes all delivery_*.php files in /php_interface/include/sale_delivery/ and /modules/sale/delivery/
	// double files with the same name are ignored
	function Initialize()
	{
		$arPathList = array( // list of valid handler include files paths (security)
			COption::GetOptionString('sale', 'delivery_handles_custom_path', BX_PERSONAL_ROOT."/php_interface/include/sale_delivery/"),
			"/bitrix/modules/sale/delivery/",
		);

		$arLoadedHandlers = array();

		foreach ($arPathList as $basePath)
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$basePath) && is_dir($_SERVER["DOCUMENT_ROOT"].$basePath))
			{
				$handle = @opendir($_SERVER["DOCUMENT_ROOT"].$basePath);
				while(($filename = readdir($handle)) !== false)
				{
					if($filename == "." || $filename == ".." || in_array($filename, $arLoadedHandlers))
						continue;

					if (!is_dir($_SERVER["DOCUMENT_ROOT"].$basePath."/".$filename) && substr($filename, 0, 9) == "delivery_")
					{
						$arLoadedHandlers[] = $filename;

						require_once($_SERVER["DOCUMENT_ROOT"].$basePath."/".$filename);
					}
				}
				@closedir($handle);
			}
		}

		define('SALE_DH_INITIALIZED', 1);
	}

	// private: get full info for all loaded handlers
	function __getHandlersData($arFullHandlersList, $SITE_ID = false)
	{
		global $DB;

		if (!is_array($arFullHandlersList) || count($arFullHandlersList) <= 0)
			return false;

		$strKeys = '';
		$cnt = count($arFullHandlersList);
		$arHandlersMap = array();

		for ($i = 0; $i < $cnt; $i++)
		{
			$strKeys .= ($i > 0 ? ', ' : '')."'".$DB->ForSql($arFullHandlersList[$i]['SID'])."'";
			$arHandlersMap[$arFullHandlersList[$i]['SID']] = $i;
		}

		$query = "
SELECT HID AS SID, LID, ACTIVE, NAME, SORT, DESCRIPTION, HANDLER, SETTINGS, PROFILES, TAX_RATE, LOGOTIP, BASE_CURRENCY
FROM b_sale_delivery_handler
WHERE HID IN (".$strKeys.")";

		if ($SITE_ID)
			$query .= "AND (LID='".$DB->ForSql($SITE_ID)."' OR LID='' OR LID IS NULL)";

		$dbRes = $DB->Query($query);
		$arHandlersList = array();

		$arInstalledHandlersMap = array();

		while ($arRes = $dbRes->Fetch())
		{
			$arHandler = $arFullHandlersList[$arHandlersMap[$arRes['SID']]];
			$arRes["LID"] = trim($arRes["LID"]);
			$arHandler["LID"] = $arRes["LID"];
			$arHandler["ACTIVE"] = $arRes["ACTIVE"];
			$arHandler["SORT"] = $arRes["SORT"];
			$arHandler["NAME"] = $arRes["NAME"];
			$arHandler["DESCRIPTION"] = $arRes["DESCRIPTION"];
			$arHandler["TAX_RATE"] = doubleval($arRes["TAX_RATE"]);

			if(strlen($arRes["BASE_CURRENCY"]) > 0)
				$arHandler["BASE_CURRENCY"] = $arRes["BASE_CURRENCY"];

			$arHandler["INSTALLED"] = "Y";

			if (intval($arRes["LOGOTIP"]) > 0)
				$arHandler["LOGOTIP"] = CFile::GetFileArray($arRes["LOGOTIP"]);

			$arInstalledHandlersMap[$arRes["SID"]] = 1;

			if (is_callable($arHandler["GETCONFIG"]))
			{
				$arHandler["CONFIG"] = call_user_func($arHandler["GETCONFIG"], $SITE_ID);

				if (strlen($arRes["SETTINGS"]) > 0 && is_callable($arHandler["DBGETSETTINGS"]))
				{
					$arConfigValues = call_user_func($arHandler["DBGETSETTINGS"], $arRes["SETTINGS"]);

					foreach ($arConfigValues as $key => $value)
					{
						if (is_array($arHandler["CONFIG"]["CONFIG"][$key]))
							$arHandler["CONFIG"]["CONFIG"][$key]["VALUE"] = $value;
					}
				}
				else
				{
					foreach ($arHandler["CONFIG"]["CONFIG"] as $key => $arConfig)
					{
						if (is_array($arHandler["CONFIG"]["CONFIG"][$key]))
							$arHandler["CONFIG"]["CONFIG"][$key]["VALUE"] = $arHandler["CONFIG"]["CONFIG"][$key]["DEFAULT"];
					}
				}
			}
			else
			{
				$arHandler["CONFIG"] = array(
					"CONFIG_GROUPS" => array(),
					"CONFIG" => array(),
				);
			}

			// set handler profiles data
			if (strlen($arRes["PROFILES"]) > 0)
			{
				$arHandler["PROFILES"] = unserialize($arRes["PROFILES"]);
				$arHandler["PROFILE_USE_DEFAULT"] = "N";
			}
			else
			{
				$arHandler["PROFILE_USE_DEFAULT"] = "Y";
				foreach ($arHandler['PROFILES'] as $pkey => $arProfile)
				{
					$arHandler['PROFILES'][$pkey]['ACTIVE'] = 'Y';
				}
			}

			$arHandlersList[] = $arHandler;
		}

		foreach ($arFullHandlersList as $key => $arHandler)
		{
			if (array_key_exists($arHandler["SID"], $arInstalledHandlersMap)) continue;

			$siteList = array();

			if(strlen($SITE_ID) <= 0 && isset($arHandler["MULTISITE_CONFIG"]) && $arHandler["MULTISITE_CONFIG"] == "Y")
			{
				$rsSites = CSite::GetList($by = "sort", $order = "asc", Array());

				while($arRes = $rsSites->Fetch())
					$siteList[] = $arRes['ID'];
			}
			else
			{
				$siteList[] = $SITE_ID;
			}

			foreach($siteList as $siteId)
			{
				$arHandler["INSTALLED"] = "N";
				$arHandler["LID"] = trim($siteId);
				$arHandler['ACTIVE'] = "N";
				$arHandler["SORT"] = '';
				$arHandler["TAX_RATE"] = 0;
				$arHandler["PROFILE_USE_DEFAULT"] = "Y";

				if (is_callable($arHandler["GETCONFIG"]))
				{
					$arHandler["CONFIG"] = call_user_func($arHandler["GETCONFIG"], $siteId);
				}
				else
				{
					$arHandler["CONFIG"] = array(
						"CONFIG_GROUPS" => array(),
						"CONFIG" => array(),
					);
				}

				$arHandlersList[] = $arHandler;
			}
		}

		foreach ($arHandlersList as $key => $arHandler)
		{
			$handler_path = strtolower($arHandler["HANDLER"]);
			$handler_path = str_replace("\\", "/", $handler_path);
			$handler_path = str_replace(strtolower($_SERVER["DOCUMENT_ROOT"]), '', $handler_path);

			if (strlen($arHandler["BASE_CURRENCY"]) <=0 || !CCurrency::GetByID($arHandler["BASE_CURRENCY"]))
				$arHandlersList[$key]["BASE_CURRENCY"] = COption::GetOptionString('sale', 'default_currency', 'RUB');

			$arHandlersList[$key]['HANDLER'] = $handler_path;
		}

		return $arHandlersList;
	}

	// private: get all handlers
	function __getRegisteredHandlers()
	{
		static $arHandlersList = null;
		if (!isset($arHandlersList))
		{
			$arHandlersList = array();
			foreach(GetModuleEvents("sale", "onSaleDeliveryHandlersBuildList", true) as $arHandler)
				$arHandlersList[] = ExecuteModuleEventEx($arHandler);
		}
		return $arHandlersList;
	}

	function __sortList(&$arHandlersList, $arSort)
	{
		if (!is_array($arSort) || count($arSort) <= 0) return;
		if (!is_array($arHandlersList) || count($arHandlersList) <= 0) return;

		foreach ($arSort as $by => $order)
		{
			$arKeyMap = array();
			foreach ($arHandlersList as $key => $arHandler)
			{
				$arKeyMap[$key] = $arHandler[$by];
			}

			if ($order == 'DESC') arsort($arKeyMap);
			else asort($arKeyMap);

			$arHandlersTmp = array();
			foreach ($arKeyMap as $mapkey => $mapvalue)
			{
				$arHandlersTmp[] = $arHandlersList[$mapkey];
			}

			$arHandlersList = $arHandlersTmp;
		}
	}

	// get full list based on FS
	function GetAdminList($arSort = array("SORT" => "ASC"))
	{
		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		$arHandlersList = CSaleDeliveryHandler::__getRegisteredHandlers();
		$arHandlersList = CSaleDeliveryHandler::__getHandlersData($arHandlersList);

		$arAllowedSort = array(
			'SORT', 'ACTIVE', 'SID', 'NAME', 'HANDLER'
		);

		$arSortTmp = array();
		if (is_array($arSort))
		{
			foreach ($arSort as $key => $value)
			{
				$key = ToUpper($key);
				if (in_array($key, $arAllowedSort))
				{
					$value = ToUpper($value);
					$value = $value == 'DESC' ? 'DESC' : 'ASC';
					$arSortTmp[$key] = $value;
				}
			}
			$arSort = $arSortTmp;
		}
		else
			$arSort = array('SORT' => 'ASC');

		CSaleDeliveryHandler::__sortList($arHandlersList, $arSort);

		$dbHandlers = new CDBResult;
		reset($arHandlersList);
		$dbHandlers->InitFromArray($arHandlersList);

		return $dbHandlers;
	}

	// get handlers list based on DB data
	function GetList($arSort = array("SORT" => "ASC"), $arFilter = array())
	{
	/*
	Filter:
		"ACTIVE" => Y (default); 'ALL' for full list without activity check;
		"SITE_ID" => SITE_ID (default); 'ALL' for full list without site check. Syn. SITE;
		"COMPABILITY" => "N" (default); arOrder for additional check inside this method
		"SID" => only with this SID. Syn: "ID";
		"HANDLER" => check by part of handler path. Syn: "PATH"
	*/
		global $DB;

		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		$arAllowedSort = array(
			"SORT" => "SORT",
			"NAME" => "NAME",
			"SID" => "HID",
			"SITE_ID" => "LID",
			"HANDLER" => "HANDLER",
			"ACTIVE" => "ACTIVE",
		);

		foreach ($arSort as $SORT => $SORT_DIR)
		{
			if (array_key_exists($SORT, $arAllowedSort))
			{
				unset($arSort[$SORT]);
				$arSort[$arAllowedSort[$SORT]] = $SORT_DIR == "DESC" ? "DESC" : "ASC";
			}
			else
				unset($arSort[$SORT]);
		}

		// additional filter synonim check
		if (is_set($arFilter, "SITE") && !is_set($arFilter, "SITE_ID")) $arFilter["SITE_ID"] = $arFilter["SITE"];
		unset($arFilter["SITE"]);

		$arFilterDefault = array(
			"SITE_ID" => SITE_ID,
			"COMPABILITY" => "N",
			"ACTIVE" => "Y",
		);

		if (!is_array($arFilter)) $arFilter = array();
		foreach ($arFilterDefault as $key => $value)
		{
			if (!is_set($arFilter, $key)) $arFilter[$key] = $value;
		}

		$bAllSite = false;
		if ($arFilter["SITE_ID"] == "ALL")
		{
			$bAllSite = true;
			unset($arFilter["SITE_ID"]);
		}

		if ($arFilter["ACTIVE"] == "ALL") unset($arFilter["ACTIVE"]);

		$arWhere = array();
		$strWhere = "";
		$arFilterKeys = array_keys($arFilter);
		foreach ($arFilter as $key => $value)
		{
			$match_value_set = (in_array($key."_EXACT_MATCH", $arFilterKeys)) ? true : false;
			$match = ($arFilter[$key."_EXACT_MATCH"] == "N" && $match_value_set) ? "Y" : "N";

			$key = ToUpper($key);

			switch ($key)
			{
				// SITE_ID is unavailable for extended sorting! only direct selection; It's needed for after-select filtration.
				case "SITE_ID":
					if (strlen($value) > 0) //$arWhere[] = GetFilterQuery("LID", $value, $match);
					{
						$siteVal = "";
						if (strpos($value, ",") !== false)
						{
							$arValues = explode(",", $value);

							foreach ($arValues as $val)
								$siteVal .= "'".$DB->ForSql(trim($val))."'";

							$siteVal = str_replace("''", "','", $siteVal);
						}
						else
						{
							$siteVal = "'".$DB->ForSql($value)."'";
						}

						$arWhere[] = "LID IN (".$siteVal.") OR LID='' OR LID IS NULL";
					}
					break;
				case "ACTIVE":
					if (strlen($value) > 0)
						$arWhere[] = "ACTIVE='".($value == 'N' ? 'N' : 'Y')."'";
					break;

				case "SID":
				case "ID":
					if (strlen($value) > 0) $arWhere[] = GetFilterQuery("HID", $DB->ForSql($value), $match);
					break;

				case "HANDLER":
				case "PATH":
					if (strlen($value) > 0) $arWhere[] = GetFilterQuery("HANDLER", $DB->ForSql($value), $match);

			}
		}

		$strWhere = GetFilterSqlSearch($arWhere);
		$query = "
SELECT HID AS SID
FROM b_sale_delivery_handler
WHERE
".$strWhere."
";

		if (count($arSort) > 0)
		{
			$query .= "ORDER BY ";

			$bFirst = true;
			foreach ($arSort as $SORT => $SORT_DIR)
			{
				if ($bFirst)
					$bFirst = false;
				else
					$query .= ", ";

				$query .= $SORT." ".$SORT_DIR;
			}
		}

		$dbRes = $DB->Query($query);

		$arLoadedHandlers = array();
		$arLoadedHandlersMap = Array();

		while ($arRes = $dbRes->Fetch())
		{
			$arLoadedHandlersMap[$arRes["SID"]] = $arRes;
		}

		$arHandlersList = CSaleDeliveryHandler::__getRegisteredHandlers();

		if (is_array($arHandlersList))
		{
			foreach ($arHandlersList as $key => $arHandler)
			{
				if (is_array($arLoadedHandlersMap) && !array_key_exists($arHandler["SID"], $arLoadedHandlersMap))
				{
					unset($arHandlersList[$key]);
				}
			}

			$arHandlersList = array_values($arHandlersList);
			$arHandlersList = CSaleDeliveryHandler::__getHandlersData($arHandlersList);
			if ($arFilter["SITE_ID"] != "ALL" && is_array($arHandlersList))
			{
				foreach ($arHandlersList as $key => $arHandler)
				{
					if (strlen($arHandler['LID']) > 0)
					{
						$bValidSite = false;
						if (strpos($arFilter["SITE_ID"], ",") !== false)
						{
							$arSites = explode(",", $arFilter["SITE_ID"]);
							foreach ($arSites as $siteID)
							{
								if ($arHandler['LID'] == trim($siteID))
									$bValidSite = true;
							}
						}
						else
						{
							if ($arHandler['LID'] == $arFilter["SITE_ID"])
								$bValidSite = true;
						}

						if (!$bValidSite)
							unset($arHandlersList[$key]);
					}
				}
			}

			if (is_array($arFilter["COMPABILITY"]) && is_array($arHandlersList))
			{
				foreach ($arHandlersList as $key => $arHandler)
				{
					$arProfiles = CSaleDeliveryHandler::GetHandlerCompability($arFilter["COMPABILITY"], $arHandler);

					if (
						!is_array($arProfiles)
						||
						count($arProfiles) <= 0
					)
					{
						unset($arHandlersList[$key]);
					}
					else
					{
						$arHandlersList[$key]["PROFILES"] = $arProfiles;
					}
				}

			}

			CSaleDeliveryHandler::__sortList($arHandlersList, $arSort);
		}

		$dbHandlers = new CDBResult;
		if (is_array($arHandlersList))
		{
			reset($arHandlersList);
			$dbHandlers->InitFromArray($arHandlersList);
		}
		else
			$dbHandlers->InitFromArray(Array());

		return $dbHandlers;
	}

	// get handler compability. result - list of delivery profiles;
	function GetHandlerCompability($arOrder, $arHandler, $SITE_ID = SITE_ID)
	{
		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		$currency = CSaleLang::GetLangCurrency($SITE_ID);

		if ($currency != $arHandler["BASE_CURRENCY"])
			$arOrder["PRICE"] = CCurrencyRates::ConvertCurrency($arOrder["PRICE"], $currency, $arHandler["BASE_CURRENCY"]);

		if (is_array($arHandler["PROFILES"]))
		{
			$arProfilesList = $arHandler["PROFILES"];
			foreach ($arProfilesList as $profile_id => $arProfile)
			{
				if (is_array($arProfile["RESTRICTIONS_WEIGHT"]) && count($arProfile["RESTRICTIONS_WEIGHT"]) > 0)
				{

					$arOrder["WEIGHT"] = doubleval($arOrder["WEIGHT"]);

					if ($arOrder["WEIGHT"] < $arProfile["RESTRICTIONS_WEIGHT"][0])
					{
						unset($arProfilesList[$profile_id]);
						continue;
					}
					else
					{
						if (
								is_set($arProfile["RESTRICTIONS_WEIGHT"], 1)
								&&
								Doubleval($arProfile["RESTRICTIONS_WEIGHT"][1]) > 0
								&&
								$arOrder["WEIGHT"] > $arProfile["RESTRICTIONS_WEIGHT"][1]
						)
						{
							unset($arProfilesList[$profile_id]);
							continue;
						}

					}
				}


				if (is_array($arProfile["RESTRICTIONS_SUM"]) && count($arProfile["RESTRICTIONS_SUM"]) > 0)
				{
					if (
						$arOrder["PRICE"] < $arProfile["RESTRICTIONS_SUM"][0]
						||
						(
							is_set($arProfile["RESTRICTIONS_SUM"], 1)
							&&
							Doubleval($arProfile["RESTRICTIONS_SUM"][1]) > 0
							&&
							$arOrder["PRICE"] > $arProfile["RESTRICTIONS_SUM"][1]
						)
					)
					{
						unset($arProfilesList[$profile_id]);
						continue;
					}
				}

				if (is_array($arProfile["RESTRICTIONS_DIMENSIONS"]) && count($arProfile["RESTRICTIONS_DIMENSIONS"]) > 0)
				{
					if (!self::checkDimensions($arOrder["MAX_DIMENSIONS"], $arProfile["RESTRICTIONS_DIMENSIONS"]))
					{

						unset($arProfilesList[$profile_id]);
						continue;
					}
				}

				if (intval($arProfile["RESTRICTIONS_DIMENSIONS_SUM"]) > 0)
				{
					if (!self::checkDimensionsSum($arOrder["ITEMS"], intval($arProfile["RESTRICTIONS_DIMENSIONS_SUM"])))
					{
						unset($arProfilesList[$profile_id]);
						continue;
					}
				}

				if (intval($arProfile["RESTRICTIONS_MAX_SIZE"]) > 0)
				{
					if (!self::checkMaxSize($arOrder["ITEMS"], intval($arProfile["RESTRICTIONS_MAX_SIZE"])))
					{
						unset($arProfilesList[$profile_id]);
						continue;
					}
				}
			}

			if (is_callable($arHandler["COMPABILITY"]))
			{
				$arHandlerProfilesList = call_user_func($arHandler["COMPABILITY"], $arOrder, $arHandler["CONFIG"]["CONFIG"]);

				if (is_array($arHandlerProfilesList))
				{
					foreach ($arProfilesList as $profile_id => $arHandler)
					{
						if (!in_array($profile_id, $arHandlerProfilesList))
							unset($arProfilesList[$profile_id]);
					}
				}
				else
					return array();
			}


			return $arProfilesList;
		}
		else
			return false;
	}

	function GetHandlerExtraParams($SID, $profileId, $arOrder, $siteId = false)
	{
		$result = array();
		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		if (!$siteId)
			$siteId = SITE_ID;

		$rsDeliveryHandler = CSaleDeliveryHandler::GetBySID($SID, $siteId);
		if ($arHandler = $rsDeliveryHandler->Fetch())
		{
			if (isset($arHandler["GETEXTRAINFOPARAMS"]) && is_callable($arHandler["GETEXTRAINFOPARAMS"]))
			{
				$result = call_user_func($arHandler["GETEXTRAINFOPARAMS"], $arOrder, $arHandler["CONFIG"]["CONFIG"], $profileId, $siteId);
			}
		}

		return $result;
	}

	function getActionsList($deliveryId)
	{
		$result = array();

		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		$arDId = CSaleDeliveryHelper::getDeliverySIDAndProfile($deliveryId);
		$rsDeliveryHandler = CSaleDeliveryHandler::GetBySID($arDId["SID"]);

		if ($arHandler = $rsDeliveryHandler->Fetch())
		{
			if (isset($arHandler["GETORDERSACTIONSLIST"]) && is_callable($arHandler["GETORDERSACTIONSLIST"]))
			{
				$result = call_user_func($arHandler["GETORDERSACTIONSLIST"]);
			}
		}

		return $result;
	}

	function executeAction($deliveryId, $actionId, $arOrder)
	{
		$result = array();
		$arDId = CSaleDeliveryHelper::getDeliverySIDAndProfile($deliveryId);

		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		$rsDeliveryHandler = CSaleDeliveryHandler::GetBySID($arDId["SID"]);

		if ($arHandler = $rsDeliveryHandler->Fetch())
		{
			if (isset($arHandler["EXECUTEACTION"]) && is_callable($arHandler["EXECUTEACTION"]))
			{
				$result = call_user_func($arHandler["EXECUTEACTION"], $actionId, $arDId["PROFILE"], $arOrder, $arHandler["CONFIG"]["CONFIG"]);
			}
		}

		return $result;
	}

		// get handler data by DB sID
	function GetBySID($SID, $SITE_ID = false)
	{
		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		static $cache = array();

		if (!isset($cache[$SITE_ID]))
			$cache[$SITE_ID] = array();

		if (!isset($cache[$SITE_ID][$SID]))
		{
			$cache[$SITE_ID][$SID] = array();

			$arHandlersList = CSaleDeliveryHandler::__getRegisteredHandlers();
			foreach ($arHandlersList as $handler)
			{
				if ($handler["SID"] == $SID)
				{
					$cache[$SITE_ID][$SID][] = $handler;
					break;
				}
			}

			if ($cache[$SITE_ID][$SID])
			{
				$cache[$SITE_ID][$SID] = CSaleDeliveryHandler::__getHandlersData($cache[$SITE_ID][$SID], $SITE_ID);
			}
		}

		$dbResult = new CDBResult();
		$dbResult->InitFromArray($cache[$SITE_ID][$SID]);

		return $dbResult;
	}

	function CheckFields($arData)
	{
		global $APPLICATION;

		$numberFieldsProf = array("RESTRICTIONS_WEIGHT", "RESTRICTIONS_SUM", "TAX_RATE", "RESTRICTIONS_MAX_SIZE", "RESTRICTIONS_DIMENSIONS_SUM");

		if(isset($arData["PROFILES"]) && is_array($arData["PROFILES"]))
		{
			foreach ($arData["PROFILES"] as $profileId => $arProfile)
			{
				foreach ($numberFieldsProf as $fName)
				{
					if (isset($arProfile[$fName]))
					{
						if(!is_array($arProfile[$fName]))
							$arProfile[$fName] = array($arProfile[$fName]);

						foreach ($arProfile[$fName] as $fValue)
						{
							if($result = CSaleDeliveryHelper::getFormatError($fValue, 'NUMBER', GetMessage("SALE_DH_CF_ERROR_P_".$fName)))
							{
								$APPLICATION->ThrowException($result, $fName);
								return false;
							}
						}
					}
				}
			}
		}

		if(isset($arData['TAX_RATE']) && $result = CSaleDeliveryHelper::getFormatError($arData['TAX_RATE'], 'NUMBER', GetMessage('SALE_DH_CF_ERROR_TAX_RATE')))
		{
			$APPLICATION->ThrowException($result, 'TAX_RATE');
			return false;
		}

		if(isset($arData['SORT']) && $result = CSaleDeliveryHelper::getFormatError($arData['SORT'], 'NUMBER', GetMessage('SALE_DH_CF_ERROR_SORT')))
		{
			$APPLICATION->ThrowException($result, 'SORT');
			return false;
		}

		return true;
	}

	function Set($SID, $arData, $SITE_ID = false)
	{
		if(!self::CheckFields($arData))
			return false;

		if ($SITE_ID == 'ALL')
			$SITE_ID = false;

		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		global $APPLICATION, $DB;

		$rsHandlerDataFull = CSaleDeliveryHandler::GetBySID($SID, $SITE_ID);

		if ($arHandlerDataFull = $rsHandlerDataFull->Fetch())
		{
			$bInstalled = $arHandlerDataFull["INSTALLED"] == "Y";

			$DB->StartTransaction();

			$arQueryFields = array();

			if ($SITE_ID)
				$arQueryFields["LID"] = "'".($SITE_ID == "ALL" ? "" : $DB->ForSql($SITE_ID))."'";
			else
				$arQueryFields["LID"] = "''";

			if (is_set($arData, "ACTIVE"))
				$arQueryFields["ACTIVE"] = $arData["ACTIVE"] == 'Y' ? "'Y'" : "'N'";
			elseif ($bInstalled)
				$arQueryFields["ACTIVE"] = "'N'";

			if (!$bInstalled)
			{
				$arQueryFields["HID"] = "'".$DB->ForSql($SID)."'";
			}

			if (is_set($arData, "SORT"))
				$arQueryFields["SORT"] = "'".intval($arData["SORT"])."'";
			elseif (!$bInstalled)
				$arQueryFields["SORT"] = '100';

			if (is_set($arData, "NAME"))
				$arQueryFields["NAME"] = "'".$DB->ForSql($arData["NAME"])."'";
			elseif (!$bInstalled)
				$arQueryFields["NAME"] = "'".$DB->ForSql($arHandlerDataFull['NAME'])."'";

			if (is_set($arData, "DESCRIPTION"))
				$arQueryFields["DESCRIPTION"] = "'".$DB->ForSql($arData["DESCRIPTION"])."'";
			elseif (!$bInstalled)
				$arQueryFields["DESCRIPTION"] = "'".$DB->ForSql($arHandlerDataFull['DESCRIPTION'])."'";

			if (is_set($arData, "HANDLER"))
				$arQueryFields["HANDLER"] = "'".$DB->ForSql($arData["HANDLER"])."'";
			elseif (!$bInstalled)
				$arQueryFields["HANDLER"] = "'".$DB->ForSql($arHandlerDataFull['HANDLER'])."'";

			if (is_set($arData, "TAX_RATE"))
				$arQueryFields["TAX_RATE"] = "'".doubleval($arData["TAX_RATE"])."'";
			elseif (!$bInstalled)
				$arQueryFields["TAX_RATE"] = 0;

			if (is_set($arData, "BASE_CURRENCY"))
				$arQueryFields["BASE_CURRENCY"] = "'".$DB->ForSql($arData["BASE_CURRENCY"])."'";
			elseif (!$bInstalled)
				$arQueryFields["BASE_CURRENCY"] = '';

			//save logotip
			if (!$bInstalled && (!isset($arData["LOGOTIP"]) || count($arData["LOGOTIP"]) <= 1))
			{
				$logo = "";
				if (is_set($arData, "HANDLER"))
					$arPath = pathinfo($arData["HANDLER"]);
				elseif (!$bInstalled)
					$arPath = pathinfo($arHandlerDataFull["HANDLER"]);

				if (!strpos($arPath["dirname"], ":"))
					$arPath["dirname"] = $_SERVER["DOCUMENT_ROOT"].$arPath["dirname"];

				if (file_exists($arPath["dirname"]."/".$SID."_logo.png"))
					$logo = $arPath["dirname"]."/".$SID."_logo.png";
				elseif (file_exists($arPath["dirname"]."/".$SID."_logo.jpg"))
					$logo = $arPath["dirname"]."/".$SID."_logo.jpg";
				elseif (file_exists($arPath["dirname"]."/".$SID."_logo.gif"))
					$logo = $arPath["dirname"]."/".$SID."_logo.gif";

				if(strlen($logo) > 0)
				{
					$arData["LOGOTIP"] = CFile::MakeFileArray($logo);
					$arData["LOGOTIP"]["MODULE_ID"] = "sale";
				}
			}

			$bDelLogotip = false;
			if ($arData["LOGOTIP"]["del"] == "Y")
				$bDelLogotip = true;

			CFile::SaveForDB($arData, "LOGOTIP", "sale/delivery/logotip");

			if (is_set($arData, 'LOGOTIP') && intval($arData["LOGOTIP"]) > 0)
				$arQueryFields["LOGOTIP"] = $arData["LOGOTIP"];

			if ($bDelLogotip)
				$arQueryFields["LOGOTIP"] = 'NULL';

			if (is_set($arData, "CONFIG"))
			{
				if (is_callable($arHandlerDataFull["DBSETSETTINGS"]))
				{
					if (!$strSettings = call_user_func($arHandlerDataFull["DBSETSETTINGS"], $arData["CONFIG"]))
					{
						$DB->Rollback();
						return false;
					}
				}
				else
				{
					$strSettings = serialize($arData["CONFIG"]);
				}
				$arQueryFields["SETTINGS"] = "'".$DB->ForSql($strSettings)."'";
			}
			/*
			if (is_set($arData, "PROFILE_USE_DEFAULT") && $arData["PROFILE_USE_DEFAULT"] == 'Y')
				$arQueryFields["PROFILES"] = "''";
			else
			{*/
				if (is_array($arData["PROFILES"]) && count($arData["PROFILES"]) > 0)
					$arQueryFields["PROFILES"] = "'".$DB->ForSql(serialize($arData["PROFILES"]))."'";
				elseif (!$bInstalled)
					$arQueryFields["PROFILES"] = "''";
			/*}*/


			if ($bInstalled)
			{
				if ($rsHandlerDataFull->SelectedRowsCount() > 1 && $SITE_ID === false)
				{
					$DB->Query("DELETE FROM b_sale_delivery_handler WHERE HID='".$DB->ForSql($SID)."' AND LID<>'".$DB->ForSql($arHandlerDataFull['LID'])."'");
					$SITE_ID = $arHandlerDataFull['LID'];
				}
				elseif ($arHandlerDataFull["LID"] == '' && $SITE_ID !== false)
				{
					CSaleDeliveryHandler::__spreadHandlerData($SID);
				}

				$strWhere = "WHERE HID='".$DB->ForSql($SID)."'";
				if ($SITE_ID) $strWhere .= " AND LID='".$DB->ForSql($SITE_ID)."'";

				$DB->Update("b_sale_delivery_handler", $arQueryFields, $strWhere);
			}
			else
			{
				$DB->Insert("b_sale_delivery_handler", $arQueryFields);
			}

			$DB->Commit();
		}
		else
		{
			$APPLICATION->ThrowException('SALE_DH_ERROR_WRONG_HANDLER_FILE');
			return false;
		}
	}

	// reset handler DB data
	function Reset($SID)
	{
		global $DB;

		$query = "DELETE FROM b_sale_delivery_handler WHERE HID='".$DB->ForSql($SID)."'";
		$DB->Query($query);

		return;
	}

	// reset all handlers DB data
	function ResetAll()
	{
		global $DB;

		$query = "DELETE FROM b_sale_delivery_handler";
		$DB->Query($query);

		return;
	}

	function __executeCalculateEvents($SID, $profile, $arOrder, $arReturn)
	{
		$arEventsList = array(
			"onSaleDeliveryHandlerCalculate",
			"onSaleDeliveryHandlerCalculate_".$SID,
		);

		foreach ($arEventsList as $event)
		{
			foreach(GetModuleEvents("sale", $event, true) as $arEventHandler)
			{
				$arReturnTmp = ExecuteModuleEventEx($arEventHandler, array($SID, $profile, $arOrder, $arReturn));
				if (is_array($arReturnTmp))
				{
					$arReturn = $arReturnTmp;
				}
			}
		}

		return $arReturn;
	}

	function CalculateFull($SID, $profile, $arOrder, $currency, $SITE_ID = false)
	{
		$bFinish = false;
		$STEP = 0;
		$TMP = false;

		while (!$bFinish)
		{
			$arResult = CSaleDeliveryHandler::Calculate(++$STEP, $SID, $profile, $arOrder, $currency, $TMP, $SITE_ID);

			if ($arResult["RESULT"] == "NEXT_STEP" && strlen($arResult["TEMP"]) > 0) $TMP = $arResult["TEMP"];

			$bFinish = $arResult["RESULT"] == "OK" || $arResult["RESULT"] == "ERROR";
		}

		return $arResult;
	}

	function Calculate($STEP, $SID, $profile, $arOrder, $currency, $TMP = false, $SITE_ID = false)
	{
		global $APPLICATION;

		if (!defined('SALE_DH_INITIALIZED'))
			CSaleDeliveryHandler::Initialize();

		if (!$SITE_ID) $SITE_ID = SITE_ID;

		$rsDeliveryHandler = CSaleDeliveryHandler::GetBySID($SID, $SITE_ID);
		if (!$arHandler = $rsDeliveryHandler->Fetch())
		{
			return array(
				"RESULT" => "ERROR",
				"TEXT" => GetMessage("SALE_DH_ERROR_HANDLER_NOT_INSTALLED")
			);
		}

		if (is_callable($arHandler["CALCULATOR"]))
		{
			$arConfig = $arHandler["CONFIG"]["CONFIG"];

			$arOrder["PRICE"] = CCurrencyRates::ConvertCurrency(
					$arOrder["PRICE"],
					$currency,
					$arHandler["BASE_CURRENCY"]
			);

			if ($res = call_user_func($arHandler["CALCULATOR"], $profile, $arConfig, $arOrder, $STEP, $TMP))
			{
				if (is_array($res))
					$arReturn = $res;
				elseif (is_numeric($res))
					$arReturn = array(
						"RESULT" => "OK",
						"VALUE" => doubleval($res)
					);
			}
			else
			{
				if ($ex = $APPLICATION->GetException())
					return array(
						"RESULT" => "ERROR",
						"TEXT" => $ex->GetString(),
					);
				else
					return array(
						"RESULT" => "OK",
						"VALUE" => 0
					);
			}

			if (
				is_array($arReturn)
				&&
				$arReturn["RESULT"] == "OK"
				&&
				$currency != $arHandler["BASE_CURRENCY"]
				&&
				CModule::IncludeModule('currency')
			)
			{
				$arReturn["VALUE"] = CCurrencyRates::ConvertCurrency(
					$arReturn["VALUE"],
					$arHandler["BASE_CURRENCY"],
					$currency
				);
			}

			$arReturn["VALUE"] *= 1 + ($arHandler["TAX_RATE"]/100);

			if(isset($arHandler['PROFILES'][$profile]['TAX_RATE']))
				$arReturn["VALUE"] *= 1 + (floatval($arHandler['PROFILES'][$profile]['TAX_RATE'])/100);

			$arReturn = CSaleDeliveryHandler::__executeCalculateEvents($SID, $profile, $arOrder, $arReturn);

			return $arReturn;
		}
		else
		{
			return array(
				"RESULT" => "ERROR",
				"TEXT" => GetMessage("SALE_DH_ERROR_WRONG_HANDLER_FILE")
			);
		}
	}

	public function checkDimensions($arOrderDimensions, $arRestrictDimensions)
	{
		$dimCount = 3;
		if(
			!is_array($arOrderDimensions)
			||
			!is_array($arRestrictDimensions)
			||
			empty($arOrderDimensions)
			||
			empty($arRestrictDimensions)
			||
			count($arOrderDimensions) != $dimCount
			||
			count($arRestrictDimensions) != $dimCount
		)
			return true;

		$result = true;

		rsort($arOrderDimensions, SORT_NUMERIC);
		rsort($arRestrictDimensions, SORT_NUMERIC);

		for ($i=0; $i < $dimCount; $i++)
		{
			if(
				floatval($arRestrictDimensions[$i]) <= 0
				||
				$arOrderDimensions[$i] <=0
			)
			{
				break;
			}

			if($arOrderDimensions[$i] > $arRestrictDimensions[$i])
			{
				$result = false;
				break;
			}
		}

		return $result;
	}

	public function checkDimensionsSum($arItems, $maxDimensionSum)
	{
		$result = true;
		$maxDimensionSum = floatval($maxDimensionSum);

		if(is_array($arItems) && $maxDimensionSum > 0)
		{
			foreach ($arItems as $arItem)
			{
				if(!self::isDimensionsExist($arItem))
					continue;

				$itemDimSumm = floatval($arItem["WIDTH"])+floatval($arItem["HEIGHT"])+floatval($arItem["LENGTH"]);

				if($itemDimSumm > $maxDimensionSum)
				{
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	public function checkMaxSize($arItems, $maxSize)
	{
		$result = true;
		$maxSize = floatval($maxSize);

		if(is_array($arItems) && $maxSize > 0)
		{

			foreach ($arItems as $arItem)
			{
				if(!self::isDimensionsExist($arItem))
					continue;

				if(
					floatval($arItem["WIDTH"]) > $maxSize
					||
					floatval($arItem["HEIGHT"]) > $maxSize
					||
					floatval($arItem["LENGTH"]) > $maxSize
					)
				{
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	private function isDimensionsExist($arItem)
	{
		return (
				isset($arItem["WIDTH"]) && floatval($arItem["WIDTH"]) > 0
				&&
				isset($arItem["HEIGHT"]) && floatval($arItem["HEIGHT"]) > 0
				&&
				isset($arItem["LENGTH"]) && floatval($arItem["LENGTH"]) > 0
				);
	}

	public function getActionsNames()
	{
		return array(
			"REQUEST_SELF" => GetMessage("SALE_DH_ACTION_REQUEST_SELF"),
			"REQUEST_TAKE" => GetMessage("SALE_DH_ACTION_REQUEST_TAKE")
		);
	}
}

?>