<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["BANNER_IBLOCK_TYPE"] != "-"?$arCurrentValues["BANNER_IBLOCK_TYPE"]:""))
);



while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[$data["ID"]] = $data["NAME"];




$arComponentParameters = array(
	'PARAMETERS' => array(
		
			

		"BANNER_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("BANNER_ELEM_TYPE_IB_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"BANNER_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("BANNER_ELEM_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		"ELEMENT_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("BANNER_ELEM_ID"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		
		"ELEMENT_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("BANNER_ELEM_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		"SORT_FIELD"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("BANNER_ELEM_SORT_FIELD"),
				"TYPE"		=> "TEXT",
				"DEFAULT"	=> "ID",
		
		),
		"SORT_BY"		=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("BANNER_ELEM_SORT_BY"),
				"TYPE"		=> "LIST",
				"VALUES"	=> array(
						"DESC"	=> "DESC",
						"ASC"	=> "ASC",
				),
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>