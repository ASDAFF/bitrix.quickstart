<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);

while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[ $data['ID'] ] = $data['NAME'];


$arComponentParameters = array(
	'PARAMETERS' => array(
		"PAGE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("SEARCH_REQUESTS_PAGE"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"LIMIT"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("SEARCH_REQUESTS_LIMIT"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
			"MULTIPLE" => "Y"	
		),
		
		"ROOT_SEARCH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage('ROOT_PATH'),
			"TYPE"		=> "TEXT",
			"VALUE"		=> "",
			"DEFAULT"	=> "/catalog/",
		),
		
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	)
);
?>