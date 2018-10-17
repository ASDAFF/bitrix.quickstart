<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arColorSchemes = array(
	'pink'      => GetMessage('KLONDIKE_COLOR_SCHEME_PINK'),
	'yellow'    => GetMessage('KLONDIKE_COLOR_SCHEME_YELLOW'),
	'orange'    => GetMessage('KLONDIKE_COLOR_SCHEME_ORANGE'),
	'siren'     => GetMessage('KLONDIKE_COLOR_SCHEME_SIREN'),
	'green'     => GetMessage('KLONDIKE_COLOR_SCHEME_GREEN'),
	'blue'     => GetMessage('KLONDIKE_COLOR_SCHEME_BLUE'),
);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE_ID"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_L = array();
$arIBlockList = is_array($arCurrentValues['IBLOCK_ID']) ? $arCurrentValues['IBLOCK_ID'] : array($arCurrentValues['IBLOCK_ID']);


foreach ($arIBlockList as $IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
	while ($arr=$rsProp->Fetch())
	{
		$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if(in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
		{
			$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

		if($arr["PROPERTY_TYPE"]=="L" && in_array($arr["CODE"], array("SPECIALOFFER","NEWPRODUCT","SALELEADER")))
		{
			$arProperty_L[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

		if($arr["PROPERTY_TYPE"]=="N")
		{
			$arProperty_N[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

		if($arr["PROPERTY_TYPE"]!="F")
		{
			if($arr["MULTIPLE"] == "Y")
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["PROPERTY_TYPE"] == "L")
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
				$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}
}

if (!empty($arCurrentValues['IBLOCK_ID']))
	$arIBlocks = $arCurrentValues['IBLOCK_ID'];
else
{
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE_ID"], "ACTIVE"=>"Y"));
	while($arr=$rsIBlock->Fetch())
		$arIBlocks = $arr["ID"];
}

if (!empty($arIBlocks)){
	$arProperty_Offers = array();

	if (intval($arIBlocks) > 0)
	{
		$arOffers = CIBlockPriceTools::GetOffersIBlock($arIBlocks);
		$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;

		if($OFFERS_IBLOCK_ID)
		{
			$OFFERS_IBLOCK_ID2 = true;
			$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$OFFERS_IBLOCK_ID));
			while($arr=$rsProp->Fetch())
			{
				if($arr["PROPERTY_TYPE"] != "F")
					$arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			}
		}
	}

}
$arPrice = array();
if(CModule::IncludeModule("catalog"))
{
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}
else
{
	$arPrice = $arProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("IBLOCK_PRICES"),
		),
		"JS_OPTIONS" => array(
			"NAME" => GetMessage("KLONDIKE_GROUP_JS_OPTIONS"),
			"SORT" => 300
		),
		"PLUGIN_OPTIONS" => array(
			"NAME" => GetMessage("KLONDIKE_GROUP_PLUGIN_OPTIONS"),
			"SORT" => 200
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
			"MULTIPLE" => "N",
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"shows" => GetMessage("IBLOCK_SORT_SHOWS"),
				"sort" => GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x" => GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name" => GetMessage("IBLOCK_SORT_NAME"),
				"id" => GetMessage("IBLOCK_SORT_ID"),
				"active_from" => GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to" => GetMessage("IBLOCK_SORT_ACTIVE_TO"),
			),
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc"
		),
		"PREVIEW_TRUNCATE_LEN" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KLONDIKE_PREVIEW_TRUNCATE_LEN"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"INTERVAL" => Array(
			"PARENT" => "PLUGIN_OPTIONS",
			"NAME" => GetMessage("KLONDIKE_INTERVAL"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"COLOR_SCHEME"=> Array(
			"PARENT" => "PLUGIN_OPTIONS",
			"NAME" => GetMessage("KLONDIKE_COLOR_SCHEME"),
			"TYPE" => "LIST",
			"VALUES" => $arColorSchemes,
			"SIZE" => "5",
			"DEFAULT" => "PINK",

		),
		"AUTO_PLAY"=> Array(
			"PARENT" => "PLUGIN_OPTIONS",
			"NAME" => GetMessage("KLONDIKE_AUTO_PLAY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"INCLUDE_JQUERY"=> Array(
			"PARENT" => "JS_OPTIONS",
			"NAME" => GetMessage("KLONDIKE_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"INCLUDE_MODERNIZER"=> Array(
			"PARENT" => "JS_OPTIONS",
			"NAME" => GetMessage("KLONDIKE_INCLUDE_MODERNIZER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ACTION_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_ACTION_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "action"
		),
		"PRODUCT_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_PRODUCT_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "id_top"
		),
		"PRODUCT_QUANTITY_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_QUANTITY_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "quantity",
		),
		"PRODUCT_PROPS_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_PROPS_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "prop",
		),
		"SECTION_ID_VARIABLE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME"		=> GetMessage("IBLOCK_SECTION_ID_VARIABLE"),
			"TYPE"		=> "STRING",
			"DEFAULT"	=> "SECTION_ID"
		),
		"ELEMENT_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "9",
		),
		"FLAG_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_PROPERTY_FLAG"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arProperty_L,
			"ADDITIONAL_VALUES" => "Y",
		),

		"OFFERS_LIMIT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('CP_BCS_OFFERS_LIMIT'),
			"TYPE" => "STRING",
			"DEFAULT" => 5,
		),
		"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("CP_BCT_OFFERS_FIELD_CODE"), "VISUAL"),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"shows" => GetMessage("IBLOCK_FIELD_SHOW_COUNTER"),
				"sort" => GetMessage("IBLOCK_FIELD_SORT"),
				"timestamp_x" => GetMessage("IBLOCK_FIELD_TIMESTAMP_X"),
				"name" => GetMessage("IBLOCK_FIELD_NAME"),
				"id" => GetMessage("IBLOCK_FIELD_ID"),
				"active_from" => GetMessage("IBLOCK_FIELD_ACTIVE_FROM"),
				"active_to" => GetMessage("IBLOCK_FIELD_ACTIVE_TO"),
			),
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"OFFERS_SORT_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCT_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),

		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"USE_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SHOW_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1"
		),
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRODUCT_PROPERTIES" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCT_PRODUCT_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_X,
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCT_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		)
	)
);

if (CModule::IncludeModule('catalog') && CModule::IncludeModule('currency'))
{
	$arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BC_CONVERT_CURRENCY'),
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
			'NAME' => GetMessage('CP_BC_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => $arCurrencyList,
			'DEFAULT' => CCurrency::GetBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}
}

if(!$OFFERS_IBLOCK_ID2)
{
	unset($arComponentParameters["PARAMETERS"]["OFFERS_FIELD_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"]);
}
else
{


	unset($arComponentParameters["PARAMETERS"]["PRODUCT_PROPERTIES"]);
	$arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("CP_BCT_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
	);

}
?>
