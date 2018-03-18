<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"ACCOUNTS" => array(
			"NAME"=>GetMessage("ACCOUNTS"),
			"TYPE" => "STRING",
			"DEFAULT"=> '1C_Bitrix, rsv_bitrix, senior_pomidor',
			"PARENT" => 'BASE',
		),
		"TITLE" => array(
			"NAME"=>GetMessage("TITLE"),
			"TYPE" => "STRING",
			"DEFAULT"=>GetMessage("DEFAULT_TITLE"),
			"PARENT" => 'BASE',
		),
		"WIDTH" => array(
			"NAME"=>GetMessage("WIDTH"),
			"TYPE" => "NUMBER",
			"DEFAULT"=> 300,
			"PARENT" => 'BASE',
		),
		"HEIGHT" => array(
			"NAME"=>GetMessage("HEIGHT"),
			"TYPE" => "NUMBER",
			"DEFAULT"=> 500,
			"PARENT" => 'BASE',
		),
		"JQUERY" => array(
			"NAME"=>GetMessage("JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>'Y',
			"PARENT" => 'BASE',
		),
	),
);

?>