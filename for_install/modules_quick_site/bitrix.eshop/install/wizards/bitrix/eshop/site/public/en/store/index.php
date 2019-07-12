<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Warehouses");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.store",
	"",
	Array(
		"SEF_MODE" => "Y",
		"PHONE" => "N",
		"SCHEDULE" => "N",
		"SET_TITLE" => "Y",
		"TITLE" => "Warehouses",
		"MAP_TYPE" => "1",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_NOTES" => "",
		"SEF_FOLDER" => "#SITE_DIR#store/",
		"SEF_URL_TEMPLATES" => Array(
			"liststores" => "index.php",
			"element" => "#store_id#"
		),
		"VARIABLE_ALIASES" => Array(
			"liststores" => Array(),
			"element" => Array(),
		)
	),
false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>