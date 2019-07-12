<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->IncludeComponent("bagmet:mobile.banner", ".default", array(
	"IBLOCK_TYPE_ID" => "banner",
	"IBLOCK_ID" => "#BANNER_IBLOCK_ID#",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "5"
	),
	false
);
?>
