<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$aMenuLinksExt = array();
if(CModule::IncludeModule('iblock'))
{
	$arFilter = array(
		"TYPE" => "brands",
		"SITE_ID" => SITE_ID,
	);

	$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
	$dbIBlock = new CIBlockResult($dbIBlock);

	if ($arIBlock = $dbIBlock->GetNext())
	{
		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

		if($arIBlock["ACTIVE"] == "Y")
		{
			$rsBrand = CIBlockElement::GetList(array("name" => "asc"), array("IBLOCK_ID" => $arIBlock["ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y"), false, false, array("ID", "NAME", "CODE"));
			$itemIndex = 0;
			while($arBrand = $rsBrand -> Fetch()){
		        $aMenuLinksExt[] = array(
			        "0" => htmlspecialcharsbx($arBrand["NAME"]),
	            	"1" => SITE_DIR."brand/".$arBrand["CODE"]."/",
	            	"2" => array(
	                    "0" => SITE_DIR."brand/".$arBrand["CODE"]."/"
	                ),
	            	"3" => array(
	                    "FROM_IBLOCK" => true,
	                    "IS_PARENT" => false,
	                    "DEPTH_LEVEL" => "1"
	                )
	            );

		        $itemIndex++;
			}
		}
	}
	if(defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");
}
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>