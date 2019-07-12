<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))return;



$arComponentParameters = array(
	'PARAMETERS' => array(
		
        "IBLOCK_ID" => Array(
            "NAME" => GetMessage("T_IBLOCK_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "ELEMENT_CODE" => Array(
            "NAME" => GetMessage("T_ELEMENT_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "CATALOG_IBLOCK_ID" => array(
            "NAME" => GetMessage("T_CATALOG_IBLOCK_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
        "CATALOG_OFFERS_IBLOCK_ID" => array(
            "NAME" => GetMessage("T_CATALOG_OFFERS_IBLOCK_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
        "ARTICLES_IBLOCK_ID" => array(
            "NAME" => GetMessage("T_ARTICLES_IBLOCK_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
        "OPT_GROUP_ID" => array(
            "NAME" => GetMessage("OPT_GROUP_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
        "OPT_PRICE_ID" => array(
            "NAME" => GetMessage("OPT_PRICE_ID"),
            "TYPE"		=> "TEXT",
            "VALUES"	=> '',
            "DEFAULT"	=> "",
            "REFRESH"	=> "N",
        ),
		
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	),
);
?>