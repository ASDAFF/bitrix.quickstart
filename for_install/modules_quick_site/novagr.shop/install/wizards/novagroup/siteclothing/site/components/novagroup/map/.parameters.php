<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(
        "ONLY_CATALOG" => array(
            "NAME" => GetMessage("ONLY_CATALOG"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),
		'CACHE_TIME'  =>  array('DEFAULT' => 3600),
	)
);
?>