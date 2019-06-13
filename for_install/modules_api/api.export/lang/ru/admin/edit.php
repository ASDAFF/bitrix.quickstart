<?
$MESS['AEAE_OPTION_EMPTY']              = '(не выбрано)';
$MESS["AEAE_ADMIN_MENU_ADD"]            = "Добавить";
$MESS["AEAE_ADMIN_MENU_COPY"]           = "Копировать";
$MESS["AEAE_ADMIN_MENU_EDIT"]           = "Изменить";
$MESS["AEAE_ADMIN_MENU_DELETE"]         = "Удалить";
$MESS["AEAE_ADMIN_MENU_CONFIRM_DELETE"] = "Желаете удалить профиль?";
$MESS['AEAE_PAGE_TITLE_ADD']            = 'Новый профиль';
$MESS['AEAE_ROW_ADD_OK']                = 'Информация сохранена';

$MESS['AEAE_TABS'] = array(
	 array(
			"DIV"   => "tab1",
			"TAB"   => '1) Профиль',
			"TITLE" => 'Настройки профиля',
	 ),
	 array(
			"DIV"   => "tab2",
			"TAB"   => '2) Основные',
			"TITLE" => 'Основные настройки экспорта',
	 ),
	 array(
			"DIV"   => "tab3",
			"TAB"   => '3) Инфоблоки',
			"TITLE" => 'Выбор каталогов для экспорта',
	 ),
	 array(
			"DIV"   => "tab4",
			"TAB"   => '4) Условия',
			"TITLE" => 'Условия отбора элементов',
	 ),
	 array(
			"DIV"   => "tab5",
			"TAB"   => '5) Поля <offer>',
			"TITLE" => 'Настройка полей входящих в &lt;offer&gt;',
	 ),
	 /*array(
			"DIV"   => "tab6",
			"TAB"   => '6) Ручной экспорт',
			//"ICON"   => '6) Ручной экспорт',
			"TITLE" => 'Экспорт товаров вручную',
			//"FIELDS" => array(),
	 ),*/
);

//tab_1
//https://support.google.com/merchants/answer/160079?hl=ru&ref_topic=2473799
$MESS['AEAE_PROFILE_CHARSET'] = array(
	 ''             => '(по умолчанию - '. SITE_CHARSET .')',
	 'ASCII'        => 'ASCII',
	 'Windows-1251' => 'Windows-1251',
	 'ISO-8859-1'   => 'ISO-8859-1',
	 'UTF-8'        => 'UTF-8',
	 'UTF-16'       => 'UTF-16',
);

$MESS['AEAE_DEFAULT_FILE_PATH']                              = 'Будет доступен после сохранения профиля';
$MESS['AEAE_TAB_HEADING_SHOP_DESCRIPTION']                   = 'Описание магазина';
$MESS['AEAE_TAB_HEADING_PRICE_TYPE']                         = 'Цены';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY']                      = 'Курсы валют';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY_HINT']                 = '<a href="https://yandex.ru/support/partnermarket/currencies.html" target="_blank">Подробное описание на Яндекс.Маркете</a>';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY_CODE']                 = 'Валюта (id)';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY_RATE']                 = 'Курс (rate)';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY_CONVERT']              = 'Конвертация';
$MESS['AEAE_TAB_HEADING_SHOP_CURRENCY_PLUS']                 = 'Наценка (plus)';
$MESS['AEAE_TAB_HEADING_DELIVERY_OPTIONS']                   = 'Стоимость и сроки доставки';
$MESS['AEAE_TAB_HEADING_DELIVERY_NOTE']                      = 'cost — стоимость доставки | days — срок доставки | order-before — время заказа.
<br>При указании периода «от — до» интервал срока доставки должен составлять не более трех дней.';
$MESS['AEAE_TAB_HEADING_DELIVERY_OPTIONS_HINT']              = '<a href="https://yandex.ru/support/partnermarket/elements/delivery-options.xml" target="_blank">Подробное описание на Яндекс.Маркете</a>';
$MESS['AEAE_TAB_HEADING_DIMENSIONS']                         = 'Габариты (Д/Ш/В)';
$MESS['AEAE_TAB_HEADING_DIMENSIONS_NOTE']                    = 'Формат Яндекс.Маркета: Д/Ш/В | Стандартный формат: ДxШxВ<br>Формат по умолчанию: #LENGTH#/#WIDTH#/#HEIGHT#';
$MESS['AEAE_TAB_HEADING_DIMENSIONS_HINT']                    = '<a href="https://yandex.ru/support/partnermarket/offers.html#offers__dimensions" target="_blank">Подробное описание на Яндекс.Маркете</a>';
$MESS['AEAE_TAB_HEADING_UTM_TAGS']                           = 'Метки UTM';
$MESS['AEAE_TAB_HEADING_UTM_TAGS_NOTE']                      = '<b>Параметры в URL</b> — это переменные, которые могут быть добавлены в ссылку на сайте рекламодателя.<br> 
Они позволяют системам веб-аналитики (Яндекс.Метрика, Google Analytics и др.) получать дополнительную информацию о переходах по этим ссылкам на ваш сайт.<br>
<br>
<b>UTM</b> — это стандарт меток для сбора статистики. Основные имена UTM-меток:<br>
utm_source — источник перехода;<br>
utm_medium — тип трафика;<br>
utm_campaign — название рекламной кампании;<br>
utm_content — дополнительная информация, которая помогает различать объявления;<br>
utm_term — ключевая фраза.<br>
<br>
<b>МАКРОСЫ</b> — заменяются на значения из полей/ключей элемента<br>
#ID# - идентификатор элемента';
$MESS['AEAE_TAB_HEADING_UTM_TAGS_HINT']                      = '<a href="https://yandex.ru/support/direct/statistics/url-tags.html#url-tags__utm" target="_blank">Подробное описание на Яндекс.Директ</a>';
$MESS['AEAE_TAB_HEADING_STOP_WORDS']                         = 'Стоп-слова';
$MESS['AEAE_TAB_HEADING_STOP_WORDS_HINT']                    = 'Перечисленные через | слова будут вырезаться из названия и описания товара, иначе яндекс.маркет заблокирует магазин';
$MESS['AEAE_STOP_WORDS']                                     = '!|скидка|распродажа|дешевый|подарок|бесплатно|акция|специальная цена|только|новинка|new|аналог|заказ|хит|предупреждение|(цвет в ассортименте)';
$MESS['AEAE_TAB_HEADING_EXPORT_DATA']                        = 'Выбор каталога и разделов';
$MESS['AEAE_TAB_HEADING_EXPORT_DATA_HINT']                   = 'Можно выбрать только один каталог';
$MESS['AEAE_TAB_HEADING_ELEMENTS_FILTER']                    = "Фильтр элементов";
$MESS['AEAE_TAB_ELEMENTS_FILTER_ACTIVE_HINT']                = "Учитывать активность";
$MESS['AEAE_TAB_ELEMENTS_FILTER_ACTIVE_DATE_HINT']           = "Учитывать дату активности";
$MESS['AEAE_TAB_ELEMENTS_FILTER_SECTION_ACTIVE_HINT']        = "Учитывать активность разделов";
$MESS['AEAE_TAB_ELEMENTS_FILTER_SECTION_GLOBAL_ACTIVE_HINT'] = "Учитывать активность родительских разделов";
$MESS['AEAE_TAB_ELEMENTS_FILTER_CATALOG_AVAILABLE_HINT']     = "Учитывать доступность товара<br>Товар считается недоступным, если его количество меньше либо равно нулю, включен количественный учет и запрещена покупка при нулевом количестве";
$MESS['AEAE_TAB_HEADING_OFFERS_FILTER']                      = "Фильтр торговых предложений";
$MESS['AEAE_TAB_OFFERS_FILTER_ACTIVE_HINT']                  = "Учитывать активность";
$MESS['AEAE_TAB_OFFERS_FILTER_ACTIVE_DATE_HINT']             = "Учитывать дату активности";
$MESS['AEAE_TAB_OFFERS_FILTER_CATALOG_AVAILABLE_HINT']       = "Учитывать доступность товара<br>Товар считается недоступным, если его количество меньше либо равно нулю, включен количественный учет и запрещена покупка при нулевом количестве";
$MESS['AEAE_CONDITIONS_EDIT_ERROR']                          = "Ошибка при изменении условия";
$MESS['AEAE_CONDITIONS_ADD_ERROR']                           = "Ошибка при добавлении условия";
$MESS['AEAE_TAB_HEADING_TYPE_SWITCH']                        = 'Типы описаний предложений';
$MESS['AEAE_TAB_HEADING_TYPE_SWITCH_HINT']                   = 'При переключении типа настройки в базе не изменяются, только после сохранения';
$MESS['AEAE_TAB_HEADING_TYPE_OFFER']                         = 'Элементы входящие в &lt;offer&gt;';
$MESS['AEAE_TAB_HEADING_FIELD_ADD']                          = 'Добавить кастомное поле';

//tab_6
$MESS['AEAT_EXPORT_SUBMIT_TEXT'] = 'Запустить экспорт';
