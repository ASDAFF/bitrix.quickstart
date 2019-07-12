<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Сайт транспортно-экспедиционной компании");
$APPLICATION->SetTitle("Наши услуги");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"main_page_services",
	Array(
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "#SERVICES_IBLOCK_ID#",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_URL" => "services/#SECTION_ID#/",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "1",
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y"
	),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>