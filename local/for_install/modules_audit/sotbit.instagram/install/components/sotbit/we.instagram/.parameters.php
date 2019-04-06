<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
    "GROUPS" => array(

    ),
	"PARAMETERS" => array(
        "WIDTH" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_WIDTH"),
			"TYPE" => "STRING",
            "DEFAULT" => '260',
			"PARENT" => "BASE",
		),
        "HEIGHT" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_HEIGHT"),
			"TYPE" => "STRING",
            "DEFAULT" => '320',
			"PARENT" => "BASE",
		),
        "INLINE" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_INLINE"),
			"TYPE" => "STRING",
            "DEFAULT" => '4',
			"PARENT" => "BASE",
		),
        "VIEW" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_VIEW"),
			"TYPE" => "STRING",
            "DEFAULT" => '12',
			"PARENT" => "BASE",
		),
        "TOOLBAR" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_TOOLBAR"),
			"TYPE" => "CHECKBOX",
            "DEFAULT" => 'Y',
			"PARENT" => "BASE",
		),
        "PREVIEW" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_PREVIEW"),
			"TYPE" => "LIST",
            "DEFAULT" => 'small',
            "PARENT" => "BASE",
			"VALUES" => array("small"=>"small - 150px", "large"=>"large - 306px", "fullsize"=>"fullsize - 640px"),
		),
        "CACHE" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_CACHE"),
			"TYPE" => "STRING",
            "DEFAULT" => '21600',
            "PARENT" => "BASE",
		),
        "TITLE" => array(
			"NAME" => GetMessage("STB_INSTAGRAM_TITLE"),
			"TYPE" => "STRING",
            "DEFAULT" => GetMessage("STB_INSTAGRAM_TITLE_DESCR"),
            "PARENT" => "BASE",
		),

	),
);
?>
