<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);

while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[$data["ID"]] = $data["NAME"];

$arComponentParameters = array(
	'PARAMETERS' => array(
		
		"PRODUCT_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("T_IBLOCK_IB"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH"	=> "N",
		),
			
		"COUNT_ELEMENTS" => array(
				"NAME" => GetMessage("COUNT_ELEMENTS"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT" => "BASE",
		),
		"CACHE_FOR_REQUEST_URI" => array(
			"NAME" => GetMessage("CACHE_FOR_REQUEST_URI"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>