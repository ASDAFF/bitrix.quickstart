<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(
		"ORDER_LIST_IBLOCK_ID"	=> array(
				"PARENT"	=> "BASE",
				"NAME"		=> GetMessage("MESS_ORDER_LIST_IBLOCK_ID"),
				"TYPE"		=> "TEXT",
				"VALUES"	=> '',
				"DEFAULT"	=> "",
		),
        "ORDER_PRODUCT_IBLOCK_ID"	=> array(
            "PARENT"	=> "BASE",
            "NAME"		=> GetMessage("MESS_ORDER_PRODUCT_IBLOCK_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
        ),
	),
);
?>