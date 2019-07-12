<?
$dop_arr = array();
$db_dop = CIBlockElement::GetList(false, array('ACTIVITY'=>'Y', 'IBLOCK_CODE'=>'dop'), false, false, array('ID', 'NAME', 'CODE', 'IBLOCK_ID', 'PROPERTY_COST'));
while($res_dop = $db_dop->GetNext()){
	$dop_arr[$res_dop['CODE']] = $res_dop['PROPERTY_COST_VALUE'];
}
$arResult['DOP_COST'] = $dop_arr;

?>