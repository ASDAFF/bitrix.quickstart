<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;

if (
    !Loader::includeModule('iblock') ||
    !Loader::includeModule('catalog') ||
    !Loader::includeModule('currency')
) {
    return;
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIblocks = array();
$res = CIBlock::GetList(array('SORT'=>'ASC'), array('ACTIVE'=>'Y'));
while($arIblock = $res->Fetch())
{
	$arIBlocks[$arIblock['ID']] = $arIblock['NAME'];
}

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arPrice = array();
$arPrice2 = array();
$rsPrice = CCatalogGroup::GetList($v1='sort', $v2='asc');
while($arr = $rsPrice->Fetch())
{
	$arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];
	$arPrice2[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME_LANG'];
}

$arProperties = array();
$arProperties[] = '--';
$resProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('ACTIVE'=>'Y', 'IBLOCK_ID'=>$arCurrentValues['IBLOCK_ID']));
while ($arProp = $resProp->GetNext())
{
	if($arProp['PROPERTY_TYPE']=='L' || $arProp['PROPERTY_TYPE']=='S')
		$arProperties[$arProp['CODE']] = '['.$arProp['CODE'].'] '.$arProp['NAME'];
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues['IBLOCK_ID']);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers['OFFERS_IBLOCK_ID']: 0;
$arProperty_Offers = array();
if(0 < $OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array('sort'=>'asc', 'name'=>'asc'), Array('IBLOCK_ID'=>$OFFERS_IBLOCK_ID, 'ACTIVE'=>'Y'));
	while($arr=$rsProp->Fetch())
	{
		if($arr['PROPERTY_TYPE'] != 'F')
			$arProperty_Offers[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	// more photo
	'ADDITIONAL_PICT_PROP' => array(
		'NAME' => GetMessage('MSG_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'MORE_PHOTO',
	),
	'OFFER_ADDITIONAL_PICT_PROP' => array(
		'NAME' => GetMessage('MSG_OFFER_ADDITIONAL_PICT_PROP'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'SKU_MORE_PHOTO',
	),
	'PROPERTY_CODE_ELEMENT_IN_MENU' => array(
		'NAME' => GetMessage('RS_SLINE.PROPERTY_CODE_ELEMENT_IN_MENU'),
		'TYPE' => 'LIST',
		'VALUES' => $arProperties,
	),

    "IBLOCK_TYPE" => array(
        "NAME" => GetMessage("RS_SLINE.IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
    ),
	'IBLOCK_ID' => array(
		'NAME' => GetMessage('RS_SLINE.IBLOCK_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlocks,
		'REFRESH' => 'Y',
	),
	'PRICE_CODE' => array(
		'NAME' => GetMessage('RS_SLINE.PRICE_CODE'),
		'TYPE' => 'LIST',
		'VALUES' => $arPrice,
	),
	'SKU_PRICE_SORT_ID' => array(
		'NAME' => GetMessage('RS_SLINE.PRICE_SORT_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arPrice2,
	),
	'PRICE_VAT_INCLUDE' => array(
		'NAME' => GetMessage('RS_SLINE.PRICE_VAT_INCLUDE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
	'CONVERT_CURRENCY' => array(
		'NAME' => GetMessage('RS_SLINE.CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
	'COUNT_ELEMENTS' => array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('CP_BCSL_COUNT_ELEMENTS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'USE_PRODUCT_QUANTITY' => array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BCS_USE_PRODUCT_QUANTITY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	)
);

if(isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY']){
	$arCurrencyList = array();
	$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
	while($arCurrency = $rsCurrencies->Fetch()){
		$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
	}
	$arTemplateParameters['CURRENCY_ID'] = array(
		'NAME' => GetMessage('RS_SLINE.CURRENCY_ID'),
		'TYPE' => 'LIST',
		'VALUES' => $arCurrencyList,
		'DEFAULT' => CCurrency::GetBaseCurrency(),
		'ADDITIONAL_VALUES' => 'Y',
	);
}
