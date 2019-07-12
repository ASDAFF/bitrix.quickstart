<?
$list = CIBlockElement::GetList(Array("SORT"=>"ASC","DATE_ACTIVE_FROM"=>"DESC","ID"=>"DESC"),Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'],"ACTIVE"=>"Y","ACTIVE_DATE"=>"Y"));
while($el = $list->GetNext()){
	if($el['ID'] == $arResult['ID']){
		$arResult['PREV'] = $prev;
		$arResult['NEXT'] = $list->GetNext();
	}
	$prev = $el;
}
?>