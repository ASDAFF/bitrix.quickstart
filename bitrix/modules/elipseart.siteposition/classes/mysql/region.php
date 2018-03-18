<?
class CEASitePositionRegion
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
						$key == "CODE"
					)
					{
						if($i > 0)
							$arrFilter .= " AND ";
						
						$arrFilter .= "tr.".$key." = '".$val."' ";
						
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
							$key == "CODE"
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
						
						$arrSort .= "tr.".$key." ".$val." ";
						
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
				b_ea_siteposition_region AS tr
				
			".(($arrFilter) ? $arrFilter : "")."
			
			".(($arrSort) ? $arrSort : "ORDER BY tr.ID DESC")."
			
			".(($limit) ? " LIMIT ".$limit : "")."
			
		";
		$res = $DB->Query($strSql);
		
		return $res;
	}
	
	function Update()
	{
		global $DB, $DBType, $APPLICATION;
		
		require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/elipseart.siteposition/classes/".$DBType."/region_list.php");
		
		foreach($region_list as $key=>$val)
		{
			$regionDB = CEASitePositionRegion::GetList(array(),array("CODE" => $key),1);
			$resReg = $regionDB->Fetch();
			if($resReg["ID"] > 0)
			{
				$arFields = array(
					//"NAME" => "'".$DB->ForSql($val)."'",
					"CODE" => "'".$DB->ForSql($key)."'"
				);
				$DB->StartTransaction();
				$DB->Update("b_ea_siteposition_region", $arFields, "WHERE ID='".$resReg["ID"]."'", $err_mess.__LINE__);
				$DB->Commit();
			}
			else
			{
				$arFields = array(
					//"NAME" => "'".$DB->ForSql($val)."'",
					"CODE" => "'".$DB->ForSql($key)."'"
				);
				$DB->StartTransaction();
				$ID = $DB->Insert("b_ea_siteposition_region", $arFields, $err_mess.__LINE__);
				$DB->Commit();
			}
		}
	}
}
?>