<?

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/ratings.php");

class CAllRatings
{
	// get specified rating record
	function GetByID($ID)
	{
		global $DB;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: GetByID<br>Line: ";

		if($ID<=0)
			return false;

		return ($DB->Query("
			SELECT
				R.*,
				".$DB->DateToCharFunction("R.CREATED")." as CREATED,
				".$DB->DateToCharFunction("R.LAST_MODIFIED")." as LAST_MODIFIED,
				".$DB->DateToCharFunction("R.LAST_CALCULATED")." as	LAST_CALCULATED
			FROM
				b_rating R
			WHERE
				ID=".$ID,
			false, $err_mess.__LINE__));
	}

	function GetArrayByID($ID)
	{
		global $DB;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: GetArrayByID<br>Line: ";
		$strID = "b".$ID;
		if(CACHED_b_rating===false)
		{
			$res = $DB->Query("
				SELECT
					R.*,
					".$DB->DateToCharFunction("R.CREATED")." as CREATED,
					".$DB->DateToCharFunction("R.LAST_MODIFIED")." as LAST_MODIFIED,
					".$DB->DateToCharFunction("R.LAST_CALCULATED")." as	LAST_CALCULATED
				FROM
					b_rating R
				WHERE
					ID=".$ID,
			false, $err_mess.__LINE__);
			$arResult = $res->Fetch();
		}
		else
		{
			global $stackCacheManager;
			$stackCacheManager->SetLength("b_rating", 100);
			$stackCacheManager->SetTTL("b_rating", CACHED_b_rating);
			if($stackCacheManager->Exist("b_rating", $strID))
				$arResult = $stackCacheManager->Get("b_rating", $strID);
			else
			{
				$res = $DB->Query("
					SELECT
						R.*,
						".$DB->DateToCharFunction("R.CREATED")." as CREATED,
						".$DB->DateToCharFunction("R.LAST_MODIFIED")." as LAST_MODIFIED,
						".$DB->DateToCharFunction("R.LAST_CALCULATED")." as	LAST_CALCULATED
					FROM
						b_rating R
					WHERE
						ID=".$ID,
				false, $err_mess.__LINE__);
				$arResult = $res->Fetch();
				if($arResult)
					$stackCacheManager->Set("b_rating", $strID, $arResult);
			}
		}

		return $arResult;
	}

	// get rating record list
	function GetList($arSort=array(), $arFilter=Array())
	{
		global $DB;

		$arSqlSearch = Array();
		$strSqlSearch = "";
		$err_mess = (CRatings::err_mess())."<br>Function: GetList<br>Line: ";

		if (is_array($arFilter))
		{
			foreach ($arFilter as $key => $val)
			{
				if (strlen($val)<=0 || $val=="NOT_REF")
					continue;
				switch(strtoupper($key))
				{
					case "ID":
						$arSqlSearch[] = GetFilterQuery("R.ID",$val,"N");
					break;
					case "ACTIVE":
						if (in_array($val, Array('Y','N')))
							$arSqlSearch[] = "R.ACTIVE = '".$val."'";
					break;
					case "AUTHORITY":
						if (in_array($val, Array('Y','N')))
							$arSqlSearch[] = "R.AUTHORITY = '".$val."'";
					break;
					case "POSITION":
						if (in_array($val, Array('Y','N')))
							$arSqlSearch[] = "R.POSITION = '".$val."'";
					break;
					case "CALCULATED":
						if (in_array($val, Array('Y','N','C')))
							$arSqlSearch[] = "R.CALCULATED = '".$val."'";
					break;
					case "NAME":
						$arSqlSearch[] = GetFilterQuery("R.NAME", $val);
					break;
					case "ENTITY_ID":
						$arSqlSearch[] = GetFilterQuery("R.ENTITY_ID", $val);
					break;
				}
			}
		}

		$sOrder = "";
		foreach($arSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"? "DESC":"ASC");
			switch (strtoupper($key))
			{
				case "ID":		$sOrder .= ", R.ID ".$ord; break;
				case "NAME":	$sOrder .= ", R.NAME ".$ord; break;
				case "CREATED":	$sOrder .= ", R.CREATED ".$ord; break;
				case "LAST_MODIFIED":	$sOrder .= ", R.LAST_MODIFIED ".$ord; break;
				case "LAST_CALCULATED":	$sOrder .= ", R.LAST_CALCULATED ".$ord; break;
				case "ACTIVE":	$sOrder .= ", R.ACTIVE ".$ord; break;
				case "AUTHORITY":$sOrder .= ", R.AUTHORITY ".$ord; break;
				case "POSITION":$sOrder .= ", R.POSITION ".$ord; break;
				case "STATUS":	$sOrder .= ", R.CALCULATED ".$ord; break;
				case "CALCULATED":	$sOrder .= ", R.CALCULATED ".$ord; break;
				case "CALCULATION_METHOD":	$sOrder .= ", R.CALCULATION_METHOD ".$ord; break;
				case "ENTITY_ID":	$sOrder .= ", R.ENTITY_ID ".$ord; break;
			}
		}

		if (strlen($sOrder)<=0)
			$sOrder = "R.ID DESC";

		$strSqlOrder = " ORDER BY ".TrimEx($sOrder,",");

		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				R.ID, R.NAME, R.ACTIVE, R.CALCULATED, R.AUTHORITY, R.POSITION, R.ENTITY_ID, R.CALCULATION_METHOD,
				".$DB->DateToCharFunction("R.CREATED")." CREATED,
				".$DB->DateToCharFunction("R.LAST_MODIFIED")." LAST_MODIFIED,
				".$DB->DateToCharFunction("R.LAST_CALCULATED")." LAST_CALCULATED
			FROM
				b_rating R
			WHERE
			".$strSqlSearch."
			".$strSqlOrder;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);

		return $res;
	}

	function GetRatingValueInfo($ratingId)
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: GetRatingValueInfo<br>Line: ";
		$ratingId = intval($ratingId);

		$strSql = "
			SELECT
				MAX(CURRENT_VALUE) as MAX,
				MIN(CURRENT_VALUE) as MIN,
				AVG(CURRENT_VALUE) as AVG,
				COUNT(*) as CNT
			FROM b_rating_results
			WHERE RATING_ID = ".$ratingId;
		return $DB->Query($strSql, false, $err_mess.__LINE__);
	}

	//Addition rating
	function Add($arFields)
	{
		global $DB, $stackCacheManager;

		$err_mess = (CRatings::err_mess())."<br>Function: Add<br>Line: ";

		// check only general field
		if(!CRatings::__CheckFields($arFields))
			return false;

		$arFields_i = Array(
			"ACTIVE"				=> $arFields["ACTIVE"] == 'Y' ? 'Y' : 'N',
			"POSITION"				=> $arFields["POSITION"] == 'Y' ? 'Y' : 'N',
			"AUTHORITY"				=> $arFields["AUTHORITY"] == 'Y' ? 'Y' : 'N',
			"NAME"					=> $arFields["NAME"],
			"ENTITY_ID"		 		=> $arFields["ENTITY_ID"],
			"CALCULATION_METHOD"	=> $arFields["CALCULATION_METHOD"],
			"~CREATED"				=> $DB->GetNowFunction(),
			"~LAST_MODIFIED"		=> $DB->GetNowFunction(),
		);
		$ID = $DB->Add("b_rating", $arFields_i);

		// queries modules and give them to inspect the field settings
		foreach(GetModuleEvents("main", "OnAfterAddRating", true) as $arEvent)
			$arFields = ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		CRatings::__AddComponents($ID, $arFields);

		$arFields_u = Array(
			"CONFIGS" => "'".$DB->ForSQL(serialize($arFields["CONFIGS"]))."'",
		);

		$DB->Update("b_rating", $arFields_u, "WHERE ID = ".$ID);

		if ($arFields['AUTHORITY'] == 'Y')
			CRatings::SetAuthorityRating($ID);

		CAgent::AddAgent("CRatings::Calculate($ID);", "main", "N", 3600, "", "Y", "");

		$stackCacheManager->Clear("b_rating");

		return $ID;
	}

	//Update rating
	function Update($ID, $arFields)
	{
		global $DB, $stackCacheManager;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: Update<br>Line: ";

		// check only general field
		if(!CRatings::__CheckFields($arFields))
			return false;

		$arFields_u = Array(
			"ACTIVE"				=> $arFields['ACTIVE'] == 'Y' ? 'Y' : 'N',
			"NAME"					=> $arFields["NAME"],
			"ENTITY_ID"		 		=> $arFields["ENTITY_ID"],
			"CALCULATION_METHOD"	=> $arFields["CALCULATION_METHOD"],
			"~LAST_MODIFIED"		=> $DB->GetNowFunction(),
		);
		$strUpdate = $DB->PrepareUpdate("b_rating", $arFields_u);
		if(!$DB->Query("UPDATE b_rating SET ".$strUpdate." WHERE ID=".$ID, false, $err_mess.__LINE__))
			return false;

		if (!isset($arFields["CONFIGS"]))
		{
			$stackCacheManager->Clear("b_rating");
			return true;
		}
		// queries modules and give them to inspect the field settings
		foreach(GetModuleEvents("main", "OnAfterUpdateRating", true) as $arEvent)
			$arFields = ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		CRatings::__UpdateComponents($ID, $arFields);

		$arFields_u = Array(
			"POSITION" => "'".($arFields['POSITION'] == 'Y' ? 'Y' : 'N')."'",
			"AUTHORITY" => "'".($arFields['AUTHORITY'] == 'Y' ? 'Y' : 'N')."'",
			"CONFIGS"  => "'".$DB->ForSQL(serialize($arFields["CONFIGS"]))."'",
		);
		$DB->Update("b_rating", $arFields_u, "WHERE ID = ".$ID);

		if ($arFields['AUTHORITY'] == 'Y')
			CRatings::SetAuthorityRating($ID);

		if ($arFields['NEW_CALC'] == 'Y')
			$DB->Query("UPDATE b_rating_results SET PREVIOUS_VALUE = 0 WHERE RATING_ID=".$ID." and ENTITY_TYPE_ID='".$DB->ForSql($arFields["ENTITY_ID"])."'", false, $err_mess.__LINE__);

		$strSql = "SELECT COMPLEX_NAME FROM b_rating_component WHERE RATING_ID = $ID and ACTIVE = 'N'";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$arrRatingComponentId = array();
		while($arRes = $res->Fetch())
			$arrRatingComponentId[] = $arRes['COMPLEX_NAME'];

		if (!empty($arrRatingComponentId))
			$DB->Query("DELETE FROM b_rating_component_results WHERE RATING_ID = $ID AND COMPLEX_NAME IN ('".implode("','", $arrRatingComponentId)."')", false, $err_mess.__LINE__);

		CRatings::Calculate($ID, true);

		CAgent::RemoveAgent("CRatings::Calculate($ID);", "main");
		$AID = CAgent::AddAgent("CRatings::Calculate($ID);", "main", "N", 3600, "", "Y", "");

		$stackCacheManager->Clear("b_rating");

		return true;
	}

	// delete rating
	function Delete($ID)
	{
		global $DB, $stackCacheManager;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: Delete<br>Line: ";

		foreach(GetModuleEvents("main", "OnBeforeDeleteRating", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($ID));

		$DB->Query("DELETE FROM b_rating WHERE ID=$ID", false, $err_mess.__LINE__);
		$DB->Query("DELETE FROM b_rating_user WHERE RATING_ID=$ID", false, $err_mess.__LINE__);
		$DB->Query("DELETE FROM b_rating_component WHERE RATING_ID=$ID", false, $err_mess.__LINE__);
		$DB->Query("DELETE FROM b_rating_component_results WHERE RATING_ID=$ID", false, $err_mess.__LINE__);
		$DB->Query("DELETE FROM b_rating_results WHERE RATING_ID=$ID", false, $err_mess.__LINE__);

		CAgent::RemoveAgent("CRatings::Calculate($ID);", "main");

		$stackCacheManager->Clear("b_rating");

		return true;
	}

	// start calculation rating-component
	function Calculate($ID, $bForceRecalc = false)
	{
		global $DB;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: Calculate<br>Line: ";

		$strSql = "SELECT
				RC.*,
				".$DB->DateToCharFunction("RC.LAST_MODIFIED")."	LAST_MODIFIED,
				".$DB->DateToCharFunction("RC.LAST_CALCULATED")." LAST_CALCULATED,
				".$DB->DateToCharFunction("RC.NEXT_CALCULATION")." NEXT_CALCULATION
			FROM
				b_rating_component RC
			WHERE
				RATING_ID = $ID
				and ACTIVE = 'Y' ".($bForceRecalc ? '' : 'AND NEXT_CALCULATION <= '.$DB->GetNowFunction());
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		while($arRes = $res->Fetch())
		{
			if(CModule::IncludeModule(strtolower($arRes['MODULE_ID']))) {
				$arRes['CONFIG'] = unserialize($arRes['CONFIG']);
				// If the type is automatic calculation of parameters * global vote weight
				$sRatingWeightType = COption::GetOptionString("main", "rating_weight_type", "auto");
				if ($sRatingWeightType == 'auto') {
					$voteWeight = COption::GetOptionString("main", "rating_vote_weight", 1);
					$arRes['CONFIG']['COEFFICIENT'] = $arRes['CONFIG']['COEFFICIENT']*$voteWeight;
				}
				if (strlen($arRes['EXCEPTION_METHOD']) > 0)
				{
					if (method_exists($arRes['CLASS'], $arRes['EXCEPTION_METHOD']))
					{
						$exceptionText = call_user_func(array($arRes['CLASS'], $arRes['EXCEPTION_METHOD']));
						if ($exceptionText === false)
							if (method_exists($arRes['CLASS'],  $arRes['CALC_METHOD']))
								$result = call_user_func(array($arRes['CLASS'], $arRes['CALC_METHOD']), $arRes);
					}
				}
				else
				{
					if (method_exists($arRes['CLASS'],  $arRes['CALC_METHOD']))
						$result = call_user_func(array($arRes['CLASS'], $arRes['CALC_METHOD']), $arRes);
				}
			}
		}

		CRatings::BuildRating($ID);

		return "CRatings::Calculate($ID);";
	}

	// queries modules and get all the available objects
	function GetRatingObjects()
	{
		$arObjects = array();

		foreach(GetModuleEvents("main", "OnGetRatingsObjects", true) as $arEvent)
		{
			$arConfig = ExecuteModuleEventEx($arEvent);
			foreach ($arConfig as $OBJ_TYPE)
				if (!in_array($OBJ_TYPE, $arObjects))
					$arObjects[] = $OBJ_TYPE;
		}
		return $arObjects;
	}

	// queries modules and get all the available entity types
	function GetRatingEntityTypes($objectType = null)
	{
		$arEntityTypes = array();

		foreach(GetModuleEvents("main", "OnGetRatingsConfigs", true) as $arEvent)
		{
			$arConfig = ExecuteModuleEventEx($arEvent);
			if (is_null($objectType))
			{
				foreach ($arConfig as $OBJ_TYPE => $OBJ_VALUE)
					foreach ($OBJ_VALUE['VOTE'] as $VOTE_VALUE)
					{
						$EntityTypeId = $VOTE_VALUE['MODULE_ID'].'_'.$VOTE_VALUE['ID'];
						if (!in_array($arEntityTypes[$OBJ_TYPE], $EntityTypeId))
							$arEntityTypes[$OBJ_TYPE][] = $EntityTypeId;
					}
			}
			else
			{
				foreach ($arConfig[$objectType]['VOTE'] as $VOTE_VALUE)
				{
					$EntityTypeId = $VOTE_VALUE['MODULE_ID'].'_'.$VOTE_VALUE['ID'];
					$arEntityTypes[$EntityTypeId] = $EntityTypeId;
				}
			}
		}

		return $arEntityTypes;
	}

	// queries modules and assemble an array of settings
	function GetRatingConfigs($objectType = null, $withRatingType = true)
	{
		$arConfigs = array();

		foreach(GetModuleEvents("main", "OnGetRatingsConfigs", true) as $arEvent)
		{
			$arConfig = ExecuteModuleEventEx($arEvent);
			if (is_null($objectType))
			{
				foreach ($arConfig["COMPONENT"] as $OBJ_TYPE => $TYPE_VALUE)
				{
					foreach ($TYPE_VALUE as $RAT_TYPE => $RAT_VALUE)
					{
						foreach ($RAT_VALUE as $VALUE)
						{
							if ($withRatingType)
								$arConfigs[$OBJ_TYPE][$arConfig['MODULE_ID']][$RAT_TYPE][$arConfig['MODULE_ID']."_".$RAT_TYPE."_".$VALUE['ID']] = $VALUE;
							else
								$arConfigs[$OBJ_TYPE][$arConfig['MODULE_ID']][$arConfig['MODULE_ID']."_".$RAT_TYPE."_".$VALUE['ID']] = $VALUE;
						}
					}
				}
			}
			else
			{
				foreach ($arConfig["COMPONENT"][$objectType] as $RAT_TYPE => $RAT_VALUE)
				{
					$arConfigs[$arConfig['MODULE_ID']]['MODULE_ID'] = $arConfig['MODULE_ID'];
					$arConfigs[$arConfig['MODULE_ID']]['MODULE_NAME'] = $arConfig['MODULE_NAME'];
					foreach ($RAT_VALUE as $VALUE)
						if ($withRatingType)
							$arConfigs[$arConfig['MODULE_ID']][$RAT_TYPE][$arConfig['MODULE_ID']."_".$RAT_TYPE."_".$VALUE['ID']] = $VALUE;
						else
							$arConfigs[$arConfig['MODULE_ID']][$arConfig['MODULE_ID']."_".$RAT_TYPE."_".$VALUE['ID']] = $VALUE;
				}
			}
		}

		return $arConfigs;
	}

	function GetRatingVoteResult($entityTypeId, $entityId, $user_id = false)
	{
		global $DB, $CACHE_MANAGER;
		$err_mess = (CRatings::err_mess())."<br>Function: GetRatingVoteResult<br>Line: ";

		$arResult = array();
		if (empty($entityTypeId) || empty($entityId))
			return $arRating;

		$user_id = intval($user_id);
		if ($user_id == 0)
			$user_id = $GLOBALS["USER"]->GetID();

		$bReturnEntityArray = true;
		if (is_array($entityId))
		{
			foreach ($entityId as $currentEntityId)
				$arResult[$currentEntityId] = self::GetRatingVoteResultCache($entityTypeId, IntVal($currentEntityId), $user_id);
		}
		else
		{
			$arResult = self::GetRatingVoteResultCache($entityTypeId, intval($entityId), $user_id);
		}

		return $arResult;
	}

	function GetRatingVoteResultCache($entityTypeId, $entityId, $user_id = false)
	{
		global $DB, $CACHE_MANAGER;
		$err_mess = (CRatings::err_mess())."<br>Function: GetRatingVoteResultCache<br>Line: ";

		$arResult = array();
		$entityId = intval($entityId);

		if (empty($entityTypeId) || empty($entityId))
			return $arRating;

		$user_id = intval($user_id);
		if ($user_id == 0)
			$user_id = $GLOBALS["USER"]->GetID();

		$bucket_size = intval(CACHED_b_rating_bucket_size);
		if($bucket_size <= 0)
			$bucket_size = 100;

		$bucket = intval($entityId/$bucket_size);
		if($CACHE_MANAGER->Read(CACHED_b_rating_vote, $cache_id="b_rvg_".$entityTypeId.$bucket, "b_rating_voting"))
		{
			$arResult = $CACHE_MANAGER->Get($cache_id);
		}
		else
		{
			$sql_str = "SELECT
							RVG.ID,
							RVG.ENTITY_ID,
							RVG.TOTAL_VALUE,
							RVG.TOTAL_VOTES,
							RVG.TOTAL_POSITIVE_VOTES,
							RVG.TOTAL_NEGATIVE_VOTES
						FROM
							b_rating_voting RVG
						WHERE
							RVG.ENTITY_TYPE_ID = '".$DB->ForSql($entityTypeId)."'
						and RVG.ENTITY_ID between ".($bucket*$bucket_size)." AND ".(($bucket+1)*$bucket_size-1)."
						and RVG.ACTIVE = 'Y'";
			$res = $DB->Query($sql_str, false, $err_mess.__LINE__);
			while($row = $res->Fetch())
			{
				$arResult[$row['ENTITY_ID']] = array(
					'USER_VOTE' => 0,
					'USER_HAS_VOTED' => 'N',
					'USER_VOTE_LIST' => Array(),
					'TOTAL_VALUE' => floatval($row['TOTAL_VALUE']),
					'TOTAL_VOTES' => intval($row['TOTAL_VOTES']),
					'TOTAL_POSITIVE_VOTES' => intval($row['TOTAL_POSITIVE_VOTES']),
					'TOTAL_NEGATIVE_VOTES' => intval($row['TOTAL_NEGATIVE_VOTES']),
				);
			}

			$sql = "SELECT RVG.ENTITY_ID, RVG.USER_ID, RVG.VALUE
					FROM b_rating_vote RVG
					WHERE RVG.ENTITY_TYPE_ID = '".$DB->ForSql($entityTypeId)."'
					and RVG.ENTITY_ID between ".($bucket*$bucket_size)." AND ".(($bucket+1)*$bucket_size-1);

			$res = $DB->Query($sql, false, $err_mess.__LINE__);
			while($row = $res->Fetch())
				$arResult[$row['ENTITY_ID']]['USER_VOTE_LIST'][$row['USER_ID']] = floatval($row['VALUE']);

			$CACHE_MANAGER->Set($cache_id, $arResult);
		}

		if (isset($arResult[$entityId]['USER_VOTE_LIST'][$user_id]))
		{
			$arResult[$entityId]['USER_VOTE'] = $arResult[$entityId]['USER_VOTE_LIST'][$user_id];
			$arResult[$entityId]['USER_HAS_VOTED'] = 'Y';
		}

		return isset($arResult[$entityId])? $arResult[$entityId]: Array();
	}

	function GetRatingResult($ID, $entityId)
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: GetRatingResult<br>Line: ";
		$ID = IntVal($ID);

		static $cacheRatingResult = array();
		if(!array_key_exists($ID, $cacheRatingResult))
			$cacheRatingResult[$ID] = array();

		$arResult = array();
		$arToSelect = array();
		if(is_array($entityId))
		{
			foreach($entityId as $value)
			{
				$value = intval($value);
				if($value > 0)
				{
					if(array_key_exists($value, $cacheRatingResult[$ID]))
						$arResult[$value] = $cacheRatingResult[$ID][$value];
					else
					{
						$arResult[$value] = $cacheRatingResult[$ID][$value] = array();
						$arToSelect[$value] = $value;
					}
				}
			}
		}
		else
		{
			$value = intval($entityId);
			if($value > 0)
			{
				if(isset($cacheRatingResult[$ID][$value]))
					$arResult[$value] = $cacheRatingResult[$ID][$value];
				else
				{
					$arResult[$value] = $cacheRatingResult[$ID][$value] = array();
					$arToSelect[$value] = $value;
				}
			}
		}

		if(!empty($arToSelect))
		{
			$strSql  = "
				SELECT ENTITY_TYPE_ID, ENTITY_ID, PREVIOUS_VALUE, CURRENT_VALUE, PREVIOUS_POSITION, CURRENT_POSITION
				FROM b_rating_results
				WHERE RATING_ID = '".$ID."'  AND ENTITY_ID IN (".implode(',', $arToSelect).")
			";
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			while($arRes = $res->Fetch())
			{

				$arRes['PROGRESS_VALUE'] = $arRes['CURRENT_VALUE'] - $arRes['PREVIOUS_VALUE'];
				$arRes['PROGRESS_VALUE'] = round($arRes['PROGRESS_VALUE'], 2);
				$arRes['PROGRESS_VALUE'] = $arRes['PROGRESS_VALUE'] > 0? "+".$arRes['PROGRESS_VALUE']: $arRes['PROGRESS_VALUE'];
				$arRes['ROUND_CURRENT_VALUE'] = round($arRes['CURRENT_VALUE']) == 0? 0: round($arRes['CURRENT_VALUE']);
				$arRes['ROUND_PREVIOUS_VALUE'] = round($arRes['PREVIOUS_VALUE']) == 0? 0: round($arRes['CURRENT_VALUE']);
				$arRes['CURRENT_POSITION'] = $arRes['CURRENT_POSITION'] > 0? $arRes['CURRENT_POSITION'] : GetMessage('RATING_NO_POSITION');
				if ($arRes['PREVIOUS_POSITION']>0)
				{
					$arRes['PROGRESS_POSITION'] = $arRes['PREVIOUS_POSITION'] - $arRes['CURRENT_POSITION'];
					$arRes['PROGRESS_POSITION'] = $arRes['PROGRESS_POSITION'] > 0? "+".$arRes['PROGRESS_POSITION']: $arRes['PROGRESS_POSITION'];
				}
				else
				{
					$arRes['PREVIOUS_POSITION'] = 0;
					$arRes['PROGRESS_POSITION'] = 0;
				}

				$arResult[$arRes["ENTITY_ID"]] = $cacheRatingResult[$ID][$arRes["ENTITY_ID"]] = $arRes;
			}
		}
		if(!is_array($entityId) && !empty($arResult))
			$arResult = array_pop($arResult);

		return $arResult;
	}


	function AddRatingVote($arParam)
	{
		global $DB, $CACHE_MANAGER;

		if (isset($_SESSION['RATING_VOTE_COUNT']) && $arParam['ENTITY_TYPE_ID'] == 'USER')
		{
			if ($_SESSION['RATING_VOTE_COUNT'] >= $_SESSION['RATING_USER_VOTE_COUNT'])
				return false;
			else
				$_SESSION['RATING_VOTE_COUNT']++;
		}

		CRatings::CancelRatingVote($arParam);

		$err_mess = (CRatings::err_mess())."<br>Function: AddRatingVote<br>Line: ";
		$votePlus = $arParam['VALUE'] < 0 ? false : true;

		$ratingId = CRatings::GetAuthorityRating();

		$arRatingUserProp = CRatings::GetRatingUserProp($ratingId, $arParam['USER_ID']);
		$voteUserWeight = $arRatingUserProp['VOTE_WEIGHT'];

		$sRatingWeightType = COption::GetOptionString("main", "rating_weight_type", "auto");
		if ($sRatingWeightType == 'auto')
		{
			if ($arParam['ENTITY_TYPE_ID'] == 'USER')
			{
				$sRatingAuthrorityWeight = COption::GetOptionString("main", "rating_authority_weight_formula", 'Y');
				if ($sRatingAuthrorityWeight == 'Y')
				{
					$communitySize = COption::GetOptionString("main", "rating_community_size", 1);
					$communityAuthority = COption::GetOptionString("main", "rating_community_authority", 1);
					$voteWeight = COption::GetOptionString("main", "rating_vote_weight", 1);
					$arParam['VALUE'] = $arParam['VALUE']*($communitySize*($voteUserWeight/$voteWeight)/$communityAuthority);
				}
			}
			else
			{
				$arParam['VALUE'] = $arParam['VALUE']*$voteUserWeight;
			}
		}
		else
		{
			$arParam['VALUE'] = $arParam['VALUE']*$voteUserWeight;
		}
		$arFields = array(
			'ACTIVE' => "'Y'",
			'TOTAL_VOTES' => "TOTAL_VOTES+1",
			'TOTAL_VALUE' => "TOTAL_VALUE".($votePlus ? '+' : '').floatval($arParam['VALUE']),
			'LAST_CALCULATED' => $DB->GetNowFunction(),
		);
		$arFields[($votePlus ? 'TOTAL_POSITIVE_VOTES' : 'TOTAL_NEGATIVE_VOTES')] = ($votePlus ? 'TOTAL_POSITIVE_VOTES+1' : 'TOTAL_NEGATIVE_VOTES+1');

		// GetOwnerDocument
		$arParam['OWNER_ID'] = 0;
		foreach(GetModuleEvents("main", "OnGetRatingContentOwner", true) as $arEvent)
		{
			$result = ExecuteModuleEventEx($arEvent, array($arParam));
			if ($result !== false)
				$arParam['OWNER_ID'] = IntVal($result);
		}

		$rowAffected = $DB->Update("b_rating_voting", $arFields, "WHERE ENTITY_TYPE_ID='".$DB->ForSql($arParam['ENTITY_TYPE_ID'])."' AND ENTITY_ID='".intval($arParam['ENTITY_ID'])."'" , $err_mess.__LINE__);
		if ($rowAffected > 0)
		{
			$rsRV = $DB->Query("SELECT ID FROM b_rating_voting WHERE ENTITY_TYPE_ID='".$DB->ForSql($arParam['ENTITY_TYPE_ID'])."' AND ENTITY_ID='".intval($arParam['ENTITY_ID'])."'", false, $err_mess.__LINE__);
			$arRV = $rsRV->Fetch();
			$arParam['RATING_VOTING_ID'] = $arRV['ID'];
		}
		else
		{
			$arFields = array(
				"ENTITY_TYPE_ID"		=> "'".$DB->ForSql($arParam["ENTITY_TYPE_ID"])."'",
				"ENTITY_ID"				=> intval($arParam['ENTITY_ID']),
				"OWNER_ID"				=> intval($arParam['OWNER_ID']),
				"ACTIVE"					=> "'Y'",
				"CREATED"				=> $DB->GetNowFunction(),
				"LAST_CALCULATED"		=> $DB->GetNowFunction(),
				"TOTAL_VOTES"			=> 1,
				"TOTAL_VALUE"			=> floatval($arParam['VALUE']),
				"TOTAL_POSITIVE_VOTES"	=> ($votePlus ? 1 : 0),
				"TOTAL_NEGATIVE_VOTES"	=> ($votePlus ? 0 : 1)
			);
			$arParam['RATING_VOTING_ID'] = $DB->Insert("b_rating_voting", $arFields, $err_mess.__LINE__);
		}

		$arFields = array(
			"RATING_VOTING_ID"	=> intval($arParam['RATING_VOTING_ID']),
			"ENTITY_TYPE_ID"		=> "'".$DB->ForSql($arParam["ENTITY_TYPE_ID"])."'",
			"ENTITY_ID"				=> intval($arParam['ENTITY_ID']),
			"VALUE"				=> floatval($arParam['VALUE']),
			"ACTIVE"				=> "'Y'",
			"CREATED"			=> $DB->GetNowFunction(),
			"USER_ID"			=> intval($arParam['USER_ID']),
			"USER_IP"			=> "'".$DB->ForSql($arParam["USER_IP"])."'",
			"OWNER_ID"			=> intval($arParam['OWNER_ID']),
		);
		$ID = $DB->Insert("b_rating_vote", $arFields, $err_mess.__LINE__);

		foreach(GetModuleEvents("main", "OnAddRatingVote", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(intval($ID), $arParam));

		if (CACHED_b_rating_vote!==false)
		{
			$bucket_size = intval(CACHED_b_rating_bucket_size);
			if($bucket_size <= 0)
				$bucket_size = 100;
			$bucket = intval(intval($arParam['ENTITY_ID'])/$bucket_size);
			$CACHE_MANAGER->Clean("b_rvg_".$DB->ForSql($arParam["ENTITY_TYPE_ID"]).$bucket, "b_rating_voting");
		}

		return true;
	}

	function CancelRatingVote($arParam)
	{
		global $DB, $CACHE_MANAGER;

		$err_mess = (CRatings::err_mess())."<br>Function: CancelRatingVote<br>Line: ";

		$sqlStr = "
			SELECT
				RVG.ID,
				RV.ID AS VOTE_ID,
				RV.VALUE AS VOTE_VALUE
			FROM
				b_rating_voting RVG,
				b_rating_vote RV
			WHERE
				RVG.ENTITY_TYPE_ID = '".$DB->ForSql($arParam['ENTITY_TYPE_ID'])."'
			and RVG.ENTITY_ID = ".intval($arParam['ENTITY_ID'])."
			and RVG.ID = RV.RATING_VOTING_ID
			and RV.USER_ID = ".intval($arParam['USER_ID']);

		$res = $DB->Query($sqlStr, false, $err_mess.__LINE__);
		if ($arVote = $res->Fetch())
		{
			$votePlus = $arVote['VOTE_VALUE'] < 0 ? false : true;
			$arFields = array(
				'TOTAL_VOTES' => "TOTAL_VOTES-1",
				'TOTAL_VALUE' => "TOTAL_VALUE".($votePlus ? '-'.floatval($arVote['VOTE_VALUE']) : '+'.floatval(-1*$arVote['VOTE_VALUE'])),
				'LAST_CALCULATED' => $DB->GetNowFunction(),
			);
			$arFields[($votePlus ? 'TOTAL_POSITIVE_VOTES' : 'TOTAL_NEGATIVE_VOTES')] = ($votePlus ? 'TOTAL_POSITIVE_VOTES-1' : 'TOTAL_NEGATIVE_VOTES-1');
			$DB->Update("b_rating_voting", $arFields, "WHERE ID=".intval($arVote['ID']), $err_mess.__LINE__);
			$DB->Query("DELETE FROM b_rating_vote WHERE ID=".intval($arVote['VOTE_ID']), false, $err_mess.__LINE__);

			foreach(GetModuleEvents("main", "OnCancelRatingVote", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(intval($arVote['VOTE_ID']), $arParam));

			if (CACHED_b_rating_vote!==false)
			{
				$bucket_size = intval(CACHED_b_rating_bucket_size);
				if($bucket_size <= 0)
					$bucket_size = 100;
				$bucket = intval(intval($arParam['ENTITY_ID'])/$bucket_size);
				$CACHE_MANAGER->Clean("b_rvg_".$DB->ForSql($arParam["ENTITY_TYPE_ID"]).$bucket, "b_rating_voting");
			}

			return true;
		}

		return false;
	}

	function UpdateRatingUserBonus($arParam)
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: AddRatingBonus<br>Line: ";

		$arParam['RATING_ID'] = intval($arParam['RATING_ID']);
		$arParam['ENTITY_ID'] = intval($arParam['ENTITY_ID']);
		$arParam['BONUS'] = floatval($arParam['BONUS']);

		$arFields = array(
			'RATING_ID'	=> $arParam['RATING_ID'],
			'ENTITY_ID'	=> $arParam['ENTITY_ID'],
			'BONUS'		=> $arParam['BONUS'],
		);

		if (isset($arParam['VOTE_WEIGHT']))
			$arFields['VOTE_WEIGHT'] = floatval($arParam['VOTE_WEIGHT']);

		if (isset($arParam['VOTE_COUNT']))
			$arFields['VOTE_COUNT'] = intval($arParam['VOTE_COUNT']);

		$rows = $DB->Update("b_rating_user", $arFields, "WHERE RATING_ID = ".$arParam['RATING_ID']." AND ENTITY_ID = ".$arParam['ENTITY_ID']);
		if ($rows == 0)
		{
			$rsRB = $DB->Query("SELECT * FROM b_rating_user WHERE RATING_ID = ".$arParam['RATING_ID']." AND ENTITY_ID = ".$arParam['ENTITY_ID'], false, $err_mess.__LINE__);
			if (!$rsRB->SelectedRowsCount())
				$DB->Insert("b_rating_user", $arFields, $err_mess.__LINE__);
		}
		return true;
	}

	function GetRatingUserProp($ratingId, $entityId)
	{
		global $DB;
		$ratingId = IntVal($ratingId);

		static $cache = array();
		if(!array_key_exists($ratingId, $cache))
			$cache[$ratingId] = array();

		$arResult = array();
		$arToSelect = array();
		if(is_array($entityId))
		{
			foreach($entityId as $value)
			{
				$value = intval($value);
				if($value > 0)
				{
					if(array_key_exists($value, $cache[$ratingId]))
						$arResult[$value] = $cache[$ratingId][$value];
					else
					{
						$arResult[$value] = $cache[$ratingId][$value] = array();
						$arToSelect[$value] = $value;
					}
				}
			}
		}
		else
		{
			$value = intval($entityId);
			if($value > 0)
			{
				if(isset($cache[$ratingId][$value]))
					$arResult[$value] = $cache[$ratingId][$value];
				else
				{
					$arResult[$value] = $cache[$ratingId][$value] = array();
					$arToSelect[$value] = $value;
				}
			}
		}

		if(!empty($arToSelect))
		{
			$strSql  = "
				SELECT RATING_ID, ENTITY_ID, BONUS, VOTE_WEIGHT, VOTE_COUNT
				FROM b_rating_user
				WHERE RATING_ID = '".$ratingId."' AND ENTITY_ID IN (".implode(',', $arToSelect).")
			";
			$res = $DB->Query($strSql, false, $err_mess.__LINE__);
			while($arRes = $res->Fetch())
				$arResult[$arRes["ENTITY_ID"]] = $cache[$ratingId][$arRes["ENTITY_ID"]] = $arRes;
		}

		if(!is_array($entityId) && !empty($arResult))
			$arResult = array_pop($arResult);

		return $arResult;
	}

	function GetAuthorityRating()
	{
		global $DB;

		static $authorityRatingId = null;

		$authorityRatingId = COption::GetOptionString("main", "rating_authority_rating", null);

		if(!is_null($authorityRatingId))
			return $authorityRatingId;

		$db_res = CRatings::GetList(array("ID" => "ASC"), array( "ENTITY_ID" => "USER", "AUTHORITY" => "Y"));
		$res = $db_res->Fetch();

		return $authorityRatingId = intval($res['ID']);
	}

	function GetWeightList($arSort=array(), $arFilter=Array())
	{
		global $DB;

		$arSqlSearch = Array();
		$strSqlSearch = "";
		$err_mess = (CRatings::err_mess())."<br>Function: GetWeightList<br>Line: ";

		if (is_array($arFilter))
		{
			foreach ($arFilter as $key => $val)
			{
				if (strlen($val)<=0 || $val=="NOT_REF")
					continue;
				switch(strtoupper($key))
				{
					case "ID":
						$arSqlSearch[] = GetFilterQuery("RW.ID",$val,"N");
					break;
					case "RATING_FROM":
						$arSqlSearch[] = GetFilterQuery("RW.RATING_FROM",$val,"N");
					break;
					case "RATING_TO":
						$arSqlSearch[] = GetFilterQuery("RW.RATING_TO",$val,"N");
					break;
					case "WEIGHT":
						$arSqlSearch[] = GetFilterQuery("RW.WEIGHT",$val,"N");
					break;
					case "COUNT":
						$arSqlSearch[] = GetFilterQuery("RW.COUNT",$val,"N");
					break;
					case "MAX":
						if (in_array($val, Array('Y','N')))
							$arSqlSearch[] = "R.MAX = '".$val."'";
					break;
				}
			}
		}

		$sOrder = "";
		foreach($arSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"? "DESC":"ASC");
			switch (strtoupper($key))
			{
				case "ID":		$sOrder .= ", RW.ID ".$ord; break;
				case "RATING_FROM":	$sOrder .= ", RW.RATING_FROM ".$ord; break;
				case "RATING_TO":		$sOrder .= ", RW.RATING_TO ".$ord; break;
				case "WEIGHT":	$sOrder .= ", RW.WEIGHT ".$ord; break;
				case "COUNT":	$sOrder .= ", RW.COUNT ".$ord; break;
			}
		}

		if (strlen($sOrder)<=0)
			$sOrder = "RW.ID DESC";

		$strSqlOrder = " ORDER BY ".TrimEx($sOrder,",");

		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
		$strSql = "
			SELECT
				RW.ID, RW.RATING_FROM, RW.RATING_TO, RW.WEIGHT, RW.COUNT
			FROM
				b_rating_weight RW
			WHERE
			".$strSqlSearch."
			".$strSqlOrder;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);

		return $res;
	}

	function SetWeight($arConfigs)
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: SetWeight<br>Line: ";

		usort($arConfigs, array('CRatings', '__SortWeight'));
		// prepare insert
		$arAdd = array();
		foreach($arConfigs as $key => $arConfig)
		{
			//If the first condition is restricted to the bottom, otherwise we take the previous high value
			if ($key == 0)
				$arConfig['RATING_FROM'] = -1000000;
			else
				$arConfig['RATING_FROM'] = floatval($arConfigs[$key-1]['RATING_TO'])+0.0001;
			// If this last condition is restricted to the top
			if (!array_key_exists('RATING_TO', $arConfig))
				$arConfig['RATING_TO'] = 1000000;
			elseif ($arConfig['RATING_TO'] > 1000000)
				$arConfig['RATING_TO'] = 1000000;

			$arAdd[$key]['RATING_FROM']   = floatval($arConfig['RATING_FROM']);
			$arAdd[$key]['RATING_TO']     = floatval($arConfig['RATING_TO']);
			$arAdd[$key]['WEIGHT'] = floatval($arConfig['WEIGHT']);
			$arAdd[$key]['COUNT']  = intval($arConfig['COUNT']);
			$arConfigs[$key] = $arAdd[$key];
		}
		// insert
		$DB->Query("DELETE FROM b_rating_weight", false, $err_mess.__LINE__);
		foreach($arAdd as $key => $arFields)
			$DB->Insert("b_rating_weight", $arFields, $err_mess.__LINE__);

		return true;
	}

	function SetVoteGroup($arGroupId, $type)
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: SetVoteGroup<br>Line: ";

		if (!in_array($type, array('R', 'A')))
			return false;

		if (!is_array($arGroupId))
			return false;

		$arFields = array();

		foreach ($arGroupId as $key => $value)
		{
			$arField = array();
			$arField['GROUP_ID'] = intval($value);
			$arField['TYPE'] = "'".$type."'";
			$arFields[$key] = $arField;
		}

		$DB->Query("DELETE FROM b_rating_vote_group WHERE TYPE = '".$type."'", false, $err_mess.__LINE__);
		foreach($arFields as $key => $arField)
			$DB->Insert("b_rating_vote_group", $arField, $err_mess.__LINE__);

		return true;
	}

	function GetVoteGroup($type = '')
	{
		global $DB;
		$err_mess = (CRatings::err_mess())."<br>Function: GetVoteGroup<br>Line: ";

		$bAllType = false;
		if (!in_array($type, array('R', 'A')))
			$bAllType = true;

		$strSql = "SELECT ID, GROUP_ID, TYPE FROM b_rating_vote_group RVG";

		if (!$bAllType)
			$strSql .= " WHERE TYPE = '".$type."'";

		return $DB->Query($strSql, false, $err_mess.__LINE__);
	}

	function ClearData()
	{
		global $DB, $CACHE_MANAGER;
		$err_mess = (CRatings::err_mess())."<br>Function: ClearData<br>Line: ";

		$DB->Query("TRUNCATE TABLE b_rating_prepare", false, $err_mess.__LINE__);
		$DB->Query("TRUNCATE TABLE b_rating_voting_prepare", false, $err_mess.__LINE__);

		$DB->Query("TRUNCATE TABLE b_rating_results", false, $err_mess.__LINE__);
		$DB->Query("TRUNCATE TABLE b_rating_component_results", false, $err_mess.__LINE__);

		$DB->Query("TRUNCATE TABLE b_rating_vote", false, $err_mess.__LINE__);
		$DB->Query("TRUNCATE TABLE b_rating_voting", false, $err_mess.__LINE__);

		$DB->Query("UPDATE b_rating_user SET VOTE_WEIGHT = 0, VOTE_COUNT = 0", false, $err_mess.__LINE__);

		$CACHE_MANAGER->CleanDir("b_rating_voting");

		return true;
	}

	function OnUserDelete($ID)
	{
		CRatings::DeleteByUser($ID);
		return true;
	}

	function OnAfterUserRegister($arFields)
	{
		global $DB;
		$userId = isset($arFields["USER_ID"]) ? intval($arFields["USER_ID"]): (isset($arFields["ID"]) ? intval($arFields["ID"]): 0);
		if($userId>0)
		{
			$authorityRatingId = CRatings::GetAuthorityRating();
			$ratingStartValue = COption::GetOptionString("main", "rating_start_authority", 3);
			$ratingCountVote = COption::GetOptionString("main", "rating_count_vote", 10);

			$arParam = array(
				'RATING_ID' => $authorityRatingId,
				'ENTITY_ID' => $userId,
				'BONUS' => intval($ratingStartValue),
				'VOTE_WEIGHT' => intval($ratingStartValue)*COption::GetOptionString("main", "rating_vote_weight", 1),
				'VOTE_COUNT' => intval($ratingCountVote)+intval($ratingStartValue),
			);
			CRatings::UpdateRatingUserBonus($arParam);

			if (IsModuleInstalled("intranet"))
			{
				$strSql = "INSERT INTO b_rating_subordinate (RATING_ID, ENTITY_ID, VOTES) VALUES ('".$authorityRatingId."', '".$userId."', '".(intval($ratingCountVote)+intval($ratingStartValue))."')";
				$DB->Query($strSql, false, $err_mess.__LINE__);
			}

			$sRatingAssignType = COption::GetOptionString("main", "rating_assign_type", 'manual');
			if ($sRatingAssignType == 'auto')
			{
				$assignRatingGroup = COption::GetOptionString("main", "rating_assign_rating_group", 0);
				$assignAuthorityGroup = COption::GetOptionString("main", "rating_assign_authority_group", 0);
				if ($assignRatingGroup == 0 && $assignAuthorityGroup == 0)
					return false;

				$arGroups = array();
				$res = CUser::GetUserGroupList($userId);
				while($res_arr = $res->Fetch())
					$arGroups[] = array("GROUP_ID"=>$res_arr["GROUP_ID"], "DATE_ACTIVE_FROM"=>$res_arr["DATE_ACTIVE_FROM"], "DATE_ACTIVE_TO"=>$res_arr["DATE_ACTIVE_TO"]);

				if ($assignRatingGroup > 0)
					$arGroups[] = array("GROUP_ID"=>intval($assignRatingGroup));
				if ($assignAuthorityGroup > 0 && $assignRatingGroup != $assignAuthorityGroup)
					$arGroups[] = array("GROUP_ID"=>intval($assignAuthorityGroup));

				CUser::SetUserGroup($userId, $arGroups);
			}
		}
	}

	function __SortWeight($a, $b)
	{
		if (isset($a['RATING_FROM']) || isset($b['RATING_FROM']))
			return 1;

		return floatval($a['RATING_TO']) < floatval($b['RATING_TO']) ? -1 : 1;
	}

	// check only general field
	function __CheckFields($arFields)
	{
		$aMsg = array();

		if(is_set($arFields, "NAME") && trim($arFields["NAME"])=="")
			$aMsg[] = array("id"=>"NAME", "text"=>GetMessage("RATING_GENERAL_ERR_NAME"));
		if(is_set($arFields, "ACTIVE") && !($arFields["ACTIVE"] == 'Y' || $arFields["ACTIVE"] == 'N'))
			$aMsg[] = array("id"=>"ACTIVE", "text"=>GetMessage("RATING_GENERAL_ERR_ACTIVE"));
		if(is_set($arFields, "ENTITY_ID"))
		{
			$arObjects = CRatings::GetRatingObjects();
			if(!in_array($arFields['ENTITY_ID'], $arObjects))
				$aMsg[] = array("id"=>"ENTITY_ID", "text"=>GetMessage("RATING_GENERAL_ERR_ENTITY_ID"));
		}
		if(is_set($arFields, "CALCULATION_METHOD") && trim($arFields["CALCULATION_METHOD"])=="")
			$aMsg[] = array("id"=>"CALCULATION_METHOD", "text"=>GetMessage("RATING_GENERAL_ERR_CAL_METHOD"));

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
			return false;
		}

		return true;
	}

	// creates a configuration record for each item rating
	function __AddComponents($ID, $arFields)
	{
		global $DB;

		$arRatingConfigs = CRatings::GetRatingConfigs($arFields["ENTITY_ID"], false);

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: __AddComponents<br>Line: ";
		foreach ($arFields['CONFIGS'] as $MODULE_ID => $RAT_ARRAY)
			foreach ($RAT_ARRAY as $RAT_TYPE => $COMPONENT)
				foreach ($COMPONENT as $COMPONENT_NAME => $COMPONENT_VALUE)
				{
					if (!isset($arRatingConfigs[$MODULE_ID][$MODULE_ID."_".$RAT_TYPE."_".$COMPONENT_NAME]))
						continue;

					$arFields_i = Array(
						"RATING_ID"			=> $ID,
						"ACTIVE"			=> isset($COMPONENT_VALUE["ACTIVE"]) && $COMPONENT_VALUE["ACTIVE"] == 'Y' ? 'Y' : 'N',
						"ENTITY_ID"			=> $arFields["ENTITY_ID"],
						"MODULE_ID"			=> $MODULE_ID,
						"RATING_TYPE"		=> $RAT_TYPE,
						"NAME"				=> $COMPONENT_NAME,
						"COMPLEX_NAME"		=> $arFields["ENTITY_ID"].'_'.$MODULE_ID.'_'.$RAT_TYPE.'_'.$COMPONENT_NAME,
						"CLASS"				=> $arRatingConfigs[$MODULE_ID][$MODULE_ID."_".$RAT_TYPE."_".$COMPONENT_NAME]["CLASS"],
						"CALC_METHOD"		=> $arRatingConfigs[$MODULE_ID][$MODULE_ID."_".$RAT_TYPE."_".$COMPONENT_NAME]["CALC_METHOD"],
						"EXCEPTION_METHOD"	=> $arRatingConfigs[$MODULE_ID][$MODULE_ID."_".$RAT_TYPE."_".$COMPONENT_NAME]["EXCEPTION_METHOD"],
						"REFRESH_INTERVAL"	=> $arRatingConfigs[$MODULE_ID][$MODULE_ID."_".$RAT_TYPE."_".$COMPONENT_NAME]["REFRESH_TIME"],
						"~LAST_MODIFIED"	=> $DB->GetNowFunction(),
						"~NEXT_CALCULATION" => $DB->GetNowFunction(),
						"IS_CALCULATED"		=> "N",
						"~CONFIG"			=> "'".serialize($COMPONENT_VALUE)."'",
					);

					$DB->Add("b_rating_component", $arFields_i, array(), "", false, $err_mess.__LINE__);
				}


		return true;
	}

	function __UpdateComponents($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		$err_mess = (CRatings::err_mess())."<br>Function: __UpdateComponents<br>Line: ";

		$DB->Query("DELETE FROM b_rating_component WHERE RATING_ID=$ID", false, $err_mess.__LINE__);

		CRatings::__AddComponents($ID, $arFields, $arConfigs);

		return true;
	}

	function err_mess()
	{
		return "<br>Class: CRatings<br>File: ".__FILE__;
	}
}
?>