<?
IncludeModuleLangFile(__FILE__);

$GLOBALS["SONET_FEATURES_CACHE"] = array();

class CAllSocNetFeatures
{
	/***************************************/
	/********  DATA MODIFICATION  **********/
	/***************************************/
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $DB, $arSocNetFeaturesSettings, $arSocNetAllowedEntityTypes;

		if ($ACTION != "ADD" && IntVal($ID) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("System error 870164", "ERROR");
			return false;
		}

		if ((is_set($arFields, "ENTITY_TYPE") || $ACTION=="ADD") && StrLen($arFields["ENTITY_TYPE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_TYPE"), "EMPTY_ENTITY_TYPE");
			return false;
		}
		elseif (is_set($arFields, "ENTITY_TYPE"))
		{
			if (!in_array($arFields["ENTITY_TYPE"], $arSocNetAllowedEntityTypes))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_NO_ENTITY_TYPE");
				return false;
			}
		}

		if ((is_set($arFields, "ENTITY_ID") || $ACTION=="ADD") && IntVal($arFields["ENTITY_ID"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "EMPTY_ENTITY_ID");
			return false;
		}
		elseif (is_set($arFields, "ENTITY_ID"))
		{
			$type = "";
			if (is_set($arFields, "ENTITY_TYPE"))
			{
				$type = $arFields["ENTITY_TYPE"];
			}
			elseif ($ACTION != "ADD")
			{
				$arRe = CSocNetFeatures::GetByID($ID);
				if ($arRe)
					$type = $arRe["ENTITY_TYPE"];
			}
			if (StrLen($type) <= 0)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_CALC_ENTITY_TYPE"), "ERROR_CALC_ENTITY_TYPE");
				return false;
			}

			if ($type == SONET_ENTITY_GROUP)
			{
				$arResult = CSocNetGroup::GetByID($arFields["ENTITY_ID"]);
				if ($arResult == false)
				{
					$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_ID"), "ERROR_NO_ENTITY_ID");
					return false;
				}
			}
			elseif ($type == SONET_ENTITY_USER)
			{
				$dbResult = CUser::GetByID($arFields["ENTITY_ID"]);
				if (!$dbResult->Fetch())
				{
					$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_ID"), "ERROR_NO_ENTITY_ID");
					return false;
				}
			}
			else
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_CALC_ENTITY_TYPE"), "ERROR_CALC_ENTITY_TYPE");
				return false;
			}
		}

		if ((is_set($arFields, "FEATURE") || $ACTION=="ADD") && StrLen($arFields["FEATURE"]) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_FEATURE_ID"), "EMPTY_FEATURE");
			return false;
		}
		elseif (is_set($arFields, "FEATURE"))
		{
			$arFields["FEATURE"] = strtolower($arFields["FEATURE"]);
			if (!array_key_exists($arFields["FEATURE"], $arSocNetFeaturesSettings))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_FEATURE_ID"), "ERROR_NO_FEATURE");
				return false;
			}
		}

		if (is_set($arFields, "DATE_CREATE") && (!$DB->IsDate($arFields["DATE_CREATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_DATE_CREATE"), "EMPTY_DATE_CREATE");
			return false;
		}

		if (is_set($arFields, "DATE_UPDATE") && (!$DB->IsDate($arFields["DATE_UPDATE"], false, LANG, "FULL")))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GB_EMPTY_DATE_UPDATE"), "EMPTY_DATE_UPDATE");
			return false;
		}

		if ((is_set($arFields, "ACTIVE") || $ACTION=="ADD") && $arFields["ACTIVE"] != "Y" && $arFields["ACTIVE"] != "N")
			$arFields["ACTIVE"] = "Y";

		return True;
	}

	function Delete($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);
		$bSuccess = True;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetFeatures");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID))===false)
				return false;

		$events = GetModuleEvents("socialnetwork", "OnSocNetFeatures");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		$DB->StartTransaction();

		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_features2perms WHERE FEATURE_ID = ".$ID."", true);
		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sonet_features WHERE ID = ".$ID."", true);

		if ($bSuccess)
			$DB->Commit();
		else
			$DB->Rollback();

		return $bSuccess;
	}

	function DeleteNoDemand($userID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($userID))
			return false;

		$userID = IntVal($userID);

		$dbResult = CSocNetFeatures::GetList(array(), array("ENTITY_TYPE" => "U", "ENTITY_ID" => $userID), false, false, array("ID"));
		while ($arResult = $dbResult->Fetch())
			$DB->Query("DELETE FROM b_sonet_features2perms WHERE FEATURE_ID = ".$arResult["ID"]."", true);

		$DB->Query("DELETE FROM b_sonet_features WHERE ENTITY_TYPE = 'U' AND ENTITY_ID = ".$userID."", true);

		return true;
	}

	function Update($ID, $arFields)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);

		$arFields1 = array();
		foreach ($arFields as $key => $value)
		{
			if (substr($key, 0, 1) == "=")
			{
				$arFields1[substr($key, 1)] = $value;
				unset($arFields[$key]);
			}
		}

		if (!CSocNetFeatures::CheckFields("UPDATE", $arFields, $ID))
			return false;

		$db_events = GetModuleEvents("socialnetwork", "OnBeforeSocNetFeaturesUpdate");
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($ID, $arFields))===false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sonet_features", $arFields);

		foreach ($arFields1 as $key => $value)
		{
			if (strlen($strUpdate) > 0)
				$strUpdate .= ", ";
			$strUpdate .= $key."=".$value." ";
		}

		if (strlen($strUpdate) > 0)
		{
			$strSql =
				"UPDATE b_sonet_features SET ".
				"	".$strUpdate." ".
				"WHERE ID = ".$ID." ";
			$DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);

			if (array_key_exists("ENTITY_TYPE", $arFields) && array_key_exists("ENTITY_ID", $arFields))
				unset($GLOBALS["SONET_FEATURES_CACHE"][$arFields["ENTITY_TYPE"]][$arFields["ENTITY_ID"]]);

			$events = GetModuleEvents("socialnetwork", "OnSocNetFeaturesUpdate");
			while ($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array($ID, $arFields));
		}
		else
		{
			$ID = False;
		}

		return $ID;
	}

	function SetFeature($type, $id, $feature, $active, $featureName = false)
	{
		global $arSocNetFeaturesSettings, $arSocNetAllowedEntityTypes, $APPLICATION;

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			return false;
		}

		$id = IntVal($id);
		if ($id <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
			return false;
		}

		$feature = StrToLower(Trim($feature));
		if (StrLen($feature) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_FEATURE_ID"), "ERROR_EMPTY_FEATURE_ID");
			return false;
		}

		if (!array_key_exists($feature, $arSocNetFeaturesSettings) || !in_array($type, $arSocNetFeaturesSettings[$feature]["allowed"]))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_FEATURE_ID"), "ERROR_NO_FEATURE_ID");
			return false;
		}

		$active = ($active ? "Y" : "N");

		$dbResult = CSocNetFeatures::GetList(
			array(),
			array(
				"ENTITY_TYPE" => $type,
				"ENTITY_ID" => $id,
				"FEATURE" => $feature
			),
			false,
			false,
			array("ID", "ACTIVE")
		);

		if ($arResult = $dbResult->Fetch())
			$r = CSocNetFeatures::Update($arResult["ID"], array("FEATURE_NAME" => $featureName, "ACTIVE" => $active, "=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction()));
		else
			$r = CSocNetFeatures::Add(array("ENTITY_TYPE" => $type, "ENTITY_ID" => $id, "FEATURE" => $feature, "FEATURE_NAME" => $featureName, "ACTIVE" => $active, "=DATE_UPDATE" => $GLOBALS["DB"]->CurrentTimeFunction(), "=DATE_CREATE" => $GLOBALS["DB"]->CurrentTimeFunction()));

		if (!$r)
		{
			$errorMessage = "";
			if ($e = $APPLICATION->GetException())
				$errorMessage = $e->GetString();
			if (StrLen($errorMessage) <= 0)
				$errorMessage = GetMessage("SONET_GF_ERROR_SET").".";

			$GLOBALS["APPLICATION"]->ThrowException($errorMessage, "ERROR_SET_RECORD");
			return false;
		}

		return $r;
	}

	/***************************************/
	/**********  DATA SELECTION  ***********/
	/***************************************/
	function GetByID($ID)
	{
		global $DB;

		if (!CSocNetGroup::__ValidateID($ID))
			return false;

		$ID = IntVal($ID);

		$dbResult = CSocNetFeatures::GetList(Array(), Array("ID" => $ID));
		if ($arResult = $dbResult->GetNext())
		{
			return $arResult;
		}

		return False;
	}
	
	/***************************************/
	/**********  COMMON METHODS  ***********/
	/***************************************/
	function IsActiveFeature($type, $id, $feature)
	{
		global $arSocNetFeaturesSettings, $arSocNetAllowedEntityTypes;

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			return false;
		}

		$feature = StrToLower(Trim($feature));
		if (StrLen($feature) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_FEATURE_ID"), "ERROR_EMPTY_FEATURE_ID");
			return false;
		}

		if (!array_key_exists($feature, $arSocNetFeaturesSettings) || !in_array($type, $arSocNetFeaturesSettings[$feature]["allowed"]))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_FEATURE_ID"), "ERROR_NO_FEATURE_ID");
			return false;
		}

		$arFeatures = array();

		if (is_array($id))
		{
			$arGroupToGet = array();
			foreach($id as $group_id)
			{
				if ($group_id <= 0)
					$arReturn[$group_id] = false;
				else
				{
					if (array_key_exists("SONET_FEATURES_CACHE", $GLOBALS)
						&& is_array($GLOBALS["SONET_FEATURES_CACHE"])
						&& array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"])
						&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type])
						&& array_key_exists($group_id, $GLOBALS["SONET_FEATURES_CACHE"][$type])
						&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type][$group_id]))
					{
						$arFeatures[$group_id] = $GLOBALS["SONET_FEATURES_CACHE"][$type][$group_id];
						
						if (!array_key_exists($feature, $arFeatures[$group_id]))
						{
							$arReturn[$group_id] = true;
							continue;
						}
						
						$arReturn[$group_id] = ($arFeatures[$group_id][$feature]["ACTIVE"] == "Y");
					}
					else
					{
						$arGroupToGet[] = $group_id;
					}
				}
			}
			
			if(!empty($arGroupToGet))
			{
				$dbResult = CSocNetFeatures::GetList(Array(), Array("ENTITY_ID" => $arGroupToGet, "ENTITY_TYPE" => $type));
				while ($arResult = $dbResult->Fetch())
					$arFeatures[$arResult["ENTITY_ID"]][$arResult["FEATURE"]] = array("ACTIVE" => $arResult["ACTIVE"], "FEATURE_NAME" => $arResult["FEATURE_NAME"]);

				foreach($arGroupToGet as $group_id)	
				{
					
					if (!array_key_exists("SONET_FEATURES_CACHE", $GLOBALS) || !is_array($GLOBALS["SONET_FEATURES_CACHE"]))
						$GLOBALS["SONET_FEATURES_CACHE"] = array();
					if (!array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"]) || !is_array($GLOBALS["SONET_FEATURES_CACHE"][$type]))
						$GLOBALS["SONET_FEATURES_CACHE"][$type] = array();

					$GLOBALS["SONET_FEATURES_CACHE"][$type][$group_id] = $arFeatures[$group_id];

					if(!isset($arFeatures[$group_id]))
						$arFeatures[$group_id] = Array();
					if (!array_key_exists($feature, $arFeatures[$group_id]))
					{
						$arReturn[$group_id] = true;
						continue;
					}
					
					$arReturn[$group_id] = ($arFeatures[$group_id][$feature]["ACTIVE"] == "Y");
				}
			}
				
			return $arReturn;
		
		}
		else // not array
		{
			$id = IntVal($id);
			if ($id <= 0)
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
				return false;
			}
			
			if (array_key_exists("SONET_FEATURES_CACHE", $GLOBALS)
				&& is_array($GLOBALS["SONET_FEATURES_CACHE"])
				&& array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"])
				&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type])
				&& array_key_exists($id, $GLOBALS["SONET_FEATURES_CACHE"][$type])
				&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type][$id]))
			{
				$arFeatures = $GLOBALS["SONET_FEATURES_CACHE"][$type][$id];
			}
			else
			{
				$dbResult = CSocNetFeatures::GetList(Array(), Array("ENTITY_ID" => $id, "ENTITY_TYPE" => $type));
				while ($arResult = $dbResult->GetNext())
					$arFeatures[$arResult["FEATURE"]] = array("ACTIVE" => $arResult["ACTIVE"], "FEATURE_NAME" => $arResult["FEATURE_NAME"]);

				if (!array_key_exists("SONET_FEATURES_CACHE", $GLOBALS) || !is_array($GLOBALS["SONET_FEATURES_CACHE"]))
					$GLOBALS["SONET_FEATURES_CACHE"] = array();
				if (!array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"]) || !is_array($GLOBALS["SONET_FEATURES_CACHE"][$type]))
					$GLOBALS["SONET_FEATURES_CACHE"][$type] = array();

				$GLOBALS["SONET_FEATURES_CACHE"][$type][$id] = $arFeatures;
			}
			
			if (!array_key_exists($feature, $arFeatures))
				return true;
				
			return ($arFeatures[$feature]["ACTIVE"] == "Y");
		}
	}

	function GetActiveFeatures($type, $id)
	{
		global $arSocNetAllowedEntityTypes, $arSocNetFeaturesSettings;

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			return false;
		}

		$id = IntVal($id);
		if ($id <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
			return false;
		}

		$arReturn = array();

		$arFeatures = array();

		if (array_key_exists("SONET_FEATURES_CACHE", $GLOBALS)
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"])
			&& array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"])
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type])
			&& array_key_exists($id, $GLOBALS["SONET_FEATURES_CACHE"][$type])
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type][$id]))
		{
			$arFeatures = $GLOBALS["SONET_FEATURES_CACHE"][$type][$id];
		}
		else
		{
			$dbResult = CSocNetFeatures::GetList(Array(), Array("ENTITY_ID" => $id, "ENTITY_TYPE" => $type));
			while ($arResult = $dbResult->GetNext())
				$arFeatures[$arResult["FEATURE"]] = array("ACTIVE" => $arResult["ACTIVE"], "FEATURE_NAME" => $arResult["FEATURE_NAME"]);

			if (!array_key_exists("SONET_FEATURES_CACHE", $GLOBALS) || !is_array($GLOBALS["SONET_FEATURES_CACHE"]))
				$GLOBALS["SONET_FEATURES_CACHE"] = array();
			if (!array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"]) || !is_array($GLOBALS["SONET_FEATURES_CACHE"][$type]))
				$GLOBALS["SONET_FEATURES_CACHE"][$type] = array();

			$GLOBALS["SONET_FEATURES_CACHE"][$type][$id] = $arFeatures;
		}

		foreach ($arSocNetFeaturesSettings as $feature => $arr)
		{
		
			if (
				!array_key_exists("allowed", $arSocNetFeaturesSettings[$feature])
				|| !is_array($arSocNetFeaturesSettings[$feature]["allowed"])
				|| !in_array($type, $arSocNetFeaturesSettings[$feature]["allowed"])
			)
				continue;

			if (array_key_exists($feature, $arFeatures) && ($arFeatures[$feature]["ACTIVE"] == "N"))
				continue;

			$arReturn[] = $feature;
		}

		return $arReturn;
	}

	function GetActiveFeaturesNames($type, $id)
	{
		global $arSocNetAllowedEntityTypes, $arSocNetFeaturesSettings;

		$type = Trim($type);
		if ((StrLen($type) <= 0) || !in_array($type, $arSocNetAllowedEntityTypes))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_ERROR_NO_ENTITY_TYPE"), "ERROR_EMPTY_TYPE");
			return false;
		}

		$id = IntVal($id);
		if ($id <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SONET_GF_EMPTY_ENTITY_ID"), "ERROR_EMPTY_ENTITY_ID");
			return false;
		}

		$arReturn = array();

		$arFeatures = array();

		if (array_key_exists("SONET_FEATURES_CACHE", $GLOBALS)
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"])
			&& array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"])
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type])
			&& array_key_exists($id, $GLOBALS["SONET_FEATURES_CACHE"][$type])
			&& is_array($GLOBALS["SONET_FEATURES_CACHE"][$type][$id]))
		{
			$arFeatures = $GLOBALS["SONET_FEATURES_CACHE"][$type][$id];
		}
		else
		{
			$dbResult = CSocNetFeatures::GetList(Array(), Array("ENTITY_ID" => $id, "ENTITY_TYPE" => $type));
			while ($arResult = $dbResult->GetNext())
				$arFeatures[$arResult["FEATURE"]] = array("ACTIVE" => $arResult["ACTIVE"], "FEATURE_NAME" => $arResult["FEATURE_NAME"]);

			if (!array_key_exists("SONET_FEATURES_CACHE", $GLOBALS) || !is_array($GLOBALS["SONET_FEATURES_CACHE"]))
				$GLOBALS["SONET_FEATURES_CACHE"] = array();
			if (!array_key_exists($type, $GLOBALS["SONET_FEATURES_CACHE"]) || !is_array($GLOBALS["SONET_FEATURES_CACHE"][$type]))
				$GLOBALS["SONET_FEATURES_CACHE"][$type] = array();

			$GLOBALS["SONET_FEATURES_CACHE"][$type][$id] = $arFeatures;
		}

		foreach ($arSocNetFeaturesSettings as $feature => $arr)
		{
				
			if (
				!array_key_exists("allowed", $arSocNetFeaturesSettings[$feature]) 
				|| !in_array($type, $arSocNetFeaturesSettings[$feature]["allowed"])
			)
				continue;

			if (array_key_exists($feature, $arFeatures) && ($arFeatures[$feature]["ACTIVE"] == "N"))
				continue;

			$arReturn[$feature] = $arFeatures[$feature]["FEATURE_NAME"];
		}

		return $arReturn;
	}
}
?>