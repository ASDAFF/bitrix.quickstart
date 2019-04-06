<?
class CEASitePositionUpdate
{
	function Update($ID=false,$reupdate=false,$timeValid=false)
	{
		CEASitePositionHost::UpdateSiteHost();
		CEASitePositionYandex::Update($ID,$reupdate,$timeValid);
		CEASitePositionGoogle::Update($ID,$reupdate,$timeValid);
		CEASitePositionBing::Update($ID,$reupdate,$timeValid);
		
		return "CEASitePositionUpdate::Update();";
	}
	
	function UpdateAll($returnScript=false)
	{
		if(!CEASitePosition::CheckKey($arSearchSystem))
			return false;
		
		CEASitePositionHost::UpdateSiteHost();
		CEASitePositionYandex::Update(false,false,"N");
		CEASitePositionGoogle::Update(false,false,"N");
		CEASitePositionBing::Update(false,false,"N");
		
		$i = 0;
		$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
		while($res = $ssDB->Fetch())
		{
			$arSearchSystem[] = $res["NAME"];
		}
		$arFilter = array(
			"ACTIVE" => "Y",
			"SEARCH_NAME" => $arSearchSystem,
		);
		$rsData = CEASitePositionKeyword::GetList(
			array(),
			$arFilter,
			true,
			false,
			false
		);
		while($res = $rsData->Fetch())
		{
			$dateDB = explode(" ",$res["DATE_YANDEX"]);
			$dateDB = $dateDB[0];
			
			if($dateDB != date("Y-m-d"))
			{
				++$i;
			}
		}
		
		if($i > 0 && $returnScript)
		{
			return $returnScript;
		}
	}
}
?>