<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = Array(
	"JQUERY" => array( 
		"NAME" => GetMessage('BEONO_BASKET_JQUERY'),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",	
		"PARENT" => "BASE",
	),
	"STYLE" => Array(
		"NAME" => GetMessage("BEONO_BASKET_STYLE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"VALUES" => array (
			"beono-basket_gray" => GetMessage("BEONO_BASKET_STYLE_GRAY"),
			"beono-basket_blue" => GetMessage("BEONO_BASKET_STYLE_BLUE"),
			"beono-basket_yellow" => GetMessage("BEONO_BASKET_STYLE_YELLOW"),
			"beono-basket_green" => GetMessage("BEONO_BASKET_STYLE_GREEN"),
			"beono-basket_simple" => GetMessage("BEONO_BASKET_STYLE_SIMPLE"),
		),
		"DEFAULT" => "beono-basket_gray",
		"PARENT" => "VISUAL",
	),
	"BORDER" => Array(
		"NAME" => GetMessage("BEONO_BASKET_BORDER"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"DEFAULT" => "N",
		"PARENT" => "VISUAL",
	),
	"IMAGE_WIDTH" => Array(
		"NAME" => GetMessage("BEONO_BASKET_IMAGE_WIDTH"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "40",
		"PARENT" => "VISUAL",
	),
	"IMAGE_HEIGHT" => Array(
		"NAME" => GetMessage("BEONO_BASKET_IMAGE_HEIGHT"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "40",
		"PARENT" => "VISUAL",
	),
	"AJAX_ACTIONS" => Array(
		"NAME" => GetMessage("BEONO_BASKET_AJAX_ACTIONS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"DEFAULT" => "N",
		"PARENT" => "VISUAL",
	),
/*	"DELIVERY_PRICE" => Array(
		"NAME" => GetMessage("BEONO_BASKET_DELIVERY_PRICE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => "VISUAL",
	),*/
);
?>