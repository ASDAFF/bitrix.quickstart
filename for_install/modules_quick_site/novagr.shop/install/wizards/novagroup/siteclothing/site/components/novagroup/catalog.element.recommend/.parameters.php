<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(
		"ELEMENT_ID"	=> array(
			"PARENT"	=> "BASE",
			"NAME"		=> GetMessage("CATALOG_ELEMENT_ID"),
			"TYPE"		=> "TEXT",
		),
        "CATALOG_IBLOCK_ID"	=> array(
            "PARENT"	=> "BASE",
            "NAME"		=> GetMessage("CATALOG_ELEM_CATALOG"),
            "TYPE"		=> "TEXT",
        ),
        "OFFERS_IBLOCK_ID"	=> array(
            "PARENT"	=> "BASE",
            "NAME"		=> GetMessage("CATALOG_ELEM_IB_OFFERS"),
            "TYPE"		=> "TEXT",
        ),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>