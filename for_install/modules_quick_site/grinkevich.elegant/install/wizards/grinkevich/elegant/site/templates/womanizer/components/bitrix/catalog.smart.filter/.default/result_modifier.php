<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach($arResult["ITEMS"] as $key => $arItem){
	if($arItem["CODE"] == "COLOR" && !empty($arItem["VALUES"])){
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"], "CODE" => $arItem["CODE"]));
		while ($prop_fields = $properties->GetNext()){
			$IBLOCK_ID = $prop_fields["LINK_IBLOCK_ID"];
		}
		
		foreach($arItem["VALUES"] as $val => $ar){
			$arSelect = Array("ID", "PROPERTY_COLORCODE");
			$arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "NAME"=> $ar["VALUE"]);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			if($ob = $res->GetNextElement()){
				$arFields = $ob->GetFields();
				$arResult["ITEMS"][$key]["VALUES"][$val]["COLORCODE"] = $arFields["PROPERTY_COLORCODE_VALUE"];
			}
		}
	}
}?>
