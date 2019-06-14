<?
class CWebdebugReviews {
	
	/**
	 *	Get list
	 */
	function GetList($arSort=false, $arFilter=false, $arGroup=false, $arSelect=false) {
		global $DB;
		if (!is_array($arSort)) {$arSort = array("ID"=>"DESC");}
		foreach ($arSort as $Key => $Value) {
			$Value = ToLower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$SelectFields = "*";
		if (is_array($arSelect) && !empty($arSelect)) {
			$arAllFields = self::GetFieldsArray();
			if (in_array("*", $arSelect)) {
				$arSelect = $arAllFields;
			}
			foreach ($arSelect as $Key => $Value) {
				if (!in_array($Key, $arAllFields)) {
					unset($arSelect[$Key]);
				}
			}
			foreach ($arSelect as $Key => $Value) {
				$arSelect[$Key] = "`b_webdebug_reviews`.".$Value;
			}
			$SelectFields = implode(",", $arSelect);
		}
		if (trim($SelectFields)=="") $SelectFields = "*";
		if (is_array($arGroup) && empty($arGroup)) $SelectFields = "COUNT(`b_webdebug_reviews`.ID) AS CNT";
		$SQL = "SELECT {$SelectFields} FROM `b_webdebug_reviews`";
		// Filter
		if (is_array($arFilter) && !empty($arFilter)) {
			foreach ($arFilter as $arFilterKey => $arFilterVal) {
				if (trim($arFilterVal)=="") {unset($arFilter[$arFilterKey]);}
			}
			$arWhere = array();
			foreach ($arFilter as $Key => $arFilterItem) {
				$SubStr2 = substr($Key, 0, 2);
				$SubStr1 = substr($Key, 0, 1);
				$Key = $DB->ForSQL($Key);
				$arFilterItem = $DB->ForSQL($arFilterItem);
				if ($SubStr2==">=" || $SubStr2=="<=") {
					$Val = substr($Key, 2);
					if ($SubStr2 == ">=") {$arWhere[] = "`b_webdebug_reviews`.`{$Val}` >= '{$arFilterItem}'";}
					if ($SubStr2 == "<=") {$arWhere[] = "`b_webdebug_reviews`.`{$Val}` <= '{$arFilterItem}'";}
				} elseif ($SubStr1==">" || $SubStr1=="<") {
					$Val = substr($Key, 1);
					if ($SubStr1 == ">") {$arWhere[] = "`b_webdebug_reviews`.`{$Val}` > '{$arFilterItem}'";}
					if ($SubStr1 == "<") {$arWhere[] = "`b_webdebug_reviews`.`{$Val}` < '{$arFilterItem}'";}
					if ($SubStr1 == "!") {$arWhere[] = "`b_webdebug_reviews`.`{$Val}` <> '{$arFilterItem}'";}
				} elseif ($SubStr1=="%") {
					$Val = substr($Key, 1);
					$arWhere[] = "upper(`b_webdebug_reviews`.`{$Val}`) like upper ('%{$arFilterItem}%') and `b_webdebug_reviews`.`{$Val}` is not null";
				} else {
					$arWhere[] = "`b_webdebug_reviews`.`{$Key}` = '{$arFilterItem}'";
				}
			}
			if (count($arWhere)>0) {
				$SQL .= " WHERE ".implode(" AND ", $arWhere);
			}
		}
		// Group
		if (is_array($arGroup) && !empty($arGroup)) {
			$SQL .= " GROUP BY ";
			$arGroupBy = array();
			foreach ($arGroup as $arGroupKey => $arGroupItem) {
				$arGroupKey = $DB->ForSQL($arGroupKey);
				$arGroupItem = $DB->ForSQL($arGroupItem);
				if (trim($arGroupKey)!="") {
					$SortBy = "`{$arGroupKey}`";
					if (trim($arGroupItem)!="") {
						$SortBy .= " {$arGroupItem}";
					}
					$arGroupBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arGroupBy);
		}
		// Sort
		if (is_array($arSort) && !empty($arSort)) {
			$SQL .= " ORDER BY ";
			$arSortBy = array();
			foreach ($arSort as $arSortKey => $arSortItem) {
				$arSortKey = $DB->ForSQL($arSortKey);
				$arSortItem = $DB->ForSQL($arSortItem);
				if (trim($arSortKey)!="") {
					$SortBy = "`{$arSortKey}`";
					if (trim($arSortItem)!="") {
						$SortBy .= " {$arSortItem}";
					}
					$arSortBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arSortBy);
		}
		return $DB->Query($SQL, false, __LINE__);
	}
	
	/**
	 *	Get by ID
	 */
	function GetByID($ID) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID) {
			return self::GetList(false, array("ID"=>$ID));
		} else {
			return new CDBResult;
		}
	}
	
	/**
	 *	Add
	 */
	function Add($arFields) {
		global $DB;
		$arAllFields = self::GetFieldsArray();
		foreach ($arFields as $Key => $Value) {
			if ($Key=="DATETIME" && trim($Value)!="") {
				$Value = trim($Value);
				$arFields[$Key] = CDatabase::FormatDate($Value, CSite::GetDateFormat("FULL"), "YYYY-MM-DD HH:MI:SS");
			} elseif (!in_array($Key, $arAllFields)) {
				unset($arFields[$Key]);
			} else {
				$arFields[$Key] = $DB->ForSQL($Value);
			}
			if (isset($arFields[$Key])) $arFields[$Key] = "'".$arFields[$Key]."'";
		}
		$resInsert = $DB->Insert(
			"b_webdebug_reviews",
			$arFields
		);
		return $resInsert;
	}
	
	/**
	 *	Update
	 */
	function Update($ID, $arFields) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID==0) {
			return false;
		}
		if (!is_array($arFields) || empty($arFields)) {
			return false;
		}
		$arAllFields = self::GetFieldsArray();
		$SQL_SET = array();
		foreach ($arFields as $Key => $Field) {
			if ($Key=="SITE_ID" || !in_array($Key, $arAllFields)) continue;
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$SQL_SET[] = "`{$Key}`='{$Field}'";
		}
		$SQL_SET = implode(",",$SQL_SET);
		$SQL = "UPDATE `b_webdebug_reviews` SET {$SQL_SET} WHERE `ID`='{$ID}' LIMIT 1";
		$Res = $DB->Query($SQL, true, __LINE__);
		if ($Res === false) {
			return false;
		}
		return $Res->AffectedRowsCount();
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID==0) {
			return false;
		}
		$SQL = "DELETE FROM `b_webdebug_reviews` WHERE `ID`='{$ID}' LIMIT 1";
		return $DB->Query($SQL, true, __LINE__);
	}
	
	/**
	 *	Get a list of all the fields
	 */
	function GetFieldsArray() {
		return array(
			"ID",
			"IBLOCK_ID",
			"ELEMENT_ID",
			"MODERATED",
			"SITE_ID",
			"USER_ID",
			"NAME",
			"EMAIL",
			"EMAIL_PUBLIC",
			"WWW",
			"DATETIME",
			"TEXT_PLUS",
			"TEXT_MINUS",
			"TEXT_COMMENTS",
			"VOTE_0",
			"VOTE_1",
			"VOTE_2",
			"VOTE_3",
			"VOTE_4",
			"VOTE_5",
			"VOTE_6",
			"VOTE_7",
			"VOTE_8",
			"VOTE_9",
		);
	}
	
	/**
	 *	Get average rating for iblock element
	 */
	function GetAverageValue($ElementID, $arVotes=array()) {
		global $DB;
		$ElementID = IntVal($ElementID);
		$Value = 0;
		if ($ElementID) {
			$arVoteTmp = array();
			if (!is_array($arVotes) || empty($arVotes)) $arVotes = array("0");
			foreach ($arVotes as $Vote) {
				$arVoteTmp[] = '`VOTE_'.$Vote.'`';
			}
			$strVotes = implode('+',$arVoteTmp);
			$Count = count($arVoteTmp);
			$SQL = "SELECT AVG({$strVotes})/{$Count} as `AVERAGE_VOTE` FROM `b_webdebug_reviews` WHERE `ELEMENT_ID`='{$ElementID}'";
			$resAverage = $DB->Query($SQL, true, __LINE__);
			if ($arAverage = $resAverage->GetNext()) {
				$Value = $arAverage["AVERAGE_VOTE"];
			}
		}
		$Value = round($Value,2);
		return $Value;
	}
	
	/**
	 *	Get reviews count for iblock element
	 */
	function GetCount($ElementID) {
		global $DB;
		$ElementID = IntVal($ElementID);
		$Value = 0;
		if ($ElementID) {
			$SQL = "SELECT COUNT(`ID`) as `COUNT` FROM `b_webdebug_reviews` WHERE `ELEMENT_ID`='{$ElementID}'";
			$resCount = $DB->Query($SQL, true, __LINE__);
			if ($arCount = $resCount->GetNext()) {
				$Value = $arCount["COUNT"];
			}
		}
		return $Value;
	}
	
	/**
	 *	Get item DETAIL_PAGE_URL
	 */
	function GetItemURL($ItemID) {
		$ItemURL = false;
		if (CModule::IncludeModule("iblock")) {
			$resItem = CIBlockElement::GetList(array(),array("ID"=>$ItemID),false,false);
			if ($arItem = $resItem->GetNext(false,false)) {
				return $arItem["DETAIL_PAGE_URL"]?$arItem["DETAIL_PAGE_URL"]:false;
			}
		}
		return $ItemURL;
	}
	
}
?>