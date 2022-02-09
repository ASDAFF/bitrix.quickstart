<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Результаты");
?> 
<?$APPLICATION->IncludeComponent(
	"bitrix:search.page",
	"catalog",
	Array(
		"RESTART" => "N",
		"NO_WORD_LOGIC" => "N",
		"CHECK_DATES" => "N",
		"USE_TITLE_RANK" => "Y",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"arrFILTER" => array(0=>"iblock_catalog",),
		"arrFILTER_iblock_catalog" => array(0=>"all",),
		"SHOW_WHERE" => "N",
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => "100",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"USE_LANGUAGE_GUESS" => "N",
		"USE_SUGGEST" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?>
<br />
<h2>Найдено на сайте:</h2>
<?$APPLICATION->IncludeComponent("bitrix:search.page", "content", Array(
	"USE_SUGGEST" => "N",	// Показывать подсказку с поисковыми фразами
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"RESTART" => "N",	// Искать без учета морфологии (при отсутствии результата поиска)
	"NO_WORD_LOGIC" => "N",	// Отключить обработку слов как логических операторов
	"USE_LANGUAGE_GUESS" => "N",	// Включить автоопределение раскладки клавиатуры
	"CHECK_DATES" => "N",	// Искать только в активных по дате документах
	"USE_TITLE_RANK" => "N",	// При ранжировании результата учитывать заголовки
	"DEFAULT_SORT" => "rank",	// Сортировка по умолчанию
	"FILTER_NAME" => "",	// Дополнительный фильтр
	"SHOW_WHERE" => "N",	// Показывать выпадающий список "Где искать"
	"SHOW_WHEN" => "N",	// Показывать фильтр по датам
	"PAGE_RESULT_COUNT" => "50",	// Количество результатов на странице
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над результатами
	"DISPLAY_BOTTOM_PAGER" => "N",	// Выводить под результатами
	"PAGER_TITLE" => "Результаты поиска",	// Название результатов поиска
	"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"arrFILTER" => array(	// Ограничение области поиска
		0 => "main",
		1 => "forum",
		2 => "blog",
		3 => "microblog",
		4 => "socialnetwork",
		5 => "socialnetwork_user",
	),
	"arrFILTER_main" => "",	// Путь к файлу начинается с любого из перечисленных
	"arrFILTER_forum" => array(	// Форумы для поиска
		0 => "all",
	),
	"arrFILTER_blog" => array(	// Блоги
		0 => "all",
	),
	"arrFILTER_socialnetwork" => array(	// Группы социальной сети
		0 => "all",
	),
	"arrFILTER_socialnetwork_user" => "",	// Пользователь социальной сети
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>