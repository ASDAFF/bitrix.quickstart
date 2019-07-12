<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-"?$arCurrentValues["CATALOG_IBLOCK_TYPE"]:""))
);
$rsOffersIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y",'SITE_ID' => $_REQUEST["site"],
	"TYPE" => ($arCurrentValues["OFFERS_IBLOCK_TYPE"] != "-"?$arCurrentValues["OFFERS_IBLOCK_TYPE"]:""))
);
while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[ $data['ID'] ] = $data['NAME'];
while($data = $rsOffersIBlock -> Fetch()) $arOffers[ $data["ID"] ] = $data['NAME'];

$arComponentParameters = array(
	'PARAMETERS' => array(
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
		
		"OFFERS_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_TYPE_IB_OFFERS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"OFFERS_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEM_IB_OFFERS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arOffers,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_PATH"),
			"TYPE"		=> "TEXT",
		),
		"BRAND_ROOT"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("BRAND_PATH"),
			"TYPE"		=> "TEXT",
		),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	)
);
?>