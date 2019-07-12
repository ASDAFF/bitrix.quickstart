<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$aMenuLinksExt = array();

if(CModule::IncludeModule('iblock'))
{
	$arFilter = array(
		"TYPE" => "looks",
		"SITE_ID" => SITE_ID,
	);

	$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
	$dbIBlock = new CIBlockResult($dbIBlock);

	if ($arIBlock = $dbIBlock->GetNext())
	{

		if($arIBlock["ACTIVE"] == "Y")
		{
			$rsCampaign = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arIBlock["ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y"), false, array("nTopCount" => 1));
			if($arCampaign = $rsCampaign -> Fetch()){
				$aMenuLinks = array(
					Array(
						$arIBlock["NAME"],
						$arIBlock["LIST_PAGE_URL"], 
						Array(), 
						Array(), 
						"" 
					),
				);
			}else{
				$aMenuLinks = array();
			}
		}else{
			$aMenuLinks = array();
		}
		
		if($arIBlock["ACTIVE"] == "Y")
		{
			$rsCampaign = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arIBlock["ID"], "ACTIVE" => "Y"), false, array("nTopCount" => 1));
			if($arCampaign = $rsCampaign -> Fetch()){

			}else{
				$aMenuLinks = array();
			}
		}else{
			$aMenuLinks = array();
		}

		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);
	}else{
		$aMenuLinks = array();
	}
}
?>