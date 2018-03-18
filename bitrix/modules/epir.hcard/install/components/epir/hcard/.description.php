<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" =>  GetMessage("EPIR_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("EPIR_COMPONENT_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"SORT" => 1,
	"ICON" => "/images/hCard.gif",
    	'PATH' => array(
            'ID' => 'epir',//'event-list',
            "NAME" => GetMessage("EPIR_IBLOCK_DESC"),//'event-list',
        ),

); 

?>