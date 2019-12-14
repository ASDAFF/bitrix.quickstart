<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $rsGoProViewedFilter;
$rsGoProViewedFilter = array();
if( is_array($templateData['ITEMS']) && count($templateData['ITEMS'])>0 )
{
	foreach($templateData['ITEMS'] as $arItem)
	{
		$rsGoProViewedFilter['ID'][] = $arItem['ID'];
	}
}

if( (is_array($rsGoProViewedFilter['ID']) && count($rsGoProViewedFilter['ID'])<1) || empty($rsGoProViewedFilter['ID']) )
{
	$rsGoProViewedFilter['ID'] = array( '0' );
}