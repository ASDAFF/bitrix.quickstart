<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $rsecCompareFilterGo;
$rsecCompareFilterGo = array();
if( is_array($arResult) && count($arResult)>0 ){
	foreach($arResult as $arItem){
		$rsecCompareFilterGo['ID'][] = $arItem['ID'];
	}
}

if( (is_array($rsecCompareFilterGo['ID']) && count($rsecCompareFilterGo['ID'])<1) || empty($rsecCompareFilterGo['ID']) ){
	$rsecCompareFilterGo['ID'] = array( '0' );
}