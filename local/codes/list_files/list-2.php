<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Используем компоненты catalog.section.list и catalog.section + кастомизируем их шаблоны");
$APPLICATION->SetPageProperty("sect_id",13);
$sect_id = $APPLICATION->GetPageProperty("sect_id");
?>  <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "projects", array(
	"IBLOCK_TYPE" => "price",
	"IBLOCK_ID" => "4",
	"SECTION_ID" => $sect_id,
	"SECTION_CODE" => "",
	"COUNT_ELEMENTS" => "N",
	"TOP_DEPTH" => "1",
	"SECTION_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>