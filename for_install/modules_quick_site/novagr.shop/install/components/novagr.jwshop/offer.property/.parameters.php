<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(
        "OFFER_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("OFFER_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "PROPERTY_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PROPERTY_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	)
);
?>