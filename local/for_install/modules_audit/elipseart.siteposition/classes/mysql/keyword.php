<?
class CEASitePositionKeyword
{
	function GetList($arSort=false,$arFilter=false,$position=false,$last_position=false,$limit=false)
	{
		global $DB, $USER, $APPLICATION;
		
		$arrFilter = false;
		$arrSort = false;
		
		$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
		while($res = $ssDB->Fetch())
		{
			$arSearchSystem[] = $res;
		}
		
		if($arFilter && count($arFilter) > 0)
		{
			$i = 0;
			foreach($arFilter as $key => $val)
			{
				if(!empty($key))
				{
					if(
						$key == "ID" ||
						$key == "HOST_ID" ||
						$key == "REGION_ID" ||
						$key == "SORT" ||
						$key == "ACTIVE" ||
						$key == "NAME" ||
						$key == "SITE_ID"
					)
					{
						if($i > 0)
							$arrFilter .= " AND ";
						
						if($key == "SITE_ID")
							$table = "th";
						else
							$table = "tk";
						
						if(is_array($val))
						{
							$arFilterArr = "";
							foreach($val as $filter)
							{
								if(!empty($arFilterArr))
									$arFilterArr .= " OR ";
								
								$arFilterArr .= $table.".".$key." = '".$filter."' ";
							}
						}
						else
						{
							$arFilterArr = $table.".".$key." = '".$val."' ";
						}
						
						if(!empty($arFilterArr))
							$arFilterArr = "(".$arFilterArr.")";
						
						$arrFilter .= $arFilterArr;
						
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
					$searchSortValid = array();
					
					foreach($arSearchSystem as $ss)
						$searchSortValid[] = "POSITION_".$ss["NAME"];
					
					
					if(
						(
							$key == "ID" ||
							$key == "HOST_ID" ||
							$key == "REGION_ID" ||
							$key == "SORT" ||
							$key == "ACTIVE" ||
							$key == "NAME" ||
							$key == "SITE_ID" ||
							$key == "HOST_NAME" ||
							$key == "REGION_CODE" ||
							//$key == "POSITION"
							in_array($key, $searchSortValid)
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
						
						if($key == "SITE_ID")
							$arrSort .= "th.SITE_ID ".$val." ";
						elseif($key == "HOST_NAME")
							$arrSort .= "th.NAME ".$val." ";
						elseif($key == "REGION_CODE")
							$arrSort .= "tr.CODE ".$val." ";
						//elseif($key == "POSITION")
						elseif(in_array($key, $searchSortValid))
						{
							$sName = str_replace("POSITION_","",$key);
							
							$i = 0;
							foreach($arSearchSystem as $ss)
							{
								if((in_array($ss["NAME"],$arFilter["SEARCH_NAME"]) || in_array("ALL",$arFilter["SEARCH_NAME"])) && $sName == $ss["NAME"])
								{
									$ssNum = $i;
									//$arrSort .= "tp".$ssNum.".POSITION ".$val." ";
									$arrSort .= $key." ".$val." ";
									++$i;
								}
							}
							
							//$arrSort .= $key." ".$val." ";
						}
						else
							$arrSort .= "tk.".$key." ".$val." ";
						
						++$i;
					}
				}
			}
			if(!empty($arrSort))
			{
				$arrSort = "ORDER BY ".$arrSort;
			}
		}
		
		$strSql = "
			SELECT
				tk.ID
				,tk.NAME
				,tk.SORT
				,tk.ACTIVE
				";
				if($position)
				{
					$i = 0;
					foreach($arSearchSystem as $ss)
					{
						if(in_array($ss["NAME"],$arFilter["SEARCH_NAME"]) || in_array("ALL",$arFilter["SEARCH_NAME"]))
						{
							$strSql .= "
								,ts".$i.".ID AS SEARCH_".$ss["NAME"]."_ID
								,ts".$i.".NAME AS SEARCH_".$ss["NAME"]."_SYSTEM
								,tp".$i.".POSITION AS POSITION_".$ss["NAME"]."
								,tp".$i.".DATE AS DATE_".$ss["NAME"]."
							";
						
							++$i;
						}
					}
				}
				if($last_position)
				{
					$i = 0;
					foreach($arSearchSystem as $ss)
					{
						if(in_array($ss["NAME"],$arFilter["SEARCH_NAME"]) || in_array("ALL",$arFilter["SEARCH_NAME"]))
						{
							$strSql .= "
								,tpl".$i.".POSITION AS LAST_POSITION_".$ss["NAME"]."
								,tpl".$i.".DATE AS LAST_DATE_".$ss["NAME"]."
							";
							
							++$i;
						}
					}
				}
				$strSql .= "
				,tr.ID AS REGION_ID
				,tr.CODE AS REGION_CODE
				,th.ID AS HOST_ID
				,th.SITE_ID AS SITE_ID
				,th.NAME AS HOST_NAME
			FROM
				b_ea_siteposition_keyword AS tk
			
			";
			
			if($position)
			{
				$i = 0;
				foreach($arSearchSystem as $ss)
				{
					if(in_array($ss["NAME"],$arFilter["SEARCH_NAME"]) || in_array("ALL",$arFilter["SEARCH_NAME"]))
					{
						$strSql .= "
							LEFT JOIN
								b_ea_siteposition_position AS tp".$i."
							ON
								tp".$i.".KEYWORD_ID = tk.ID
							AND
								tp".$i.".ID = (SELECT ID FROM b_ea_siteposition_position WHERE KEYWORD_ID = tk.ID AND SEARCH_ID = '".$ss["ID"]."' ORDER BY DATE DESC LIMIT 1)
							
							LEFT JOIN
								b_ea_siteposition_search_system AS ts".$i."
							ON
								(tp".$i.".SEARCH_ID = ts".$i.".ID)
						";
						
						++$i;
					}
				}
			}
			
			if($last_position)
			{
				$i = 0;
				foreach($arSearchSystem as $ss)
				{
					if(in_array($ss["NAME"],$arFilter["SEARCH_NAME"]) || in_array("ALL",$arFilter["SEARCH_NAME"]))
					{
						$strSql .= "
							LEFT JOIN
								b_ea_siteposition_position AS tpl".$i."
							ON
								tpl".$i.".KEYWORD_ID = tk.ID
							AND
								tpl".$i.".ID = (SELECT ID FROM b_ea_siteposition_position WHERE KEYWORD_ID = tk.ID AND SEARCH_ID = '".$ss["ID"]."' ORDER BY DATE DESC LIMIT 1,1)
						";
						
						++$i;
					}
				}
			}
			
			$strSql .= "
			LEFT JOIN
				b_ea_siteposition_host AS th
			ON
				tk.HOST_ID = th.ID 
			
			LEFT JOIN
				b_ea_siteposition_region AS tr
			ON
				tk.REGION_ID = tr.ID 

			".(($arrFilter) ? $arrFilter : "")."
			
			".(($arrSort) ? $arrSort : "ORDER BY tk.ID DESC")."
			
			".(($limit) ? " LIMIT ".$limit : "")."
			
		";
		
		$res = $DB->Query($strSql);
		
		return $res;
	}
	
	function Add($hostId,$regionId=false,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"HOST_ID" => "'".intval($hostId)."'",
			"REGION_ID" => "'".intval($regionId)."'",
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",
			"SORT" => "'".intval($arParam["SORT"])."'",			
		);
		$DB->StartTransaction();
		$ID = $DB->Insert("b_ea_siteposition_keyword", $arFields, $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($ID);
	}
	
	function Update($id,$hostId,$regionId=false,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"HOST_ID" => "'".intval($hostId)."'",
			"REGION_ID" => "'".intval($regionId)."'",
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",
			"SORT" => "'".intval($arParam["SORT"])."'",			
		);
		$DB->StartTransaction();
		$DB->Update("b_ea_siteposition_keyword", $arFields, "WHERE ID='".intval($id)."'", $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($id);
	}
	
	function Delete($id)
	{
		global $DB, $USER, $APPLICATION;
		
		$strSql = "DELETE FROM b_ea_siteposition_keyword WHERE ID = '".intval($id)."' limit 1";
		$res = $DB->Query($strSql);
		
		$strSql = "DELETE FROM b_ea_siteposition_position WHERE KEYWORD_ID = '".intval($id)."' ";
		$res = $DB->Query($strSql);
		
		return true;
	}
}
?>