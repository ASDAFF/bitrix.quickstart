<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Импорт RSS");
?><?$APPLICATION->IncludeComponent(
	"bitrix:rss.show",
	"",
	Array(
		"SITE" => "www.1c-bitrix.ru",
		"PORT" => "80",
		"PATH" => "/bitrix/rss.php",
		"QUERY_STR" => "ID=news_sm&LANG=ru&TYPE=news&LIMIT=5",
		"OUT_CHANNEL" => "N",
		"NUM_NEWS" => "10",
		"CACHE_TIME" => "3600"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>