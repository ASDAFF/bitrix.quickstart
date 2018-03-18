<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	
$arFilter = Array(
	"IBLOCK_ID" => $arParams['IBLOCK_ID'], 
	"ACTIVE_DATE" => "Y", 
	"ACTIVE" => "Y"
);
foreach ($arResult["ELEMENTS_LIST_JS"] as $key => &$item){	
	$arFilter['ID']=$item['id'];
	$dbElement = CIBlockElement::GetList(Array(), $arFilter, 
		false, Array("nTopCount"=>1), Array("ID", "NAME", "PREVIEW_PICTURE"));
	while($arElement = $dbElement->GetNext()){	
		$rsFile = CFile::GetByID($arElement["PREVIEW_PICTURE"]);
		$item['DETAIL_PICTURE'] = $rsFile->Fetch();
		$item['DETAIL_PICTURE']['SRC']= CFile::GetPath($item['PREVIEW_PICTURE']['ID']);					  
	}	
$item['DETAIL_PICTURE']['SRC'] = $item["src"];

}

?>