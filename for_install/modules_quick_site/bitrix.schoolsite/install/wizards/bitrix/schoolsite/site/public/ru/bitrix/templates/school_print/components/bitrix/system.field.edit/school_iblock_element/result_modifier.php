<?
 if(intVal($arParams["arUserField"]["SETTINGS"]["IBLOCK_ID"]) && CModule::IncludeModule("iblock"))
 {
  $rsSec = CIBlockSection::GetList(Array("left_margin"=>"asc"), Array("IBLOCK_ID"=>intVal($arParams["arUserField"]["SETTINGS"]["IBLOCK_ID"]), "GLOBAL_ACTIVE"=>"Y"), false);
  while($arSec = $rsSec->Fetch())
  {
   $arSec["ITEMS"] = Array();
   $arParams["arUserField"]["USER_TYPE"]["FIELDS"][$arSec["ID"]] = $arSec;
  }
  
  $rsElem = CIBlockElement::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array("IBLOCK_ID"=>intVal($arParams["arUserField"]["SETTINGS"]["IBLOCK_ID"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"), false, false, Array("ID", "NAME", "IBLOCK_SECTION_ID"));
  while($arElem = $rsElem->Fetch())
  {
   if(array_key_exists($arElem["IBLOCK_SECTION_ID"], $arParams["arUserField"]["USER_TYPE"]["FIELDS"]))
    $arParams["arUserField"]["USER_TYPE"]["FIELDS"][$arElem["IBLOCK_SECTION_ID"]]["ITEMS"][$arElem["ID"]] = $arElem["NAME"];
  }
 }
?>