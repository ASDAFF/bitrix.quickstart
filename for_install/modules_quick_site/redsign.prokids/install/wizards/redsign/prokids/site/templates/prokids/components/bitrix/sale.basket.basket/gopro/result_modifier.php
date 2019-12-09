<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

global $arrAccFilter;

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH' => 40, 'MAX_HEIGHT' => 40));
// /get no photo

$arResult['HAVE_PRODUCT_TYPE'] = array(
	'ITEMS' => false,
	'DELAYED' => false,
	'NOT_AVAILABLE' => false,
	'SUBSCRIBED' => false,
);
foreach($arResult['GRID']['ROWS'] as $k => $arItem)
{
	if($arItem['DELAY']=='N' && $arItem['CAN_BUY']=='Y')
	{
		$arResult['HAVE_PRODUCT_TYPE']['ITEMS'] = true;
	}
	
	if($arItem['DELAY']=='Y' && $arItem['CAN_BUY']=='Y')
	{
		$arResult['HAVE_PRODUCT_TYPE']['DELAYED'] = true;
	}
	
	if(isset($arItem['NOT_AVAILABLE']) && $arItem['NOT_AVAILABLE']==true)
	{
		$arResult['HAVE_PRODUCT_TYPE']['NOT_AVAILABLE'] = true;
	}
	
	if ($arItem['CAN_BUY']=='N' && $arItem['SUBSCRIBE']=='Y')
	{
		$arResult['HAVE_PRODUCT_TYPE']['SUBSCRIBED'] = true;
	}
}