<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( is_array($arResult['GRID']['ROWS']) && count($arResult['GRID']['ROWS'])>0 )
{
	foreach($arResult['GRID']['ROWS'] as $k => $arItem)
	{
		if($arItem['DELAY']=='N' && $arItem['CAN_BUY']=='Y')
		{
			// MEASURE
			if( empty($arItem['MEASURE_RATIO']) && $arItem['MEASURE_RATIO']<1 )
			{
				$arResult['GRID']['ROWS'][$k]['MEASURE_RATIO'] = 1;
			}
			
			// AVALIABLE  QUANTITY
			if( empty($arItem['AVAILABLE_QUANTITY']) )
			{
				$arResult['GRID']['ROWS'][$k]['AVAILABLE_QUANTITY'] = 0;
			}
		}
	}
}