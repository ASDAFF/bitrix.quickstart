<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	$arResult['ITEMS'] = array();
	
	if($arParams["CATEGORY_ID"]>0 && $arParams["IBLOCK_ID"]>0 && $arParams["COUNT"]>0 && CModule::IncludeModule("iblock")) {
		$arSelect = Array();
		$arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "SECTION_ID" => $arParams["CATEGORY_ID"]);
		$res = CIBlockElement::GetList(Array('sort'=>'asc'), $arFilter, false, Array("nPageSize"=>$arParams["COUNT"]), $arSelect);
		while($ob = $res->GetNextElement(true,false))
		{
		  $arFields = $ob->GetFields();
		  $arResult['ITEMS'][] = $arFields;
		}
	}

?>