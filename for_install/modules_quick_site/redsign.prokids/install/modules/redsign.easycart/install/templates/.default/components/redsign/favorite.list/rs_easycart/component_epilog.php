<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $rsecFavoriteFilterGo;
$rsecFavoriteFilterGo = array();
if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 ){
	foreach($arResult['ITEMS'] as $arItem){
		$rsecFavoriteFilterGo['ID'][] = $arItem['ELEMENT_ID'];
	}
}

if( (is_array($rsecFavoriteFilterGo['ID']) && count($rsecFavoriteFilterGo['ID'])<1) || empty($rsecFavoriteFilterGo['ID']) ){
	$rsecFavoriteFilterGo['ID'] = array( '0' );
}