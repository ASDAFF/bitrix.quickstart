<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("#TABC_DEMO_TITLE_PAGE#");

$APPLICATION->IncludeComponent(
	"tatonimus.abc:abc",
	"",
	Array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "3",
		"PROPERTY" => "IBLOCK_SECTION_ID",
		"REQUEST_KEY" => "ID",
		"FILTER_NAME" => "arrFilter",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
	),
false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>