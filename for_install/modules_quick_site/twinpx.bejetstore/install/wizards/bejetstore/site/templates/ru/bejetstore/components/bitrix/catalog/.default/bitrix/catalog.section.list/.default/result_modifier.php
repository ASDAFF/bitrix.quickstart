<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (0 < $arResult['SECTIONS_COUNT'])
{
	foreach ($arResult['SECTIONS'] as $key => $arSection)
	{
	    $arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'],'>LEFT_MARGIN' => $arSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arSection['DEPTH_LEVEL']); // выберет потомков без учета активности
	    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
	    while ($arSect = $rsSect->GetNext()){
			$arSelect1 = Array("ID", "NAME", "IBLOCK_ID");
			$arFilter1 = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "!CATALOG_PRICE_BASE"=>"", "SECTION_ID"=>$arSect['ID']);
			if('Y' == $arParams['HIDE_NOT_AVAILABLE']){
				$arFilter1[">QUANTITY"] = 0;
				$arFilter1["CATALOG_AVAILABLE"] = 'Y';
				$arFilter1[">QUANTITY"] = 0;
			}
			$res = CIBlockElement::GetList(Array(), $arFilter1, false, Array("nPageSize"=>2), $arSelect1);
			$arSect["ELEMENTS_CNT"] = $res->SelectedRowsCount();
			if($arSection["DEPTH_LEVEL"] == 1){
				$arResult['SECTIONS'][$key]["ELEMENTS_CNT"] = $arSect["ELEMENTS_CNT"];
			}
			if($ob = $res->GetNextElement())////есть первый товар
			{
				$arResult['SECTIONS'][$key]["SUBSECTIONS"][] = $arSect;
			}
	    }

	    if('Y' == $arParams['HIDE_NOT_AVAILABLE']){
	    	if($arSection["DEPTH_LEVEL"] == 1 && empty($arResult['SECTIONS'][$key]["SUBSECTIONS"]) && intval($arResult['SECTIONS'][$key]["ELEMENTS_CNT"]) <= 0){
				$arSelect1 = Array("ID", "NAME", "IBLOCK_ID");
				$arFilter1 = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], ">QUANTITY"=>0, "CATALOG_AVAILABLE"=>'Y', "ACTIVE"=>"Y", "!CATALOG_PRICE_BASE"=>"", "SECTION_ID"=>$arSection['ID']);
				$res = CIBlockElement::GetList(Array(), $arFilter1, false, Array("nPageSize"=>2), $arSelect1);
				$ELEMENTS_CNT = $res->SelectedRowsCount();
				if(intval($ELEMENTS_CNT) <= 0){
					unset($arResult['SECTIONS'][$key]);
				}
			}
	    }
	}
	$arResult['SECTIONS'] = array_values($arResult['SECTIONS']);
}
?>