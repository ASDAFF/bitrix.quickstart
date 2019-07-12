<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//Получаем из настроек модуля
$strAccount = "";

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"ACCOUNT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ACCOUNT"),
			"TYPE" => "TEXTBOX",
			"VALUES" => $strAccount,
		),
        
        "COUNT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COUNT"),
			"TYPE" => "TEXTBOX",
			"VALUES" => 1,
		),
        
        "CONSUMER_KEY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CONSUMER_KEY"),
			"TYPE" => "TEXTBOX",
		),
        
        "CONSUMER_SECRET" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CONSUMER_SECRET"),
			"TYPE" => "TEXTBOX",
		),
        
        "USER_TOKEN" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("USER_TOKEN"),
			"TYPE" => "TEXTBOX",
		),
        
        "USER_SECRET" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("USER_SECRET"),
			"TYPE" => "TEXTBOX",
		),
        
        'CACHE_TIME' => array('DEFAULT'=>1800),
	),
);
?>