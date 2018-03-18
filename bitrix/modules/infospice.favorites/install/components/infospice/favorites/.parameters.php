<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-" => " "));

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("ACTIVE" => "Y", "TYPE"	 => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	if ($arRes['CODE'] == 'favorites') {
		$favoritesID = $arRes['ID'];
	}
}

$arSorts = Array("ASC"	 => GetMessage("T_IBLOCK_DESC_ASC"), "DESC"	 => GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
	"ID"			 => GetMessage("T_IBLOCK_DESC_FID"),
	"NAME"			 => GetMessage("T_IBLOCK_DESC_FNAME"),
	"ACTIVE_FROM"	 => GetMessage("T_IBLOCK_DESC_FACT"),
	"SORT"			 => GetMessage("T_IBLOCK_DESC_FSORT"),
	"TIMESTAMP_X"	 => GetMessage("T_IBLOCK_DESC_FTSAMP")
);
$arGroups = array();
$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch()) {
	if ($arGroup['ANONYMOUS'] == 'N') {
		$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
	}
}


$arComponentParameters = array(
	"GROUPS"	 => array(),
	"PARAMETERS" => array(
		"IBLOCK_TYPE"	 => Array(
			"PARENT"	 => "BASE",
			"NAME"		 => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE"		 => "LIST",
			"VALUES"	 => $arTypesEx,
			"DEFAULT"	 => "favorites",
			"REFRESH"	 => "Y",
		),
		"IBLOCK_ID"		 => Array(
			"PARENT"			 => "BASE",
			"NAME"				 => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE"				 => "LIST",
			"VALUES"			 => $arIBlocks,
			"DEFAULT"			 => $favoritesID,
			"ADDITIONAL_VALUES"	 => "Y",
			"REFRESH"			 => "Y",
		),
		"GROUPS"		 => array(
			"PARENT"			 => "BASE",
			"NAME"				 => GetMessage("INFOSPICE_FAVORITES_GRUPPY_POLQZOVATELEY"),
			"TYPE"				 => "LIST",
			"MULTIPLE"			 => "Y",
			"ADDITIONAL_VALUES"	 => "N",
			"VALUES"			 => $arGroups,
			"DEFAULT"			 => 2
		),
	/* "CACHE_TIME"	 => Array(
	  "DEFAULT" => 36000000
	  ), */
	),
);
?>
