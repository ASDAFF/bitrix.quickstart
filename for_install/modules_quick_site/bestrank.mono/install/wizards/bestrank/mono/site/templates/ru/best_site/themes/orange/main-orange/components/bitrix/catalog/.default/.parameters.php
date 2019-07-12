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
		"DEFAULT" => "180",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "225",
	),
	"DISPLAY_DETAIL_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_DETAIL_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_MORE_PHOTO_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"DISPLAY_MORE_PHOTO_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_MORE_PHOTO_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	"SHARPEN" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_SHARPEN"),
		"TYPE" => "TEXT",
		"DEFAULT" => "280",
	),
	);
?>
