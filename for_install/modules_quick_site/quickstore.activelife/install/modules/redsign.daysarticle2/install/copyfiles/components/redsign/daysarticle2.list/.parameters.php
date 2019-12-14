<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;

if(!CModule::IncludeModule('catalog'))
	return;

if(!CModule::IncludeModule('currency'))
	return;

$arIBlocks = array();
$arIBlocks['-'] = GetMessage('IBLOCK_ID_EMPTY');
$res = CIBlock::GetList(array(), array(), true);
while($ar_res = $res->Fetch())
{
	$arIBlocks[$ar_res['ID']] = '['.$ar_res['CODE'].'] '.$ar_res['NAME'];
}

$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while($arr = $rsPrice->Fetch())
	$arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];

$arCurrencyList = array();
$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
while ($arCurrency = $rsCurrencies->Fetch())
{
	$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
}

$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
if (IntVal($arCurrentValues['IBLOCK_ID'])>0)
{
	$rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$arCurrentValues['IBLOCK_ID'], 'ACTIVE'=>'Y'));
	while ($arr=$rsProp->Fetch())
	{
		if($arr['PROPERTY_TYPE'] != 'F')
			$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];

		if($arr['PROPERTY_TYPE']=='N')
			$arProperty_N[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];

		if($arr['PROPERTY_TYPE']!='F')
		{
			if($arr['MULTIPLE'] == 'Y')
				$arProperty_X[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			elseif($arr['PROPERTY_TYPE'] == 'L')
				$arProperty_X[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
			elseif($arr['PROPERTY_TYPE'] == 'E' && $arr['LINK_IBLOCK_ID'] > 0)
				$arProperty_X[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
		}
	}
}

$arrChose1 = array(
	'-' => '-',
	'text' => GetMessage('DA_CHOSE1_TEXT'),
	'prop' => GetMessage('DA_CHOSE1_PROP'),
);

// offers
$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues['IBLOCK_ID']);
$OFFERS_IBLOCK_ID = is_array($arOffers) ? $arOffers['OFFERS_IBLOCK_ID'] : 0;
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
	while($arr=$rsProp->Fetch()){
		if($arr['PROPERTY_TYPE'] != 'F')
			$arProperty_Offers[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}
$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);
$arAscDesc = array(
	'asc' => GetMessage('IBLOCK_SORT_ASC'),
	'desc' => GetMessage('IBLOCK_SORT_DESC'),
);
// /offers

$arComponentParameters = array(
	'PARAMETERS' => array(
		'IBLOCK_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('IBLOCK_ID'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'VALUES' => $arIBlocks,
			'REFRESH' => 'Y',
		),
		'PRICE_CODE' => array(
			'PARENT' => 'BASE',
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('PRICE_CODE'),
			'TYPE' => 'LIST',
			'VALUES' => $arPrice,
		),
		// offers
		'OFFERS_FIELD_CODE' => CIBlockParameters::GetFieldCode(GetMessage('CP_BCS_OFFERS_FIELD_CODE')),
		'OFFERS_PROPERTY_CODE' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_PROPERTY_CODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $arProperty_Offers,
			'ADDITIONAL_VALUES' => 'Y',
		),
		'OFFERS_SORT_FIELD' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_SORT_FIELD'),
			'TYPE' => 'LIST',
			'VALUES' => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'sort',
		),
		'OFFERS_SORT_ORDER' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_SORT_ORDER'),
			'TYPE' => 'LIST',
			'VALUES' => $arAscDesc,
			'DEFAULT' => 'asc',
			'ADDITIONAL_VALUES' => 'Y',
		),
		'OFFERS_SORT_FIELD2' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_SORT_FIELD2'),
			'TYPE' => 'LIST',
			'VALUES' => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => 'id',
		),
		'OFFERS_SORT_ORDER2' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_SORT_ORDER2'),
			'TYPE' => 'LIST',
			'VALUES' => $arAscDesc,
			'DEFAULT' => 'desc',
			'ADDITIONAL_VALUES' => 'Y',
		),
		'OFFERS_LIMIT' => array(
			'NAME' => GetMessage('CP_BCS_OFFERS_LIMIT'),
			'TYPE' => 'STRING',
			'DEFAULT' => 5,
		),
		// /offers
		'PRICE_VAT_INCLUDE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('PRICE_VAT_INCLUDE'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
		),
		'SHOW_TYPE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('SHOW_TYPE'),
			'TYPE' => 'CHECKBOX',
			'VALUE' => 'Y',
		),
		'TEXT_OR_PROP' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('TEXT_OR_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'VALUES' => $arrChose1,
			'REFRESH' => 'N',
		),
		'PROPERTY_CODE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('PROPERTY_CODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $arProperty,
		),
		'MAX_WIDTH' => array(
			'NAME' => GetMessage('MAX_WIDTH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '180',
		),
		'MAX_HEIGHT' => array(
			'NAME' => GetMessage('MAX_HEIGHT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '150',
		),
		'CACHE_TIME'  =>  Array(
			'PARENT' => 'CACHE_SETTINGS',
			'DEFAULT' => 3600
		),
	),
);

if (CModule::IncludeModule('currency'))
{
	$arComponentParameters['PARAMETERS']['CONVERT_CURRENCY'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BCS_CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	if (isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'])
	{
		$arCurrencyList = array();
		$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
		while ($arCurrency = $rsCurrencies->Fetch())
		{
			$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
		}
		$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('CP_BCS_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arCurrencyList,
			'DEFAULT' => CCurrency::GetBaseCurrency(),
			'ADDITIONAL_VALUES' => 'Y',
		);
	}
}