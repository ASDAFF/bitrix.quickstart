<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews2_Vote {
	
	const ModuleID = 'webdebug.reviews';
	const TableName = 'b_wd_reviews2_voting';
	
	/**
	 *	Format date from site format to MySQL format
	 */
	function DateFormatToMySQL($Date) {
		$mResult = CDatabase::FormatDate($Date, FORMAT_DATETIME, 'YYYY-MM-DD HH:MI:SS');
		if ($mResult===false) {
			$mResult = '0000-00-00 00:00:00';
		}
		return $mResult;
	}
	
	/**
	 *	Add
	 */
	function Add($arFields) {
		global $DB;
		$TableName = self::TableName;
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$arFields['DATE_CREATED'] = $Now;
		$arSqlFields = array();
		$arSqlValues = array();
		foreach($arFields as $Key => $Value) {
			$arSqlFields[] = '`'.$Key.'`';
			if ($Key=='USER_IP') {
				$arFields['USER_IP'] = $DB->ForSQL($arFields['USER_IP']);
				$Value = "INET_ATON('{$arFields['USER_IP']}')";
			} else {
				$Value = '\''.$DB->ForSQL($Value).'\'';
			}
			$arSqlValues[] = $Value;
		}
		$strSqlFields = implode(',',$arSqlFields);
		$strSqlValues = implode(',',$arSqlValues);
		$SQL = "INSERT INTO `{$TableName}` ({$strSqlFields}) VALUES ({$strSqlValues});";
		if ($DB->Query($SQL)) {
			return true;
		}
		return false;
	}
	
	// Update
	function Update($ID, $arFields) {
		global $DB;
		$arFields = self::RemoveWrongFields($arFields);
		$arSQL = array();
		foreach ($arFields as $Key => $Field) {
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$arSQL[] = "`{$Key}`='{$Field}'";
		}
		$strSQL = implode(',',$arSQL);
		$TableName = self::TableName;
		$SQL = "UPDATE `{$TableName}` SET {$strSQL} WHERE `ID`='{$ID}' LIMIT 1;";
		if ($DB->Query($SQL, true)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "DELETE FROM `{$TableName}` WHERE `ID`='{$ID}';";
		if ($DB->Query($SQL, true)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Get list
	 */
	function GetList($arSort=false, $arFilter=false) {
		global $DB;
		if (!is_array($arSort)) {$arSort = array('ID'=>'ASC');}
		foreach ($arSort as $Key => $Value) {
			$Value = strtolower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$SelectDateCreated = $DB->DateToCharFunction('DATE_CREATED').' DATE_CREATED';
		$TableName = self::TableName;
		$SQL = "SELECT *,{$SelectDateCreated},INET_NTOA(`USER_IP`) AS `USER_IP` FROM `{$TableName}`";
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
					if ($SubStr2 == ">=") {$arWhere[] = "`{$Val}` >= '{$arFilterItem}'";}
					if ($SubStr2 == "<=") {$arWhere[] = "`{$Val}` <= '{$arFilterItem}'";}
				} elseif ($SubStr1==">" || $SubStr1=="<") {
					$Val = substr($Key, 1);
					if ($SubStr1 == ">") {$arWhere[] = "`{$Val}` > '{$arFilterItem}'";}
					if ($SubStr1 == "<") {$arWhere[] = "`{$Val}` < '{$arFilterItem}'";}
					if ($SubStr1 == "!") {$arWhere[] = "`{$Val}` <> '{$arFilterItem}'";}
				} elseif ($SubStr1=="%") {
					$Val = substr($Key, 1);
					$arWhere[] = "upper(`{$Val}`) like upper ('%{$arFilterItem}%') and `{$Val}` is not null";
				} else {
					if (ToUpper($Key)=='USER_IP') {
						$arWhere[] = "`{$Key}` = INET_ATON('{$arFilterItem}')";
					} else {
						$arWhere[] = "`{$Key}` = '{$arFilterItem}'";
					}
				}
			}
			if (count($arWhere)>0) {
				$SQL .= " WHERE ".implode(" AND ", $arWhere);
			}
		}
		// Sort
		if (is_array($arSort) && !empty($arSort)) {
			$SQL .= " ORDER BY ";
			$arSortBy = array();
			foreach ($arSort as $arSortKey => $arSortItem) {
				$arSortKey = $DB->ForSQL($arSortKey);
				$arSortItem = $DB->ForSQL($arSortItem);
				if (trim($arSortKey)!="") {
					if (in_array($arSortKey,array('DATE_CREATED'))) {
						$arSortKey = "{$TableName}`.`{$arSortKey}";
					}
					$SortBy = "`{$arSortKey}`";
					if (trim($arSortItem)!="") {
						$SortBy .= " {$arSortItem}";
					}
					$arSortBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arSortBy);
		}
		return $DB->Query($SQL);
	}
	
	/**
	 *	Get by ID
	 */
	function GetByID($ID) {
		return self::GetList(false,array("ID"=>$ID));
	}
	
	/**
	 *	Get fields in table
	 */
	function GetTableFields() {
		global $DB;
		$arResult = array();
		$Table = self::TableName;
		$SQL = "SHOW COLUMNS FROM `{$Table}`";
		$resColumns = $DB->Query($SQL);
		while ($arColumn = $resColumns->GetNext(false,false)) {
			$arResult[] = $arColumn['Field'];
		}
		return $arResult;
	}
	
	/**
	 *	Remove not existance fields
	 */
	function RemoveWrongFields($arFields) {
		$arResult = array();
		if (!is_array($arFields)) {
			$arFields = array();
		}
		$arExistsFields = self::GetTableFields();
		foreach($arFields as $Key => $Value) {
			$KeyName = trim($Key,"\r\n\t<>=%!");
			if (in_array($KeyName,$arExistsFields)) {
				$arResult[$Key] = $Value;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check, if user can vote. It can be false, if he already voted for selected review
	 */
	function UserCanVote($ID, $bAllowUnregVoting=false) {
		global $DB, $USER;
		$Table = self::TableName;
		$arFilter = array(
			'REVIEW_ID' => $ID,
		);
		if ($bAllowUnregVoting && !$USER->IsAuthorized()) {
			$arFilter['USER_IP'] = $_SERVER['REMOTE_ADDR'];
		} else {
			$arFilter['USER_ID'] = $USER->GetID();
		}
		$resItem = self::GetList(false,$arFilter);
		if ($arItem = $resItem->GetNext(false,false)) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 *	Save user vote
	 */
	function SaveVote($ID, $Plus, $bAllowUnregVoting=false) {
		global $USER;
		$UserID = IntVal($USER->GetID());
		$resReview = CWD_Reviews2_Reviews::GetByID($ID);
		if ($arReview = $resReview->GetNext(false,false)) {
			$arFields = array(
				'USER_ID' => $UserID,
				'REVIEW_ID' => $ID,
				'VALUE' => $Plus===true ? '1' : '-1',
			);
			if ($bAllowUnregVoting && !$USER->IsAuthorized()) {
				$arFields['USER_ID'] = '0';
				$arFields['USER_IP'] = $_SERVER['REMOTE_ADDR'];
			}
			if (self::Add($arFields)>0) {
				$VotesY = IntVal($arReview['VOTES_Y']);
				$VotesN = IntVal($arReview['VOTES_N']);
				$VoteResult = IntVal($arReview['VOTE_RESULT']);
				if ($Plus) {
					$VotesY++;
					$VoteResult++;
				} else {
					$VotesN++;
					$VoteResult--;
				}
				$arFields = array(
					'VOTES_Y' => $VotesY,
					'VOTES_N' => $VotesN,
					'VOTE_RESULT' => $VoteResult,
					'SKIP_CHECK' => 'Y',
					'DATE_VOTING' => date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
				);
				$obReviews = new CWD_Reviews2_Reviews;
				if ($obReviews->Update($ID,$arFields)) {
					return true;
				}
			}
		}
		return false;
	}
	
}

?>