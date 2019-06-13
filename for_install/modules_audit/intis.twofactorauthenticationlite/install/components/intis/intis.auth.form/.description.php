<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("TWO_FACTOR_AUTH_TITLE"),
	"DESCRIPTION" => GetMessage("TWO_FACTOR_AUTH_DESCR"),
	"ICON" => "/images/user_authform.gif",
	"PATH" => array(
			"ID" => "utility",
			"CHILD" => array(
				"ID" => "user",
				"NAME" => GetMessage("TWO_FACTOR_AUTH_NAME")
			)
		),	
);
?>