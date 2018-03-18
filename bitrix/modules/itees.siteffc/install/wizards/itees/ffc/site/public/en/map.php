<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Карта сайта");
if(!isset($global_error_number)) {
$APPLICATION->SetTitle("Карта сайта");
} elseif($global_error_number == 404) {
$APPLICATION->SetPageProperty("title", "Ошибка 404 - документ не найден");
$APPLICATION->SetTitle("Ошибка 404 - документ не найден");
?><p>Запрошенная страница не существует. Попробуйте найти нужную Вам страницу на карте нашего сайта ниже.</p><?
}

?><?$APPLICATION->IncludeComponent("bitrix:main.map", "sitemap", array(
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "3600",
	"SET_TITLE" => "N",
	"LEVEL" => "3",
	"COL_NUM" => "1",
	"SHOW_DESCRIPTION" => "N"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>