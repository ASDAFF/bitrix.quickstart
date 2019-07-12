<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(		
		"CATALOG_IBLOCK_ID"	=> array(
                "PARENT"    => "BASE",
                "NAME"        => GetMessage("CATALOG_IBLOCK_ID"),
                "TYPE"        => "TEXT",
                "VALUES"    => '',
                "DEFAULT"    => "",
                "REFRESH"    => "N",
		),
		"CATALOG_ELEMENT_ID"	=> array(
                "PARENT"    => "BASE",
                "NAME"        => GetMessage("CATALOG_ELEMENT_ID"),
                "TYPE"        => "TEXT",
                "VALUES"    => '',
                "DEFAULT"    => "",
                "REFRESH"    => "N",
		),
        "PHOTO_WIDTH"    => array(
                "PARENT"    => "BASE",
                "NAME"        => GetMessage("PHOTO_WIDTH"),
                "TYPE"        => "TEXT",
                "VALUES"    => '',
                "DEFAULT"    => "",
                "REFRESH"    => "N",
        ),
        "PHOTO_HEIGHT"    => array(
                "PARENT"    => "BASE",
                "NAME"        => GetMessage("PHOTO_HEIGHT"),
                "TYPE"        => "TEXT",
                "VALUES"    => '',
                "DEFAULT"    => "",
                "REFRESH"    => "N",
        ),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>