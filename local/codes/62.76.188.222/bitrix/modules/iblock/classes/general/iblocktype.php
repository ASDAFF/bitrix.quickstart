<?
IncludeModuleLangFile(__FILE__);

class CIBlockType
{
	///////////////////////////////////////////////////////////////////
	// Get list if information blocks function
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder = Array("SORT"=>"ASC"), $arFilter=Array())
	{
		global $DB;
		$bLang = false;
		$strSqlSearch = "1=1\n";
		foreach($arFilter as $key=>$val)
		{
			if(!is_array($val) && strlen($val) <= 0)
				continue;

			switch(strtoupper($key))
			{
			case "ID":
				$strSqlSearch .= "AND UPPER(T.ID) LIKE UPPER('".$DB->ForSql($val)."')\n";
				break;
			case "=ID":
				if(is_array($val))
				{
					if(!empty($val))
					{
						$sqlVal = array_map(array($DB, 'ForSQL'), $val);
						$strSqlSearch .= "AND T.ID in ('".implode("', '", $sqlVal)."')\n";
					}
				}
				else
				{
					$strSqlSearch .= "AND T.ID = '".$DB->ForSql($val)."'\n";
				}
				break;
			case "NAME":
				$strSqlSearch .= "AND UPPER(TL.NAME) LIKE UPPER('%".$DB->ForSql($val)."%')\n";
				$bLang = true;
				break;
			}
		}

		$strSqlOrder = '';
		foreach($arOrder as $by=>$order)
		{
			$by = strtoupper($by);
			if($by != "ID")
				$by = "SORT";

			$order = strtolower($order);
			if($order!="desc")
				$order = "asc";

			if($strSqlOrder=='')
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ', ';

			$strSqlOrder .= "T.".$by." ".$order;
		}

		$strSql = "
			SELECT ".($bLang?"DISTINCT":"")." T.*
			FROM b_iblock_type T
			".($bLang?" LEFT JOIN b_iblock_type_lang TL ON TL.IBLOCK_TYPE_ID = T.ID ":"")."
			WHERE ".$strSqlSearch.$strSqlOrder;

		if(CACHED_b_iblock_type===false)
		{
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			global $CACHE_MANAGER;
			if($CACHE_MANAGER->Read(CACHED_b_iblock_type, $cache_id = "b_iblock_type".md5($strSql), "b_iblock_type"))
			{
				$arResult = $CACHE_MANAGER->Get($cache_id);
			}
			else
			{
				$arResult = array();
				$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
				while($ar = $res->Fetch())
					$arResult[]=$ar;
				$CACHE_MANAGER->Set($cache_id, $arResult);
			}
			$res = new CDBResult;
			$res->InitFromArray($arResult);
		}

		return $res;
	}

	function _GetCache($ID)
	{
		global $DB,$CACHE_MANAGER;
		if($CACHE_MANAGER->Read(CACHED_b_iblock_type, "b_iblock_type", "b_iblock_type"))
			$arIBlocks = $CACHE_MANAGER->Get("b_iblock_type");
		else
		{
			$arIBlocks = array();
			$rs = $DB->Query("SELECT * FROM b_iblock_type");
			while($ar = $rs->GetNext())
			{
				$ar["_lang"]=array();
				$arIBlocks[$ar['ID']] = $ar;
			}
			$rs = $DB->Query("SELECT * FROM b_iblock_type_lang");
			while($ar = $rs->GetNext())
			{
				$arIBlocks[$ar['IBLOCK_TYPE_ID']]["_lang"][$ar["LID"]] = $ar;
			}
			$CACHE_MANAGER->Set("b_iblock_type", $arIBlocks);
		}
		$ID = trim($ID);
		if(array_key_exists($ID, $arIBlocks))
			return $arIBlocks[$ID];
		else
			return false;
	}
	///////////////////////////////////////////////////////////////////
	// This function will return information block by ID
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		if(CACHED_b_iblock_type===false)
		{
			return CIBlockType::GetList(array(),array("=ID"=>$ID));
		}
		else
		{
			$arResult = CIBlockType::_GetCache($ID);
			$res = new CDBResult;
			if($arResult!==false)
			{
				unset($arResult["_lang"]);
				$res->InitFromArray(array($arResult));
			}
			else
			{
				$res->InitFromArray(array());
			}
			return $res;
		}
	}

	///////////////////////////////////////////////////////////////////
	// This function will get language information by ID
	///////////////////////////////////////////////////////////////////
	function GetByIDLang($ID, $LID, $bFindAny=true)
	{
		global $DB;
		$LID = $DB->ForSQL($LID, 2);

		if(CACHED_b_iblock_type===false)
		{
			$strSql =
				"SELECT BTL.*, BT.* ".
				"FROM b_iblock_type BT, b_iblock_type_lang BTL ".
				"WHERE BTL.IBLOCK_TYPE_ID = '".$DB->ForSQL($ID)."' ".
				"	AND BTL.LID='".$LID."'".
				"	AND BT.ID=BTL.IBLOCK_TYPE_ID ";

			$res = $DB->Query($strSql);

			if($r = $res->GetNext())
				return $r;
		}
		else
		{
			$arResult = CIBlockType::_GetCache($ID);
			if($arResult!==false && array_key_exists($LID, $arResult["_lang"]))
			{
				$res = $arResult["_lang"][$LID];
				unset($arResult["_lang"]);
				return array_merge($res, $arResult);
			}
		}

		if(!$bFindAny)
			return false;

		$strSql =
			"SELECT BTL.*, BT.* ".
			"FROM b_iblock_type BT, b_iblock_type_lang BTL, b_language L ".
			"WHERE BTL.IBLOCK_TYPE_ID = '".$DB->ForSQL($ID)."' ".
			"	AND BTL.LID = L.LID ".
			"	AND BT.ID=BTL.IBLOCK_TYPE_ID ".
			"ORDER BY L.DEF DESC, L.SORT";

		$res = $DB->Query($strSql);

		if($r = $res->GetNext())
			return $r;

		return false;
	}

	///////////////////////////////////////////////////////////////////
	// Delete function
	///////////////////////////////////////////////////////////////////
	function Delete($ID)
	{
		global $DB, $USER;
		if(CACHED_b_iblock_type!==false) $GLOBALS["CACHE_MANAGER"]->CleanDir("b_iblock_type");
		$iblocks = CIBlock::GetList(Array(), Array("=TYPE"=>$ID));
		while($iblock = $iblocks->Fetch())
		{
			if(!CIBlock::Delete($iblock["ID"]))
				return false;
		}

		if(!$DB->Query("DELETE FROM b_iblock_type_lang WHERE IBLOCK_TYPE_ID='".$DB->ForSql($ID)."'", true))
			return false;
		return $DB->Query("DELETE FROM b_iblock_type WHERE ID='".$DB->ForSql($ID)."'", true);
	}

	///////////////////////////////////////////////////////////////////
	// This one called before any Add or Update action
	///////////////////////////////////////////////////////////////////
	function CheckFields($arFields, $ID=false)
	{
		global $DB;
		$this->LAST_ERROR = "";

		if($ID === false)
		{
			if(strlen($arFields["ID"]) <= 0)
			{
				$this->LAST_ERROR .= GetMessage("IBLOCK_TYPE_BAD_ID")."<br>";
			}
			elseif(preg_match("/[^A-Za-z0-9_]/", $arFields["ID"]))
			{
				$this->LAST_ERROR .= GetMessage("IBLOCK_TYPE_ID_HAS_WRONG_CHARS")."<br>";
			}
			else
			{
				$chk = $DB->Query("SELECT 'x' FROM b_iblock_type WHERE ID='".$DB->ForSQL($arFields["ID"])."'");
				if($chk->Fetch())
				{
					$this->LAST_ERROR .= GetMessage("IBLOCK_TYPE_DUBL_ID")."<br>";
					return false;
				}
			}
		}

		if(is_set($arFields, "LANG") && is_array($arFields["LANG"]))
		{
			foreach($arFields["LANG"] as $lid => $arFieldsLang)
			{
				if(strlen($arFieldsLang["NAME"])<=0)
				{
					$this->LAST_ERROR .= GetMessage("IBLOCK_TYPE_BAD_NAME")." ".$lid.".<br>";
				}
			}
		}

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}

	///////////////////////////////////////////////////////////////////
	// Add action
	///////////////////////////////////////////////////////////////////
	function Add($arFields)
	{
		global $DB, $USER;
		if(CACHED_b_iblock_type!==false) $GLOBALS["CACHE_MANAGER"]->CleanDir("b_iblock_type");

		$arFields["SECTIONS"] = ($arFields["SECTIONS"]=="Y"?"Y":"N");
		$arFields["IN_RSS"] = ($arFields["IN_RSS"]=="Y"?"Y":"N");

		if(!$this->CheckFields($arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_iblock_type", $arFields);

		$strSql =
			"INSERT INTO b_iblock_type(".$arInsert[0].") ".
			"VALUES(".$arInsert[1].")";

		$DB->Query($strSql);
		$ID = $DB->ForSQL($arFields["ID"]);

		if(is_array($arFields["LANG"]))
		{
			$DB->Query("DELETE FROM b_iblock_type_lang WHERE IBLOCK_TYPE_ID='".$ID."'");
			foreach($arFields["LANG"] as $lid => $arFieldsLang)
			{
				if(strlen($arFieldsLang["NAME"])>0 || strlen($arFieldsLang["ELEMENT_NAME"])>0)
				{
					$strSql =
						"INSERT INTO b_iblock_type_lang(IBLOCK_TYPE_ID, LID, NAME, SECTION_NAME, ELEMENT_NAME) ".
						"SELECT BT.ID, L.LID, '".$DB->ForSql($arFieldsLang["NAME"], 100)."', '".$DB->ForSql($arFieldsLang["SECTION_NAME"], 100)."', '".$DB->ForSql($arFieldsLang["ELEMENT_NAME"], 100)."' ".
						"FROM b_iblock_type BT, b_language L ".
						"WHERE BT.ID='".$ID."' AND L.LID='".$DB->ForSQL($lid)."' ";
					$DB->Query($strSql);
				}
			}
		}

		return $arFields["ID"];
	}


	function Update($ID, $arFields)
	{
		global $DB, $USER;
		if(CACHED_b_iblock_type!==false) $GLOBALS["CACHE_MANAGER"]->CleanDir("b_iblock_type");

		$arFields["SECTIONS"] = $arFields["SECTIONS"]=="Y"?"Y":"N";
		$arFields["IN_RSS"] = $arFields["IN_RSS"]=="Y"?"Y":"N";

		if(!$this->CheckFields($arFields, $ID))
			return false;

		$str_update = $DB->PrepareUpdate("b_iblock_type", $arFields);
		$strSql = "UPDATE b_iblock_type SET ".$str_update." WHERE ID='".$DB->ForSQL($ID)."'";
		$DB->Query($strSql);

		if(is_array($arFields["LANG"]))
		{
			$DB->Query("DELETE FROM b_iblock_type_lang WHERE IBLOCK_TYPE_ID='".$DB->ForSQL($ID)."'");
			foreach($arFields["LANG"] as $lid => $arFieldsLang)
			{
				if(strlen($arFieldsLang["NAME"])>0 || strlen($arFieldsLang["ELEMENT_NAME"])>0)
				{
					$strSql =
						"INSERT INTO b_iblock_type_lang(IBLOCK_TYPE_ID, LID, NAME, SECTION_NAME, ELEMENT_NAME) ".
						"SELECT BT.ID, L.LID, '".$DB->ForSql($arFieldsLang["NAME"], 100)."', '".$DB->ForSql($arFieldsLang["SECTION_NAME"], 100)."', '".$DB->ForSql($arFieldsLang["ELEMENT_NAME"], 100)."' ".
						"FROM b_iblock_type BT, b_language L ".
						"WHERE BT.ID='".$DB->ForSQL($ID)."' AND L.LID='".$DB->ForSQL($lid)."' ";
					$DB->Query($strSql);
				}
			}
		}

		return true;
	}
}

?>
