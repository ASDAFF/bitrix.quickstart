<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SBBL_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"DEFAULT" => '={SITE_DIR."personal/cart/"}',
			"COLS" => 25,
			"PARENT" => "BASE"
		),
		"PATH_TO_PERSONAL" => Array(
			"NAME" => GetMessage("SBBL_PATH_TO_PERSONAL"),
			"TYPE" => "STRING",
			"DEFAULT" => '={SITE_DIR."personal/"}',
			"COLS" => 25,
			"PARENT" => "BASE"
		),
		"SHOW_PERSONAL_LINK" => Array(
			"NAME" => GetMessage("SBBL_SHOW_PERSONAL_LINK"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "BASE"
		),
		"SHOW_NUM_PRODUCTS" => Array(
			"NAME" => GetMessage("SBBL_SHOW_NUM_PRODUCTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE"
		),
		"SHOW_TOTAL_PRICE" => Array(
			"NAME" => GetMessage("SBBL_SHOW_TOTAL_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE"
		),

		"SHOW_PRODUCTS" => Array(
			"NAME" => GetMessage("SBBL_SHOW_PRODUCTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "BASE"
		),

		"POSITION_FIXED" => Array(
			"NAME" => GetMessage("SBBL_POSITION_FIXED"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "VISUAL",
			"REFRESH" => "Y",
		),
	)
);

if($arCurrentValues["POSITION_FIXED"] == "Y")
{
	$arComponentParameters["PARAMETERS"] += array(
		"POSITION_HORIZONTAL" => Array(
			"NAME"=>GetMessage("SBBL_POSITION_HORIZONTAL"),
			"TYPE"=>"LIST",
			"VALUES"=>array(
				"left" => GetMessage("SBBL_POSITION_HORIZONTAL_LEFT"),
				"right" => GetMessage("SBBL_POSITION_HORIZONTAL_RIGHT")
			),
			"DEFAULT"=>"right",
			"PARENT" => "VISUAL",
		),
		"POSITION_VERTICAL" => Array(
			"NAME"=>GetMessage("SBBL_POSITION_VERTICAL"),
			"TYPE"=>"LIST",
			"VALUES"=>array(
				"top" => GetMessage("SBBL_POSITION_VERTICAL_TOP"),
				"bottom" => GetMessage("SBBL_POSITION_VERTICAL_BOTTOM")
			),
			"DEFAULT"=>"top",
			"PARENT" => "VISUAL",
		),
	);
}

if($arCurrentValues["SHOW_PRODUCTS"] == "Y")
{
	$arComponentParameters["PARAMETERS"] += array(
		"PATH_TO_ORDER" => Array(
			"NAME" => GetMessage("SBBL_PATH_TO_ORDER"),
			"TYPE" => "STRING",
			"DEFAULT" => '={SITE_DIR."personal/order/make/"}',
			"COLS" => 25,
		),
		"SHOW_DELAY" => array(
			"NAME" => GetMessage('SBBL_SHOW_DELAY'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_NOTAVAIL" => array(
			"NAME" => GetMessage('SBBL_SHOW_NOTAVAIL'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_SUBSCRIBE" => array(
			"NAME" => GetMessage('SBBL_SHOW_SUBSCRIBE'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),

		"SHOW_IMAGE" => array(
			"NAME" => GetMessage('SBBL_SHOW_IMAGE'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_PRICE" => array(
			"NAME" => GetMessage('SBBL_SHOW_PRICE'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_SUMMARY" => array(
			"NAME" => GetMessage('SBBL_SHOW_SUMMARY'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	);
}

