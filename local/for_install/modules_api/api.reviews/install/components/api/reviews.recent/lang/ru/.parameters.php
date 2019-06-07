<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$MESS['CHOOSE']                   = '-- Не выбрано --';
$MESS['API_REVIEWS_MODULE_ERROR'] = 'Модуль "TS Умные отзывы о магазине и о товаре" не установлен';

//BASE
$MESS['CHOOSE']             = '-- Не выбрано --';
$MESS['INCLUDE_CSS']        = 'Подключать встроенные стили CSS';
$MESS['ITEMS_LIMIT']        = 'Элементов на странице';
$MESS['TEXT_LIMIT']         = 'Макс. длина текста';
$MESS['USE_LINK']           = 'Ссылки в тексте кликабельные';
$MESS['ACTIVE_DATE_FORMAT'] = 'Формат даты';
$MESS['SORT_FIELD_1']       = 'Поле первой сортировки';
$MESS['SORT_ORDER_1']       = 'Направление первой сортировки';
$MESS['SORT_FIELD_2']       = 'Поле второй сортировки';
$MESS['SORT_ORDER_2']       = 'Направление второй сортировки';

$MESS['SORT_FIELDS']  = array(
	 'ID'          => 'ID (уникальный айди)',
	 'ACTIVE_FROM' => 'Начало активности',
	 'DATE_CREATE' => 'Дата создания',
	 'RATING'      => 'Оценка (Рейтинг)',
	 'THUMBS_UP'   => 'Полезность +',
	 'THUMBS_DOWN' => 'Полезность -',
	 'REPLY'       => 'С ответом',
);
$MESS['ORDER_FIELDS'] = array(
	 'ASC'  => 'По возрастанию',
	 'DESC' => 'По убыванию',
);

$MESS['DISPLAY_FIELDS']        = 'Выводить поля';
$MESS['DISPLAY_FIELDS_VALUES'] = array(
	 'RATING'       => 'Рейтинг',
	 'ACTIVE_FROM'  => 'Дата',
	 'TITLE'        => 'Заголовок',
	 'ADVANTAGE'    => 'Достоинства',
	 'DISADVANTAGE' => 'Недостатки',
	 'ANNOTATION'   => 'Комментарий',
);

$MESS['REVIEWS_FILTER'] = 'Фильтры / Привязка отзывов';
$MESS['IBLOCK_ID']      = 'ID инфоблока';
$MESS['SECTION_ID']     = 'ID раздела';
$MESS['ELEMENT_ID']     = 'ID элемента';
$MESS['URL']            = 'URL страницы';


//VISUAL
$MESS['HEADER_TITLE']         = 'Заголовок виджета';
$MESS['HEADER_TITLE_DEFAULT'] = 'Отзывы покупателей';
$MESS['FOOTER_TITLE']         = 'Текст ссылки на все отзывы';
$MESS['FOOTER_TITLE_DEFAULT'] = 'Смотреть все отзывы &rarr;';
$MESS['FOOTER_URL']           = 'Ссылка на все отзывы';
$MESS['FOOTER_URL_DEFAULT']   = '/reviews/';

//SHOW_RATING
$MESS['SHOW_RATING'] = 'Выводить рейтинг магазина';

//TIP
$MESS['INCLUDE_CSS_TIP'] = 'Подключаются все встроенные в компонент стили оформления';
$MESS['IBLOCK_ID_TIP']   = 'К заданному ID будут привязываться все новые отзывы.<br>Если вы не разработчик, просто оставьте это поле пустым.<br>Только число';
$MESS['SECTION_ID_TIP']  = 'К заданному ID будут привязываться все новые отзывы.<br>Только число.';
$MESS['ELEMENT_ID_TIP']  = 'К заданному ID будут привязываться все новые отзывы.<br>Только число.';
$MESS['TEXT_LIMIT_TIP']  = 'Если значение 0, то текст не обрезается';
$MESS['USE_LINK_TIP']    = 'Все ссылки в тексте не индексируются и не передают вес страницы';
$MESS['URL_TIP']         = 'К заданному URL будут привязываться все новые отзывы. Хоть число, хоть строка, в БД это поле типа TEXT';
