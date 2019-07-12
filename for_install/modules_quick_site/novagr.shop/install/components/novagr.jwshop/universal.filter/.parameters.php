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
$rsPhotosIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y",'SITE_ID' => $_REQUEST["site"],
	"TYPE" => ($arCurrentValues["PHOTOS_IBLOCK_TYPE"] != "-"?$arCurrentValues["PHOTOS_IBLOCK_TYPE"]:""))
);

$arOffers[0] = GetMessage("FILTER_NOT_USED");

while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[ $data['ID'] ] = $data['NAME'];
while($data = $rsOffersIBlock -> Fetch()) $arOffers[ $data["ID"] ] = $data['NAME'];
while($data = $rsPhotosIBlock -> Fetch()) $arPhotos[ $data["ID"] ] = $data['NAME'];

$arComponentParameters = array(
	'PARAMETERS' => array(
		"CATALOG_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_TYPE_IB_CATALOGS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"CATALOG_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_CATALOG"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"OFFERS_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_TYPE_IB_OFFERS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"OFFERS_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_OFFERS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arOffers,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"PHOTOS_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_TYPE_IB_PHOTOS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"PHOTOS_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_PHOTOS"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arPhotos,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		
		"PRICE_ORDER"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_PRICES_SORT"),
			"TYPE"		=> "TEXT",
			"DEFAULT"	=> "0",
		),
		
		"ROOT_PATH"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("FILTER_CATALOG_PATH"),
			"TYPE"		=> "TEXT",
		),
		
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	)
);
?>