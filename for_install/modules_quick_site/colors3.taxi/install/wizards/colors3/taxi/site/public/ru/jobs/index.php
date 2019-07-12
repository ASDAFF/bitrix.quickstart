<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Работа в такси");
?>
<p>
<?$APPLICATION->IncludeComponent("bitrix:news.list", "jobs", Array(
	"DISPLAY_DATE" => "N",	// Выводить дату элемента
	"DISPLAY_NAME" => "Y",	// Выводить название элемента
	"DISPLAY_PICTURE" => "N",	// Выводить изображение для анонса
	"DISPLAY_PREVIEW_TEXT" => "N",	// Выводить текст анонса
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"IBLOCK_TYPE" => "jobs",	// Тип информационного блока (используется только для проверки)
	"IBLOCK_ID" => "#JOBS_IBLOCK_ID#",	// Код информационного блока
	"NEWS_COUNT" => "40",	// Количество новостей на странице
	"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
	"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
	"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
	"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
	"FILTER_NAME" => "",	// Фильтр
	"FIELD_CODE" => "",	// Поля
	"PROPERTY_CODE" => "",	// Свойства
	"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
	"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
	"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	"SET_STATUS_404" => "Y",	// Устанавливать статус 404, если не найдены элемент или раздел
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
	"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
	"PARENT_SECTION" => "",	// ID раздела
	"PARENT_SECTION_CODE" => "",	// Код раздела
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
	"DISPLAY_BOTTOM_PAGER" => "N",	// Выводить под списком
	"PAGER_TITLE" => "Новости",	// Название категорий
	"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
	"PAGER_TEMPLATE" => "",	// Название шаблона
	"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
	"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "N",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	),
	false
);?>
</p>
<p>
<?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form",
	"anketa",
	Array(
		"SEF_MODE" => "N",
		"IBLOCK_TYPE" => "jobs",
		"IBLOCK_ID" => "#DRIVER_IBLOCK_ID#",		
		"PROPERTY_CODES" => array(
			0=>"NAME",
			1 => "#PROP_1#",
			2 => "#PROP_2#",
			3 => "#PROP_3#",
			4 => "#PROP_4#",
			5 => "#PROP_5#",
			6 => "#PROP_6#",
			7 => "#PROP_7#",
			8 => "#PROP_8#",
			9 => "#PROP_9#",
			10 => "#PROP_10#",
			11 => "#PROP_11#",
			12 => "#PROP_12#",
			13 => "#PROP_13#",
			14 => "#PROP_14#",
			15 => "#PROP_15#",
			16 => "#PROP_16#",
			17 => "#PROP_17#",
			18 => "#PROP_18#",
			19 => "#PROP_19#",
			20 => "#PROP_20#",
			21 => "#PROP_21#",
			22 => "#PROP_22#",
			23 => "#PROP_23#",
			24 => "#PROP_24#",
			25 => "#PROP_25#",
			26 => "#PROP_26#",
			27 => "#PROP_27#",
			28 => "#PROP_28#",
			29 => "#PROP_29#",
		),
		"PROPERTY_CODES_REQUIRED" => array(
			0=>"NAME",
			1 => "#PROP_1#",
			2 => "#PROP_2#",
			3 => "#PROP_3#",
			4 => "#PROP_4#",
			5 => "#PROP_5#",
			6 => "#PROP_6#",
			8 => "#PROP_8#",
			10 => "#PROP_10#",
			11 => "#PROP_11#",
			12 => "#PROP_12#",
			13 => "#PROP_13#",
			14 => "#PROP_14#",
			16 => "#PROP_16#",			
			26 => "#PROP_26#",
			27 => "#PROP_27#",	
			29 => "#PROP_29#",
		),
		"GROUPS" => array("2"),
		"STATUS_NEW" => "N",
		"STATUS" => "ANY",
		"LIST_URL" => "",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"MAX_USER_ENTRIES" => "100000",
		"MAX_LEVELS" => "100000",
		"LEVEL_LAST" => "Y",
		"USE_CAPTCHA" => "N",
		"USER_MESSAGE_EDIT" => "Спасибо. Ваша анкета принята к рассмотрению.",
		"USER_MESSAGE_ADD" => "Спасибо. Ваша анкета принята к рассмотрению.",
		"DEFAULT_INPUT_SIZE" => "50",
		"RESIZE_IMAGES" => "N",
		"MAX_FILE_SIZE" => "0",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"CUSTOM_TITLE_NAME" => "Фамилия Имя Отчество",
		"CUSTOM_TITLE_TAGS" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
        "TITLE" => "Заполнить анкету водителя"
	),
false
);?>
</p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>