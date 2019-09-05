<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arYesNo = Array(
	"Y" => GetMessage("BEONO_BASKET_DESC_YES"),
	"N" => GetMessage("BEONO_BASKET_DESC_NO"),
);

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_ORDER" => Array(
			"NAME" => GetMessage("BEONO_BASKET_PATH_TO_ORDER"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/order/make/",
			"COLS" => 25,
			"PARENT" => "BASE",
		),
		"HIDE_COUPON" => Array(
			"NAME"=>GetMessage("BEONO_BASKET_HIDE_COUPON"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "VISUAL",
		),
		"COLUMNS_LIST" => Array(
			"NAME"=>GetMessage("BEONO_BASKET_COLUMNS_LIST"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES"=>array(
				"IMAGE" => GetMessage("BEONO_BASKET_BIMAGE"),
				"NAME" => GetMessage("BEONO_BASKET_BNAME"),
				"DATE" => GetMessage("BEONO_BASKET_BDATE"),
				"PROPS" => GetMessage("BEONO_BASKET_BPROPS"),
				"WEIGHT" => GetMessage("BEONO_BASKET_BWEIGHT"),
				"QUANTITY" => GetMessage("BEONO_BASKET_BQUANTITY"),		
				"PRICE" => GetMessage("BEONO_BASKET_BPRICE"),
				"TYPE" => GetMessage("BEONO_BASKET_BTYPE"),
				"DISCOUNT" => GetMessage("BEONO_BASKET_BDISCOUNT"),		
			//	"DELETE" => GetMessage("BEONO_BASKET_BDELETE"),
				"DELAY" => GetMessage("BEONO_BASKET_BDELAY")),
			"DEFAULT"=>array("IMAGE", "NAME", "PRICE", "TYPE", "DISCOUNT", "QUANTITY", "DELETE", "DELAY", "WEIGHT"),
			"SIZE"=> 10,
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "VISUAL",
		),
/*
		"QUANTITY_FLOAT" => array(
			"NAME" => GetMessage('BEONO_BASKET_QUANTITY_FLOAT'),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),

		"PRICE_VAT_INCLUDE" => array(
			"NAME" => GetMessage('BEONO_BASKET_VAT_INCLUDE'),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "Y",
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => Array(
			"NAME"=>GetMessage("BEONO_BASKET_COUNT_DISCOUNT_4_ALL_QUANTITY"), 
			"TYPE"=>"LIST", "MULTIPLE"=>"N", 
			"VALUES"=>array(
					"N" => GetMessage("BEONO_BASKET_DESC_NO"), 
					"Y" => GetMessage("BEONO_BASKET_DESC_YES")
				), 
			"DEFAULT"=>"N", 
			"COLS"=>25, 
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		),
*/
		"PRICE_VAT_SHOW_VALUE" => array(
			"NAME" => GetMessage('BEONO_BASKET_VAT_SHOW_VALUE'),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "VISUAL",
		),
		"AJAX_MODE" => array()
	)
);
?>