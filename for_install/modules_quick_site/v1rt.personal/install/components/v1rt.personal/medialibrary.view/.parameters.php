<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("v1rt.personal")) die();

$arFolders[0] = "[0] ".GetMessage("NULL_VALUE_LIST");
$arFolders = CMediaComponents::getListName($arFolders);

$arResizeMode = array(
    "N" => GetMessage("RESIZE_MODE_NO"),
    "F" => GetMessage("RESIZE_MODE_FLY"),
    "P" => GetMessage("RESIZE_MODE_PREVIEW")
);

$arComponentParameters = array(
	"GROUPS" => array(
        "NAVIGATION" => array(
			"NAME" => GetMessage("NAVIGATION")
		),
        "RESIZE_SETTINGS" => array(
            "NAME" => GetMessage("RESIZE_SETTINGS")
        ),
        "TPL_SETTINGS" => array(
            "NAME" => GetMessage("TPL_SETTINGS")
        ),
	),
	"PARAMETERS" => array(
		"FOLDERS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FOLDERS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arFolders,
		),
        "VARIABLE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("VARIABLE"),
			"TYPE" => "TEXTBOX",
		),
		"COUNT_IMAGE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COUNT_IMAGE"),
			"TYPE" => "TEXTBOX",
		),
        "RANDOM" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("RANDOM"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
		),
        "PAGE_LINK" => array(
			"PARENT" => "TPL_SETTINGS",
			"NAME" => GetMessage("PAGE_LINK"),
			"TYPE" => "TEXTBOX",
		),
        "PAGE_LINK_TEXT" => array(
			"PARENT" => "TPL_SETTINGS",
			"NAME" => GetMessage("PAGE_LINK_TEXT"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "Просмотреть все фото",
		),
        "TITLE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("TITLE"),
			"TYPE" => "CHECKBOX",
		),
        "LOAD_JS" => array(
			"PARENT" => "TPL_SETTINGS",
			"NAME" => GetMessage("LOAD_JS"),
			"TYPE" => "CHECKBOX",
		),
        
        
        
        "RESIZE_MODE" => array(
            "PARENT" => "RESIZE_SETTINGS",
			"NAME" => GetMessage("RESIZE_MODE"),
			"TYPE" => "LIST",
            "VALUES" => $arResizeMode,
		),
        "RESIZE_MODE_W" => array(
            "PARENT" => "RESIZE_SETTINGS",
			"NAME" => GetMessage("RESIZE_MODE_W"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "145",
		), 
        "RESIZE_MODE_H" => array(
            "PARENT" => "RESIZE_SETTINGS",
			"NAME" => GetMessage("RESIZE_MODE_H"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "108",
		),
        
        
              
        "PAGE_NAV_MODE" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("PAGE_NAV_MODE"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
		),
        "ELEMENT_PAGE" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("ELEMENT_PAGE"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "5",
		),
        "PAGER_SHOW_ALL" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("PAGER_SHOW_ALL"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
		),
        "PAGER_SHOW_ALWAYS" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("PAGER_SHOW_ALWAYS"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
		),
        "PAGER_TITLE" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("PAGER_TITLE"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "Фотографии",
		),
        "PAGER_TEMPLATE" => array(
            "PARENT" => "NAVIGATION",
			"NAME" => GetMessage("PAGER_TEMPLATE"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "modern",
		),
        
        'CACHE_TIME' => array('DEFAULT'=>3600),
	),
);
?>