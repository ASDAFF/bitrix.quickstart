<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);

while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[$data["CODE"]] = $data["NAME"];

$arComponentParameters = array(
	'PARAMETERS' => array(
		"SORT_FIELD"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_SORT_FIELD"),
			"TYPE"		=> "TEXT",
			"DEFAULT"	=> "ID",

		),
		"SORT_BY"		=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_SORT_BY"),
			"TYPE"		=> "LIST",
			"VALUES"	=> array(
				"DESC"	=> "DESC",
				"ASC"	=> "ASC",
			),
			"DEFAULT"	=> "DESC",
		),
		"CATALOG_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_TYPE_IB_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),		
		"BRANDS_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_IB_BRANDS_CODE"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH"	=> "N",
		),
		"COUNT_RECORDS"	=> array(
				"PARENT"	=> "ADDITIONAL_SETTINGS",
				"NAME"		=> GetMessage("COUNT_RECORDS"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
		),

		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>