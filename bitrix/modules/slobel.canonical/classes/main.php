<?
IncludeModuleLangFile(__FILE__);

class SlobelCanonical
{
	var $LAST_ERROR="";
	function GetList($aSort=array(), $aFilter=array())
	{
		global $DB;
		
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			if(strlen($val)<=0)
				continue;
		
			$key = strtoupper($key);
			switch($key)
			{
				case "ID":
				case "ACTIVE":
					$arFilter[] = "S.".$key." = '".$DB->ForSql($val)."'";
					break;
				case "RULE":
				case "FILE":
				case "BASE":
					$arFilter[] = "S.".$key." like '%".$DB->ForSql($val)."%'";
					break;
			}
		}
		
		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"? "DESC": "ASC");
			$key = strtoupper($key);
		
			switch($key)
			{
				case "ID":
				case "BASE":
				case "RULE":
				case "FILE":
				case "ACTIVE":
					$arOrder[] = "S.".$key." ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
			$arOrder[] = "S.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);
		
		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);
	
		$strSql = "
			SELECT
				S.ID
				,S.RULE
				,S.ACTIVE
				,S.BASE
				,S.FILE
			FROM
				slobel_canonical_list S
			".$sFilter.$sOrder;
	
		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function GetByID($ID)
	{
		global $DB;
		$ID = intval($ID);
	
		$strSql = "
			SELECT
				S.*
			FROM slobel_canonical_list S
			WHERE S.ID = ".$ID."
		";
	
		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = intval($ID);
	
		$strUpdate = $DB->PrepareUpdate("slobel_canonical_list", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE slobel_canonical_list SET ".$strUpdate." WHERE ID=".$ID;
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return true;
	}
	
	function Delete($ID)
	{
		global $DB;
		$ID = intval($ID);
	
		$DB->StartTransaction();
		
		$res = $DB->Query("DELETE FROM slobel_canonical_list WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	
		if($res)
			$DB->Commit();
		else
			$DB->Rollback();
	
		return $res;
	}
	
	function Add($arFields)
	{
		global $DB;
	
		$ID = $DB->Add("slobel_canonical_list", $arFields);
		return $ID;
	}
}
?>