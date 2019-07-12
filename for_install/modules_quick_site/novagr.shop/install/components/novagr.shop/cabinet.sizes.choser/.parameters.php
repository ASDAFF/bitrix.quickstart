<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
	'TYPE' => ($arCurrentValues["CLASSIFICATOR_IBLOCK_TYPE"] != "-"?$arCurrentValues["CLASSIFICATOR_IBLOCK_TYPE"]:""))
);
/*
$rsPhotosIBlock = CIBlock::GetList(
	array('SORT' => "ASC"),
	array('ACTIVE' => "Y",'SITE_ID' => $_REQUEST["site"],
	"TYPE" => ($arCurrentValues["PRODUCTS_PHOTOS_IBLOCK_TYPE"] != "-"?$arCurrentValues["PHOTOS_IBLOCK_TYPE"]:""))
);
*/
while($data = $rsCatalogIBlock -> Fetch()) $arCatalogs[$data["ID"]] = $data["NAME"];

$arComponentParameters = array(
	'PARAMETERS' => array(
		
		"CLASSIFICATOR_IBLOCK_TYPE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CLASSIFICATOR_IBLOCK_TYPE"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arTypes,
			"DEFAULT"	=> "",
			"REFRESH"	=> "Y",
		),
		
		"STD_SIZE_IBLOCK_CODE"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("STD_SIZE_IBLOCK_CODE"),
			"TYPE"		=> "LIST",
			"VALUES"	=> $arCatalogs,
			"DEFAULT"	=> "",
			"REFRESH"	=> "N",
		),
		"HEADLINE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("HEADLINE"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
		),
		"BUTTON_TITLE"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("BUTTON_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("DEFAULT_BTN_LABEL"),
		),
		"DIV_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("DIV_ID"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
		),
		
		"nPageSize"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("nPageSize"),
				"TYPE" => "STRING",
				"DEFAULT" => "10",
		),
		"AJAX" => array(
				"PARENT" => "BASE",
				"NAME"=>GetMessage("AJAX_PARAM"),
				"TYPE"=>"CHECKBOX",
				"DEFAULT"=>"N",
		),
		"AJAX_CONTENT" => array(
				"PARENT" => "BASE",
				"NAME"=>GetMessage("AJAX_CONTENT"),
				"TYPE"=>"CHECKBOX",
				"DEFAULT"=>"N",
		),
		"SELECTED_SECTION" => array(
				"PARENT" => "BASE",
				"NAME"=>GetMessage("SELECTED_SECTION"),
				"TYPE"=>"LIST",
				"MULTIPLE" => "Y",
				"VALUES" => "",
				"ADDITIONAL_VALUES" => "Y",
		),
			
			
	),
);
?>