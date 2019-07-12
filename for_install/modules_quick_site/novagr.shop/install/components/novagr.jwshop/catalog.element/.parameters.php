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



$arSorts = array(
	"ASC"	=> "ASC",
	"DESC"	=> "DESC"
);
$arSortFields = Array(
		"ID"			=> GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"			=> GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"	=> GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"			=> GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"	=> GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

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
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		

		"CATALOG_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_TYPE_IB_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"CATALOG_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"CATALOG_OFFERS_IBLOCK_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_OFFERS"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "Y",
		),
			
		"PHOTO_IBLOCK_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_PHOTOS"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
			
			
		"ARTICLES_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_IB_ARTICLES"),
			"TYPE"		=> "TEXT",
			"VALUES"	=> '',
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"SAMPLES_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_SAMPLES_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		
		"BRANDNAME_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_BRANDS_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
	
		"COLORS_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_IB_COLORS_CODE"),
			"TYPE"		=> "TEXT",
			"VALUES"	=> '',
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"STYLE_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_IB_STYLE_CODE"),
			"TYPE"		=> "TEXT",
			"VALUES"	=> '',
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		"STONE_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_STONE_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		"METAL_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_METAL_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
		"METAL_COLOR_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_METAL_COLOR_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
			
		"STD_SIZES_IBLOCK_CODE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("CATALOG_ELEM_IB_SIZES_CODE"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
        "CATALOG_SUBSCRIBE_ENABLE" => array(
            "NAME" => GetMessage("CATALOG_SUBSCRIBE_ENABLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "PARENT" => "BASE",
        ),
		"INET_MAGAZ_ADMIN_USER_GROUP_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("INET_MAGAZ_ADMIN_USER_GROUP_ID"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),
        "CATALOG_ELEM_CS" => array(
            "NAME" => GetMessage("CATALOG_ELEM_CS"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>