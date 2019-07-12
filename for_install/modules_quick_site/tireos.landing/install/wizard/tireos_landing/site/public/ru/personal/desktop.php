<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный рабочий стол");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:desktop",
	"",
	Array(
		"ID" => "holder1",
		"CAN_EDIT" => "Y",
		"COLUMNS" => "3",
		"COLUMN_WIDTH_0" => "33%",
		"COLUMN_WIDTH_1" => "33%",
		"COLUMN_WIDTH_2" => "33%",
		"GADGETS" => Array("ALL"),
		"G_RSSREADER_CACHE_TIME" => "3600",
		"G_RSSREADER_SHOW_URL" => "Y",
		"G_RSSREADER_PREDEFINED_RSS" => "",
		"GU_RSSREADER_CNT" => "10",
		"GU_RSSREADER_RSS_URL" => "",
		"G_PROBKI_CACHE_TIME" => "3600",
		"G_PROBKI_SHOW_URL" => "Y",
		"GU_PROBKI_CITY" => "c213",
		"G_WEATHER_CACHE_TIME" => "3600",
		"G_WEATHER_SHOW_URL" => "Y",
		"GU_WEATHER_CITY" => "c213"
	),
false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>