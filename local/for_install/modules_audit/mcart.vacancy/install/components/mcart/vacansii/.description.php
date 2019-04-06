<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("VACANSII_NAME"),
	"DESCRIPTION" => GetMessage("VACANSII_DESCRIPTION"),
	"ICON" => "/images/news_all.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "mcart",
		"NAME"=> GetMessage('MCART_PARTNER_NAME'),
		"CHILD" => array(
			"ID" => "vacansii",
			"NAME" => GetMessage("VACANSII_NAME"),
			"SORT" => 10
		),
	),
);

?>