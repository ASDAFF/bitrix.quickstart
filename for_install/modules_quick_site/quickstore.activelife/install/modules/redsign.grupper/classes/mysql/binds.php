<?
class CRSGBinds
{
	function GetList($aSort=array("SORT"=>"ASC"), $aFilter=array())
	{
		global $DB;
		
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			$val = $DB->ForSql($val);
			if(strlen($val)<=0)
				continue;
			switch(strtoupper($key))
			{
				case "ID":
					$arFilter[] = "GB.ID='".$val."'";
					break;
				case "IBLOCK_PROPERTY_ID":
					$arFilter[] = "GB.IBLOCK_PROPERTY_ID='".$val."'";
					break;
					break;
				case "GROUP_ID":
					$arFilter[] = "GB.GROUP_ID='".$val."'";
					break;
			}
		}
		
		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> "ASC"?"DESC":"ASC");
			switch(strtoupper($key))
			{
				case "ID":
					$arOrder[] = "GB.ID ".$ord;
					break;
				case "IBLOCK_PROPERTY_ID":
					$arOrder[] = "GB.IBLOCK_PROPERTY_ID ".$ord;
					break;
				case "GROUP_ID":
					$arOrder[] = "GB.GROUP_ID ".$ord;
					break;
			}
		}
		
		if(count($arOrder) == 0)
			$arOrder[] = "GB.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);
		
		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);
		
		$strSql = "
			SELECT
				GB.*
			FROM
				b_redsign_grupper_binds GB
			".$sFilter.$sOrder;

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function GetByID( $ID )
	{
		return CRSGGroups::Getlist(array(),array("ID"=>$ID));
	}

	function Delete( $ID )
	{
		global $DB;
		$ID = intval($ID);

		$DB->StartTransaction();

		$res = $DB->Query("DELETE FROM b_redsign_grupper_binds WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res)
		{
			$DB->Commit();
		} else {
			$DB->Rollback();
		}

		return $res;
	}

	function Add( $arFields )
	{
		global $DB;
		
		$ID = $DB->Add("b_redsign_grupper_binds", $arFields);
		if(IntVal($ID)>0)
			return $ID;
		else
			return FALSE;
	}

	function Update( $ID, $arFields )
	{
		global $DB;
		
		$ID = intval($ID);
		if(isset($arFields["ID"]))
			unset($arFields["ID"]);
		
		$strUpdate = $DB->PrepareUpdate("b_redsign_grupper_binds", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE b_redsign_grupper_binds SET ".$strUpdate." WHERE ID=".$ID;
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			return $ID;
		} else {
			return FALSE;
		}
	}
	
	function DeleteBindsForGroupID($GROUP_ID)
	{
		$res1 = CRSGBinds::GetList(array("ID"=>"ASC"),array("GROUP_ID"=>$GROUP_ID));
		while($data1 = $res1->Fetch())
		{
			CRSGBinds::Delete($data1["ID"]);
		}
		return TRUE;
	}
}
?>