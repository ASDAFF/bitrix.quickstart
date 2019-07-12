<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arTemplateParameters = array(
	"PATH_TO_SHIPPING"=>array(
		"NAME" => GetMessage("PATH_TO_SHIPPING"),
		"TYPE" => "STRING",
		"DEFAULT" => "#SITE_DIR#about/delivery/",	
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "75",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "225",
	),
	"DISPLAY_DETAIL_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "350",
	),
	"DISPLAY_DETAIL_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "1000",
	),
	"DISPLAY_MORE_PHOTO_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "50",
	),
	"DISPLAY_MORE_PHOTO_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "50",
	),
	"SHARPEN" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_SHARPEN"),
		"TYPE" => "TEXT",
		"DEFAULT" => "30",
	),
	"USE_PRICES_RANGE" => Array(
			"NAME" => GetMessage("IBLOCK_USE_PRICES_RANGE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	),
	"MAKE_LIST" => array(			
			"NAME" => GetMessage("MAKE_LIST"),
			"TYPE" => "STRING",
			"MULTIPLE" => "Y",			
		),
	"ADD_PROP_TO_CART" => array(			
			"NAME" => GetMessage("ADD_PROP_TO_CART"),
			"TYPE" => "STRING",
			"MULTIPLE" => "Y",			
		),
	);
if($arCurrentValues["USE_PRICES_RANGE"]=="Y")
{
	$arTemplateParameters["PRICES_RANGE"] = array(
		"NAME" => GetMessage("IBLOCK_PRICES_RANGE"),
		"TYPE" => "STRING",
		"VALUES" => array(),
		"MULTIPLE" => "Y",
		"DEFAULT" => array("0","100","500","1000","3000"),
		"ADDITIONAL_VALUES" => "Y",
	);
}
?>
