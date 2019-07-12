<?
if(sizeof($arResult["SECTIONS"]) <= 1){
	$current = $arResult['SECTION'];
	$list = CIBlockSection::GetList(Array("NAME"=>"ASC"),Array("IBLOCK_ID"=>$current['IBLOCK_ID'],"SECTION_ID"=>$current['IBLOCK_SECTION_ID']),$arParams["COUNT_ELEMENTS"]);
	while($s = $list->GetNext()){
		if($s['ID']==$current['ID']) $s['OPENED'] = true;
		$arResult["SECTIONS"][] = $s;
	}
}


?>