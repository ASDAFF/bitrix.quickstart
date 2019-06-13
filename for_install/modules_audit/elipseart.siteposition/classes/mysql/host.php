<?
class CEASitePositionHost
{
	function GetList($arSort=false,$arFilter=false,$limit=false)
	{
		$arrFilter = false;
		$arrSort = false;
		
		if($arFilter && count($arFilter) > 0)
		{
			$i = 0;
			foreach($arFilter as $key => $val)
			{
				if(!empty($key))
				{
					if(
						$key == "ID" ||
						$key == "SITE_ID" ||
						$key == "NAME" ||
						$key == "ACTIVE"
					)
					{
						if($i > 0)
							$arrFilter .= " AND ";
						
						$arrFilter .= "th.".$key." = '".$val."' ";
						
						++$i;
					}
				}
			}
			if(!empty($arrFilter))
			{
				$arrFilter = "WHERE ".$arrFilter;
			}
		}
		
		if($arSort && count($arSort) > 0)
		{
			$i = 0;
			foreach($arSort as $key => $val)
			{
				if(!empty($key))
				{
					if(
						(
							$key == "ID" ||
							$key == "SITE_ID" ||
							$key == "NAME" ||
							$key == "ACTIVE"
						)
						&&
						(
							strtoupper($val) == "DESC" ||
							strtoupper($val) == "ASC"
						)
					)
					{
						if($i > 0)
							$arrSort .= " , ";
						
						$arrSort .= "th.".$key." ".$val." ";
						
						++$i;
					}
				}
			}
			if(!empty($arrSort))
			{
				$arrSort = "ORDER BY ".$arrSort;
			}
		}
		
		global $DB, $USER, $APPLICATION;
		
		$strSql = "
			SELECT
				*
			FROM
				b_ea_siteposition_host AS th
				
			".(($arrFilter) ? $arrFilter : "")."
			
			".(($arrSort) ? $arrSort : "ORDER BY th.ID ASC")."
			
			".(($limit) ? " LIMIT ".$limit : "")."
			
		";
		$res = $DB->Query($strSql);
		
		return $res;
	}
	
	function Add($siteId,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"SITE_ID" => "'".$DB->ForSql($siteId,2)."'",
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",			
		);
		$DB->StartTransaction();
		$ID = $DB->Insert("b_ea_siteposition_host", $arFields, $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($ID);
	}
	
	function Update($id,$siteId,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"SITE_ID" => "'".$DB->ForSql($siteId,2)."'",
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",			
		);
		$DB->StartTransaction();
		$DB->Update("b_ea_siteposition_host", $arFields, "WHERE ID='".intval($id)."'", $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($id);
	}
	
	function Delete($id)
	{
		global $DB, $USER, $APPLICATION;
		
		$keywordDB = CEASitePositionKeyword::GetList(array(),array("HOST_ID"=>intval($id)));
		while($res = $keywordDB->Fetch())
		{
			$arKeywordId[] = $res["ID"];
		}
		if(count($arKeywordId) > 0)
		{
			$arId = "";
			$i = 0;
			foreach($arKeywordId as $val)
			{
				if(intval($val) > 0)
				{
					if($i == 0)
						$arId .= " WHERE ";
						
					if($i > 0)
						$arId .= " OR ";
					
					$arId .= "KEYWORD_ID = ".intval($val)."";
					
					++$i;
				}
			} 
		}
		
		if(!empty($arId)){
			$strSql = "DELETE FROM b_ea_siteposition_position".$arId;
			$res = $DB->Query($strSql);
		}
		
		$strSql = "DELETE FROM b_ea_siteposition_keyword WHERE HOST_ID = '".intval($id)."' ";
		$res = $DB->Query($strSql);
		
		$strSql = "DELETE FROM b_ea_siteposition_host WHERE ID = '".intval($id)."' limit 1";
		$res = $DB->Query($strSql);
		
		return true;
	}
	
	function UpdateSiteHost()
	{
		$rsSites = CSite::GetList($by="sort", $order="desc", array());
		while ($arSite = $rsSites->Fetch())
		{
			$domain_list = explode("\n",$arSite["DOMAINS"]);
			foreach($domain_list as $val)
			{
				$val = trim($val);
				if(!empty($val))
				{
					$hostDB = CEASitePositionHost::GetList(array(),array("NAME"=>$val),1);
					$res = $hostDB->Fetch();
					if($res["ID"] > 0)
					{
						$arParam = array(
							"ACTIVE" => "Y",
							"NAME" => $val,
						);
						$DOMAIN_ID = CEASitePositionHost::Update($res["ID"],$arSite["LID"],$arParam);
						$domain[] = $arParam["NAME"];
					}
					else
					{
						$arParam = array(
							"ACTIVE" => "Y",
							"NAME" => $val,
						);
						$DOMAIN_ID = CEASitePositionHost::Add($arSite["LID"],$arParam);
						$domain[] = $arParam["NAME"];
					}
				}
			}
		}
		
		$hostDB = CEASitePositionHost::GetList();
		while($res = $hostDB->Fetch())
		{
			if( !in_array($res["NAME"],$domain) )
			{
				CEASitePositionHost::Delete($res["ID"]);
			}
		}
	}
}
?>