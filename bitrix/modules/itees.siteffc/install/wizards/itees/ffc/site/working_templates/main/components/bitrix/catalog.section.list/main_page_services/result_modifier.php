<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//	выбираем элементы раздела
if(count($arResult["SECTIONS"])>0){
	$ServicesIBlockId = $arResult["SECTIONS"][0]["IBLOCK_ID"];
	$arSectionsId = array();
	foreach($arResult["SECTIONS"] as $arSection){
		$arSectionsId[] = $arSection["ID"];
	}
	$arFilter = array("IBLOCK_ID"=>$ServicesIBlockId, "SECTION_ID"=>$arSectionsId);
	$arSelect = array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID", "DETAIL_TEXT");
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
	$arSectionElements = array();
	while($ar_fields = $res->GetNext()){
		$arSectionElements[$ar_fields["IBLOCK_SECTION_ID"]]["ITEMS"][] = $ar_fields;	
	}
	foreach($arResult["SECTIONS"] as $key=>$arSection){
		if(array_key_exists($arSection["ID"], $arSectionElements))
			$arResult["SECTIONS"][$key]["ITEMS"] = $arSectionElements[$arSection["ID"]]["ITEMS"];
	}
}

$ServicesIBlockId = $arResult["SECTIONS"][0]["IBLOCK_ID"];
$arFilter = array("IBLOCK_ID"=>$ServicesIBlockId, "SECTION_ID"=>$arResult["SECTION"]["ID"]);
$arSelect = array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID", "DETAIL_TEXT");
$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
$arSectionElem = array();
while($ar_fields = $res->GetNext()){
	$arSectionElem[] = $ar_fields;	
}
if($arResult["SECTION"]["ID"] !== 0){
	$arResult["SECTION"]["ITEMS"] = $arSectionElem;
}else{
	foreach($arSectionElem as $element){
		$arElem = array();
		$arElem["NAME"] = $element["NAME"];
		$arElem["ID"] = $element["ID"];
		$arElem["SECTION_PAGE_URL"] = "/services/0/".$element["ID"]."/";
		$arResult["SECTIONS"][] = $arElem;
	}
}

//echo "<pre>"; print_r($arResult); echo "</pre>";
?>