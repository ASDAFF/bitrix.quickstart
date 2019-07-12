<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (is_array($arResult['VALUE']) && count($arResult['VALUE']) > 0)
{
	if(!CModule::IncludeModule("iblock"))
		return;

	$arValue = array();
 $arSections = Array();
	$dbRes = CIBlockElement::GetList(array('sort' => 'asc', 'name' => 'asc'), array('ID' => $arResult['VALUE']), false, false, Array('ID', 'IBLOCK_SECTION_ID', 'NAME'));
	while ($arRes = $dbRes->Fetch())
	{
		if($arRes["IBLOCK_SECTION_ID"])
  {
   $db_list = CIBlockSection::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('ID' => $arRes["IBLOCK_SECTION_ID"]), false);
   if($arSec = $db_list->Fetch())
    $arRes["NAME"] .= " (".$arSec["NAME"].")";
  }
  
  $arValue[] = $arRes;
	}
	$arResult['VALUE'] = $arValue;
}
?>