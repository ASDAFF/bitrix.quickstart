<?
class CRSGGroups
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
					$arFilter[] = "GP.ID='".$val."'";
					break;
				case "XML_ID":
					$arFilter[] = "GP.XML_ID='".$val."'";
					break;
				case "CODE":
					$arFilter[] = "GP.CODE LIKE '%".$val."%'";
					break;
				case "NAME":
					$arFilter[] = "GP.NAME LIKE '%".$val."%'";
					break;
				case "SORT":
					$arFilter[] = "GP.SORT='".$val."'";
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
					$arOrder[] = "GP.ID ".$ord;
					break;
				case "XML_ID":
					$arOrder[] = "GP.XML_ID ".$ord;
					break;
				case "CODE":
					$arOrder[] = "GP.CODE ".$ord;
					break;
				case "NAME":
					$arOrder[] = "GP.NAME ".$ord;
					break;
				case "CODE":
					$arOrder[] = "GP.CODE ".$ord;
					break;
				case "SORT":
					$arOrder[] = "GP.SORT ".$ord;
					break;
			}
		}
		
		if(count($arOrder) == 0)
			$arOrder[] = "GP.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);
		
		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);
		
		$strSql = "
			SELECT
			DISTINCT
				GP.*
			FROM
				b_redsign_grupper_groups GP
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

		$res = $DB->Query("DELETE FROM b_redsign_grupper_groups WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res)
		{
			CRSGBinds::DeleteBindsForGroupID($ID);
			$DB->Commit();
		} else {
			$DB->Rollback();
		}

		return $res;
	}

	function Add( $arFields )
	{
		global $DB;
		
		$arParams = array("change_case"=>"U","replace_space"=>"_","replace_other"=>"_");
		if($arFields["CODE"]!="")
			$arFields["CODE"] = Cutil::translit($arFields["CODE"],"ru",$arParams);
		else
			$arFields["CODE"] = Cutil::translit($arFields["NAME"],"ru",$arParams);
		
		$ID = $DB->Add("b_redsign_grupper_groups", $arFields);
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
		
		$arParams = array("change_case"=>"U","replace_space"=>"_","replace_other"=>"_");
		if($arFields["CODE"]!="")
			$arFields["CODE"] = Cutil::translit($arFields["CODE"],"ru",$arParams);
		else
			$arFields["CODE"] = Cutil::translit($arFields["NAME"],"ru",$arParams);
		
		$strUpdate = $DB->PrepareUpdate("b_redsign_grupper_groups", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE b_redsign_grupper_groups SET ".$strUpdate." WHERE ID=".$ID;
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			return $ID;
		} else {
			return FALSE;
		}
	}
}
?>