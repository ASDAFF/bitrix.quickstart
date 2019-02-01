<?
class CWD_Reviews2_Fields {
	const TableName = 'b_wd_reviews2_fields';
	
	function DateFormatToMySQL($Date) {
		return CDatabase::FormatDate($Date, FORMAT_DATETIME, 'YYYY-MM-DD HH:MI:SS');
	}
	
	/**
	 *	Add
	 */
	function Add($arFields) {
		global $DB;
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$arFields['DATE_CREATED'] = $Now;
		$arFields['DATE_MODIFIED'] = $Now;
		$ID = $DB->Add(self::TableName, $arFields, array(), '', true);
		if ($ID) {
			self::HandleSave($ID);
			return true;
		}
		return false;
	}
	
	// Update
	function Update($ID, $arFields) {
		global $DB;
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$arFields['DATE_MODIFIED'] = self::DateFormatToMySQL($Now);
		$arSQL = array();
		foreach ($arFields as $Key => $Field) {
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$arSQL[] = "`{$Key}`='{$Field}'";
		}
		$strSQL = implode(',',$arSQL);
		$TableName = self::TableName;
		$SQL = "UPDATE `{$TableName}` SET {$strSQL} WHERE `ID`='{$ID}' LIMIT 1;";
		if ($DB->Query($SQL, false)) {
			self::HandleSave($ID);
			return true;
		}
		return false;
	}
	
	/**
	 *	Handler for save (Add, Update)
	 */
	function HandleSave($ID) {
		$resField = self::GetByID($ID);
		if ($arField = $resField->GetNext(false,false)) {
			$InterfaceID = IntVal($arField['INTERFACE_ID']);
			if ($InterfaceID>0) {
				CWD_Reviews2_Interface::CreateEventType($InterfaceID);
			}
		}
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "SELECT `INTERFACE_ID` FROM `{$TableName}` WHERE `ID`='{$ID}';";
		if ($resItem = $DB->Query($SQL, false)) {
			if ($arItem = $resItem->GetNext(false,false)) {
				$SQL = "DELETE FROM `{$TableName}` WHERE `ID`='{$ID}';";
				if ($DB->Query($SQL, false)) {
					CWD_Reviews2_Interface::CreateEventType($arItem['INTERFACE_ID']);
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Delete by interface id
	 */
	function DeleteByInterface($InterfaceID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "DELETE FROM `{$TableName}` WHERE `INTERFACE_ID`='{$InterfaceID}';";
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
		if (!is_array($arSort)) {$arSort = array('SORT'=>'ASC','ID'=>'ASC');}
		foreach ($arSort as $Key => $Value) {
			$Value = strtolower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$SelectDateCreated = $DB->DateToCharFunction('DATE_CREATED').' DATE_CREATED';
		$SelectDateModified = $DB->DateToCharFunction('DATE_MODIFIED').' DATE_MODIFIED';
		$TableName = self::TableName;
		$SQL = "SELECT *,{$SelectDateCreated},{$SelectDateModified} FROM `{$TableName}`";
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
					$arWhere[] = "`{$Key}` = '{$arFilterItem}'";
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
					if (in_array($arSortKey,array('DATE_CREATED','DATE_MODIFIED'))) {
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
		return $DB->Query($SQL, true);
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
	
}

?>