<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
?>

<?$APPLICATION->IncludeComponent("bitrix:search.page", "clear", Array(
	"RESTART" => "N",	// Искать без учета морфологии (при отсутствии результата поиска)
	"CHECK_DATES" => "N",	// Искать только в активных по дате документах
	"USE_TITLE_RANK" => "N",	// При ранжировании результата учитывать заголовки
	"DEFAULT_SORT" => "rank",	// Сортировка по умолчанию
	"arrFILTER" => array(	// Ограничение области поиска
		0 => "main",
		1 => "iblock_services",
		2 => "iblock_news",
		3 => "iblock_catalog",
	),
	"arrFILTER_main" => "",	// Путь к файлу начинается с любого из перечисленных
	"arrFILTER_iblock_services" => array(	// Искать в информационных блоках типа "iblock_services"
		0 => "all",
	),
	"arrFILTER_iblock_news" => array(	// Искать в информационных блоках типа "iblock_news"
		0 => "all",
	),
	"arrFILTER_iblock_catalog" => array(	// Искать в информационных блоках типа "iblock_catalog"
		0 => "all",
	),
	"SHOW_WHERE" => "N",	// Показывать выпадающий список "Где искать"
	"SHOW_WHEN" => "N",	// Показывать фильтр по датам
	"PAGE_RESULT_COUNT" => "25",	// Количество результатов на странице
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над результатами
	"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под результатами
	"PAGER_TITLE" => "Результаты поиска",	// Название результатов поиска
	"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
	"PAGER_TEMPLATE" => "arrows",	// Название шаблона
	"USE_SUGGEST" => "N",	// Показывать подсказку с поисковыми фразами
	"SHOW_ITEM_TAGS" => "N",	// Показывать теги документа
	"SHOW_ITEM_DATE_CHANGE" => "N",	// Показывать дату изменения документа
	"SHOW_ORDER_BY" => "N",	// Показывать сортировку
	"SHOW_TAGS_CLOUD" => "N",	// Показывать облако тегов
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>