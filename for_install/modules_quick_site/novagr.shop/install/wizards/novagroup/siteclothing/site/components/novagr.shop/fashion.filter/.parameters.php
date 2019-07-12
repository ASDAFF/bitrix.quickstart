<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock")) return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalog = array('' => "");
$arTradeOf = array('' => "");

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array(
		'ACTIVE'	=> "Y",
		//'SITE_ID'	=> "s1",
		'TYPE'		=> ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);

$rsTradeOfIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array(
		'ACTIVE'	=> "Y",
		//'SITE_ID'	=> "s1",
		'TYPE'		=> ($arCurrentValues["TRADEOF_IBLOCK_TYPE"] != "-"?$arCurrentValues["TRADEOF_IBLOCK_TYPE"]:""))
);

while($data = $rsCatalogIBlock -> Fetch()) $arCatalog[$data["CODE"]] = $data["NAME"];
while($data = $rsTradeOfIBlock -> Fetch()) $arTradeOf[$data["CODE"]] = $data["NAME"];

$arComponentParameters = array(
	'PARAMETERS' => array(
		
		"CATALOG_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> SITE_ID."/".GetMessage('CATALOG_IBLOCK_TYPE'),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		"CATALOG_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('CATALOG_IBLOCK_CODE'),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalog,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		"ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('ROOT_PATH'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "/catalog/",
		),
		
		"TRADEOF_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('TRADEOF_IBLOCK_TYPE'),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		"TRADEOF_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('TRADEOF_IBLOCK_CODE'),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTradeOf,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"SPECIAL_SORT_ORDER"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('SPECIAL_SORT_ORDER'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "-5",
		),
		"SECTION_SORT_ORDER"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('SECTION_SORT_ORDER'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "-20",
		),
		"PRICE_SORT_ORDER"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('PRICE_SORT_ORDER'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "-10",
		),
		"SHOW_PRICE_SLIDER"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('SHOW_PRICE_SLIDER'),
			"TYPE"		=> "CHECKBOX",
			"VALUE"		=> "",
			"DEFAULT"	=> "Y",
		),
		"SHOW_SECTION"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('SHOW_SECTION'),
			"TYPE"		=> "CHECKBOX",
			"VALUE"		=> "",
			"DEFAULT"	=> "Y",
		),
		"BRAND_ROOT"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('BRAND_ROOT'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "/brand/",
		),
		
		"FASHION_MODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('FASHION_MODE'),
			"TYPE"		=> "CHECKBOX",
			"VALUE"		=> "",
			"DEFAULT"	=> "N",
		),
		"FASHION_ROOT"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('FASHION_ROOT'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "/imageries/",
		),
		
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>