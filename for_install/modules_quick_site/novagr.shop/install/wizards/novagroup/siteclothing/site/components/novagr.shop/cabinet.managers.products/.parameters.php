<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => "")
);

while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[$data["ID"]] = $data["NAME"];
//deb($arCatalogs);


$arComponentParameters = array(
	'PARAMETERS' => array(
		
		
		"PRODUCT_IBLOCK_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("PRODUCT_IBLOCK_ID"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH"	=> "N",
		),
		
		"OFFERS_IBLOCK_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("OFFERS_IBLOCK_ID"),
				"TYPE"		=> "LIST",
				"VALUES"	=> $arCatalogs,
				"DEFAULT"	=> "",
				"ADDITIONAL_VALUES" => "Y",
				"REFRESH"	=> "N",
		)	
			
	),
);
?>