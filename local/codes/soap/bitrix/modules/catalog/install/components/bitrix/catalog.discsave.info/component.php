<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;
global $USER;

$arParams['SITE_ID'] = strval($arParams['SITE_ID']);
if (0 >= strlen($arParams['SITE_ID']))
	$arParams['SITE_ID'] = SITE_ID;

$arParams['USER_ID'] = intval($arParams['USER_ID']);
if (0 >= $arParams['USER_ID'])
	$arParams['USER_ID'] = intval($USER->GetID());

if (0 >= $arParams['USER_ID'])
	return;

$arParams['SHOW_NEXT_LEVEL'] = (isset($arParams['SHOW_NEXT_LEVEL']) && 'Y' == $arParams['SHOW_NEXT_LEVEL'] ? 'Y' : 'N');

if (!CModule::IncludeModule('catalog'))
	return;

if (!CBXFeatures::IsFeatureEnabled('CatDiscountSave'))
{
	CCatalogDiscountSave::Disable();
	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));
	return;
}
$arFields = array(
	'USER_ID' => $arParams['USER_ID'],
	'SITE_ID' => $arParams['SITE_ID'],
);

$arResult = CCatalogDiscountSave::GetDiscount($arFields);
if (!empty($arResult))
{
	foreach ($arResult as $key => $arDiscountSave)
	{
		if ('Y' == $arParams['SHOW_NEXT_LEVEL'])
		{
			$rsRanges = CCatalogDiscountSave::GetRangeByDiscount(array('RANGE_FROM' => 'ASC'), array('DISCOUNT_ID' => $arDiscountSave['ID'], '>RANGE_FROM' => $arDiscountSave['RANGE_FROM'], false, array('nTopCount' => 1)));
			if ($arRange = $rsRanges->Fetch())
			{
				$arTempo = array(
					'RANGE_FROM' => $arRange['RANGE_FROM'],
					'VALUE' => $arRange['VALUE'],
					'VALUE_TYPE' => $arRange['TYPE']
				);
				$arDiscountSave['NEXT_LEVEL'] = $arTempo;
			}
		}
		$arDiscountSave['~NAME'] = $arDiscountSave['NAME'];
		$arDiscountSave['NAME'] = htmlspecialcharsex($arDiscountSave['NAME']);
		$arResult[$key] = $arDiscountSave;
	}
}

$this->IncludeComponentTemplate();
?>