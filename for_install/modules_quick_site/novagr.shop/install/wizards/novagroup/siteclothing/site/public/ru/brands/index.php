<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");
?><?$APPLICATION->IncludeComponent(
	"novagroup:brands",
	"complex",
	Array(
		"SORT_FIELD" => "NAME",
		"SORT_BY" => "ASC",
		"CATALOG_IBLOCK_TYPE" => "-",
		"BRANDS_IBLOCK_CODE" => "vendor",
		"COUNT_RECORDS" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>