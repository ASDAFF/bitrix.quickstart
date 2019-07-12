<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arCatalogs = array();
$arFashion = array();
$arOffers = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);

$rsFashionIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["FASHION_IBLOCK_TYPE"] != "-"?$arCurrentValues["FASHION_IBLOCK_TYPE"]:""))
);

$rsOffersIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["OFFERS_IBLOCK_TYPE"] != "-"?$arCurrentValues["OFFERS_IBLOCK_TYPE"]:""))
);
while($data = $rsCatalogIBlock -> Fetch()) $arCatalog[ $data['ID'] ] = $data['NAME'];
while($data = $rsFashionIBlock -> Fetch()) $arFashion[ $data['ID'] ] = $data['NAME'];
while($data = $rsOffersIBlock -> Fetch()) $arOffers[ $data['ID'] ] = $data['NAME'];

$arComponentParameters = array(
	'PARAMETERS' => array(
		"FASHION_IBLOCK_TYPE"	=> array(
			"PARENT"			=> "BASE",
			"NAME"				=> GetMessage("CATALOG_ELEM_TYPE_IB_IMAGERIES"),
			"TYPE"				=> "LIST",
			"VALUES"			=> $arTypes,
			"DEFAULT"			=> "",
			"REFRESH"			=> "Y",
		),
		
		"FASHION_IBLOCK_ID"	=> array(
			"PARENT"		=> "BASE",
			"NAME"			=> GetMessage("CATALOG_ELEM_IMAGERIES"),
			"TYPE"			=> "LIST",
			"VALUES"		=> $arCatalog,
			"DEFAULT"		=> "",
			"REFRESH"		=> "N",
		),
		
		"CATALOG_IBLOCK_TYPE"	=> array(
			"PARENT"			=> "BASE",
			"NAME"				=> GetMessage("CATALOG_ELEM_TYPE_IB_CATALOG"),
			"TYPE"				=> "LIST",
			"VALUES"			=> $arTypes,
			"DEFAULT"			=> "",
			"REFRESH"			=> "Y",
		),
		"CATALOG_IBLOCK_ID"	=> array(
			"PARENT"		=> "BASE",
			"NAME"			=> GetMessage("CATALOG_ELEM_CATALOG"),
			"TYPE"			=> "LIST",
			"VALUES"		=> $arFashion,
			"DEFAULT"		=> "",
			"REFRESH"		=> "N",
		),
		
		"OFFERS_IBLOCK_TYPE"	=> array(
			"PARENT"			=> "BASE",
			"NAME"				=> GetMessage("CATALOG_ELEM_TYPE_IB_OFFERS"),
			"TYPE"				=> "LIST",
			"VALUES"			=> $arTypes,
			"DEFAULT"			=> "",
			"REFRESH"			=> "Y",
		),
		"OFFERS_IBLOCK_ID"	=> array(
			"PARENT"		=> "BASE",
			"NAME"			=> GetMessage("CATALOG_ELEM_IB_OFFERS"),
			"TYPE"			=> "LIST",
			"VALUES"		=> $arOffers,
			"DEFAULT"		=> "",
			"REFRESH"		=> "N",
		),
			
		"FASHION_ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FASHION_PATH"),
			"TYPE"		=> "TEXT",
		),
		
		"CATALOG_ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_PATH"),
			"TYPE"		=> "TEXT",
		),
		
		"VENDOR_ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("BRAND_PATH"),
			"TYPE"		=> "TEXT",
		),
		
		"INET_MAGAZ_ADMIN_USER_GROUP_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("INET_MAGAZ_ADMIN_USER_GROUP_ID"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
				"REFRESH"	=> "N",
		),

        "USE_SEARCH_STATISTIC" => array(
            "PARENT"	=> "BASE",
            "NAME"		=> GetMessage("CATALOG_LIST_USE_SEARCH_STATISTIC"),
            "TYPE"		=> "CHECKBOX",
        ),
        "OPT_GROUP_ID" => array(
            "NAME" => GetMessage("OPT_GROUP_ID"),
            "TYPE"		=> "TEXT",
            "PARENT"	=> "BASE",
        ),
        "OPT_PRICE_ID" => array(
            "NAME" => GetMessage("OPT_PRICE_ID"),
            "TYPE"		=> "TEXT",
            "PARENT"	=> "BASE",
        ),
        'CACHE_TIME'  =>  array('DEFAULT' => 3600),
        "CACHE_GROUPS" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("CP_BN_CACHE_GROUPS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
	)
);
?>