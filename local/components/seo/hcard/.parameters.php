<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("fileman");
CMedialib::Init();

$ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'PARENT_ID' => 0)));

$arCollection = array();
foreach($ar as $Collection){
    $arCollection[$Collection['ID']] = $Collection['NAME'];
}
// echo "<pre>"; print_r($ar); echo '</pre>';

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
        "CATEGORY" => array(
			"PARENT" => "BASE",
			"NAME"		=> GetMessage("EPIR_PARAM_CATEG"),
			"TYPE"		=> "STRING",
		),
        "ORG_NAME" => array(
			"PARENT" => "BASE",
			"NAME"		=> GetMessage("EPIR_PARAM_ORGNAME"),
			"TYPE"		=> "STRING",
		),
        "LOCALITY" => array(
			"PARENT" => "BASE",
			"NAME"		=> GetMessage("EPIR_PARAM_GOROD"),
			"TYPE"		=> "STRING",
		),
        "ADRES" => array(
			"PARENT" => "BASE",
			"NAME"		=> GetMessage("EPIR_PARAM_ADRES"),
			"TYPE"		=> "STRING",
		),
        "TEL" => Array(
			"PARENT" => "BASE",
			"NAME"=> GetMessage("EPIR_PARAM_TEL"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"Y",
		),
        "EMAILS" => Array(
			"PARENT" => "BASE",
			"NAME"=> GetMessage("EPIR_PARAM_MAIL"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"Y",
		),
        "WORKHOURS" => Array(
			"PARENT" => "BASE",
			"NAME"=> GetMessage("EPIR_PARAM_WORK"),
			"TYPE"=>"STRING",
            "DEFAULT" => GetMessage("EPIR_PARAM_WORKDEMO"),
		),
        "URL" => Array(
			"PARENT" => "BASE",
			"NAME"=> GetMessage("EPIR_PARAM_URL"),
			"TYPE"=>"STRING",
            "DEFAULT" => 'http://www.'.$_SERVER['SERVER_NAME'],
		)
    )
);
