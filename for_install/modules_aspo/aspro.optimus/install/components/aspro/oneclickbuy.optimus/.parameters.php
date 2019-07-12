<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("currency"))
{
	$arIBlocks=Array();
	if ($arCurrentValues["IBLOCK_TYPE"]!="-") 
	{
		$res = CIBlock::GetList( Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => $arCurrentValues["IBLOCK_TYPE"]) );
		while ($arRes = $res->Fetch()) {$arIBlocks[$arRes["ID"]] = '[' . $arRes["ID"] . ']' . $arRes["NAME"];}
	}
	
	$arOrderFields = array( "FIO" => GetMessage('FIELD_OPTION_USER_NAME'), "PHONE" => GetMessage('FIELD_OPTION_PHONE'), "EMAIL" => GetMessage('FIELD_OPTION_EMAIL'), );
	
	$arPersonTypes = array();
	$res = CSalePersonType::GetList( array('ID'=>'ASC'), array('LID'=>$_REQUEST["site"]));
	while ($arRes = $res->Fetch())	{ $arPersonTypes[$arRes['ID']] = '[' . $arRes['ID'] . '] ' . $arRes['NAME'];}

	$arDeliveries = array('0' => GetMessage('NOT_SET'));
	$res = CSaleDelivery::GetList( array('SORT' => 'ASC'), array('ACTIVE' => 'Y', 'LID'=>$_REQUEST["site"]), false, false, array('ID', 'NAME'));
	while ($arRes = $res->Fetch())	{ $arDeliveries[$arRes["ID"]] = '[' . $arRes["ID"] . ']' . $arRes["NAME"]; }

	$arPayments = array('0' => GetMessage('NOT_SET'));
	$res = CSalePaySystem::GetList(array('SORT' => 'ASC'),array('ACTIVE' => 'Y', 'LID'=>$_REQUEST["site"]),false, false,array('ID', 'NAME'));
	while ($arRes = $res->Fetch())	{ $arPayments[$arRes["ID"]] = '[' . $arRes["ID"] . ']' . $arRes["NAME"];}

	$arCurrencies = array();
	$res = CCurrency::GetList(($b = 'name'), ($o = 'asc'));
	while ($arRes = $res->Fetch())	{ $arCurrencies[$arRes['CURRENCY']] = '[' . $arRes['CURRENCY'] . '] ' . $arRes['FULL_NAME'];}	
	$default_currency = COption::GetOptionString('sale', 'default_currency', 'RUB');	

	$arPrices = array();
	$res = CCatalogGroup::GetList(array('SORT'=>'ASC'));
	while ($arRes = $res->Fetch())	{ $arPrices[$arRes['ID']] = '[' . $arRes['ID'] . '] ' . $arRes['NAME'] . ($arRes['BASE']=='Y'? GetMessage('BASE_PRICE') : '');}
		
	$arComponentParameters = array(
		"GROUPS" => array(
			"SKU_PROPERTIES" => array( "NAME" => GetMessage("GROUP_SKU_PROPERTIES"), ),
		),
		"PARAMETERS" => array(
			"IBLOCK_TYPE" => Array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_IBLOCK_TYPE"),
				"TYPE" => "LIST",
				"VALUES" => CIBlockParameters::GetIBlockTypes(),
				"DEFAULT" => "",
				"REFRESH" => "Y",
			),
			"IBLOCK_ID" => Array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_IBLOCK_ID"),
				"TYPE" => "LIST",
				"VALUES" => $arIBlocks,
				"DEFAULT" => '',
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "Y",
			),
			"ELEMENT_ID" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_ELEMENT_ID"),
				"TYPE" => "STRING",
				"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
			),
			"USE_QUANTITY" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_USE_QUANTITY"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
			),
			"SEF_FOLDER" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_SEF_FOLDER"),
				"TYPE" => "STRING",
				"DEFAULT" => '/catalog/',
			),
			"PROPERTIES" => Array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_PROPERTIES"),
				"TYPE" => "LIST",
				"VALUES" => $arOrderFields,
				"DEFAULT" => array('FIO', 'PHONE', 'EMAIL'),
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "N",
				"MULTIPLE" => "Y",
			),
			"REQUIRED" => Array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_REQUIRED"),
				"TYPE" => "LIST",
				"VALUES" => $arOrderFields,
				"DEFAULT" => array('FIO', 'PHONE'),
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "N",
				"MULTIPLE" => "Y",
			),
			"DEFAULT_PERSON_TYPE" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_DEFAULT_PERSON_TYPE"),
				"TYPE" => "LIST",
				"VALUES" => $arPersonTypes,
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "N",
				"MULTIPLE" => "N",
				"DEFAULT" => 1,
			),
			"DEFAULT_DELIVERY" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_DEFAULT_DELIVERY"),
				"TYPE" => "LIST",
				"VALUES" => $arDeliveries,
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "N",
				"MULTIPLE" => "N",
			),
			"DEFAULT_PAYMENT" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_DEFAULT_PAYMENT"),
				"TYPE" => "LIST",
				"VALUES" => $arPayments,
				"ADDITIONAL_VALUES" => "N",
				"REFRESH" => "N",
				"MULTIPLE" => "N",
			),
			"DEFAULT_CURRENCY" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_DEFAULT_CURRENCY"),
				"TYPE" => "LIST",
				"VALUES" => $arCurrencies,
				"ADDITIONAL_VALUES" => "N",
				"DEFAULT" => $default_currency,
				"REFRESH" => "N",
				"MULTIPLE" => "N",
			),
			"PRICE_ID" => array(
				"PARENT" => "BASE",
				"NAME" => GetMessage("PARAMETER_PRICE_ID"),
				"TYPE" => "LIST",
				"VALUES" => $arPrices,
				"DEFAULT" => "",
			),
			"USE_SKU" => array(
				"PARENT" => "SKU_PROPERTIES",
				"NAME" => GetMessage("PARAMETER_USE_SKU"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"REFRESH" => "Y",
				"SORT" => 10,
			),
			"CACHE_TIME" => array(
				"DEFAULT" => 36000
			),
		),
	);

	if ($arCurrentValues['USE_SKU'] == 'Y') 
	{
		$arSKUProps = array();
		$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
		if (is_array($arOffersIBlock)) 
		{
			$res = CIBlockProperty::GetList( array('SORT'=>'ASC', 'NAME'=>'ASC'), array('ACTIVE'=>'Y', 'IBLOCK_ID'=>$arOffersIBlock['OFFERS_IBLOCK_ID']) );
			while ($arRes = $res->Fetch()) { if ($arRes['CODE'] != 'CML2_LINK' || $arRes["PROPERTY_TYPE"] != "F") {$arSKUProps[$arRes['CODE']] = '[' . $arRes['CODE'] . ']' . $arRes['NAME'];} }
					
			$arComponentParameters["PARAMETERS"]["SKU_PROPERTIES_CODES"] = array(
				"PARENT" => "SKU_PROPERTIES",
				"NAME" => GetMessage("PARAMETER_SKU_PROPERTIES_CODES"),
				"TYPE" => "LIST",
				"VALUES" => $arSKUProps,
				"MULTIPLE" => "Y",
				"SORT" => 20,
			);
			$arComponentParameters["PARAMETERS"]["SKU_COUNT"] = array(
				"PARENT" => "SKU_PROPERTIES",
				"NAME" => GetMessage("PARAMETER_SKU_COUNT"),
				"DEFAULT" => '10',
				"SORT" => 30,
			);
		}
	}

}
?>
