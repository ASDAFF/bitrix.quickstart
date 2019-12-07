<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if(!empty($arResult["ITEMS"]))
{
	$arSections = array();
	$rsSections = CIBlockSection::GetList(array(), array("ACTIVE" => "Y","GLOBAL_ACTIVE" => "Y","IBLOCK_ID" => $arParams["IBLOCK_ID"],"CNT_ACTIVE" => "Y"), false,array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL" ));
	while($ar_result = $rsSections->GetNext())
		$arSections[$ar_result["ID"]] = $ar_result;

	foreach($arResult["ITEMS"] as &$arItem)
		$arItem["IBLOCK_SECTION"] = $arSections[$arItem["IBLOCK_SECTION_ID"]];
}
?>