<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arFilter = array("IBLOCK_ID"=>$arResult["IBLOCK_ID"], "SECTION_ID"=>$arResult["IBLOCK_SECTION_ID"]);
$arSelect = array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
$arSectionItems = array();
while($ar_fields = $res->GetNext()){
	$item = $ar_fields;
	if($item["ID"] == $arResult["ID"])
		$item["SELECTED"] = "Y";
	else
		$item["SELECTED"] = "N";
	$arSectionItems[] = $item;	
}
if(count($arSectionItems)>1){
	$arResult["SECTION_ITEMS"] = $arSectionItems;
}

//echo "<pre>"; print_r($arResult); echo "</pre>";
?>