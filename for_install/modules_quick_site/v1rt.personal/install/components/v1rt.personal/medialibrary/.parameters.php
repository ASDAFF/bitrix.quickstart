<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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
        "DETAIL_PAGE_SETTINGS" => array(
            "NAME" => GetMessage("DETAIL_PAGE_SETTINGS"),
        ),
        "RESIZE_SETTINGS" => array(
            "NAME" => GetMessage("RESIZE_SETTINGS")
        ),
        "NAVIGATION" => array(
			"NAME" => GetMessage("NAVIGATION")
		),
	),
	"PARAMETERS" => array(

		"FOLDERS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FOLDERS"),
			"TYPE" => "LIST",
			"VALUES" => $arFolders,
            "MULTIPLE" => "Y",
		),

		"VARIABLE_ALIASES" => array(
			"GALLERY_ID" => array(
				"NAME" => GetMessage("GALLERY_ID"),
				"DEFAULT" => "GALLERY_ID",
			),
		),
        
		"SEF_MODE" => array(
			"list" => array(
				"NAME" => GetMessage("LIST_PAGE"),
				"DEFAULT" => "/",
				"VARIABLES" => array()
			),
			"detail" => array(
				"NAME" => GetMessage("DETAIL_PAGE"),
				"DEFAULT" => "/#GALLERY_ID#/",
				"VARIABLES" => array("GALLERY_ID")
			),
		),
        
        "TITLE" => array(
			"PARENT" => "DETAIL_PAGE_SETTINGS",
			"NAME" => GetMessage("TITLE"),
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
            "DEFAULT" => "150",
		), 
        "RESIZE_MODE_H" => array(
            "PARENT" => "RESIZE_SETTINGS",
			"NAME" => GetMessage("RESIZE_MODE_H"),
			"TYPE" => "TEXTBOX",
            "DEFAULT" => "150",
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