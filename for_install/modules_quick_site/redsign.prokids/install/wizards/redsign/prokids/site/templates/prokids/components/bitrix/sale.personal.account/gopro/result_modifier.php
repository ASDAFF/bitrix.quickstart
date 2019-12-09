<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('catalog'))
	return;

if( is_array($arResult['ACCOUNT_LIST']) && count($arResult['ACCOUNT_LIST'])>0 )
{
	foreach($arResult['ACCOUNT_LIST'] as $key => $arVal)
	{
		$arResult['ACCOUNT_LIST'][$key]['FORMAT_VALUE'] = FormatCurrency($arVal['ACCOUNT_LIST']['CURRENT_BUDGET'], $arVal['ACCOUNT_LIST']['CURRENCY']);
		$arResult['ACCOUNT_LIST'][$key]['FORMAT_NAME'] = $arVal['CURRENCY']['CURRENCY'].' ('.$arVal['CURRENCY']['FULL_NAME'].')';
	}
}