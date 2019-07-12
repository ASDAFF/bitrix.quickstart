<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BND_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"TITLE_BLOCK" => array(
			"NAME" => GetMessage("TITLE_BLOCK"),
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
		"VK" => array(
			"NAME" => GetMessage("VKONTAKTE"),
			"TYPE" => "STRING",
			"DEFAULT" => "http://vkontakte.ru/aspro74"
		),
		"ODN" => array(
			"NAME" => GetMessage("ODN"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"FACE" => array(
			"NAME" => "Facebook",
			"TYPE" => "STRING",
			"DEFAULT" => "http://www.facebook.com/aspro74"
		),
		"TWIT" => array(
			"NAME" => "Twitter",
			"TYPE" => "STRING",
			"DEFAULT" => "http://twitter.com/aspro_ru"
		),
		"INST" => array(
			"NAME" => GetMessage("INST"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"MAIL" => array(
			"NAME" => GetMessage("MAIL"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"YOUTUBE" => array(
			"NAME" => GetMessage("YOUTUBE"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
		"GOOGLE_PLUS" => array(
			"NAME" => GetMessage("GOOGLE_PLUS"),
			"TYPE" => "STRING",
			"DEFAULT" => "#"
		),
	),
);
?>
