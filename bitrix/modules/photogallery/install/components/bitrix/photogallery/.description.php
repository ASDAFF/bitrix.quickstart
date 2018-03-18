<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_PHOTO_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_PHOTO_DESCRIPTION"),
	"ICON" => "/images/photo.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "photogallery",
			"NAME" => GetMessage("T_IBLOCK_DESC_PHOTO"),
			"SORT" => 20,
			"CHILD" => array(
				"ID" => "photo_gallery",
			),
		),
	),
);

?>