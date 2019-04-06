<?
class CMillcomPhpThumbTemplates
{
	const CACHE_ID = 'phpthumb';
	const CACHE_DIR = '/phpthumb/templates';
	const CACHE_TIME = 120;
	const MYSQL_ERROR = 'MySQL error CMillcomPhpThumbTemplates:';

	static function GetList() {
		global $DB;
		$sqlQuery = "SELECT * FROM c_millcom_phpthumb";
		return $DB->Query($sqlQuery, false, self::MYSQL_ERROR . __LINE__);
	}

  static function GetOptionsByID($ID, $CLEAR_CACHE = false) {
  	global $CACHE_MANAGER;
		$result = false;
		$cache_id = self::CACHE_ID.$ID;

		$obCache = new CPHPCache();
		if($obCache->InitCache(self::CACHE_TIME, $cache_id, self::CACHE_DIR) && !$CLEAR_CACHE) {
			$result = $obCache->GetVars();
		} else {
			$CACHE_MANAGER->StartTagCache(self::CACHE_DIR);
			$res = self::GetByID($ID);
			$row = $res->Fetch();
			$result = unserialize($row['OPTIONS']);

			$CACHE_MANAGER->RegisterTag($cache_id);
			$CACHE_MANAGER->EndTagCache();
			$obCache->EndDataCache($result);
		}
		return $result;
	}
	static function GetByID($ID) {
		global $DB;
		$ID = $DB->ForSql($ID);
		$sqlQuery = 'SELECT * FROM c_millcom_phpthumb WHERE ID = "'.$ID.'"';
		$result = $DB->Query($sqlQuery, false, self::MYSQL_ERROR . __LINE__);
		return $result;
	}

	static function Update($ID, $arFields) {
		global $DB;
		if(empty($arFields))
			return false;

		foreach($arFields as $key => $value) {
			$arFields[$key] = "'" . $DB->ForSQL($arFields[$key]) . "'";
		}

		return $DB->Update('c_millcom_phpthumb', $arFields, 'WHERE ID = '.$ID, self::MYSQL_ERROR . __LINE__);
	}

  static function Add($arFields) {
		global $DB;
		if(empty($arFields))
			return false;

		foreach($arFields as $key => $value) {
			$arFields[$key] = "'" . $DB->ForSQL($arFields[$key]) . "'";
		}
    return $DB->Insert('c_millcom_phpthumb', $arFields, self::MYSQL_ERROR . __LINE__);
  }

	static function Delete($ID) {
		global $DB;
		$ID = $DB->ForSql($ID);
		if (!$ID)
			return false;
		$sqlQuery = 'DELETE FROM c_millcom_phpthumb WHERE ID = "'.$ID.'"';
		$result = $DB->Query($sqlQuery, false, self::MYSQL_ERROR . __LINE__);
		return $result;
	}  
}
?>