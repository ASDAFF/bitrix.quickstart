<?
 $arElements = Array();
 $arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
 $arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
 $rsElements = CIBlockElement::GetList(Array("sort"=>"asc", "name"=>"asc"), $arFilter, false, false, $arSelect);
 $rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
 while($arElement = $rsElements->GetNext())
  $arElements[$arElement["IBLOCK_SECTION_ID"]][] = $arElement;
 
 foreach($arResult["SECTIONS"] as $index=>$arSection)
  $arResult["SECTIONS"][$index]["ITEMS"] = $arElements[$arSection["ID"]];
?>