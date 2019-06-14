<?
class CEASitePositionSearchSystem
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
						$key == "NAME" ||
						$key == "ACTIVE"
					)
					{
						if($i > 0)
							$arrFilter .= " AND ";
						
						$arrFilter .= "ts.".$key." = '".$val."' ";
						
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
						
						$arrSort .= "ts.".$key." ".$val." ";
						
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
				b_ea_siteposition_search_system AS ts
				
			".(($arrFilter) ? $arrFilter : "")."
			
			".(($arrSort) ? $arrSort : "ORDER BY ts.ID ASC")."
			
			".(($limit) ? " LIMIT ".$limit : "")."
			
		";
		$res = $DB->Query($strSql);
		
		return $res;
	}
	
	function Add($arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",			
		);
		$DB->StartTransaction();
		$ID = $DB->Insert("b_ea_siteposition_search_system", $arFields, $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($ID);
	}
	
	function Update($id,$arParam)
	{
		global $DB, $USER, $APPLICATION;
		
		$arFields = array(
			"ACTIVE" => "'".$DB->ForSql($arParam["ACTIVE"],1)."'",
			"NAME" => "'".$DB->ForSql($arParam["NAME"])."'",			
		);
		$DB->StartTransaction();
		$DB->Update("b_ea_siteposition_search_system", $arFields, "WHERE ID='".intval($id)."'", $err_mess.__LINE__);
		$DB->Commit();
		
		return intval($id);
	}
	
	function Delete($id)
	{
		
	}
	
	function UpdateSearchSystem()
	{
		$arSearchSystem[] = "YANDEX";
		$arSearchSystem[] = "GOOGLE";
		$arSearchSystem[] = "BING";
		
		if(count($arSearchSystem) > 0)
		{
			foreach($arSearchSystem as $val)
			{
				$arParam = array(
					"ACTIVE" => "Y",
					"NAME" => $val,
				);
				$ssDB = CEASitePositionSearchSystem::GetList(array(),array("NAME"=>$val),1);
				$res = $ssDB->Fetch();
				if($res["ID"] > 0)
				{
					$SEARCH_ID = CEASitePositionSearchSystem::Update($res["ID"],$arParam);
				}
				else
				{
					$SEARCH_ID = CEASitePositionSearchSystem::Add($arParam);
				}
			}
		}
	}
}
?>