<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"bejetstore",
	Array(
		"IBLOCK_TYPE" => "brands",
		"IBLOCK_ID" => BEJET_SELLER_BRANDS,//#BRANDS_IBLOCK_ID#
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => "{$_REQUEST["BRAND_CODE"]}",
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => array(0 => "NAME",1 => "PREVIEW_TEXT",2 => "PREVIEW_PICTURE",3 => "DETAIL_TEXT",4 => "DETAIL_PICTURE"),
		"PROPERTY_CODE" => array("", ""),
		"IBLOCK_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ACTIVE_DATE_FORMAT" => "",
		"USE_PERMISSIONS" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"PAGER_TEMPLATE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Страница",
		"PAGER_SHOW_ALL" => "Y"
	)
);?>
<?include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."catalog/sort_params.php");?>
<?$APPLICATION->IncludeComponent(
	"bejetstore:catalog", 
	".default", 
	array(
		"IBLOCK_TYPE" => "catalog",	// Тип инфоблока
		"IBLOCK_ID" => BEJET_SELLER_CLOTHES,//#CATALOG_IBLOCK_ID#
		"TEMPLATE_THEME" => "site",	// Цветовая тема
		"HIDE_NOT_AVAILABLE" => "N",	// Не отображать товары, которых нет на складах
		"BASKET_URL" => "/personal/cart/",	// URL, ведущий на страницу с корзиной покупателя
		"ACTION_VARIABLE" => "action",	// Название переменной, в которой передается действие
		"PRODUCT_ID_VARIABLE" => "id",	// Название переменной, в которой передается код товара для покупки
		"SECTION_ID_VARIABLE" => "SECTION_ID",	// Название переменной, в которой передается код группы
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",	// Название переменной, в которой передается количество товара
		"PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
		"SEF_MODE" => "N",	// Включить поддержку ЧПУ
		"SEF_FOLDER" => "/catalog/",	// Каталог ЧПУ (относительно корня сайта)
		"AJAX_MODE" => "N",	// Включить режим AJAX
		"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
		"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
		"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
		"CACHE_GROUPS" => "Y",	// Учитывать права доступа
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"ADD_SECTION_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",	// Включать название элемента в цепочку навигации
		"SET_STATUS_404" => "Y",	// Устанавливать статус 404, если не найдены элемент или раздел
		"DETAIL_DISPLAY_NAME" => "N",	// Выводить название элемента
		"USE_ELEMENT_COUNTER" => "Y",	// Использовать счетчик просмотров
		"USE_FILTER" => "Y",	// Показывать фильтр
		"FILTER_NAME" => "",	// Фильтр
		"FILTER_VIEW_MODE" => $filterView,	// Вид отображения умного фильтра
		"FILTER_FIELD_CODE" => array(	// Поля
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(	// Свойства
			0 => "",
			1 => "",
		),
		"FILTER_PRICE_CODE" => array(	// Тип цены
			0 => "BASE",
		),
		"FILTER_OFFERS_FIELD_CODE" => array(	// Поля предложений
			0 => "PREVIEW_PICTURE",
			1 => "DETAIL_PICTURE",
			2 => "",
		),
		"FILTER_OFFERS_PROPERTY_CODE" => array(	// Свойства предложений
			0 => "",
			1 => "",
		),
		"USE_REVIEW" => "Y",	// Разрешить отзывы
		"MESSAGES_PER_PAGE" => "10",	// Количество сообщений на одной странице
		"USE_CAPTCHA" => "Y",	// Использовать CAPTCHA
		"REVIEW_AJAX_POST" => "Y",	// Использовать AJAX в диалогах
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",	// Путь относительно корня сайта к папке со смайлами
		"FORUM_ID" => "11",	// ID форума для отзывов
		"URL_TEMPLATES_READ" => "",	// Страница чтения темы (пусто - получить из настроек форума)
		"SHOW_LINK_TO_FORUM" => "Y",	// Показать ссылку на форум
		"USE_COMPARE" => "N",	// Использовать компонент сравнения
		"PRICE_CODE" => array(	// Тип цены
			0 => "BASE",
		),
		"USE_PRICE_COUNT" => "N",	// Использовать вывод цен с диапазонами
		"SHOW_PRICE_COUNT" => "1",	// Выводить цены для количества
		"PRICE_VAT_INCLUDE" => "Y",	// Включать НДС в цену
		"PRICE_VAT_SHOW_VALUE" => "N",	// Отображать значение НДС
		"PRODUCT_PROPERTIES" => "",	// Характеристики товара, добавляемые в корзину
		"USE_PRODUCT_QUANTITY" => "Y",	// Разрешить указание количества товара
		"CONVERT_CURRENCY" => "Y",	// Показывать цены в одной валюте
		"CURRENCY_ID" => "RUB",	// Валюта, в которую будут сконвертированы цены
		"QUANTITY_FLOAT" => "N",
		"OFFERS_CART_PROPERTIES" => array(	// Свойства предложений, добавляемые в корзину
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
		),
		"SHOW_TOP_ELEMENTS" => "N",	// Выводить топ элементов
		"SECTION_COUNT_ELEMENTS" => "N",	// Показывать количество элементов в разделе
		"SECTION_TOP_DEPTH" => "1",	// Максимальная отображаемая глубина разделов
		"SECTIONS_VIEW_MODE" => "TEXT",	// Вид списка подразделов
		"SECTIONS_SHOW_PARENT_NAME" => "N",	// Показывать название раздела
		"PAGE_ELEMENT_COUNT" => "9",	// Количество элементов на странице
		"LINE_ELEMENT_COUNT" => "3",	// Количество элементов, выводимых в одной строке таблицы
		"ELEMENT_SORT_FIELD" => "{$arSortParams["ELEMENT_SORT_FIELD"]}",	// По какому полю сортируем товары в разделе
		"ELEMENT_SORT_ORDER" => "{$arSortParams["ELEMENT_SORT_ORDER"]}",	// Порядок сортировки товаров в разделе
		"ELEMENT_SORT_FIELD2" => "{$arSortParams["ELEMENT_SORT_FIELD2"]}",	// Поле для второй сортировки товаров в разделе
		"ELEMENT_SORT_ORDER2" => "{$arSortParams["ELEMENT_SORT_ORDER2"]}",	// Порядок второй сортировки товаров в разделе
		"LIST_PROPERTY_CODE" => array(	// Свойства
			0 => "NEWPRODUCT",
			1 => "SALELEADER",
			2 => "SPECIALOFFER",
			3 => "",
		),
		"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
		"LIST_META_KEYWORDS" => "UF_KEYWORDS",	// Установить ключевые слова страницы из свойства раздела
		"LIST_META_DESCRIPTION" => "UF_META_DESCRIPTION",	// Установить описание страницы из свойства раздела
		"LIST_BROWSER_TITLE" => "UF_BROWSER_TITLE",	// Установить заголовок окна браузера из свойства раздела
		"LIST_OFFERS_FIELD_CODE" => array(	// Поля предложений
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(	// Свойства предложений
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
			3 => "MORE_PHOTO",
			4 => "ARTNUMBER",
			5 => "",
		),
		"LIST_OFFERS_LIMIT" => "0",	// Максимальное количество предложений для показа (0 - все)
		"DETAIL_PROPERTY_CODE" => array(	// Свойства
			0 => "NEWPRODUCT",
			1 => "MANUFACTURER",
			2 => "MATERIAL",
			3 => "CHARACTERISTICS"
		),
		"DETAIL_META_KEYWORDS" => "KEYWORDS",	// Установить ключевые слова страницы из свойства
		"DETAIL_META_DESCRIPTION" => "META_DESCRIPTION",	// Установить описание страницы из свойства
		"DETAIL_BROWSER_TITLE" => "TITLE",	// Установить заголовок окна браузера из свойства
		"DETAIL_OFFERS_FIELD_CODE" => array(	// Поля предложений
			0 => "NAME",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(	// Свойства предложений
			0 => "ARTNUMBER",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "COLOR_REF",
			4 => "MORE_PHOTO",
			5 => "",
		),
		"LINK_IBLOCK_TYPE" => "",	// Тип инфоблока, элементы которого связаны с текущим элементом
		"LINK_IBLOCK_ID" => "",	// ID инфоблока, элементы которого связаны с текущим элементом
		"LINK_PROPERTY_SID" => "",	// Свойство, в котором хранится связь
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",	// URL на страницу, где будет показан список связанных элементов
		"USE_ALSO_BUY" => "Y",	// Показывать блок "С этим товаром покупают"
		"ALSO_BUY_ELEMENT_COUNT" => "4",	// Количество элементов для отображения
		"ALSO_BUY_MIN_BUYES" => "1",	// Минимальное количество покупок товара
		"OFFERS_SORT_FIELD" => "sort",	// По какому полю сортируем предложения товара
		"OFFERS_SORT_ORDER" => "asc",	// Порядок сортировки предложений товара
		"OFFERS_SORT_FIELD2" => "id",	// Поле для второй сортировки предложений товара
		"OFFERS_SORT_ORDER2" => "desc",	// Порядок второй сортировки предложений товара
		"PAGER_TEMPLATE" => "bejetstore",	// Шаблон постраничной навигации
		"DISPLAY_TOP_PAGER" => "Y",	// Выводить над списком
		"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
		"PAGER_TITLE" => "Товары",	// Название категорий
		"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
		"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",	// Время кеширования страниц для обратной навигации
		"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
		"ADD_PICT_PROP" => "MORE_PHOTO",	// Дополнительная картинка основного товара
		"LABEL_PROP" => "NEWPRODUCT",	// Свойство меток товара
		"PRODUCT_DISPLAY_MODE" => "Y",	// Схема отображения
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",	// Дополнительные картинки предложения
		"OFFER_TREE_PROPS" => array(	// Свойства для отбора предложений
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
			3 => "",
		),
		"SHOW_DISCOUNT_PERCENT" => "Y",	// Показывать процент скидки
		"SHOW_OLD_PRICE" => "Y",	// Показывать старую цену
		"MESS_BTN_BUY" => "Купить",	// Текст кнопки "Купить"
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",	// Текст кнопки "Добавить в корзину"
		"MESS_BTN_COMPARE" => "Сравнение",	// Текст кнопки "Сравнение"
		"MESS_BTN_DETAIL" => "Подробнее",	// Текст кнопки "Подробнее"
		"MESS_NOT_AVAILABLE" => "Нет в наличии",	// Сообщение об отсутствии товара
		"DETAIL_USE_VOTE_RATING" => "Y",	// Включить рейтинг товара
		"DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",	// В качестве рейтинга показывать
		"DETAIL_USE_COMMENTS" => "Y",	// Включить отзывы о товаре
		"DETAIL_BLOG_USE" => "Y",	// Использовать комментарии
		"DETAIL_VK_USE" => "N",	// Использовать Вконтакте
		"DETAIL_FB_USE" => "N",	// Использовать Facebook
		"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
		"USE_STORE" => "N",	// Показывать блок "Количество товара на складе"
		"USE_STORE_PHONE" => "Y",	// Выводить телефон
		"USE_STORE_SCHEDULE" => "Y",	// Выводить график работы
		"USE_MIN_AMOUNT" => "N",	// Подменять числа на выражения
		"STORE_PATH" => "#SITE_DIR#store/#store_id#",	// Шаблон пути к каталогу STORE (относительно корня)
		"MAIN_TITLE" => "Наличие на складах",	// Заголовок блока
		"MIN_AMOUNT" => "10",
		"DETAIL_BRAND_USE" => "Y",	// Использовать компонент "Бренды"
		"DETAIL_BRAND_PROP_CODE" => "BRAND_REF",	// Таблица с брендами
		"VARIABLE_ALIASES" => array(
			"ELEMENT_CODE" => "ELEMENT_CODE",
		)
	),
	false
);?>
<hr class="i-size-L">
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.viewed.products", 
	"bejetstore", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => BEJET_SELLER_CLOTHES,
		"SHOW_FROM_SECTION" => "Y",
		"HIDE_NOT_AVAILABLE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"PAGE_ELEMENT_COUNT" => "6",
		"LINE_ELEMENT_COUNT" => "6",
		"TEMPLATE_THEME" => "blue",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/cart/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"SHOW_PRODUCTS_2" => "Y",
		"SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
		"SECTION_ELEMENT_CODE" => "",
		"DEPTH" => "2",
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["CONVERPRODUCT_QUANTITY_VARIABLET_CURRENCY"],
		"PROPERTY_CODE_2" => array(
			0 => "MORE_PHOTO",
			1 => "BLOG_COMMENTS_CNT",
			2 => "FORUM_MESSAGE_CNT",
			3 => "vote_count",
			4 => "rating",
			5 => "RECOMMEND",
			6 => "vote_sum",
			7 => "FORUM_TOPIC_ID",
			8 => "MINIMUM_PRICE",
			9 => "",
		),
		"CART_PROPERTIES_2" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO",
		"LABEL_PROP_2" => "NEWPRODUCT",
		"PROPERTY_CODE_3" => array(
			0 => "ARTNUMBER",
			1 => "COLOR_REF",
			2 => "SIZES_SHOES",
			3 => "SIZES_CLOTHES",
			4 => "MORE_PHOTO",
			5 => "",
		),
		"CART_PROPERTIES_3" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "",
		),
		"ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_3" => array(
			0 => "COLOR_REF",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
		),
		"COMPONENT_TEMPLATE" => "bejetstore",
		"SHOW_PRODUCTS_#BEJET_SELLER_CLOTHES#" => "Y",
		"PROPERTY_CODE_#BEJET_SELLER_CLOTHES#" => array(
			0 => "TITLE",
			1 => "KEYWORDS",
			2 => "META_DESCRIPTION",
			3 => "BRAND_REF",
			4 => "NEWPRODUCT",
			5 => "SALELEADER",
			6 => "SALE_RECOMMEND",
			7 => "SPECIALOFFER",
			8 => "ARTNUMBER",
			9 => "MANUFACTURER",
			10 => "MATERIAL",
			11 => "COLOR",
			12 => "BRAND",
			13 => "BLOG_POST_ID",
			14 => "MORE_PHOTO",
			15 => "BLOG_COMMENTS_CNT",
			16 => "FORUM_MESSAGE_CNT",
			17 => "vote_count",
			18 => "rating",
			19 => "RECOMMEND",
			20 => "vote_sum",
			21 => "FORUM_TOPIC_ID",
			22 => "MINIMUM_PRICE",
			23 => "MAXIMUM_PRICE",
			24 => "CHARACTERISTICS",
			25 => "",
		),
		"CART_PROPERTIES_#BEJET_SELLER_CLOTHES#" => array(
			0 => "BRAND_REF",
			1 => "NEWPRODUCT",
			2 => "SALELEADER",
			3 => "SALE_RECOMMEND",
			4 => "SPECIALOFFER",
			5 => "BRAND",
			6 => "RECOMMEND",
			7 => "",
		),
		"ADDITIONAL_PICT_PROP_#BEJET_SELLER_CLOTHES#" => "MORE_PHOTO",
		"LABEL_PROP_#BEJET_SELLER_CLOTHES#" => "-",
		"PROPERTY_CODE_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_#BEJET_SELLER_OFFERS_CLOTHES#" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_#BEJET_SELLER_OFFERS_CLOTHES#" => array(
		)
	),
	false
);?>

<hr class="i-size-L">
<?include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."small_banners.php");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>