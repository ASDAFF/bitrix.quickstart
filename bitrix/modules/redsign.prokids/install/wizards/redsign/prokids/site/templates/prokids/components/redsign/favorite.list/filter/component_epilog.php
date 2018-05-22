<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $rsGoProFavoriteFilter;
$rsGoProFavoriteFilter = array();
if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 )
{
	foreach($arResult['ITEMS'] as $arItem)
	{
		$rsGoProFavoriteFilter['ID'][] = $arItem['ELEMENT_ID'];
	}
}

if( (is_array($rsGoProFavoriteFilter['ID']) && count($rsGoProFavoriteFilter['ID'])<1) || empty($rsGoProFavoriteFilter['ID']) )
{
	$rsGoProFavoriteFilter['ID'] = array( '0' );
}