<?
class CEASitePosition
{
	function GetList($arSort=false,$arFilter=false,$limit=false)
	{
		global $DB, $USER, $APPLICATION;
		
		$arrFilter = false;
		$arrSort = false;
		
		if($arFilter["SEARCH_NAME"])
		{
			$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
			while($res = $ssDB->Fetch())
			{
				$arSearchSystem[] = $res;
			}
		}
		
		if($arFilter && count($arFilter) > 0)
		{
			$i = 0;
			foreach($arFilter as $key => $val)
			{
				if(!empty($key))
				{
					$OperAct = "=";
					//if(ereg(">",$key))
					if(strpos($key,">") !== false)
					{
						$key = str_replace(">","",$key);
						$OperAct = ">";
					}
					//elseif(ereg(">=",$key))
					elseif(strpos($key,">=") !== false)
					{
						$key = str_replace(">=","",$key);
						$OperAct = ">=";
					}
					//elseif(ereg("<",$key))
					elseif(strpos($key,"<") !== false)
					{
						$key = str_replace("<","",$key);
						$OperAct = "<";
					}
					//elseif(ereg("<=",$key))
					elseif(strpos($key,"<=") !== false)
					{
						$key = str_replace("<=","",$key);
						$OperAct = "<=";
					}
					//elseif(ereg("!",$key))
					elseif(strpos($key,"!") !== false)
					{
						$key = str_replace("!","",$key);
						$OperAct = "!=";
					}
					
					if(
						$key == "ID" ||
						$key == "KEYWORD_ID" ||
						$key == "DATE" ||
						$key == "POSITION" ||
						$key == "SEARCH_NAME" ||
						$key == "SEARCH_ID"
					)
					{
						if($i > 0)
							$arrFilter .= " AND ";
						
						$table = "tp";
						
						if($key == "SEARCH_NAME")
						{
							if(is_array($val))
							{
								$arFilterArr = "";
								
								foreach($val as $filter)
								{
									foreach($arSearchSystem as $ss)
									{
										if($ss["NAME"] == $filter)
										{
											if(!empty($arFilterArr))
												$arFilterArr .= " OR ";
											
											$arFilterArr .= $table.".SEARCH_ID ".$OperAct." '".$ss["ID"]."' ";
										}
									}
								}
								
								if(!empty($arFilterArr))
									$arFilterArr = "(".$arFilterArr.")";
								
								$arrFilter .= $arFilterArr;
								
							}
							else
							{
								foreach($arSearchSystem as $ss)
								{
										if($ss["NAME"] == $val)
										{
											$arrFilter .= $table.".SEARCH_ID ".$OperAct." '".$ss["ID"]."' ";
										}
								}
							}
						}
						else
						{
							if(is_array($val))
							{
								$arFilterArr = "";
								foreach($val as $filter)
								{
									if(!empty($arFilterArr))
										$arFilterArr .= " OR ";
									
									$arFilterArr .= $table.".".$key." ".$OperAct." '".$filter."' ";
								}
							}
							else
							{
								$arFilterArr = $table.".".$key." ".$OperAct." '".$val."' ";
							}
							
							if(!empty($arFilterArr))
								$arFilterArr = "(".$arFilterArr.")";
							
							$arrFilter .= $arFilterArr;
							
						}
						
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
							$key == "DATE" ||
							$key == "POSITION"
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
						
						$arrSort .= "tp.".$key." ".$val." ";
						
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
				tp.ID
				,tp.DATE
				,tp.POSITION
				,tk.ID AS KEYWORD_ID
				,tk.NAME AS NAME
				,tk.HOST_ID AS HOST_ID
				,ts.ID AS SEARCH_ID
				,ts.NAME AS SEARCH_SYSTEM
				,th.SITE_ID AS SITE_ID
				,th.ID AS HOST_ID
				,th.NAME AS HOST_NAME
				,tr.ID AS REGION_ID
				,tr.CODE AS REGION_CODE
			FROM
				b_ea_siteposition_position AS tp

			LEFT JOIN
				b_ea_siteposition_keyword AS tk
			ON
				(tp.KEYWORD_ID = tk.ID)
			
			LEFT JOIN
				b_ea_siteposition_search_system AS ts
			ON
				(tp.SEARCH_ID = ts.ID)
			
			LEFT JOIN
				b_ea_siteposition_host AS th
			ON
				th.ID = tk.HOST_ID
			
			LEFT JOIN
				b_ea_siteposition_region AS tr
			ON
				tr.ID = tk.REGION_ID

			".(($arrFilter) ? $arrFilter : "")."
			
			".(($arrSort) ? $arrSort : "ORDER BY tp.ID DESC")."
			
			".(($limit) ? " LIMIT ".$limit : "")."
			
		";
		$res = $DB->Query($strSql);
		
		return $res;
	}
	
	function Add($searchId,$keywordId,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"SEARCH_ID" => "'".intval($searchId)."'",
			"KEYWORD_ID" => "'".intval($keywordId)."'",
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"DATE" => "'".$DB->ForSql($arParam["DATE"])."'",
			"POSITION" => "'".intval($arParam["POSITION"])."'",
			"PAGE" => "'".$DB->ForSql($arParam["PAGE"])."'",
		);
		$DB->StartTransaction();
		$ID = $DB->Insert("b_ea_siteposition_position", $arFields, $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($ID);
	}
	
	function Update($posId,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"DATE" => "'".$DB->ForSql($arParam["DATE"])."'",
			"POSITION" => "'".intval($arParam["POSITION"])."'",
			"PAGE" => "'".$DB->ForSql($arParam["PAGE"])."'",
		);
		$DB->StartTransaction();
		$DB->Update("b_ea_siteposition_position", $arFields, "WHERE ID='".intval($posId)."'", $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($posId);
	}
	
	function CheckDate(&$lAdmin, $arDates)
	{
		global $DB, $USER, $APPLICATION;
		
		$DB = CDatabase::GetModuleConnection('elipseart.siteposition');
		
		$ok1 = false;
		list($id1, $date1) = each($arDates);
		if(strlen($date1)>0)
		{
			if(!CheckDateTime($date1))
			{
				if(is_object($lAdmin))
					$lAdmin->AddFilterError(GetMessage("STAT_WRONG_DATE_FROM"));
				else
					$lAdmin.=GetMessage("STAT_WRONG_DATE_FROM")."<br>";
			}
			else
			{
				$ok1 = true;
			}
		}
	
		$ok2 = false;
		list($id2, $date2) = each($arDates);
		if(strlen($date2)>0)
		{
			if(!CheckDateTime($date2))
			{
				if(is_object($lAdmin))
					$lAdmin->AddFilterError(GetMessage("STAT_WRONG_DATE_TILL"));
				else
					$lAdmin.=GetMessage("STAT_WRONG_DATE_TILL")."<br>";
			}
			else
			{
				$ok2 = true;
			}
		}
	
		if($ok1 && $ok2 && $DB->CompareDates($date1, $date2)==1)
		{
			if(is_object($lAdmin))
				$lAdmin->AddFilterError(GetMessage("STAT_FROM_TILL_DATE"));
			else
				$lAdmin.=GetMessage("STAT_FROM_TILL_DATE")."<br>";
		}
	
		return true;
	}
	
	function CheckKey($arSearchSystem=false)
	{
		$module_id = "elipseart.siteposition";
		$valid = false;
		
		if(!$arSearchSystem)
		{
			$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
			while($res = $ssDB->Fetch())
			{
				$arSearchSystem[] = $res;
			}
		}
		
		foreach($arSearchSystem as $ss)
		{	
			if($ss["NAME"] == "YANDEX")
			{
				$login = COption::GetOptionString($module_id, $ss["NAME"]."_LOGIN");
				$key = COption::GetOptionString($module_id, $ss["NAME"]."_KEY");
				if(!empty($login) && !empty($key))
					$valid = true;
			}
			else
			{
				$key = COption::GetOptionString($module_id, $ss["NAME"]."_KEY");
				if(!empty($key))
					$valid = true;
				$key = COption::GetOptionString($module_id, $ss["NAME"]."_KEY_0");
				if(!empty($key))
					$valid = true;
			}
		}
		
		return $valid;
	}
}
?>