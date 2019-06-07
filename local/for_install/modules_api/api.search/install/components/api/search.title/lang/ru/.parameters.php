<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_SEARCH_MODULE_ERROR'] = 'Модуль «Умный поиск элементов» не установлен';
$MESS['IBLOCK_MODULE_ERROR']     = 'Модуль «Инфоблоки» не установлен';
$MESS['NOT_SET']                 = '(не выбрано)';


//BASE
$MESS['USE_TITLE_RANK']   = 'Релевантные заголовки самые первые';
$MESS['USE_SEARCH_QUERY'] = 'Не очищать поисковый запрос';
$MESS['ITEMS_LIMIT']      = 'Элементов на странице';
$MESS['RESULT_PAGE']      = 'Страница результатов поиска';

$MESS['SEARCH_MODE']        = 'Режим поиска';
$MESS['SEARCH_MODE_VALUES'] = array(
	'EXACT' => 'Точное совпадение слов',
	'JOIN'  => 'Склонения слов (больше результатов)',
);


$MESS['SORT_BY1']    = 'Поле первой сортировки';
$MESS['SORT_BY2']    = 'Поле второй сортировки';
$MESS['SORT_BY3']    = 'Поле третьей сортировки';
$MESS['SORT_ORDER']  = 'Направление';
$MESS['SORT_FIELDS'] = array(
	'ID'                  => 'ID',
	'NAME'                => 'Название',
	'SORT'                => 'Сортировка',
	'ACTIVE_FROM'         => 'Дата начала активности',
	'TIMESTAMP_X'         => 'Дата последнего изменения',
	'SHOW_COUNTER'        => 'Количество просмотров',
	'SHOWS'               => 'Количество просмотров в среднем',
	'RAND'                => 'Cлучайный порядок',
	'CREATED'             => 'Время создания',
	'CREATED_DATE'        => 'Дата создания без учета времени',
	'HAS_PREVIEW_PICTURE' => 'По наличию картинок анонса',
	'HAS_DETAIL_PICTURE'  => 'По наличию картинок детальных',
);
$MESS['SORT_ORDERS'] = array(
	'ASC'        => 'По возрастанию',
	'NULLS,ASC'  => 'по возрастанию с пустыми значениями в начале выборки',
	'ASC,NULLS'  => 'по возрастанию с пустыми значениями в конце выборки',
	'DESC'       => 'По убыванию',
	'NULLS,DESC' => 'По убыванию с пустыми значениями в начале выборки',
	'DESC,NULLS' => 'По убыванию с пустыми значениями в конце выборки',
);


//VISUAL
$MESS['INPUT_PLACEHOLDER']         = 'Текст внутри поля поиска';
$MESS['INPUT_PLACEHOLDER_DEFAULT'] = 'Поиск';
$MESS['BUTTON_TEXT']               = 'Текст кнопки поиска';
$MESS['BUTTON_TEXT_DEFAULT']       = 'НАЙТИ';
$MESS['RESULT_NOT_FOUND']          = 'Текст ненайденного';
$MESS['RESULT_NOT_FOUND_DEFAULT']  = 'По вашему запросу ничего не найдено...';
$MESS['RESULT_URL_TEXT']           = 'Текст ссылки на все результаты';
$MESS['RESULT_URL_TEXT_DEFAULT']   = 'Смотреть все результаты &rarr;';
$MESS['PICTURE']                   = 'Искать изображение в полях';
$MESS['RESIZE_PICTURE']            = 'Размер изображения';
$MESS['RESIZE_PICTURE_DEFAULT']    = '48x48';
$MESS['PICTURE_VALUES']            = array(
	''                => '(не выбрано)',
	'PREVIEW_PICTURE' => 'Картинка для анонса',
	'DETAIL_PICTURE'  => 'Детальная картинка',
);

//ADDITIONAL_SETTINGS
$MESS['INCLUDE_CSS'] = 'Подключить стили шаблона';


//GROUP_PRICES
$MESS['GROUP_PRICES']            = 'Цены';
$MESS['PRICE_CODE']              = 'Тип цены';
$MESS['PRICE_VAT_INCLUDE']       = 'Включать НДС в цену';
$MESS['CONVERT_CURRENCY']        = 'Показывать цены в одной валюте';
$MESS['CONVERT_CURRENCY_ID']     = 'Валюта, в которую будут сконвертированы цены';
$MESS['USE_CURRENCY_SYMBOL']     = 'Свой символ валюты';
$MESS['CURRENCY_SYMBOL']         = 'html/text символа валюты';
$MESS['CURRENCY_SYMBOL_DEFAULT'] = ' <span class="ruble">&#8381;</span>';
$MESS['PRICE_EXT']               = 'Расширенный вид цен';

//GROUP_JQUERY
$MESS['GROUP_JQUERY']                   = 'jQuery-плагины';
$MESS['INCLUDE_JQUERY']                 = 'Включить jQuery 1.8.3';
$MESS['INCLUDE_JQUERY_TIP']             = 'Включайте только если не работает что-то, например, выпадающие результаты поиска не отображаются';
$MESS['JQUERY_BACKDROP_BACKGROUND']     = 'Цвет фона (background)';
$MESS['JQUERY_BACKDROP_BACKGROUND_TIP'] = 'Цвет фона по умолчанию: #3879D9';
$MESS['JQUERY_BACKDROP_OPACITY']        = 'Прозрачность фона (opacity)';
$MESS['JQUERY_BACKDROP_Z_INDEX']        = 'Позиция фона по z-оси (z-index)';
$MESS['JQUERY_WAIT_TIME']               = 'Время задержки поиска, мс';
$MESS['JQUERY_SEARCH_PARENT_ID']        = 'id или класс фиксированного блока родителя';
$MESS['JQUERY_SEARCH_PARENT_ID_TIP']    = 'Для фиксированного блока, в котором размещен поиск, укажите класс или id этого блока, по умолчанию .api-search-title';
$MESS['JQUERY_SCROLL_THEME']            = 'Тема скролла';

//GROUP_IBLOCK
$MESS['GROUP_IBLOCK'] = 'Источник данных';
$MESS['IBLOCK_TYPE']  = 'Тип инфоблока';
$MESS['IBLOCK_ID']    = 'Инфоблок';

//IBLOCK_ID
$MESS['CATEGORY_NAME']            = '##NAME## - настройка';
$MESS['CATEGORY_TITLE']           = 'Название категории';
$MESS['SEARCH_IN_FIELD']          = 'Искать в полях';
$MESS['SEARCH_IN_PROPERTY']       = 'Искать в свойствах';
$MESS['SEARCH_IN_SECTION']        = 'Искать в заданных разделах';
$MESS['SHOW_FIELD']               = 'Выводить поля';
$MESS['SHOW_PROPERTY']            = 'Выводить свойства';
$MESS['SEARCH_IN_PROPERTY_LABEL'] = 'Свойство -> ';
$MESS['SEARCH_IN_TITLE']          = 'Выводить в шаблоне';
$MESS['SEARCH_IN_FIELD_DEFAULT']  = array(
	''             => '(не выбрано)',
	'NAME'         => 'Название',
	'TAGS'         => 'Теги',
	'PREVIEW_TEXT' => 'Описание анонса',
	'DETAIL_TEXT'  => 'Детальное описание',
);
$MESS['SHOW_FIELD_DEFAULT']       = array(
	''     => '(не выбрано)',
	'TAGS' => 'Теги',
);

$MESS['SEARCH_IN_REGEX']         = 'Изменение поискового запроса пользователя регулярным выражением preg_replace()';
$MESS['SEARCH_IN_REGEX_DEFAULT'] = '';// /[-\s^%]?([\d]{2,}+)[-\s^%]?/i

$MESS['IBLOCK_SECTION_URL'] = 'URL, ведущий на страницу с содержимым раздела';
$MESS['IBLOCK_DETAIL_URL'] = 'URL, ведущий на страницу с содержимым элемента раздела';