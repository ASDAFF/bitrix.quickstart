<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_GALLERY_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_GALLERY_DESCRIPTION"),
    "ICON" => "/images/gallery.gif",
    "SORT" => 90,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "service",
    ),
);

?>
