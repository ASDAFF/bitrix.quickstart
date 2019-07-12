<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("v1rt.personal")) die();

$arFolders[0] = "[0] ".GetMessage("NULL_VALUE_LIST");
$arFolders = CMediaComponents::getListName($arFolders);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"FOLDERS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FOLDERS"),
			"TYPE" => "LIST",
			"VALUES" => $arFolders,
            "MULTIPLE" => "Y",
		),
        "CHILDREN" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CHILDREN"),
			"TYPE" => "CHECKBOX",
		),
        "DETAIL_URL" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("DETAIL_URL"),
			"TYPE" => "TEXTBOX",
			"DEFAULT" => "/gallery/detail.php?ID=#ID#",
		),
        
        'CACHE_TIME' => array('DEFAULT'=>3600),
	),
);
?>