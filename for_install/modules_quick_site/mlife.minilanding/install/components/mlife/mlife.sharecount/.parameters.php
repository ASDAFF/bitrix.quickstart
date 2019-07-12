<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("iblock"))
	return;
	
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"SHAREDATE" => array(
			'NAME' => GetMessage("MLIFE_SHARECOUNT_SHAREDATE"),
			'TYPE' => 'TEXT',
			'DEFAULT' => '2013-01-01 00:00:00',
			"PARENT" => "",
			"REFRESH" => "N",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
