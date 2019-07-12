<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $rsecViewedFilterGo;
$rsecViewedFilterGo = array();
if( is_array($templateData['ITEMS']) && count($templateData['ITEMS'])>0 ){
	foreach($templateData['ITEMS'] as $arItem){
		$rsecViewedFilterGo['ID'][] = $arItem['ID'];
	}
}

if( (is_array($rsecViewedFilterGo['ID']) && count($rsecViewedFilterGo['ID'])<1) || empty($rsecViewedFilterGo['ID']) ){
	$rsecViewedFilterGo['ID'] = array( '0' );
}